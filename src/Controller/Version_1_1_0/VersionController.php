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

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("1.1.0")
 * @OA\Tag(name="Version")
 */
class VersionController extends AbstractController
{
    /**
     * Geeft versie nummer
     *
     * @Route("/version/", methods={"GET"})
     */
    public function getAction()
    {
        return new JsonResponse(
            [
                'apiVersion' => '1.1.0',
                'androidVersion' => $this->getParameter('android_version'),
                'androidBuild' => $this->getParameter('android_build'),
            ], Response::HTTP_OK);
    }
}
