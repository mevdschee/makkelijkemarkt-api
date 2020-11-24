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

use App\Entity\Token;
use App\Enum\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("1.1.0")
 */
class LoginController extends AbstractController
{
    /**
     * Genereert een nieuw token op accountId + password
     *
     * @Method("POST")
     * @Route("/login/basicId/")
     * @ApiDoc(
     *  section="Login",
     *  parameters={
     *      {"name"="accountId", "dataType"="integer", "required"=true, "description"="Account ID"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password"},
     *      {"name"="deviceUuid", "dataType"="string", "required"=false, "description"="UUID van het gebruikte device"},
     *      {"name"="clientApp", "dataType"="string", "required"=false, "description"="Appliciatie type"},
     *      {"name"="clientVersion", "dataType"="string", "required"=false, "description"="Versie van de client"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function basicIdAction(Request $request)
    {
        $message = json_decode($request->getContent(false), true);
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        $message = array_merge(['accountId' => null, 'password' => null], $message);

        if ($message['accountId'] === null) {
            throw $this->createAccessDeniedException('Account unknown');
        }

        $token = new Token();
        $token->setClientApp(isset($message['clientApp']) === true ? $message['clientApp'] : null);
        $token->setClientVersion(isset($message['clientVersion']) === true ? $message['clientVersion'] : null);
        $token->setDeviceUuid(isset($message['deviceUuid']) === true ? $message['deviceUuid'] : null);
        $token->setLifeTime(60 * 60 * 8 * 1);

        /* @var $accountRepo \App\Entity\AccountRepository */
        $accountRepo = $this->get('appapi.repository.account');
        $account = $accountRepo->getById($message['accountId']);
        if ($account === null) {
            throw $this->createAccessDeniedException('Account unknown');
        }

        if ($account->getLocked() === true) {
            return new JsonResponse('Account is locked', Response::HTTP_LOCKED, []);
        }

        if ($account->getActive() === false) {
            return new JsonResponse('Account is not active', Response::HTTP_FORBIDDEN, []);
        }

        $token->setAccount($account);

        $account->setLastAttempt(new \DateTime());
        $em = $this->get('doctrine.orm.entity_manager');
        $em->flush();

        $encoder = $this->container->get('security.password_encoder');
        if ($encoder->isPasswordValid($account, $message['password']) === false) {
            $attempts = $account->getAttempts();
            $attempts++;
            $account->setAttempts($attempts++);
            if ($attempts >= 9) {
                $account->setLocked(true);
            }
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();
            throw $this->createAccessDeniedException('Password invalid');
        }

        $account->setAttempts(0);
        $em->flush();

        $em->persist($token);
        $em->flush();

        /* @var $mapper \App\Mapper\TokenMapper */
        $mapper = $this->get('appapi.mapper.token');
        $response = $mapper->singleEntityToModel($token);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Genereert een nieuw token op username + password
     *
     * @Method("POST")
     * @Route("/login/basicUsername/")
     * @ApiDoc(
     *  section="Login",
     *  parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="Username"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password"},
     *      {"name"="deviceUuid", "dataType"="string", "required"=false, "description"="UUID van het gebruikte device"},
     *      {"name"="clientApp", "dataType"="string", "required"=false, "description"="Appliciatie type"},
     *      {"name"="clientVersion", "dataType"="string", "required"=false, "description"="Versie van de client"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function basicUsernameAction(Request $request)
    {
        $message = json_decode($request->getContent(false), true);
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        $message = array_merge(['username' => null, 'password' => null], $message);

        if ($message['username'] === null) {
            throw $this->createAccessDeniedException('Account unknown');
        }

        $token = new Token();
        $token->setClientApp(isset($message['clientApp']) === true ? $message['clientApp'] : null);
        $token->setClientVersion(isset($message['clientVersion']) === true ? $message['clientVersion'] : null);
        $token->setDeviceUuid(isset($message['deviceUuid']) === true ? $message['deviceUuid'] : null);
        $token->setLifeTime(60 * 60 * 8 * 1);

        /* @var $accountRepo \App\Entity\AccountRepository */
        $accountRepo = $this->get('appapi.repository.account');
        $account = $accountRepo->getByUsername($message['username']);
        if ($account === null) {
            throw $this->createAccessDeniedException('Account unknown');
        }

        if ($account->getLocked() === true) {
            return new JsonResponse('Account is locked', Response::HTTP_LOCKED, []);
        }

        if ($account->getActive() === false) {
            return new JsonResponse('Account is not active', Response::HTTP_FORBIDDEN, []);
        }

        $token->setAccount($account);

        $account->setLastAttempt(new \DateTime());
        $em = $this->get('doctrine.orm.entity_manager');
        $em->flush();

        $encoder = $this->container->get('security.password_encoder');
        if ($encoder->isPasswordValid($account, $message['password']) === false) {
            $attempts = $account->getAttempts();
            $attempts++;
            $account->setAttempts($attempts++);
            if ($attempts >= 9) {
                $account->setLocked(true);
            }
            $em = $this->get('doctrine.orm.entity_manager');
            $em->flush();
            throw $this->createAccessDeniedException('Password invalid');
        }

        $account->setAttempts(0);
        $em->flush();

        $em->persist($token);
        $em->flush();

        /* @var $mapper \App\Mapper\TokenMapper */
        $mapper = $this->get('appapi.mapper.token');
        $response = $mapper->singleEntityToModel($token);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * @Method("GET")
     * @Route("/login/whoami/")
     * @ApiDoc(
     *  section="Login",
     *  parameters={},
     *  views = { "default", "1.1.0" }
     * )
     * \@IsGranted("ROLE_USER")
     */
    public function whoamiAction(Request $request)
    {
        $account = $this->getUser();
        if ($account === null) {
            return new JsonResponse(['account' => null, 'authorization-header' => $request->headers->get('Authorization')], Response::HTTP_NOT_FOUND, []);
        }

        $result = $this->get('appapi.mapper.account')->singleEntityToModel($account);
        return new JsonResponse(['account' => $result, 'authorization-header' => $request->headers->get('Authorization')], Response::HTTP_OK, []);
    }

    /**
     * @Method("GET")
     * @Route("/logout/")
     * @ApiDoc(
     *  section="Login",
     *  parameters={},
     *  views = { "default", "1.1.0" }
     * )
     */
    public function logoutAction(EntityManagerInterface $em, Request $request)
    {
        $request->headers->get('Authorization');

        $authorizationHeader = $request->headers->get('Authorization');
        if ($authorizationHeader === null) {
            return new JsonResponse(['msg' => 'No Authorization header found'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $header = explode(' ', $authorizationHeader);
        if ($header[0] !== 'Bearer') {
            return new JsonResponse(['msg' => 'Authorization header not containing Bearer'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (isset($header[1]) === false) {
            return new JsonResponse(['msg' => 'Authorization header not containing ID'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $token = $this->get('appapi.repository.token')->getByUuid($header[1]);
        $token->setLifeTime(0);

        $em->flush();

        /* @var $mapper \App\Mapper\TokenMapper */
        $mapper = $this->get('appapi.mapper.token');
        $response = $mapper->singleEntityToModel($token);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * @Method("GET")
     * @Route("/login/roles")
     * @ApiDoc(
     *  section="Login",
     *  parameters={},
     *  views = { "default", "1.1.0" }
     * )
     */
    public function rolesListAction()
    {
        return new JsonResponse(Roles::all(), Response::HTTP_OK, []);
    }
}
