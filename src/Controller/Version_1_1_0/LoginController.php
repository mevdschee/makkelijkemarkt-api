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

use App\Entity\Token;
use App\Enum\Roles;
use App\Mapper\AccountMapper;
use App\Mapper\TokenMapper;
use App\Repository\AccountRepository;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("api/1.1.0")
 * @OA\Tag(name="Login")
 */
class LoginController extends AbstractController
{
    /**
     * Genereert een nieuw token op accountId + password
     *
     * @Route("/login/basicId/", methods={"POST"})
     * @OA\Parameter(name="accountId", @OA\Schema(type="integer"), required="true", description="Account ID")
     * @OA\Parameter(name="password", @OA\Schema(type="string"), required="true", description="Password")
     * @OA\Parameter(name="deviceUuid", @OA\Schema(type="string"), required="false", description="UUID van het gebruikte device")
     * @OA\Parameter(name="clientApp", @OA\Schema(type="string"), required="false", description="Appliciatie type")
     * @OA\Parameter(name="clientVersion", @OA\Schema(type="string"), required="false", description="Versie van de client")
     */
    public function basicIdAction(
        EntityManagerInterface $em,
        AccountRepository $accountRepo,
        TokenMapper $mapper,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $message = json_decode($request->getContent(false), true);
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        $message = array_merge(['accountId' => null, 'password' => null], $message);

        if ($message['accountId'] === null) {
            throw $this->createAccessDeniedException('Account unknown');
        }

        $token = new Token();
        $token->setClientApp($message['clientApp'] ?? null);
        $token->setClientVersion($message['clientVersion'] ?? null);
        $token->setDeviceUuid($message['deviceUuid'] ?? null);
        $token->setLifeTime(60 * 60 * 8 * 1);

        $account = $accountRepo->getById($message['accountId']);
        if ($account === null) {
            throw $this->createAccessDeniedException('Account unknown');
        }

        if ($account->getLocked() === true) {
            return new JsonResponse(['error' => 'Account is locked'], Response::HTTP_LOCKED, []);
        }

        if ($account->getActive() === false) {
            return new JsonResponse(['error' => 'Account is not active'], Response::HTTP_FORBIDDEN, []);
        }

        $token->setAccount($account);

        $account->setLastAttempt(new \DateTime());
        $em->flush();

        if ($passwordEncoder->isPasswordValid($account, $message['password']) === false) {
            $attempts = $account->getAttempts();
            $attempts++;
            $account->setAttempts($attempts++);
            if ($attempts >= 9) {
                $account->setLocked(true);
            }
            $em->flush();
            throw $this->createAccessDeniedException('Password invalid');
        }

        $account->setAttempts(0);
        $em->flush();

        $em->persist($token);
        $em->flush();

        $response = $mapper->singleEntityToModel($token);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * Genereert een nieuw token op username + password
     *
     * @Route("/login/basicUsername/", methods={"POST"})
     * @OA\Parameter(name="username", @OA\Schema(type="string"), required="true", description="Username")
     * @OA\Parameter(name="password", @OA\Schema(type="string"), required="true", description="Password")
     * @OA\Parameter(name="deviceUuid", @OA\Schema(type="string"), required="false", description="UUID van het gebruikte device")
     * @OA\Parameter(name="clientApp", @OA\Schema(type="string"), required="false", description="Appliciatie type")
     * @OA\Parameter(name="clientVersion", @OA\Schema(type="string"), required="false", description="Versie van de client")
     */
    public function basicUsernameAction(
        EntityManagerInterface $em,
        AccountRepository $accountRepo,
        TokenMapper $mapper,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $message = json_decode($request->getContent(false), true);
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        $message = array_merge(['username' => null, 'password' => null], $message);

        if ($message['username'] === null) {
            throw $this->createAccessDeniedException('Account unknown');
        }

        $token = new Token();
        $token->setClientApp($message['clientApp'] ?? null);
        $token->setClientVersion($message['clientVersion'] ?? null);
        $token->setDeviceUuid($message['deviceUuid'] ?? null);
        $token->setLifeTime(60 * 60 * 8 * 1);

        $account = $accountRepo->getByUsername($message['username']);
        if ($account === null) {
            throw $this->createAccessDeniedException('Account unknown');
        }

        if ($account->getLocked() === true) {
            return new JsonResponse(['error' => 'Account is locked'], Response::HTTP_LOCKED, []);
        }

        if ($account->getActive() === false) {
            return new JsonResponse(['error' => 'Account is not active'], Response::HTTP_FORBIDDEN, []);
        }

        $token->setAccount($account);

        $account->setLastAttempt(new \DateTime());
        $em->flush();

        if ($passwordEncoder->isPasswordValid($account, $message['password']) === false) {
            $attempts = $account->getAttempts();
            $attempts++;
            $account->setAttempts($attempts++);
            if ($attempts >= 9) {
                $account->setLocked(true);
            }
            $em->flush();
            throw $this->createAccessDeniedException('Password invalid');
        }

        $account->setAttempts(0);
        $em->flush();

        $em->persist($token);
        $em->flush();

        $response = $mapper->singleEntityToModel($token);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * @Route("/login/whoami/", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function whoamiAction(AccountMapper $mapper, Request $request)
    {
        $account = $this->getUser();
        if ($account === null) {
            return new JsonResponse(['account' => null, 'authorization-header' => $request->headers->get('Authorization')], Response::HTTP_NOT_FOUND, []);
        }

        $result = $mapper->singleEntityToModel($account);
        return new JsonResponse(['account' => $result, 'authorization-header' => $request->headers->get('Authorization')], Response::HTTP_OK, []);
    }

    /**
     * @Route("/logout/", methods={"GET"})
     */
    public function logoutAction(TokenRepository $repository, TokenMapper $mapper, EntityManagerInterface $em, Request $request)
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

        $token = $repository->getByUuid($header[1]);
        $token->setLifeTime(0);

        $em->flush();

        $response = $mapper->singleEntityToModel($token);

        return new JsonResponse($response, Response::HTTP_OK, []);
    }

    /**
     * @Route("/login/roles", methods={"GET"})
     */
    public function rolesListAction()
    {
        return new JsonResponse(Roles::all(), Response::HTTP_OK, []);
    }
}
