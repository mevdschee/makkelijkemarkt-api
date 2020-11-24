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

use App\Mapper\KoopmanMapper;
use App\Repository\KoopmanRepository;
use App\Repository\VervangerRepository;
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
 */
class KoopmanController extends AbstractController
{
    /**
     * Zoek door alle koopmannen
     *
     * @Route("/koopman/", methods={"GET"})
     * @OA\Parameter(name="freeSearch", in="query", required="false", @OA\Schema(type="string"))
     * @OA\Parameter(name="voorletters", in="query", required="false", @OA\Schema(type="string"))
     * @OA\Parameter(name="achternaam", in="query", required="false", @OA\Schema(type="string"))
     * @OA\Parameter(name="email", in="query", required="false", @OA\Schema(type="string"))
     * @OA\Parameter(name="erkenningsnummer", in="query", required="false", @OA\Schema(type="string"))
     * @OA\Parameter(name="status", in="query", required="false", description="-1 = ignore, 0 = only removed, 1 = only active", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listOffset", in="query", required="false", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listLength", in="query", required="false", description="Default=100", @OA\Schema(type="integer"))
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
    public function getByIdAction(KoopmanRepository $repo, KoopmanMapper $mapper, $id)
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
    public function getByKoopmanAction(
        KoopmanRepository $repo,
        KoopmanMapper $mapper,
        $erkenningsnummer
    ) {
        // replace erkenningsnummer
        $erkenningsnummer = str_replace('.', '', $erkenningsnummer);

        $object = $repo->getByErkenningsnummer($erkenningsnummer);
        if ($object === null) {
            throw $this->createNotFoundException('Not found koopman with erkenningsnummer ' . $erkenningsnummer);
        }

        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Gegevens van koopman op basis van erkenningsnummer
     *
     * @Route("/koopman/pasuid/{pasUid}", methods={"GET"})
     * @OA\Parameter(name="pasUid", in="path", required="true", @OA\Schema(type="string"))
     * @OA\Tag(name="Koopman")
     * @IsGranted("ROLE_USER")
     */
    public function getByPasUid(
        KoopmanRepository $koopmanRepository,
        VervangerRepository $vervangerRepository,
        $pasUid
    ) {

        $object = $koopmanRepository->findOneByPasUid(strtoupper($pasUid));
        if ($object === null) {
            // dit is geen bekende koop OF een vervangers pas
            $object = $vervangerRepository->findOneByPasUid(strtoupper($pasUid));
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
     * @OA\Parameter(name="marktId", in="path", required="true", @OA\Schema(type="integer"))
     * @OA\Parameter(name="sollicitatieNummer", in="path", required="true", @OA\Schema(type="integer"))
     * @OA\Tag(name="Koopman")
     * @IsGranted("ROLE_USER")
     */
    public function getByMarktAndSollicitatieNummerAction(KoopmanRepository $repo, KoopmanMapper $mapper, $marktId, $sollicitatieNummer)
    {

        $object = $repo->getBySollicitatienummer($marktId, $sollicitatieNummer);
        if ($object === null) {
            throw $this->createNotFoundException('Not found koopman with sollicitatieNummer ' . $sollicitatieNummer . ' and marktId ' . $marktId);
        }

        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Toggle Handhavingsverzoek
     *
     * @Route("/koopman/toggle_handhavingsverzoek/{id}/{date}", methods={"POST"})
     * @OA\Parameter(name="id", in="path", required="true", @OA\Schema(type="integer"))
     * @OA\Parameter(name="date", in="path", required="true", description="Datum yyyy-mm-dd", @OA\Schema(type="string"))
     * @OA\Tag(name="Koopman")
     * @IsGranted("ROLE_SENIOR")
     */
    public function toggleHandhavingsVerzoekAction(EntityManagerInterface $em, KoopmanRepository $repo, KoopmanMapper $mapper, $id, $date)
    {
        $koopman = $repo->find($id);
        if ($koopman === null) {
            throw $this->createNotFoundException('Koopman not found');
        }

        $date = new \DateTime($date);

        $koopman->setHandhavingsVerzoek($date);
        $em->flush();

        $response = $mapper->singleEntityToModel($koopman);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

}
