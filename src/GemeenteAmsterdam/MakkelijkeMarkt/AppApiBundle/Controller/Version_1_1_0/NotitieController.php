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
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Notitie;

/**
 * @Route("1.1.0")
 */
class NotitieController extends Controller
{
    /**
     * Geeft alle notities voor een bepaalde dag en markt
     *
     * @Method("GET")
     * @Route("/notitie/{marktId}/{dag}")
     * @ApiDoc(
     *  section="Notitie",
     *  requirements={
     *      {"name"="marktId", "dataType"="integer"},
     *      {"name"="dag", "dataType"="string"},
     *  },
     *  filters={
     *      {"name"="listOffset", "dataType"="integer"},
     *      {"name"="listLength", "dataType"="integer", "description"="Default=100"},
     *      {"name"="verwijderdStatus", "dataType"="integer", "description"="-1 = alles, 0 = actief, 1 = enkel verwijderd, default: 0"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function listByMarktAndDagAction(Request $request, $marktId, $dag)
    {
        /* @var $notitieRepository \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\NotitieRepository */
        $notitieRepository = $this->get('appapi.repository.notitie');
        /* @var $marktRepository \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\NotitieRepository */
        $marktRepository = $this->get('appapi.repository.markt');

        // check if markt exists
        $markt = $marktRepository->getById($marktId);
        if ($markt === null)
            throw new $this->createNotFoundException('Can not find markt with id ' . $marktId);

        // get results
        $results = $notitieRepository->findByMarktAndDag($markt, $dag, $request->query->get('verwijderdStatus', 0), $request->query->get('listOffset', 0), $request->query->get('listLength', 100));

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\NotitieMapper */
        $mapper = $this->get('appapi.mapper.notitie');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Geeft een specifieke notitie
     *
     * @Method("GET")
     * @Route("/notitie/{id}")
     * @ApiDoc(
     *  section="Notitie",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "description"="Notitie id"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getAction(Request $request, $id)
    {
        /* @var $repository \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\NotitieRepository */
        $repositoryNotitie = $this->get('appapi.repository.notitie');

        $notitie = $repositoryNotitie->getById($id);
        if ($notitie === null)
            throw $this->createNotFoundException('No notitie with id ' . $id);

        $result = $this->get('appapi.mapper.notitie')->singleEntityToModel($notitie);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Maak een nieuw notitie
     *
     * @Method("POST")
     * @Route("/notitie/")
     * @ApiDoc(
     *  section="Notitie",
     *  parameters={
     *      {"name"="marktId", "dataType"="integer", "required"="true"},
     *      {"name"="dag", "dataType"="string", "required"="true"},
     *      {"name"="bericht", "dataType"="string", "required"="true"},
     *      {"name"="afgevinkt", "dataType"="boolean", "required"="false", "description"="If not set, false"},
     *      {"name"="aangemaaktGeolocatie", "dataType"="string", "required"="false", "description"="Geolocation as lat,long or as tupple [lat,long]"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function postAction(Request $request)
    {
        /* @var $marktRepository \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\MarktRepository */
        $marktRepository = $this->get('appapi.repository.markt');

        // parse body content
        $message = json_decode($request->getContent(false), true);

        // validate message
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);
        if (isset($message['marktId']) === false)
            return new JsonResponse(['error' => 'Required field marktId is missing']);
        if (isset($message['dag']) === false)
            return new JsonResponse(['error' => 'Required field dag is missing']);
        if (isset($message['bericht']) === false)
            return new JsonResponse(['error' => 'Required field bericht is missing']);

        // set defaults
        if (isset($message['afgevinkt']) === false)
            $message['afgevinkt'] = false;
        if (isset($message['aangemaaktGeolocatie']) === false)
            $message['aangemaaktGeolocatie'] = null;

        // convert values
        $message['afgevinkt'] = boolval($message['afgevinkt']);

        // get relations
        $markt = $marktRepository->getById($message['marktId']);
        if ($markt === null)
            throw $this->createNotFoundException('No markt with id ' . $message['marktId'] . ' found');

        // get notitie
        $notitie = new Notitie();

        // set values
        $notitie->setAangemaaktDatumtijd(new \DateTime());
        if ($message['aangemaaktGeolocatie'] !== null && is_string($message['aangemaaktGeolocatie']) === true && $message['aangemaaktGeolocatie'] !== '')
        {
            $point = explode(',', $message['aangemaaktGeolocatie']);
            $notitie->setAangemaaktGeolocatie($point[0], $point[1]);
        }
        elseif ($message['aangemaaktGeolocatie'] !== null && is_array($message['aangemaaktGeolocatie']) === true && count($message['aangemaaktGeolocatie']) === 2)
        {
            $notitie->setAangemaaktGeolocatie($message['aangemaaktGeolocatie'][0], $message['aangemaaktGeolocatie'][1]);
        }
        if ($message['afgevinkt'] === true)
            $notitie->setAfgevinktDatumtijd(new \DateTime());
        $notitie->setAfgevinktStatus($message['afgevinkt']);
        $notitie->setBericht($message['bericht']);
        $notitie->setDag(\DateTime::createFromFormat('Y-m-d', $message['dag']));
        $notitie->setMarkt($markt);
        $notitie->setVerwijderd(false);

        // save
        $this->getDoctrine()->getManager()->persist($notitie);
        $this->getDoctrine()->getManager()->flush();

        // return
        $result = $this->get('appapi.mapper.notitie')->singleEntityToModel($notitie);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Werk een notitie bij
     *
     * @Method("PUT")
     * @Route("/notitie/{id}")
     * @ApiDoc(
     *  section="Notitie",
     *  requirements={
     *      {"name"="id", "dataType"="integer"},
     *  },
     *  parameters={
     *      {"name"="bericht", "dataType"="string", "required"="true"},
     *      {"name"="afgevinkt", "dataType"="boolean", "required"="true", "description"="If not set, false"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function putAction(Request $request, $id)
    {
        /* @var $notitieRepository \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\NotitieRepository */
        $notitieRepository = $this->get('appapi.repository.notitie');

        // parse body content
        $message = json_decode($request->getContent(false), true);

        // validate message
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);
        if (isset($message['afgevinkt']) === false)
            return new JsonResponse(['error' => 'Required field afgevinkt is missing']);
        if (isset($message['bericht']) === false)
            return new JsonResponse(['error' => 'Required field bericht is missing']);

        // convert values
        $message['afgevinkt'] = boolval($message['afgevinkt']);

        // get notitie
        $notitie = $notitieRepository->getById($id);
        if ($notitie === null)
            throw $this->createNotFoundException('No notitie found with id ' . $id);

        if ($message['afgevinkt'] === true)
            $notitie->setAfgevinktDatumtijd(new \DateTime());
        else
            $notitie->setAfgevinktDatumtijd(null);
        $notitie->setAfgevinktStatus($message['afgevinkt']);
        $notitie->setBericht($message['bericht']);

        // save
        $this->getDoctrine()->getManager()->flush();

        // return
        $result = $this->get('appapi.mapper.notitie')->singleEntityToModel($notitie);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Verwijderd een notitie
     *
     * @Method("DELETE")
     * @Route("/notitie/{id}")
     * @ApiDoc(
     *  section="Notitie",
     *  requirements={
     *      {"name"="id", "dataType"="integer"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAction(Request $request, $id)
    {
        /* @var $notitieRepository \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\NotitieRepository */
        $notitieRepository = $this->get('appapi.repository.notitie');

        // get notitie
        $notitie = $notitieRepository->getById($id);
        if ($notitie === null)
            throw $this->createNotFoundException('No notitie found with id ' . $id);

        // check if already deleted
        if ($notitie->getVerwijderd() === true)
            throw $this->createNotFoundException('Notitie with id ' . $id . ' already deleted');

        // save
        $notitie->setVerwijderd(true);
        $notitie->setVerwijderdDatumtijd(new \DateTime());

        // sync db
        $this->getDoctrine()->getManager()->flush();

        // done
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
