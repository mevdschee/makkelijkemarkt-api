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

namespace App\Controller\Version_1_1_0;

use App\Entity\BtwTarief;
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
class BtwController extends AbstractController
{

    /**
     * Maake of update en btw tarief
     *
     * @Method("POST")
     * @Route("/btw/")
     * @ApiDoc(
     *  section="Btw",
     *  parameters={
     *      {"name"="jaar", "dataType"="integer", "required"=true, "description"="Jaar van het BTW tarief"},
     *      {"name"="hoog", "dataType"="float", "required"=true, "description"="Btw tarief hoog"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @IsGranted("ROLE_ADMIN")
     */
    public function createOrUpdateAction(EntityManagerInterface $em, Request $request)
    {
        $message = json_decode($request->getContent(false), true);

        // check inputs
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        if (isset($message['jaar']) === false) {
            return new JsonResponse(['error' => 'Required field jaar is missing']);
        }

        if (isset($message['hoog']) === false) {
            return new JsonResponse(['error' => 'Required field hoog is missing']);
        }

        $btwTariefRepo = $repo = $em->getRepository('AppApiBundle:BtwTarief');

        $btwTarief = $btwTariefRepo->findOneBy(array('jaar' => $message['jaar']));

        if (null === $btwTarief) {
            $btwTarief = new BtwTarief();
            $btwTarief->setJaar($message['jaar']);
            $em->persist($btwTarief);
        }

        $btwTarief->setHoog($message['hoog']);

        $em->flush();

        $mapper = $this->get('appapi.mapper.btwtarief');
        $result = $mapper->singleEntityToModel($btwTarief);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Zoek door alle markten
     *
     * @Method("GET")
     * @Route("/btw/")
     * @ApiDoc(
     *  section="Btw",
     *  filters={
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function listAction(EntityManagerInterface $em)
    {
        $repo = $em->getRepository('AppApiBundle:BtwTarief');

        $results = $repo->findAll();

        $mapper = $this->get('appapi.mapper.btwtarief');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

}
