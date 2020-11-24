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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LijstController
 * @package App\Controller\Version_1_1_0
 * @Route("1.1.0")
 */
class LijstController extends AbstractController
{
    /**
     * @Method("GET")
     * @Route("/lijst/week/{marktId}/{types}/{startDate}/{endDate}")
     * @Route("/lijst/week/{marktId}/{types}")
     * @Route("/lijst/week/{marktId}")
     * @ApiDoc(
     *  section="Lijst",
     *  requirements={
     *      {"name"="marktId", "required"="true", "dataType"="integer", "description"="ID van markt"},
     *      {"name"="types", "required"="false", "dataType"="string", "description"="Koopman types gescheiden met een |"},
     *      {"name"="startDate", "required"="false", "dataType"="string", "description"="date as yyyy-mm-dd"},
     *      {"name"="endDate", "required"="false", "dataType"="string", "description"="date as yyyy-mm-dd"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @IsGranted("ROLE_USER")
     */
    public function weeklijstAction($marktId, $types = null, $startDate = null, $endDate = null)
    {
        if (null === $types) {
            $types = array();
        } else {
            $types = explode('|', $types);
        }
        if (null !== $startDate) {
            $startDate = new \DateTime($startDate);
        }
        if (null !== $endDate) {
            $endDate = new \DateTime($endDate);
        }

        $marktRepo = $this->getDoctrine()->getRepository('AppApiBundle:Markt');
        $markt = $marktRepo->findOneById($marktId);

        $sollicitatieRepo = $this->getDoctrine()->getRepository('AppApiBundle:Sollicitatie');
        $sollicitaties = $sollicitatieRepo->findByMarktInPeriod($markt, $types, $startDate, $endDate);

        /* @var $sollicitatieMapper \App\Mapper\SollicitatieMapper */
        $sollicitatieMapper = $this->get('appapi.mapper.sollicitatie');

        $mappedSollicitaties = $sollicitatieMapper->multipleEntityToModel($sollicitaties);
        return new JsonResponse($mappedSollicitaties, Response::HTTP_OK, ['X-Api-ListSize' => count($mappedSollicitaties)]);
    }
}
