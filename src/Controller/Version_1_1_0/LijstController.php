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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LijstController
 * @package App\Controller\Version_1_1_0
 * @Route("1.1.0")
 */
class LijstController extends AbstractController
{
    /**
     * @Route("/lijst/week/{marktId}/{types}/{startDate}/{endDate}", methods={"GET"})
     * @Route("/lijst/week/{marktId}/{types}", methods={"GET"})
     * @Route("/lijst/week/{marktId}", methods={"GET"})
     * @OA\Parameter(name="marktId", in="path", required="true", @OA\Schema(type="integer"), description="ID van markt")
     * @OA\Parameter(name="types", in="path", required="false", @OA\Schema(type="string"), description="Koopman types gescheiden met een |")
     * @OA\Parameter(name="startDate", in="path", required="false", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @OA\Parameter(name="endDate", in="path", required="false", @OA\Schema(type="string"), description="date as yyyy-mm-dd")
     * @OA\Tag(name="Lijst")
     * @IsGranted("ROLE_USER")
     */
    public function weeklijstAction(
        MarktRepository $marktRepo,
        SollicitatieRepository $sollicitatieRepo,
        SollicitatieMapper $sollicitatieMapper,
        $marktId,
        $types = null,
        $startDate = null,
        $endDate = null
    ) {
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

        $markt = $marktRepo->findOneById($marktId);

        $sollicitaties = $sollicitatieRepo->findByMarktInPeriod($markt, $types, $startDate, $endDate);

        $mappedSollicitaties = $sollicitatieMapper->multipleEntityToModel($sollicitaties);
        return new JsonResponse($mappedSollicitaties, Response::HTTP_OK, ['X-Api-ListSize' => count($mappedSollicitaties)]);
    }
}
