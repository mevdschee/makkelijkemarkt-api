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

use App\Entity\Concreetplan;
use App\Entity\Factuur;
use App\Entity\Lineairplan;
use App\Entity\Product;
use App\Entity\Tariefplan;
use App\Mapper\FactuurMapper;
use App\Mapper\MarktMapper;
use App\Mapper\TariefplanMapper;
use App\Repository\DagvergunningRepository;
use App\Repository\FactuurRepository;
use App\Repository\MarktRepository;
use App\Repository\TariefplanRepository;
use App\Service\FactuurService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("1.1.0")
 * @OA\Tag(name="Tariefplan")
 */
class TariefplanController extends AbstractController
{
    /**
     * Retourneert alle tariefplannen voor een markt
     *
     * @Route("/tariefplannen/list/{marktId}", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function listAction(
        MarktRepository $marktRepo,
        TariefplanRepository $tariefplanRepo,
        MarktMapper $mapper,
        $marktId
    ) {
        $markt = $marktRepo->findOneById($marktId);

        if (null === $markt) {
            return new JsonResponse(array(), Response::HTTP_OK, ['X-Api-ListSize' => 0]);
        }

        $tariefplannen = $tariefplanRepo->findBy(array('markt' => $markt), array('geldigVanaf' => 'DESC'));

        $response = $mapper->multipleEntityToModel($tariefplannen);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($tariefplannen)]);
    }

    /**
     * Retourneert een tariefplan
     *
     * @Route("/tariefplannen/get/{tariefPlanId}", methods={"GET"})
     * @OA\Parameter(name="tariefPlanId", in="path", required="true", description="Tariefplan id", @OA\Schema(type="integer"))
     * @IsGranted("ROLE_USER")
     */
    public function getAction(
        TariefplanRepository $tariefplanRepo,
        TariefplanMapper $mapper,
        $tariefPlanId
    ) {
        $tariefplan = $tariefplanRepo->findOneById($tariefPlanId);

        if (null === $tariefplan) {
            return new JsonResponse(array(), Response::HTTP_OK, ['X-Api-ListSize' => 0]);
        }

        $response = $mapper->singleEntityToModel($tariefplan);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Verwijdert een tariefplan
     *
     * @Route("/tariefplannen/delete/{tariefPlanId}", methods={"DELETE"})
     * @OA\Parameter(name="tariefPlanId", in="path", required="true", description="Tariefplan id", @OA\Schema(type="integer"))
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction(
        EntityManagerInterface $em,
        TariefplanRepository $tariefplanRepo,
        $tariefPlanId
    ) {
        $tariefplan = $tariefplanRepo->findOneById($tariefPlanId);

        if (null === $tariefplan) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        $lineairplan = $tariefplan->getLineairplan();
        if (null !== $lineairplan) {
            $lineairplan->setTariefplan(null);
            $tariefplan->setLineairplan(null);
            $em->flush();
            $em->remove($lineairplan);
        }

        $concreetplan = $tariefplan->getConcreetplan();
        if (null !== $concreetplan) {
            $concreetplan->setTariefplan(null);
            $tariefplan->setConcreetplan(null);
            $em->flush();
            $em->remove($concreetplan);
        }

        $em->remove($tariefplan);
        $em->flush();

        return new JsonResponse(true, Response::HTTP_OK);
    }

    /**
     * Maak een nieuw lineair tariefplan
     *
     * @Route("/tariefplannen/{marktId}/create/lineair", methods={"POST"})
     * @OA\Parameter(name="marktId", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="naam", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="geldigVanaf", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="geldigTot", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="tariefPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="reinigingPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="toeslagBedrijfsafvalPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="toeslagKrachtstroomPerAansluiting", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="promotieGeldenPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="promotieGeldenPerKraam", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="afvaleiland", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="eenmaligElektra", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="elektra", in="body", @OA\Schema(type="string"), required="true")
     * @IsGranted("ROLE_ADMIN")
     */
    public function postLineairAction(
        EntityManagerInterface $em,
        MarktRepository $marktRepo,
        TariefplanMapper $mapper,
        Request $request,
        $marktId
    ) {
        $markt = $marktRepo->findOneById($marktId);

        if (null === $markt) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        // parse body content
        $message = json_decode($request->getContent(false), true);

        $tariefplan = new Tariefplan();
        $markt->addTariefplannen($tariefplan);
        $tariefplan->setMarkt($markt);

        $lineairplan = new Lineairplan();
        $lineairplan->setTariefplan($tariefplan);
        $tariefplan->setLineairplan($lineairplan);

        $this->processLineairPlan($tariefplan, $lineairplan, $message);

        $em->persist($tariefplan);
        $em->persist($lineairplan);
        $em->flush();

        // return
        $result = $mapper->singleEntityToModel($tariefplan);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Update een lineair tariefplan
     *
     * @Route("/tariefplannen/{tariefPlanId}/update/lineair", methods={"POST"})
     * @OA\Parameter(name="tariefPlanId", in="path", required="true", description="Tariefplan id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="naam", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="geldigVanaf", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="geldigTot", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="tariefPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="reinigingPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="toeslagBedrijfsafvalPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="toeslagKrachtstroomPerAansluiting", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="promotieGeldenPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="promotieGeldenPerKraam", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="afvaleiland", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="eenmaligElektra", in="body", @OA\Schema(type="string"), required="true")
     * @IsGranted("ROLE_ADMIN")
     */
    public function updateLineairAction(
        EntityManagerInterface $em,
        TariefplanRepository $tariefPlanRepo,
        TariefplanMapper $mapper,
        Request $request,
        $tariefPlanId
    ) {
        $tariefplan = $tariefPlanRepo->findOneById($tariefPlanId);

        if (null === $tariefplan) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        $lineairplan = $tariefplan->getLineairplan();
        if (null === $lineairplan) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        // parse body content
        $message = json_decode($request->getContent(false), true);

        $this->processLineairPlan($tariefplan, $lineairplan, $message);

        $em->flush();

        // return
        $result = $mapper->singleEntityToModel($tariefplan);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    protected function processLineairPlan(Tariefplan &$tariefplan, Lineairplan &$lineairplan, $message)
    {
        $tariefplan->setNaam($message['naam']);
        $geldigVanaf = new \DateTime($message['geldigVanaf']['date']);
        $tariefplan->setGeldigVanaf($geldigVanaf);
        $geldigTot = new \DateTime($message['geldigTot']['date']);
        $tariefplan->setGeldigTot($geldigTot);

        $lineairplan->setTariefPerMeter($message['tariefPerMeter']);
        $lineairplan->setReinigingPerMeter($message['reinigingPerMeter']);
        $lineairplan->setToeslagBedrijfsafvalPerMeter($message['toeslagBedrijfsafvalPerMeter']);
        $lineairplan->setToeslagKrachtstroomPerAansluiting($message['toeslagKrachtstroomPerAansluiting']);
        $lineairplan->setPromotieGeldenPerMeter($message['promotieGeldenPerMeter']);
        $lineairplan->setPromotieGeldenPerKraam($message['promotieGeldenPerKraam']);
        $lineairplan->setAfvaleiland($message['afvaleiland']);
        $lineairplan->setEenmaligElektra($message['eenmaligElektra']);
        $lineairplan->setElektra($message['elektra']);
    }

    protected function processConcreetPlan(Tariefplan &$tariefplan, Concreetplan &$concreetplan, $message)
    {
        $tariefplan->setNaam($message['naam']);
        $geldigVanaf = new \DateTime($message['geldigVanaf']['date']);
        $tariefplan->setGeldigVanaf($geldigVanaf);
        $geldigTot = new \DateTime($message['geldigTot']['date']);
        $tariefplan->setGeldigTot($geldigTot);

        $concreetplan->setEenMeter($message['een_meter']);
        $concreetplan->setDrieMeter($message['drie_meter']);
        $concreetplan->setVierMeter($message['vier_meter']);
        $concreetplan->setElektra($message['elektra']);
        $concreetplan->setPromotieGeldenPerMeter($message['promotieGeldenPerMeter']);
        $concreetplan->setPromotieGeldenPerKraam($message['promotieGeldenPerKraam']);
        $concreetplan->setAfvaleiland($message['afvaleiland']);
        $concreetplan->setEenmaligElektra($message['eenmaligElektra']);

    }

    /**
     * Maak een nieuw concreet tariefplan
     *
     * @Route("/tariefplannen/{marktId}/create/concreet", methods={"POST"})
     * @OA\Parameter(name="marktId", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="naam", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="geldigVanaf", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="geldigTot", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="een_meter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="drie_meter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="vier_meter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="elektra", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="promotieGeldenPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="promotieGeldenPerKraam", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="afvaleiland", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="eenmaligElektra", in="body", @OA\Schema(type="string"), required="true")
     * @IsGranted("ROLE_ADMIN")
     */
    public function postConcreetAction(
        EntityManagerInterface $em,
        MarktRepository $marktRepo,
        TariefplanMapper $mapper,
        Request $request,
        $marktId
    ) {
        $markt = $marktRepo->findOneById($marktId);

        if (null === $markt) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        // parse body content
        $message = json_decode($request->getContent(false), true);

        $tariefplan = new Tariefplan();
        $markt->addTariefplannen($tariefplan);
        $tariefplan->setMarkt($markt);

        $concreetplan = new Concreetplan();
        $concreetplan->setTariefplan($tariefplan);
        $tariefplan->setConcreetplan($concreetplan);

        $this->processConcreetPlan($tariefplan, $concreetplan, $message);

        $em->persist($tariefplan);
        $em->persist($concreetplan);
        $em->flush();

        // return
        $result = $mapper->singleEntityToModel($tariefplan);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Update een concreet tariefplan
     *
     * @Route("/tariefplannen/{tariefPlanId}/update/concreet", methods={"POST"})
     * @OA\Parameter(name="tariefPlanId", in="path", required="true", description="Tariefplan id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="naam", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="geldigVanaf", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="geldigTot", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="een_meter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="drie_meter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="vier_meter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="elektra", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="promotieGeldenPerMeter", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="promotieGeldenPerKraam", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="afvaleiland", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="eenmaligElektra", in="body", @OA\Schema(type="string"), required="true")
     * @IsGranted("ROLE_ADMIN")
     */
    public function updateConcreetAction(
        EntityManagerInterface $em,
        TariefplanRepository $tariefPlanRepo,
        TariefplanMapper $mapper,
        Request $request,
        $tariefPlanId
    ) {
        $tariefplan = $tariefPlanRepo->findOneById($tariefPlanId);

        if (null === $tariefplan) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        $concreetplan = $tariefplan->getConcreetplan();
        if (null === $concreetplan) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        // parse body content
        $message = json_decode($request->getContent(false), true);

        $this->processConcreetPlan($tariefplan, $concreetplan, $message);

        $em->flush();

        // return
        $result = $mapper->singleEntityToModel($tariefplan);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Retouneert een factuur concept
     *
     * @Route("/factuur/concept/{dagvergunningId}", methods={"GET"})
     * @OA\Parameter(name="dagvergunningId", in="path", required="true", description="Dagvergunning id", @OA\Schema(type="integer"))
     * @IsGranted("ROLE_USER")
     */
    public function factuurConceptAction(
        DagvergunningRepository $dagvergunningRepo,
        FactuurService $factuurService,
        FactuurMapper $mapper,
        $dagvergunningId
    ) {
        $dagvergunning = $dagvergunningRepo->findOneById($dagvergunningId);

        if (null === $dagvergunning) {
            return new JsonResponse(null, Response::HTTP_OK);
        }

        $factuur = $factuurService->createFactuur($dagvergunning);
        $factuurService->saveFactuur($factuur);
        $response = $mapper->singleEntityToModel($factuur);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Factuur overzicht
     *
     * @Route("/report/factuur/overzicht/{van}/{tot}", methods={"GET"})
     * @OA\Parameter(name="van", in="path", required="true", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @OA\Parameter(name="tot", in="path", required="true", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @IsGranted("ROLE_SENIOR")
     */
    public function factuurOverzichtAction(
        FactuurRepository $factuurRepo,
        FactuurService $factuurService,
        $van,
        $tot
    ) {
        $facturen = $factuurRepo->getFacturenByDateRange($van, $tot);

        $response = [
            'markten' => [],
            'totaal' => 0,
            'solltotaal' => 0,
        ];
        foreach ($facturen as $factuur) {
            $dagvergunning = $factuur->getDagvergunning();
            $markt = $dagvergunning->getMarkt();
            if (!isset($response['markten'][$markt->getId()])) {
                $response['markten'][$markt->getId()] = [
                    'id' => $markt->getId(),
                    'naam' => $markt->getNaam(),
                    'soll' => 0,
                    'totaal' => 0,
                ];
            }
            $totaal = $factuurService->getTotaal($factuur);
            $response['markten'][$markt->getId()]['totaal'] += $totaal;
            $response['totaal'] += $totaal;
            $response['markten'][$markt->getId()]['soll']++;
            $response['solltotaal']++;
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Markt Factuur overzicht
     *
     * @Route("/report/factuur/overzichtmarkt/{marktId}/{van}/{tot}", methods={"GET"})
     * @OA\Parameter(name="marktId", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="van", in="path", required="true", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @OA\Parameter(name="tot", in="path", required="true", description="Als yyyy-mm-dd", @OA\Schema(type="string"))
     * @IsGranted("ROLE_SENIOR")
     */
    public function factuurOverzichtMarktAction(
        MarktRepository $marktRepo,
        FactuurRepository $factuurRepo,
        $marktId,
        $van,
        $tot
    ) {

        $markt = $marktRepo->findOneById($marktId);
        /**
         * @var Factuur[] $facturen
         */
        $facturen = $factuurRepo->getFacturenByDateRangeAndMarkt($markt, $van, $tot);

        $response = [];

        foreach ($facturen as $factuur) {
            $arr = [];
            $dagvergunning = $factuur->getDagvergunning();
            $koopman = $dagvergunning->getKoopman();
            $producten = $factuur->getProducten();
            $arr['dagvergunningId'] = $dagvergunning->getId();
            $arr['koopmanErkenningsnummer'] = $koopman->getErkenningsnummer();
            $arr['dag'] = $dagvergunning->getDag();
            $arr['voorletters'] = $koopman->getVoorletters();
            $arr['achternaam'] = $koopman->getAchternaam();
            foreach ($producten as $product) {
                /**
                 * @var Product $product
                 */
                $arr['productNaam'] = $product->getNaam();
                $arr['productAantal'] = $product->getAantal();
                $arr['productBedrag'] = $product->getBedrag();
                $response[] = $arr;
            }
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
