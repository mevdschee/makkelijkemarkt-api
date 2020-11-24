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

use App\Entity\Koopman;
use App\Mapper\KoopmanMapper;
use App\Repository\KoopmanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("1.1.0")
 */
class KoopmanController extends AbstractController
{
    /**
     * Zoek door alle koopmannen
     *
     * @Route("/koopman/", methods={"GET"})
     * @OA\Parameter(name="freeSearch", @OA\Schema(type="string"))
     * @OA\Parameter(name="voorletters", @OA\Schema(type="string"))
     * @OA\Parameter(name="achternaam", @OA\Schema(type="string"))
     * @OA\Parameter(name="email", @OA\Schema(type="string"))
     * @OA\Parameter(name="erkenningsnummer", @OA\Schema(type="string"))
     * @OA\Parameter(name="status", description="-1 = ignore, 0 = only removed, 1 = only active", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listOffset", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listLength", description="Default=100", @OA\Schema(type="integer")}
     * @OA\Tag(name="Koopman")
     * @IsGranted("ROLE_USER")
     */
    public function listAction(KoopmanRepository $repo, KoopmanMapper $mapper, Request $request)
    {
        $q = [];
        if ($request->query->has('freeSearch') === true) {
            $q['freeSearch'] = $request->query->get('freeSearch');
        }

        if ($request->query->has('voorletters') === true) {
            $q['voorletters'] = $request->query->get('voorletters');
        }

        if ($request->query->has('achternaam') === true) {
            $q['achternaam'] = $request->query->get('achternaam');
        }

        if ($request->query->has('email') === true) {
            $q['email'] = $request->query->get('email');
        }

        if ($request->query->has('erkenningsnummer') === true) {
            $q['erkenningsnummer'] = str_replace('.', '', $request->query->get('erkenningsnummer'));
        }

        if ($request->query->has('status') === true) {
            $q['status'] = $request->query->get('status');
        }

        $results = $repo->search($q, $request->query->get('listOffset'), $request->query->get('listLength', 100));

        $response = $mapper->multipleEntityToSimpleModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Gegevens van koopman op basis van API id
     *
     * @Route("/koopman/id/{id}", methods={"GET"})
     * @OA\Parameter(name="id", in="path", required="true", description="Koopman id", @OA\Schema(type="integer"))
     * @OA\Tag(name="Koopman")
     * @IsGranted("ROLE_USER")
     */
    public function getByIdAction(KoopmanRepository $repo, KoopmanMapper $mapper, Request $request, $id)
    {
        $object = $repo->getById($id);
        if ($object === null) {
            throw $this->createNotFoundException('Not found koopman with id ' . $id);
        }

        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Gegevens van koopman op basis van erkenningsnummer
     *
     * @Route("/koopman/erkenningsnummer/{erkenningsnummer}", methods={"GET"})
     * @OA\Parameter(name="erkenningsnummer", in="path", required="true", description="Erkenningsnummer", @OA\Schema(type="string"))
     * @OA\Tag(name="Koopman")
     * @IsGranted("ROLE_USER")
     */
    public function getByKoopmanAction(Request $request, $erkenningsnummer)
    {
        /* @var $repo \App\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

        // replace erkenningsnummer
        $erkenningsnummer = str_replace('.', '', $erkenningsnummer);

        $object = $repo->getByErkenningsnummer($erkenningsnummer);
        if ($object === null) {
            throw $this->createNotFoundException('Not found koopman with erkenningsnummer ' . $erkenningsnummer);
        }

        /* @var $mapper \App\Mapper\KoopmanMapper */
        $mapper = $this->get('appapi.mapper.koopman');
        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Gegevens van koopman op basis van erkenningsnummer
     *
     * @Route("/koopman/pasuid/{pasUid}", methods={"GET"})
     * @ApiDoc(
     *  section="Koopman",
     *  requirements={
     * @OA\Parameter(name="pasUid", in="path", required="true", @OA\Schema(type="string")}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @OA\Tag(name="Koopman")
     * @IsGranted("ROLE_USER")
     */
    public function getByPasUid(Request $request, $pasUid)
    {
        /* @var $repo \App\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

        $object = $repo->findOneByPasUid(strtoupper($pasUid));
        if ($object === null) {
            // dit is geen bekende koop OF een vervangers pas
            $repo = $this->get('appapi.repository.vervanger');
            $object = $repo->findOneByPasUid(strtoupper($pasUid));
            if ($object === null) {
                // ook geen vervanger
                throw $this->createNotFoundException('Not found koopman with pasUid ' . $pasUid);
            }

            // convert vervangersvermelding in koopman
            $object = $object->getVervanger();
        }

        /* @var $mapper \App\Mapper\KoopmanMapper */
        $mapper = $this->get('appapi.mapper.koopman');
        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Gegevens van koopman op basis van markt en sollicitatienummer
     *
     * @Route("/koopman/markt/{marktId}/sollicitatienummer/{sollicitatieNummer}", methods={"GET"})
     * @ApiDoc(
     *  section="Koopman",
     *  requirements={
     * @OA\Parameter(name="marktId", in="path", required="true", @OA\Schema(type="integer"))
     * @OA\Parameter(name="sollicitatieNummer", in="path", required="true", @OA\Schema(type="integer")},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @OA\Tag(name="Koopman")
     * @IsGranted("ROLE_USER")
     */
    public function getByMarktAndSollicitatieNummerAction(Request $request, $marktId, $sollicitatieNummer)
    {
        /* @var $repo \App\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

        $object = $repo->getBySollicitatienummer($marktId, $sollicitatieNummer);
        if ($object === null) {
            throw $this->createNotFoundException('Not found koopman with sollicitatieNummer ' . $sollicitatieNummer . ' and marktId ' . $marktId);
        }

        /* @var $mapper \App\Mapper\KoopmanMapper */
        $mapper = $this->get('appapi.mapper.koopman');
        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Toggle Handhavingsverzoek
     *
     * @Route("/koopman/toggle_handhavingsverzoek/{id}/{date}", methods={"POST"})
     * @OA\Parameter(name="id", in="path", required="true", @OA\Schema(type="integer"))
     * @OA\Parameter(name="date", in="path", required="true", "dataType"="string yyyy-mm-dd")
     * @OA\Tag(name="Koopman")
     * @IsGranted("ROLE_SENIOR")
     */
    public function toggleHandhavingsVerzoekAction(EntityManagerInterface $em, $id, $date)
    {
        /* @var $repo \App\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

        $koopman = $repo->find($id);
        if ($koopman === null) {
            throw $this->createNotFoundException('Koopman not found');
        }
        /**
         * @var Koopman $koopman
         */

        $date = new \DateTime($date);

        $koopman->setHandhavingsVerzoek($date);
        $em->flush();

        $mapper = $this->get('appapi.mapper.koopman');
        $response = $mapper->singleEntityToModel($koopman);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

}
