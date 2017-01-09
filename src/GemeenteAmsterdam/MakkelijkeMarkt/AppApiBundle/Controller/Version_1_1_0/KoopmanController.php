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
class KoopmanController extends Controller
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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function listAction(Request $request)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

        $q = [];
        if ($request->query->has('freeSearch') === true)
            $q['freeSearch'] = $request->query->get('freeSearch');
        if ($request->query->has('voorletters') === true)
            $q['voorletters'] = $request->query->get('voorletters');
        if ($request->query->has('achternaam') === true)
            $q['achternaam'] = $request->query->get('achternaam');
        if ($request->query->has('email') === true)
            $q['email'] = $request->query->get('email');
        if ($request->query->has('erkenningsnummer') === true)
            $q['erkenningsnummer'] = str_replace('.', '', $request->query->get('erkenningsnummer'));
        if ($request->query->has('status') === true)
            $q['status'] = str_replace('.', '', $request->query->get('status'));

        $results = $repo->search($q, $request->query->get('listOffset'), $request->query->get('listLength', 100));

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\KoopmanMapper */
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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getByIdAction(Request $request, $id)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

        $object = $repo->getById($id);
        if ($object === null)
            throw $this->createNotFoundException('Not found koopman with id ' . $id);

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\KoopmanMapper */
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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getByKoopmanAction(Request $request, $erkenningsnummer)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

        // replace erkenningsnummer
        $erkenningsnummer = str_replace('.', '', $erkenningsnummer);

        $object = $repo->getByErkenningsnummer($erkenningsnummer);
        if ($object === null)
            throw $this->createNotFoundException('Not found koopman with erkenningsnummer ' . $erkenningsnummer);

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\KoopmanMapper */
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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getByPasUid(Request $request, $pasUid)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');


        $object = $repo->findOneByPasUid(strtoupper($pasUid));
        if ($object === null)
            throw $this->createNotFoundException('Not found koopman with pasUid ' . $pasUid);

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\KoopmanMapper */
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
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getByMarktAndSollicitatieNummerAction(Request $request, $marktId, $sollicitatieNummer)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\KoopmanRepository */
        $repo = $this->get('appapi.repository.koopman');

        $object = $repo->getBySollicitatienummer($marktId, $sollicitatieNummer);
        if ($object === null)
            throw $this->createNotFoundException('Not found koopman with sollicitatieNummer ' . $sollicitatieNummer . ' and marktId ' . $marktId);

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\KoopmanMapper */
        $mapper = $this->get('appapi.mapper.koopman');
        $response = $mapper->singleEntityToModel($object);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }


}
