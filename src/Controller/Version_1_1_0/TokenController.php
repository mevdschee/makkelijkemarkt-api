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

use App\Mapper\TokenMapper;
use App\Repository\AccountRepository;
use App\Repository\TokenRepository;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/1.1.0")
 * @OA\Tag(name="Token")
 */
class TokenController extends AbstractController
{
    /**
     * Geeft tokens
     *
     * @Route("/account/{accountId}/tokens", methods={"GET"})
     * @OA\Parameter(name="accountId", in="path", required="true", @OA\Schema(type="integer"), description="Account ID")
     * @OA\Parameter(name="listOffset", in="query", required="false", @OA\Schema(type="integer"), description="Default=0")
     * @OA\Parameter(name="listLength", in="query", required="false", @OA\Schema(type="integer"), description="Default=100")
     * @IsGranted("ROLE_USER")
     */
    public function listAction(
        TokenRepository $repoToken,
        AccountRepository $repoAccount,
        TokenMapper $mapper,
        Request $request,
        $accountId
    ) {
        $account = $repoAccount->find($accountId);
        if ($account === null) {
            return new JsonResponse(['error' => 'Cannot find account with id ' . $accountId], Response::HTTP_NOT_FOUND);
        }

        $results = $repoToken->search($account, $request->query->get('listOffset'), $request->query->get('listLength', 100));

        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }
}
