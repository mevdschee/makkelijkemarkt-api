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

use App\Mapper\SollicitatieMapper;
use App\Repository\MarktRepository;
use App\Repository\SollicitatieRepository;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/1.1.0")
 * @OA\Tag(name="Sollicitatie")
 */
class SollicitatieController extends AbstractController
{
    /**
     * Vraag sollicitaties op voor een markt
     *
     * @Route("/sollicitaties/markt/{marktId}", methods={"GET"})
     * @OA\Parameter(name="marktId", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listOffset", in="query", required="false", @OA\Schema(type="integer"), description="Default=0")
     * @OA\Parameter(name="listLength", in="query", required="false", @OA\Schema(type="integer"), description="Default=100")
     * @OA\Parameter(name="includeDoorgehaald", in="query", required="false", @OA\Schema(type="integer"), description="Default=1")
     * @IsGranted("ROLE_USER")
     */
    public function listMarktAction(
        SollicitatieRepository $repoSollicitatie,
        MarktRepository $repoMarkt,
        SollicitatieMapper $mapper,
        Request $request,
        $marktId
    ) {
        $markt = $repoMarkt->getById($marktId);
        if ($markt === null) {
            throw $this->createNotFoundException('Not found markt with id ' . $marktId);
        }

        $results = $repoSollicitatie->findByMarkt($markt, $request->query->get('listOffset'), $request->query->get('listLength', 100), $request->query->getBoolean('includeDoorgehaald', true));

        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Gegevens van sollicitatie op basis van id
     *
     * @Route("/sollicitaties/id/{id}", methods={"GET"})
     * @OA\Parameter(name="id", in="path", required="true", description="Sollicitatie id", @OA\Schema(type="integer"))
     * @IsGranted("ROLE_USER")
     */
    public function getByIdAction(SollicitatieRepository $repo, SollicitatieMapper $mapper, $id)
    {
        $object = $repo->getById($id);
        if ($object === null) {
            throw $this->createNotFoundException('Not found sollicitatie with id ' . $id);
        }

        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Gegevens van sollicitatie op basis van markt en sollicitatienummer
     *
     * @Route("/sollicitaties/markt/{marktId}/{sollicitatieNummer}", methods={"GET"})
     * @OA\Parameter(name="marktId", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="sollicitatieNummer", in="path", required="true", description="sollicitatieNummer", @OA\Schema(type="integer"))
     * @IsGranted("ROLE_USER")
     */
    public function getByMarktAndSollicitatieNummerAction(
        MarktRepository $repoMarkt,
        SollicitatieRepository $repoSollicitatie,
        SollicitatieMapper $mapper,
        $marktId,
        $sollicitatieNummer
    ) {
        $markt = $repoMarkt->getById($marktId);
        if ($markt === null) {
            throw $this->createNotFoundException('Not found markt with id ' . $marktId);
        }

        $object = $repoSollicitatie->getByMarktAndSollicitatieNummer($markt, $sollicitatieNummer);
        if ($object === null) {
            throw $this->createNotFoundException('Not found sollicitatie with sollicitatieNummer ' . $sollicitatieNummer);
        }

        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }
}
