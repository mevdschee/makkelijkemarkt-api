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

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Concreetplan;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Factuur;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Lineairplan;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Markt;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Product;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Tariefplan;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class TariefplanController
 * @package GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Controller\Version_1_1_0
 * @Route("1.1.0")
 */
class TariefplanController extends Controller
{
    /**
     * Retourneert alle tariefplannen voor een markt
     *
     * @Method("GET")
     * @Route("/tariefplannen/list/{marktId}")
     * @ApiDoc(
     *  section="Tariefplan",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function listAction($marktId)
    {
        $marktRepo = $this->getDoctrine()->getRepository('AppApiBundle:Markt');
        $tariefplanRepo = $this->getDoctrine()->getRepository('AppApiBundle:Tariefplan');

        /**
         * @var Markt $markt
         */
        $markt = $marktRepo->findOneById($marktId);

        if (null === $markt) {
            return new JsonResponse(array(), Response::HTTP_OK, ['X-Api-ListSize' => 0]);
        }

        $tariefplannen = $tariefplanRepo->findBy(array('markt' => $markt), array('geldigVanaf'=>'DESC'));

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\MarktMapper */
        $mapper = $this->get('appapi.mapper.tariefplan');
        $response = $mapper->multipleEntityToModel($tariefplannen);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($tariefplannen)]);
    }

    /**
     * Retourneert een tariefplan
     *
     * @Method("GET")
     * @Route("/tariefplannen/get/{tariefPlanId}")
     * @ApiDoc(
     *  section="Tariefplan",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getAction($tariefPlanId)
    {
        $tariefplanRepo = $this->getDoctrine()->getRepository('AppApiBundle:Tariefplan');

        /**
         * @var TariefPlan $tariefplan
         */
        $tariefplan = $tariefplanRepo->findOneById($tariefPlanId);

        if (null === $tariefplan) {
            return new JsonResponse(array(), Response::HTTP_OK, ['X-Api-ListSize' => 0]);
        }

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\TariefPlanMapper */
        $mapper = $this->get('appapi.mapper.tariefplan');
        $response = $mapper->singleEntityToModel($tariefplan);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Verwijdert een tariefplan
     *
     * @Method("DELETE")
     * @Route("/tariefplannen/delete/{tariefPlanId}")
     * @ApiDoc(
     *  section="Tariefplan",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction($tariefPlanId)
    {
        $tariefplanRepo = $this->getDoctrine()->getRepository('AppApiBundle:Tariefplan');

        /**
         * @var TariefPlan $tariefplan
         */
        $tariefplan = $tariefplanRepo->findOneById($tariefPlanId);

        if (null === $tariefplan) {
            return new JsonResponse(false, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();

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
     * @Method("POST")
     * @Route("/tariefplannen/{marktId}/create/lineair")
     * @ApiDoc(
     *  section="Tariefplan",
     *  parameters={
     *      {"name"="naam", "dataType"="string", "required"="true"},
     *      {"name"="geldigVanaf", "dataType"="string", "required"="true"},
     *      {"name"="geldigTot", "dataType"="string", "required"="true"},
     *      {"name"="tariefPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="reinigingPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="toeslagBedrijfsafvalPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="toeslagKrachtstroomPerAansluiting", "dataType"="string", "required"="true"},
     *      {"name"="promotieGeldenPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="promotieGeldenPerKraam", "dataType"="string", "required"="true"},
     *      {"name"="afvaleiland", "dataType"="string", "required"="true"},
     *      {"name"="eenmaligElektra", "dataType"="string", "required"="true"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function postLineairAction(Request $request, $marktId)
    {
        $marktRepo = $this->getDoctrine()->getRepository('AppApiBundle:Markt');

        /**
         * @var Markt $markt
         */
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

        $em = $this->getDoctrine()->getManager();
        $em->persist($tariefplan);
        $em->persist($lineairplan);
        $em->flush();

        // return
        $result = $this->get('appapi.mapper.tariefplan')->singleEntityToModel($tariefplan);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Update een lineair tariefplan
     *
     * @Method("POST")
     * @Route("/tariefplannen/{tariefPlanId}/update/lineair")
     * @ApiDoc(
     *  section="Tariefplan",
     *  parameters={
     *      {"name"="naam", "dataType"="string", "required"="true"},
     *      {"name"="geldigVanaf", "dataType"="string", "required"="true"},
     *      {"name"="geldigTot", "dataType"="string", "required"="true"},
     *      {"name"="tariefPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="reinigingPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="toeslagBedrijfsafvalPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="toeslagKrachtstroomPerAansluiting", "dataType"="string", "required"="true"},
     *      {"name"="promotieGeldenPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="promotieGeldenPerKraam", "dataType"="string", "required"="true"},
     *      {"name"="afvaleiland", "dataType"="string", "required"="true"},
     *      {"name"="eenmaligElektra", "dataType"="string", "required"="true"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateLineairAction(Request $request, $tariefPlanId)
    {
        $tariefPlanRepo = $this->getDoctrine()->getRepository('AppApiBundle:Tariefplan');

        /**
         * @var TariefPlan $tariefPlan
         */
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

        $this->getDoctrine()->getManager()->flush();

        // return
        $result = $this->get('appapi.mapper.tariefplan')->singleEntityToModel($tariefplan);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    protected function processLineairPlan(Tariefplan &$tariefplan, Lineairplan &$lineairplan, $message) {
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
    }


    protected function processConcreetPlan(Tariefplan &$tariefplan, Concreetplan &$concreetplan, $message) {
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
     * @Method("POST")
     * @Route("/tariefplannen/{marktId}/create/concreet")
     * @ApiDoc(
     *  section="Tariefplan",
     *  parameters={
     *      {"name"="naam", "dataType"="string", "required"="true"},
     *      {"name"="geldigVanaf", "dataType"="string", "required"="true"},
     *      {"name"="geldigTot", "dataType"="string", "required"="true"},
     *      {"name"="een_meter", "dataType"="string", "required"="true"},
     *      {"name"="drie_meter", "dataType"="string", "required"="true"},
     *      {"name"="vier_meter", "dataType"="string", "required"="true"},
     *      {"name"="elektra", "dataType"="string", "required"="true"},
     *      {"name"="promotieGeldenPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="promotieGeldenPerKraam", "dataType"="string", "required"="true"},
     *      {"name"="afvaleiland", "dataType"="string", "required"="true"},
     *      {"name"="eenmaligElektra", "dataType"="string", "required"="true"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function postConcreetAction(Request $request, $marktId)
    {
        $marktRepo = $this->getDoctrine()->getRepository('AppApiBundle:Markt');

        /**
         * @var Markt $markt
         */
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

        $em = $this->getDoctrine()->getManager();
        $em->persist($tariefplan);
        $em->persist($concreetplan);
        $em->flush();

        // return
        $result = $this->get('appapi.mapper.tariefplan')->singleEntityToModel($tariefplan);
        return new JsonResponse($result, Response::HTTP_OK);
    }



    /**
     * Update een concreet tariefplan
     *
     * @Method("POST")
     * @Route("/tariefplannen/{tariefPlanId}/update/concreet")
     * @ApiDoc(
     *  section="Tariefplan",
     *  parameters={
     *      {"name"="naam", "dataType"="string", "required"="true"},
     *      {"name"="geldigVanaf", "dataType"="string", "required"="true"},
     *      {"name"="geldigTot", "dataType"="string", "required"="true"},
     *      {"name"="een_meter", "dataType"="string", "required"="true"},
     *      {"name"="drie_meter", "dataType"="string", "required"="true"},
     *      {"name"="vier_meter", "dataType"="string", "required"="true"},
     *      {"name"="elektra", "dataType"="string", "required"="true"},
     *      {"name"="promotieGeldenPerMeter", "dataType"="string", "required"="true"},
     *      {"name"="promotieGeldenPerKraam", "dataType"="string", "required"="true"},
     *      {"name"="afvaleiland", "dataType"="string", "required"="true"},
     *      {"name"="eenmaligElektra", "dataType"="string", "required"="true"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function updateConcreetAction(Request $request, $tariefPlanId)
    {
        $tariefPlanRepo = $this->getDoctrine()->getRepository('AppApiBundle:Tariefplan');

        /**
         * @var TariefPlan $tariefplan
         */
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

        $this->getDoctrine()->getManager()->flush();

        // return
        $result = $this->get('appapi.mapper.tariefplan')->singleEntityToModel($tariefplan);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Retouneert een factuur concept
     *
     * @Method("GET")
     * @Route("/factuur/concept/{dagvergunningId}")
     * @ApiDoc(
     *  section="Tariefplan",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function factuurConceptAction($dagvergunningId)
    {
        $dagvergunningRepo = $this->getDoctrine()->getRepository('AppApiBundle:Dagvergunning');
        $factuurService = $this->get('appapi.factuurservice');

        /**
         * @var Dagvergunning $dagvergunning
         */
        $dagvergunning = $dagvergunningRepo->findOneById($dagvergunningId);

        if (null === $dagvergunning) {
            return new JsonResponse(null, Response::HTTP_OK);
        }

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\FactuurMapper */
        $mapper = $this->get('appapi.mapper.factuur');

        $factuur = $factuurService->createFactuur($dagvergunning);
        $factuurService->saveFactuur($factuur);
        $response = $mapper->singleEntityToModel($factuur);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Factuur overzicht
     *
     * @Method("GET")
     * @Route("/report/factuur/overzicht/{van}/{tot}")
     * @ApiDoc(
     *  section="Tariefplan",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function factuurOverzichtAction($van, $tot)
    {
        $factuurRepo = $this->getDoctrine()->getRepository('AppApiBundle:Factuur');
        $factuurService = $this->get('appapi.factuurservice');

        /**
         * @var Factuur[] $facturen
         */
        $facturen = $factuurRepo->getFacturenByDateRange($van, $tot);

        $response = [
            'markten'    => [],
            'totaal'     => 0,
            'solltotaal' => 0
        ];
        foreach ($facturen as $factuur) {
            $dagvergunning = $factuur->getDagvergunning();
            $markt = $dagvergunning->getMarkt();
            if (!isset($response['markten'][$markt->getId()])) {
                $response['markten'][$markt->getId()] = [
                    'id'     => $markt->getId(),
                    'naam'   => $markt->getNaam(),
                    'soll'   => 0,
                    'totaal' => 0
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
     * @Method("GET")
     * @Route("/report/factuur/overzichtmarkt/{marktId}/{van}/{tot}")
     * @ApiDoc(
     *  section="Tariefplan",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function factuurOverzichtMarktAction($marktId, $van, $tot)
    {
        $marktRepo = $this->getDoctrine()->getRepository('AppApiBundle:Markt');
        $factuurRepo = $this->getDoctrine()->getRepository('AppApiBundle:Factuur');
        $factuurMapper = $this->get('appapi.mapper.factuur');


        $markt = $marktRepo->findOneById($marktId);
        /**
         * @var Factuur[] $facturen
         */
        $facturen = $factuurRepo->getFacturenByDateRangeAndMarkt($markt, $van, $tot);

        $response = [];

        foreach ($facturen as $factuur) {
            $arr = [];
            $dagvergunning = $factuur->getDagvergunning();
            $koopman       = $dagvergunning->getKoopman();
            $producten     = $factuur->getProducten();
            $arr['dagvergunningId']         = $dagvergunning->getId();
            $arr['koopmanErkenningsnummer'] = $koopman->getErkenningsnummer();
            $arr['dag']                     = $dagvergunning->getDag();
            $arr['voorletters']             = $koopman->getVoorletters();
            $arr['achternaam']              = $koopman->getAchternaam();
            foreach ($producten as $product) {
                /**
                 * @var Product $product
                 */
                $arr['productNaam']   = $product->getNaam();
                $arr['productAantal'] = $product->getAantal();
                $arr['productBedrag'] = $product->getBedrag();
                $response[] = $arr;
            }
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
