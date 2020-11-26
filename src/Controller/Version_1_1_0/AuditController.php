<?php
/*
 *  Copyright (C) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Controller\Version_1_1_0;

use App\Entity\Dagvergunning;
use App\Entity\Markt;
use App\Mapper\DagvergunningMapper;
use App\Repository\DagvergunningRepository;
use App\Repository\MarktRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/1.1.0")
 * @OA\Tag(name="Audit")
 */
class AuditController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var DagvergunningRepository
     */
    private $dagvergunningRepository;

    /**
     * @var MarktRepository
     */
    private $marktRepository;

    /**
     * @var DagvergunningMapper
     */
    private $dagvergunningMapper;

    /**
     * AuditController constructor.
     * @param EntityManagerInterface $this->em
     * @param DagvergunningRepository $dagvergunningRepository
     * @param MarktRepository $marktRepository
     * @param DagvergunningMapper $dagvergunningMapper
     */
    public function __construct(
        EntityManagerInterface $em,
        DagvergunningRepository $dagvergunningRepository,
        MarktRepository $marktRepository,
        DagvergunningMapper $dagvergunningMapper
    ) {
        $this->em = $em;
        $this->dagvergunningRepository = $dagvergunningRepository;
        $this->marktRepository = $marktRepository;
        $this->dagvergunningMapper = $dagvergunningMapper;
    }

    /**
     * Haal de lijst van te auditen dagvergunning op
     *
     * @Route("/audit/{marktId}/{datum}", methods={"GET"})
     * @OA\Parameter(name="marktId", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="datum", in="path", required="true", description="datum YYYY-MM-DD", @OA\Schema(type="string"))
     * @IsGranted("ROLE_USER")
     */
    public function getAction(Request $request, $marktId, $datum)
    {
        $date = new \DateTime($datum);

        $markt = $this->marktRepository->find($marktId);
        if (null === $markt) {
            return new JsonResponse(['error' => 'Market not found']);
        }

        $dagvergunningen = $this->dagvergunningRepository->findBy([
            'audit' => true,
            'markt' => $markt,
            'dag' => $date,
            'doorgehaald' => false,
        ]);

        return new JsonResponse($this->dagvergunningMapper->multipleEntityToModel($dagvergunningen), Response::HTTP_OK,
            ['X-Api-ListSize' => count($dagvergunningen)]);
    }

    /**
     * Haal de lijst van te auditen dagvergunning op
     *
     * @Route("/audit/{marktId}/{datum}", methods={"POST"})
     * @OA\Parameter(name="marktId", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="datum", in="path", required="true", description="datum YYYY-MM-DD", @OA\Schema(type="string"))
     * @IsGranted("ROLE_USER")
     */
    public function postAction($marktId, $datum)
    {
        $now = new \DateTime($datum);
        $now->setTime(0, 0, 0);

        /* @var Markt $markt */
        $markt = $this->marktRepository->find($marktId);
        if (null === $markt) {
            return new JsonResponse(['error' => 'Market not found']);
        }

        $dagvergunningen = $this->dagvergunningRepository->findBy([
            'audit' => true,
            'markt' => $markt,
            'dag' => $now,
            'doorgehaald' => false,
        ]);

        // if there is already an audit list, return it
        if (count($dagvergunningen)) {
            return new JsonResponse($this->dagvergunningMapper->multipleEntityToModel($dagvergunningen), Response::HTTP_OK, ['X-Api-ListSize' => count($dagvergunningen)]);
        }

        $this->em->beginTransaction();

        // alle dagvergunningen van vandaag voor deze markt
        $dagvergunningenToday = $this->dagvergunningRepository->findByMarktAndDag($markt, $now, false);

        // deze lijst zal straks alle dagvergunningen bevatten die gecontroleerd moeten worden
        $audits = [];

        // iedereen met een handhavingsverzoek toevoegen aan de lijst
        foreach ($dagvergunningenToday as $dagvergunning) {
            /* @var $dagvergunning Dagvergunning */
            if (null !== $dagvergunning->getKoopman()->getHandhavingsVerzoek() && $now <= $dagvergunning->getKoopman()->getHandhavingsVerzoek()) {
                $dagvergunning->setAuditReason(Dagvergunning::AUDIT_HANDHAVINGS_VERZOEK);
                $dagvergunning->setAudit(true);
                $audits[] = $dagvergunning;
            }
        }

        // aantallen die worden getrokken uit elke poule
        $aantalPouleA = floor((($markt->getAuditMax() - count($audits)) / 100) * 25); // iedereen - handhavingsverzoek
        $aantalPouleB = ceil((($markt->getAuditMax() - count($audits)) / 100) * 75); // 's ochtends niet zelf aanwezig - handhavingsverzoek

        // maak twee poules waarin iedereen wordt toegevoegd
        $pouleA = (new ArrayCollection($dagvergunningenToday))->filter(function (Dagvergunning $dagvergunning) use ($now) {
            // verwijder iedereen uit deze poule die al in de lijst zit (want handhavingsverzoek)
            if (null !== $dagvergunning->getKoopman()->getHandhavingsVerzoek() && $now <= $dagvergunning->getKoopman()->getHandhavingsVerzoek()) {
                return false;
            }
            // verwijder iedereen uit deze poule die NIET zelf aanwezig was
            if ($dagvergunning->getAanwezig() !== 'zelf') {
                return false;
            }
            return true;
        });
        $pouleB = (new ArrayCollection($dagvergunningenToday))->filter(function (Dagvergunning $dagvergunning) use ($now) {
            // verwijder iedereen uit deze poule die al in de lijst zit (want handhavingsverzoek)
            if (null !== $dagvergunning->getKoopman()->getHandhavingsVerzoek() && $now <= $dagvergunning->getKoopman()->getHandhavingsVerzoek()) {
                return false;
            }
            // verwijder iedereen uit deze poule die zelf aanwezig was
            if ($dagvergunning->getAanwezig() === 'zelf') {
                return false;
            }
            return true;
        });

        $pouleBselected = 0;
        while ($pouleB->count() > 0 && $pouleBselected < $aantalPouleB) {
            $key = array_rand($pouleB->toArray());
            $dagvergunning = $pouleB->get($key);

            $dagvergunning->setAudit(true);
            $dagvergunning->setAuditReason(Dagvergunning::AUDIT_VERVANGER_ZONDER_TOESTEMMING);
            $audits[] = $dagvergunning;
            $pouleB->removeElement($dagvergunning);
            $pouleBselected++;
        }

        $pouleAselected = 0;
        while ($pouleA->count() > 0 && $pouleAselected < $aantalPouleA) {
            $key = array_rand($pouleA->toArray());
            $dagvergunning = $pouleA->get($key);

            $dagvergunning->setAudit(true);
            $dagvergunning->setAuditReason(Dagvergunning::AUDIT_LOTEN);
            $audits[] = $dagvergunning;
            $pouleA->removeElement($dagvergunning);
            $pouleAselected++;
        }

        while ($pouleA->count() > 0 && (count($audits) < ($markt->getAuditMax()))) {
            $key = array_rand($pouleA->toArray());
            $dagvergunning = $pouleA->get($key);

            $dagvergunning->setAudit(true);
            $dagvergunning->setAuditReason(Dagvergunning::AUDIT_LOTEN);
            $audits[] = $dagvergunning;
            $pouleA->removeElement($dagvergunning);
        }

        $this->em->flush();
        $this->em->commit();

        return new JsonResponse($this->dagvergunningMapper->multipleEntityToModel($audits),
            Response::HTTP_OK,
            ['X-Api-ListSize' => count($audits)]
        );
    }

    /**
     * Reset te auditen dagvergunning op een markt en dag
     *
     * @Route("/audit_reset/{marktId}/{datum}", methods={"POST"})
     * @OA\Parameter(name="marktId", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="datum", in="path", required="true", description="datum YYYY-MM-DD", @OA\Schema(type="string"))
     * @IsGranted("ROLE_SENIOR")
     */
    public function resetAction($marktId, $datum)
    {
        $date = new \DateTime($datum);

        $markt = $this->marktRepository->find($marktId);
        if (null === $markt) {
            return new JsonResponse(['error' => 'Market not found']);
        }
        /**
         * @var Markt $markt
         */

        $dagvergunningen = $this->dagvergunningRepository->findBy([
            'audit' => true,
            'markt' => $markt,
            'dag' => $date,
        ]);
        /**
         * @var Dagvergunning[] $dagvergunningen
         */
        foreach ($dagvergunningen as $dagvergunning) {
            $dagvergunning->setAudit(false);
            $dagvergunning->setAuditReason(null);
            foreach ($dagvergunning->getVergunningControles() as $controle) {
                $this->em->remove($controle);
            }
        }

        $this->em->flush();

        return new JsonResponse([],
            Response::HTTP_OK,
            ['X-Api-ListSize' => 0]);
    }
}
