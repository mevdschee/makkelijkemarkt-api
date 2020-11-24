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

use App\Entity\Account;
use App\Enum\Roles;
use App\Mapper\AccountMapper;
use App\Repository\AccountRepository;
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
 * @Route("1.1.0")
 */

class AccountController extends AbstractController
{
    /**
     * Geeft accounts
     *
     * @Route("/account/", methods={"GET"})
     * @OA\Parameter(name="order", in="query", description="Deel van een naam", @OA\Schema(type="string"))
     * @OA\Parameter(name="active", in="query", description="Actief status 1 = actief, 0 = non actief, -1 = geen selectie", @OA\Schema(type="string"))
     * @OA\Parameter(name="locked", in="query", description="Locked status 1 = actief, 0 = non actief, -1 = geen selectie", @OA\Schema(type="string"))
     * @OA\Tag(name="Account")
     */
    public function listAction(AccountRepository $repository, AccountMapper $mapper, Request $request): Response
    {
        $q = [];
        if ($request->query->has('naam') === true) {
            $q['naam'] = $request->query->get('naam');
        }

        if ($request->query->getInt('active', -1) !== -1) {
            $q['active'] = ($request->query->getInt('active') === 1);
        }

        if ($request->query->getInt('locked', -1) !== -1) {
            $q['locked'] = ($request->query->getInt('locked') === 1);
        }

        $results = $repository->search($q, $request->query->get('listOffset'), $request->query->get('listLength', 200));

        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Geeft informatie over specifiek account
     *
     * @Route("/account/{id}", methods={"GET"})
     * @OA\Parameter(name="id", in="path", description="Account id", @OA\Schema(type="string"))
     * @OA\Tag(name="Account")
     * @IsGranted("ROLE_SENIOR")
     */
    public function getAction(AccountMapper $mapper, $id): Response
    {
        /* @var $repository \App\Entity\AccountRepository */
        $repositoryAccount = $this->get('appapi.repository.account');

        $account = $repositoryAccount->getById($id);
        if ($account === null) {
            throw $this->createNotFoundException('No account with id ' . $id);
        }

        $result = $mapper->singleEntityToModel($account);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Slaat informatie over een account op
     *
     * @Route("/account/{id}", methods={"PUT"})
     * @OA\Parameter(name="id", in="path", description="Account id", @OA\Schema(type="string"))
     * @OA\Parameter(name="naam", in="body", @OA\Schema(type="string"))
     * @OA\Parameter(name="email", in="body", @OA\Schema(type="string"))
     * @OA\Parameter(name="username", in="body", @OA\Schema(type="string"))
     * @OA\Parameter(name="password", in="body", @OA\Schema(type="string"))
     * @OA\Tag(name="Account")
     * @IsGranted("ROLE_ADMIN")
     */
    public function putAction(EntityManagerInterface $em, AccountRepository $repository, AccountMapper $mapper, Request $request, $id): Response
    {
        // parse body content
        $message = json_decode($request->getContent(false), true);

        // validate message
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        if (isset($message['naam']) === false) {
            return new JsonResponse(['error' => 'Required field naam is missing']);
        }

        if (isset($message['email']) === false) {
            return new JsonResponse(['error' => 'Required field email is missing']);
        }

        if (isset($message['username']) === false) {
            return new JsonResponse(['error' => 'Required field username is missing']);
        }

        $roles = Roles::all();
        if (!array_key_exists($message['role'], $roles)) {
            return new JsonResponse(['error' => 'Unknown role']);
        }

        // get account
        /* @var $account Account */
        $account = $repository->getById($id);
        if ($account === null) {
            throw $this->createNotFoundException('No account with id ' . $id);
        }

        // set values
        $account->setNaam($message['naam']);
        $account->setEmail($message['email']);
        $account->setUsername($message['username']);
        $account->setRole($message['role']);
        $account->setActive(($message['active'] == 1));

        if (isset($message['password']) === true) {
            // encrypt password
            $unencryptedPassword = $message['password'];
            $encoder = $this->container->get('security.password_encoder');
            $encryptedPassword = $encoder->encodePassword($account, $unencryptedPassword);

            $account->setPassword($encryptedPassword);
        }

        // save
        $em->flush();

        // return
        $result = $mapper->singleEntityToModel($account);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Maak een nieuw account
     *
     * @Route("/account/", methods={"POST"})
     * @OA\Parameter(name="naam", in="body", @OA\Schema(type="string"))
     * @OA\Parameter(name="email", in="body", @OA\Schema(type="string"))
     * @OA\Parameter(name="username", in="body", @OA\Schema(type="string"))
     * @OA\Parameter(name="password", in="body", @OA\Schema(type="string"))
     * @OA\Parameter(name="role", in="body", @OA\Schema(type="string"))
     * @OA\Tag(name="Account")
     * @IsGranted("ROLE_ADMIN")
     */
    public function postAction(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, AccountMapper $accountMapper, Request $request): Response
    {
        // parse body content
        $message = json_decode($request->getContent(false), true);

        // validate message
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }

        if (isset($message['naam']) === false) {
            return new JsonResponse(['error' => 'Required field naam is missing']);
        }

        if (isset($message['email']) === false) {
            return new JsonResponse(['error' => 'Required field email is missing']);
        }

        if (isset($message['username']) === false) {
            return new JsonResponse(['error' => 'Required field username is missing']);
        }

        if (isset($message['password']) === false) {
            return new JsonResponse(['error' => 'Required field password is missing']);
        }

        if (isset($message['role']) === false) {
            return new JsonResponse(['error' => 'Required field role is missing']);
        }

        $roles = Roles::all();
        if (!array_key_exists($message['role'], $roles)) {
            return new JsonResponse(['error' => 'Unknown role']);
        }

        // get account
        $account = new Account();

        // encrypt password
        $unencryptedPassword = $message['password'];
        $encryptedPassword = $encoder->encodePassword($account, $unencryptedPassword);

        // set values
        $account->setNaam($message['naam']);
        $account->setEmail($message['email']);
        $account->setUsername($message['username']);
        $account->setPassword($encryptedPassword);
        $account->setRole($message['role']);
        $account->setLocked(false);
        $account->setAttempts(0);
        $account->setActive(true);

        // save
        $em->persist($account);
        $em->flush();

        // return
        $result = $accountMapper->singleEntityToModel($account);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Update passwords
     *
     * @Route("/account_password/{id}", methods={"PUT"})
     * @OA\Parameter(name="id", in="path", description="Account id", @OA\Schema(type="string"))
     * @OA\Parameter(name="password", in="body", @OA\Schema(type="string"))
     * @OA\Tag(name="Account")
     * @IsGranted("ROLE_SENIOR")
     */
    public function updatePasswordAction(EntityManagerInterface $em, AccountRepository $accountRepository, Request $request, $id): Response
    {
        // parse body content
        $message = json_decode($request->getContent(false), true);

        // validate message
        if ($message === null) {
            return new JsonResponse(['error' => json_last_error_msg()]);
        }
        if (isset($message['password']) === false) {
            return new JsonResponse(['error' => 'Password field is missing']);
        }

        $account = $accountRepository->find($id);
        if (null === $account) {
            return new JsonResponse(['error' => 'Account not found']);
        }
        /**
         * @var Account $account
         */

        if ('ROLE_ADMIN' === $account->getRole() && 'ROLE_ADMIN' !== $this->getUser()->getRole()) {
            return new JsonResponse(['error' => 'Access denied']);
        }

        // encrypt password
        $unencryptedPassword = $message['password'];
        $encoder = $this->container->get('security.password_encoder');
        $encryptedPassword = $encoder->encodePassword($account, $unencryptedPassword);

        $account->setPassword($encryptedPassword);

        $em->flush();

        // return
        $result = $this->get('appapi.mapper.account')->singleEntityToModel($account);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Unlock an account
     *
     * @Route("/account/unlock/{id}", methods={"POST"})
     * @OA\Parameter(name="id", in="path", description="Account id", @OA\Schema(type="string"))
     * @OA\Tag(name="Account")
     * @IsGranted("ROLE_SENIOR")
     */
    public function unlockAction(EntityManagerInterface $em, AccountRepository $accountRepository, $id): Response
    {
        /** @var Account $account */
        $account = $accountRepository->findOneById($id);

        if (null === $account) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        $account->setAttempts(0);
        $account->setLocked(false);
        $em->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }
}
