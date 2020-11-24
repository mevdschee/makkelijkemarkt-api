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
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("1.1.0")
 */
class KoopmanController extends AbstractController
{
    /**
     * Zoek door alle koopmannen
     *
     * @Method("GET")
     * @Route("/koopman/")
     * @ApiDoc(
     *  section="Koopman",
     *  filters={
     *      {"name"="freeSearch", "dataType"="string"},
     *      {"name"="voorletters", "dataType"="string"},
     *      {"name"="achternaam", "dataType"="string"},
     *      {"name"="email", "dataType"="string"},
     *      {"name"="erkenningsnummer", "dataType"="string"},
     *      {"name"="status", "dataType"="integer", "description"="-1 = ignore, 0 = only removed, 1 = only active"},
     *      {"name"="listOffset", "dataType"="integer"},
     *      {"name"="listLength", "dataType"="integer", "description"="Default=100"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @IsGranted("ROLE_USER")
     */
    public function listAction(Request $request)
    {
        /* @var $repo \App\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

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

        /* @var $mapper \App\Mapper\KoopmanMapper */
        $mapper = $this->get('appapi.mapper.koopman');
        $response = $mapper->multipleEntityToSimpleModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Gegevens van koopman op basis van API id
     *
     * @Method("GET")
     * @Route("/koopman/id/{id}")
     * @ApiDoc(
     *  section="Koopman",
     *  requirements={
     *      {"name"="id", "dataType"="integer"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @IsGranted("ROLE_USER")
     */
    public function getByIdAction(Request $request, $id)
    {
        /* @var $repo \App\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

        $object = $repo->getById($id);
        if ($object === null) {
            throw $this->createNotFoundException('Not found koopman with id ' . $id);
        }

        /* @var $mapper \App\Mapper\KoopmanMapper */
        $mapper = $this->get('appapi.mapper.koopman');
        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Gegevens van koopman op basis van erkenningsnummer
     *
     * @Method("GET")
     * @Route("/koopman/erkenningsnummer/{erkenningsnummer}")
     * @ApiDoc(
     *  section="Koopman",
     *  requirements={
     *      {"name"="erkenningsnummer", "dataType"="string"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
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
     * @Method("GET")
     * @Route("/koopman/pasuid/{pasUid}")
     * @ApiDoc(
     *  section="Koopman",
     *  requirements={
     *      {"name"="pasUid", "dataType"="string"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
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
     * @Method("GET")
     * @Route("/koopman/markt/{marktId}/sollicitatienummer/{sollicitatieNummer}")
     * @ApiDoc(
     *  section="Koopman",
     *  requirements={
     *      {"name"="marktId", "dataType"="integer"},
     *      {"name"="sollicitatieNummer", "dataType"="integer"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
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
     * @Method("POST")
     * @Route("/koopman/toggle_handhavingsverzoek/{id}/{date}")
     * @ApiDoc(
     *  section="Koopman",
     *  requirements={
     *      {"name"="id", "dataType"="integer"},
     *      {"name"="date", "dataType"="string yyyy-mm-dd"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
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
