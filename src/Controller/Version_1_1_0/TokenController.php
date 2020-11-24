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
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("1.1.0")
 */
class TokenController extends AbstractController
{
    /**
     * Geeft accounts
     *
     * @Method("GET")
     * @Route("/account/{accountId}/tokens")
     * @IsGranted("ROLE_USER")
     * @ApiDoc(
     *  section="Token",
     *  requirements={
     * @OA\Parameter(name="accountId", @OA\Schema(type="integer"), description="Account ID"}
     *  },
     *  filters={
     * @OA\Parameter(name="listOffset", @OA\Schema(type="integer"))
     * @OA\Parameter(name="listLength", @OA\Schema(type="integer"), description="Default=100"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function listAction(Request $request, $accountId)
    {
        /** @var $repoToken \App\Entity\TokenRepository */
        $repoToken = $this->get('appapi.repository.token');
        /** @var $repoAccount \App\Entity\AccountRepository */
        $repoAccount = $this->get('appapi.repository.account');

        $account = $repoAccount->find($accountId);
        if ($account === null) {
            throw $this->createNotFoundException('Account unknown');
        }

        $results = $repoToken->search($account, $request->query->get('listOffset'), $request->query->get('listLength', 100));

        /** @var $mapper \App\Mapper\TokenMapper */
        $mapper = $this->get('appapi.mapper.token');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }
}
