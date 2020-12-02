<?php
/*
 *  Copyright (c) 2020 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace App\Controller\Version_1_1_0;

use App\Entity\Notitie;
use App\Mapper\NotitieMapper;
use App\Repository\MarktRepository;
use App\Repository\NotitieRepository;
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
 * @OA\Tag(name="Notitie")
 */
class NotitieController extends AbstractController
{
    /**
     * Geeft alle notities voor een bepaalde dag en markt
     *
     * @Route("/notitie/{marktId}/{dag}", methods={"GET"})
     * @OA\Parameter(name="marktId", in="path", required="true",  @OA\Schema(type="integer"))
     * @OA\Parameter(name="dag", in="path", required="true", @OA\Schema(type="string"))
     * @OA\Parameter(name="listOffset", in="query", required="false", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listLength", in="query", required="false", @OA\Schema(type="integer"), description="Default=100")
     * @OA\Parameter(name="verwijderdStatus", in="query", required="false", @OA\Schema(type="integer"), description="-1 = alles, 0 = actief, 1 = enkel verwijderd, default: 0")
     * @IsGranted("ROLE_USER")
     */
    public function listByMarktAndDagAction(
        NotitieRepository $notitieRepository,
        MarktRepository $marktRepository,
        NotitieMapper $mapper,
        Request $request,
        $marktId,
        $dag
    ) {
        // check if markt exists
        $markt = $marktRepository->getById($marktId);
        if ($markt === null) {
            return new JsonResponse(['error' => 'Cannot find markt with id ' . $marktId], Response::HTTP_NOT_FOUND);
        }

        // get results
        $results = $notitieRepository->findByMarktAndDag($markt, $dag, $request->query->get('verwijderdStatus', 0), $request->query->get('listOffset', 0), $request->query->get('listLength', 100));

        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Geeft een specifieke notitie
     *
     * @Route("/notitie/{id}", methods={"GET"})
     * @OA\Parameter(name="id", in="path", required="true", @OA\Schema(type="integer"), description="Notitie id")
     * @IsGranted("ROLE_USER")
     */
    public function getAction(NotitieRepository $repository, NotitieMapper $mapper, $id)
    {
        $notitie = $repository->getById($id);
        if ($notitie === null) {
            return new JsonResponse(['error' => 'Cannot find notitie with id ' . $id], Response::HTTP_NOT_FOUND);
        }

        $result = $mapper->singleEntityToModel($notitie);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Maak een nieuw notitie
     *
     * @Route("/notitie/", methods={"POST"})
     * @OA\Parameter(name="marktId", in="body", required="true", @OA\Schema(type="integer"))
     * @OA\Parameter(name="dag", in="body", required="true", @OA\Schema(type="string"))
     * @OA\Parameter(name="bericht", in="body", required="true", @OA\Schema(type="string"))
     * @OA\Parameter(name="afgevinkt", in="body", required="false", @OA\Schema(type="boolean"), description="If not set, false")
     * @OA\Parameter(name="aangemaaktGeolocatie", in="body", required="false", @OA\Schema(type="string"), description="Geolocation as lat,long or as tupple [lat,long]")
     * @IsGranted("ROLE_USER")
     */
    public function postAction(EntityManagerInterface $em, MarktRepository $marktRepository, NotitieMapper $mapper, Request $request)
    {
        // parse body content
        $message = json_decode($request->getContent(false), true);

        // validate message
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (isset($message['marktId']) === false) {
            return new JsonResponse(['error' => 'Required field marktId is missing']);
        }

        if (isset($message['dag']) === false) {
            return new JsonResponse(['error' => 'Required field dag is missing']);
        }

        if (isset($message['bericht']) === false) {
            return new JsonResponse(['error' => 'Required field bericht is missing']);
        }

        // set defaults
        if (isset($message['afgevinkt']) === false) {
            $message['afgevinkt'] = false;
        }

        if (isset($message['aangemaaktGeolocatie']) === false) {
            $message['aangemaaktGeolocatie'] = null;
        }

        // convert values
        $message['afgevinkt'] = boolval($message['afgevinkt']);

        // get relations
        $markt = $marktRepository->getById($message['marktId']);
        if ($markt === null) {
            return new JsonResponse(['error' => 'Cannot find markt with id ' . $message['marktId']], Response::HTTP_NOT_FOUND);
        }

        // get notitie
        $notitie = new Notitie();

        // set values
        $notitie->setAangemaaktDatumtijd(new \DateTime());
        if ($message['aangemaaktGeolocatie'] !== null && is_string($message['aangemaaktGeolocatie']) === true && $message['aangemaaktGeolocatie'] !== '') {
            $point = explode(',', $message['aangemaaktGeolocatie']);
            $notitie->setAangemaaktGeolocatie($point[0], $point[1]);
        } elseif ($message['aangemaaktGeolocatie'] !== null && is_array($message['aangemaaktGeolocatie']) === true && count($message['aangemaaktGeolocatie']) === 2) {
            $notitie->setAangemaaktGeolocatie($message['aangemaaktGeolocatie'][0], $message['aangemaaktGeolocatie'][1]);
        }
        if ($message['afgevinkt'] === true) {
            $notitie->setAfgevinktDatumtijd(new \DateTime());
        }

        $notitie->setAfgevinktStatus($message['afgevinkt']);
        $notitie->setBericht($message['bericht']);
        $notitie->setDag(\DateTime::createFromFormat('Y-m-d', $message['dag']));
        $notitie->setMarkt($markt);
        $notitie->setVerwijderd(false);

        // save
        $em->persist($notitie);
        $em->flush();

        // return
        $result = $mapper->singleEntityToModel($notitie);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Werk een notitie bij
     *
     * @Route("/notitie/{id}", methods={"PUT"})
     * @OA\Parameter(name="id", in="path", required="true", @OA\Schema(type="integer"), description="Notitie id")
     * @OA\Parameter(name="bericht", in="body", @OA\Schema(type="string"), required="true")
     * @OA\Parameter(name="afgevinkt", in="body", @OA\Schema(type="boolean"), required="true", description="If not set, false")
     * @IsGranted("ROLE_USER")
     */
    public function putAction(EntityManagerInterface $em, NotitieRepository $notitieRepository, NotitieMapper $mapper, Request $request, $id)
    {
        // parse body content
        $message = json_decode($request->getContent(false), true);

        // validate message
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (isset($message['afgevinkt']) === false) {
            return new JsonResponse(['error' => 'Required field afgevinkt is missing']);
        }

        if (isset($message['bericht']) === false) {
            return new JsonResponse(['error' => 'Required field bericht is missing']);
        }

        // convert values
        $message['afgevinkt'] = boolval($message['afgevinkt']);

        // get notitie
        $notitie = $notitieRepository->getById($id);
        if ($notitie === null) {
            return new JsonResponse(['error' => 'Cannot find notitie with id ' . $id], Response::HTTP_NOT_FOUND);
        }

        if ($message['afgevinkt'] === true) {
            $notitie->setAfgevinktDatumtijd(new \DateTime());
        } else {
            $notitie->setAfgevinktDatumtijd(null);
        }

        $notitie->setAfgevinktStatus($message['afgevinkt']);
        $notitie->setBericht($message['bericht']);

        // save
        $em->flush();

        // return
        $result = $mapper->singleEntityToModel($notitie);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Verwijderd een notitie
     *
     * @Route("/notitie/{id}", methods={"DELETE"})
     * @OA\Parameter(name="id", in="path", required="true", @OA\Schema(type="integer"), description="Notitie id")
     * @IsGranted("ROLE_USER")
     */
    public function deleteAction(EntityManagerInterface $em, NotitieRepository $notitieRepository, $id)
    {
        // get notitie
        $notitie = $notitieRepository->getById($id);
        if ($notitie === null) {
            return new JsonResponse(['error' => 'Cannot find notitie with id ' . $id], Response::HTTP_NOT_FOUND);
        }

        // check if already deleted
        if ($notitie->getVerwijderd() === true) {
            return new JsonResponse(['error' => 'Notitie with id ' . $id . ' already deleted'], Response::HTTP_NOT_FOUND);
        }

        // save
        $notitie->setVerwijderd(true);
        $notitie->setVerwijderdDatumtijd(new \DateTime());

        // sync db
        $em->flush();

        // done
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
