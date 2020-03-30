<?php
/*
 *  Copyright (C) 2017 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Controller\Version_1_1_0;

use Doctrine\ORM\EntityManagerInterface;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\DagvergunningRepository;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\MarktRepository;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\KoopmanRepository;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\DagvergunningMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("1.1.0")
 */
class AuditController extends Controller
{
    /**
     * @var DagvergunningRepository
     */
    private $dagvergunningRepository;

    /**
     * @var MarktRepository
     */
    private $marktRepository;

    /**
     * @var KoopmanRepository
     */
    private $koopmanRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var DagvergunningMapper
     */
    private $dagvergunningMapper;

    /**
     * AuditController constructor.
     * @param DagvergunningRepository $dagvergunningRepository
     * @param MarktRepository $marktRepository
     * @param KoopmanRepository $koopmanRepository
     * @param EntityManagerInterface $em
     * @param DagvergunningMapper $dagvergunningMapper
     */
    public function __construct(
        DagvergunningRepository $dagvergunningRepository,
        MarktRepository $marktRepository,
        KoopmanRepository $koopmanRepository,
        EntityManagerInterface $em,
        DagvergunningMapper $dagvergunningMapper
    ) {
        $this->dagvergunningRepository = $dagvergunningRepository;
        $this->marktRepository = $marktRepository;
        $this->dagvergunningMapper = $dagvergunningMapper;
        $this->em = $em;
    }

    /**
     * Haal de lijst van te auditen dagvergunning op
     *
     * @Method("GET")
     * @Route("/audit/{marktId}/{datum}")
     * @ApiDoc(
     *  section="Audit",
     *  requirements={
     *      {"name"="marktId", "dataType"="integer"},
     *      {"name"="datum", "dataType"="string", "description"="datum YYYY-MM-DD"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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
     * @Method("POST")
     * @Route("/audit/{marktId}/{datum}")
     * @ApiDoc(
     *  section="Audit",
     *  requirements={
     *      {"name"="marktId", "dataType"="integer"},
     *      {"name"="datum", "dataType"="string", "description"="datum YYYY-MM-DD"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function postAction(Request $request, $marktId, $datum)
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
     * @Method("POST")
     * @Route("/audit_reset/{marktId}/{datum}")
     * @ApiDoc(
     *  section="Audit",
     *  requirements={
     *      {"name"="marktId", "dataType"="integer"},
     *      {"name"="datum", "dataType"="string", "description"="datum YYYY-MM-DD"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_SENIOR')")
     */
    public function resetAction(Request $request, $marktId, $datum)
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
