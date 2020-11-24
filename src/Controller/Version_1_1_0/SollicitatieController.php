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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("1.1.0")
 * @OA\Tag(name="Sollicitatie")
 */
class SollicitatieController extends AbstractController
{
    /**
     * Vraag sollicitaties op voor een markt
     *
     * @Route("/sollicitaties/markt/{marktId}", methods={"GET"})
     * @ApiDoc(
     *  section="Sollicitatie",
     *  requirements={
     * @OA\Parameter(name="marktId", @OA\Schema(type="integer")},
     *  },
     *  filters={
     * @OA\Parameter(name="listOffset", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listLength", @OA\Schema(type="integer"), description="Default=100")
     * @OA\Parameter(name="includeDoorgehaald", @OA\Schema(type="integer"), description="Default=1"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @IsGranted("ROLE_USER")
     */
    public function listMarktAction(Request $request, $marktId)
    {
        /* @var $repoSollicitatie \App\Entity\SollicitatieRepository */
        $repoSollicitatie = $this->get('appapi.repository.sollicitatie');
        /* @var $repoMarkt \App\Entity\MarktRepository */
        $repoMarkt = $this->get('appapi.repository.markt');

        $markt = $repoMarkt->getById($marktId);
        if ($markt === null) {
            throw $this->createNotFoundException('Not found markt with id ' . $marktId);
        }

        $results = $repoSollicitatie->findByMarkt($markt, $request->query->get('listOffset'), $request->query->get('listLength', 100), $request->query->getBoolean('includeDoorgehaald', true));

        /* @var $mapper \App\Mapper\SollicitatieMapper */
        $mapper = $this->get('appapi.mapper.sollicitatie');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Gegevens van sollicitatie op basis van id
     *
     * @Method("GET")
     * @Route("/sollicitaties/id/{id}")
     * @ApiDoc(
     *  section="Sollicitatie",
     *  requirements={
     * @OA\Parameter(name="id", @OA\Schema(type="integer")}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @IsGranted("ROLE_USER")
     */
    public function getByIdAction(Request $request, $id)
    {
        /* @var $repo \App\Entity\SollicitatieRepository */
        $repo = $this->get('appapi.repository.sollicitatie');

        $object = $repo->getById($id);
        if ($object === null) {
            throw $this->createNotFoundException('Not found sollicitatie with id ' . $id);
        }

        /* @var $mapper \App\Mapper\SollicitatieMapper */
        $mapper = $this->get('appapi.mapper.sollicitatie');
        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Gegevens van sollicitatie op basis van markt en sollicitatienummer
     *
     * @Method("GET")
     * @Route("/sollicitaties/markt/{marktId}/{sollicitatieNummer}")
     * @ApiDoc(
     *  section="Sollicitatie",
     *  requirements={
     * @OA\Parameter(name="marktId", @OA\Schema(type="integer"))
     * @OA\Parameter(name="sollicitatieNummer", @OA\Schema(type="integer")},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @IsGranted("ROLE_USER")
     */
    public function getByMarktAndSollicitatieNummerAction(Request $request, $marktId, $sollicitatieNummer)
    {
        /* @var $repoMarkt \App\Entity\SollicitatieRepository */
        $repoMarkt = $this->get('appapi.repository.markt');
        /* @var $repoSollicitatie \App\Entity\SollicitatieRepository */
        $repoSollicitatie = $this->get('appapi.repository.sollicitatie');

        $markt = $repoMarkt->getById($marktId);
        if ($markt === null) {
            throw $this->createNotFoundException('Not found markt with id ' . $marktId);
        }

        $object = $repoSollicitatie->getByMarktAndSollicitatieNummer($markt, $sollicitatieNummer);
        if ($object === null) {
            throw $this->createNotFoundException('Not found sollicitatie with id ' . $id);
        }

        /* @var $mapper \App\Mapper\SollicitatieMapper */
        $mapper = $this->get('appapi.mapper.sollicitatie');
        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }
}
