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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Route("1.1.0")
 */
class TokenController extends Controller
{
    /**
     * Geeft accounts
     *
     * @Method("GET")
     * @Route("/account/{accountId}/tokens")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @ApiDoc(
     *  section="Token",
     *  requirements={
     *      {"name"="accountId", "dataType"="integer", "description"="Account ID"}
     *  },
     *  filters={
     *      {"name"="listOffset", "dataType"="integer"},
     *      {"name"="listLength", "dataType"="integer", "description"="Default=100"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function listAction(Request $request, $accountId)
    {
        /** @var $repoToken \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\TokenRepository */
        $repoToken = $this->get('appapi.repository.token');
        /** @var $repoAccount \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\AccountRepository */
        $repoAccount = $this->get('appapi.repository.account');

        $account = $repoAccount->find($accountId);
        if ($account === null) {
            throw $this->createNotFoundException('Account unknown');
        }

        $results = $repoToken->search($account, $request->query->get('listOffset'), $request->query->get('listLength', 100));

        /** @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\TokenMapper */
        $mapper = $this->get('appapi.mapper.token');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }
}
