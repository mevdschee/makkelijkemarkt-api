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

use App\Mapper\MarktMapper;
use App\Repository\MarktRepository;
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
 * @OA\Tag(name="Markt")
 */
class MarktController extends AbstractController
{
    /**
     * Zoek door alle markten
     *
     * @Route("/markt/", methods={"GET"})
     */
    public function listAction(MarktRepository $repo, MarktMapper $mapper)
    {
        $results = $repo->findAll();

        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Vraag een markt op
     *
     * @Route("/markt/{id}", methods={"GET"})
     * @OA\Parameter(name="id", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     */
    public function getAction(MarktRepository $repo, MarktMapper $mapper, $id)
    {

        $result = $repo->findOneById($id);

        $response = $mapper->singleEntityToModel($result);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Sla extra markt gegevens op die niet uit PerfectView komen
     *
     * @Route("/markt/{id}", methods={"POST"})
     * @OA\Parameter(name="id", in="path", required="true", description="Markt id", @OA\Schema(type="integer"))
     * @OA\Parameter(name="aantalKramen", in="body", @OA\Schema(type="integer"), required="true", description="Aantal kramen op de markt (capaciteit)")
     * @OA\Parameter(name="aantalMeter", in="body", @OA\Schema(type="integer"), required="true", description="Aantal meter op de markt (capaciteit)")
     * @OA\Parameter(name="auditMax", in="body", @OA\Schema(type="integer"), required="true", description="Aantal plaatsen op de audit lijst")
     * @IsGranted("ROLE_USER")
     */
    public function saveExtraInformation(EntityManagerInterface $em, MarktRepository $repo, MarktMapper $mapper, Request $request, $id)
    {
        $markt = $repo->getById($id);
        if ($markt === null) {
            return new JsonResponse(['error' => 'Cannot find Markt with id ' . $id], Response::HTTP_NOT_FOUND);
        }

        $message = json_decode($request->getContent(false), true);

        $markt->setAantalKramen($message['aantalKramen']);
        $markt->setAantalMeter($message['aantalMeter']);
        $markt->setAuditMax($message['auditMax']);
        $markt->setKiesJeKraamActief($message['kiesJeKraamActief']);
        $markt->setKiesJeKraamFase($message['kiesJeKraamFase']);
        $markt->setKiesJeKraamMededelingActief($message['kiesJeKraamMededelingActief']);
        $markt->setKiesJeKraamMededelingTekst($message['kiesJeKraamMededelingTekst']);
        $markt->setKiesJeKraamMededelingTitel($message['kiesJeKraamMededelingTitel']);
        $markt->setKiesJeKraamGeblokkeerdePlaatsen($message['kiesJeKraamGeblokkeerdePlaatsen']);
        $markt->setKiesJeKraamGeblokkeerdeData($message['kiesJeKraamGeblokkeerdeData']);
        $markt->setKiesJeKraamEmailKramenzetter($message['kiesJeKraamEmailKramenzetter']);
        $markt->setMakkelijkeMarktActief($message['makkelijkeMarktActief']);
        $markt->setMarktDagenTekst($message['marktDagenTekst']);
        $markt->setIndelingsTijdstipTekst($message['indelingsTijdstipTekst']);
        $markt->setTelefoonNummerContact($message['telefoonNummerContact']);
        $markt->setIndelingstype($message['indelingstype']);

        $em->flush();

        $response = $mapper->singleEntityToModel($markt);

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
