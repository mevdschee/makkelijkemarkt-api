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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Route("1.1.0")
 */
class MarktController extends Controller
{
    /**
     * Zoek door alle markten
     *
     * @Method("GET")
     * @Route("/markt/")
     * @ApiDoc(
     *  section="Markt",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function listAction(Request $request)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\MarktRepository */
        $repo = $this->get('appapi.repository.markt');

        $results = $repo->findAll();

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\MarktMapper */
        $mapper = $this->get('appapi.mapper.markt');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Vraag een markt op
     *
     * @Method("GET")
     * @Route("/markt/{id}")
     * @ApiDoc(
     *  section="Markt",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function getAction($id)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\MarktRepository */
        $repo = $this->get('appapi.repository.markt');

        $result = $repo->findOneById($id);

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\MarktMapper */
        $mapper = $this->get('appapi.mapper.markt');
        $response = $mapper->singleEntityToModel($result);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Sla extra markt gegevens op die niet uit PerfectView komen
     *
     * @Method("POST")
     * @Route("/markt/{id}")
     * @ApiDoc(
     *  section="Markt",
     *  parameters={
     *      {"name"="aantalKramen", "dataType"="integer", "required"=true, "description"="Aantal kramen op de markt (capaciteit)"},
     *      {"name"="aantalMeter", "dataType"="integer", "required"=true, "description"="Aantal meter op de markt (capaciteit)"}
     *  }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function saveExtraInformation(Request $request, $id)
    {
        /** @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\MarktRepository */
        $repo = $this->get('appapi.repository.markt');

        $markt = $repo->getById($id);
        if ($markt === null) {
            throw $this->createNotFoundException('Markt not found, id = ' . $id);
        }

        $message = json_decode($request->getContent(false), true);

        $markt->setAantalKramen($message['aantalKramen']);
        $markt->setAantalMeter($message['aantalMeter']);

        $this->getDoctrine()->getEntityManager()->flush();

        /** @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\MarktMapper */
        $mapper = $this->get('appapi.mapper.markt');
        $response = $mapper->singleEntityToModel($markt);

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
