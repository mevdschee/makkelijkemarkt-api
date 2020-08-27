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

use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Enum\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Dagvergunning;
use Symfony\Component\Validator\Constraints\DateTime;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Account;

/**
 * @Route("1.1.0")
 */
class AccountController extends Controller
{
    /**
     * Geeft accounts
     *
     * @Method("GET")
     * @Route("/account/")
     * @ApiDoc(
     *  section="Account",
     *  filters={
     *      {"name"="naam", "dataType"="string", "description"="Deel van een naam"},
     *      {"name"="active", "dataType"="string", "default"="-1", "description"="Actief status 1 = actief, 0 = non actief, -1 = geen selectie"},
     *      {"name"="locked", "dataType"="string", "default"="-1", "description"="Locked status 1 = actief, 0 = non actief, -1 = geen selectie"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     */
    public function listAction(Request $request)
    {
        /* @var $repo \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\AccountRepository */
        $repo = $this->get('appapi.repository.account');

        $q = [];
        if ($request->query->has('naam') === true)
            $q['naam'] = $request->query->get('naam');
        if ($request->query->getInt('active', -1) !== -1)
            $q['active'] = ($request->query->getInt('active') === 1);
        if ($request->query->getInt('locked', -1) !== -1)
            $q['locked'] = ($request->query->getInt('locked') === 1);
        $results = $repo->search($q, $request->query->get('listOffset'), $request->query->get('listLength', 200));

        /* @var $mapper \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Mapper\AccountMapper */
        $mapper = $this->get('appapi.mapper.account');
        $response = $mapper->multipleEntityToModel($results);

        return new JsonResponse($response, Response::HTTP_OK, ['X-Api-ListSize' => count($results)]);
    }

    /**
     * Geeft informatie over specifiek account
     *
     * @Method("GET")
     * @Route("/account/{id}")
     * @ApiDoc(
     *  section="Account",
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Account id"}
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_SENIOR')")
     */
    public function getAction(Request $request, $id)
    {
        /* @var $repository \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\AccountRepository */
        $repositoryAccount = $this->get('appapi.repository.account');

        $account = $repositoryAccount->getById($id);
        if ($account === null)
            throw $this->createNotFoundException('No account with id ' . $id);

        $result = $this->get('appapi.mapper.account')->singleEntityToModel($account);

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Slaat informatie over een account op
     *
     * @Method("PUT")
     * @Route("/account/{id}")
     * @ApiDoc(
     *  section="Account",
     *  requirements={
     *      {"name"="id", "dataType"="string", "description"="Account id"}
     *  },
     *  parameters={
     *      {"name"="naam", "dataType"="string", "required"="true"},
     *      {"name"="email", "dataType"="string", "required"="true"},
     *      {"name"="username", "dataType"="string", "required"="true"},
     *      {"name"="password", "dataType"="string", "required"="true"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function putAction(Request $request, $id)
    {
        /* @var $repository \GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\AccountRepository */
        $repositoryAccount = $this->get('appapi.repository.account');

        // parse body content
        $message = json_decode($request->getContent(false), true);

        // validate message
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);
        if (isset($message['naam']) === false)
            return new JsonResponse(['error' => 'Required field naam is missing']);
        if (isset($message['email']) === false)
            return new JsonResponse(['error' => 'Required field email is missing']);
        if (isset($message['username']) === false)
            return new JsonResponse(['error' => 'Required field username is missing']);
        $roles = Roles::all();
        if (!array_key_exists($message['role'],$roles)) {
            return new JsonResponse(['error' => 'Unknown role']);
        }

        // get account
        /* @var $account Account */
        $account = $repositoryAccount->getById($id);
        if ($account === null)
            throw $this->createNotFoundException('No account with id ' . $id);

        // set values
        $account->setNaam($message['naam']);
        $account->setEmail($message['email']);
        $account->setUsername($message['username']);
        $account->setRole($message['role']);
        $account->setActive(($message['active'] == 1));

        if (isset($message['password']) === true)
        {
            // encrypt password
            $unencryptedPassword = $message['password'];
            $encoder = $this->container->get('security.password_encoder');
            $encryptedPassword = $encoder->encodePassword($account, $unencryptedPassword);

            $account->setPassword($encryptedPassword);
        }

        // save
        $this->getDoctrine()->getManager()->flush();

        // return
        $result = $this->get('appapi.mapper.account')->singleEntityToModel($account);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Maak een nieuw account
     *
     * @Method("POST")
     * @Route("/account/")
     * @ApiDoc(
     *  section="Account",
     *  parameters={
     *      {"name"="naam", "dataType"="string", "required"="true"},
     *      {"name"="email", "dataType"="string", "required"="true"},
     *      {"name"="username", "dataType"="string", "required"="true"},
     *      {"name"="password", "dataType"="string", "required"="true"},
     *      {"name"="role", "dataType"="string", "required"="true"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function postAction(Request $request)
    {
        // parse body content
        $message = json_decode($request->getContent(false), true);

        // validate message
        if ($message === null)
            return new JsonResponse(['error' => json_last_error_msg()]);
        if (isset($message['naam']) === false)
            return new JsonResponse(['error' => 'Required field naam is missing']);
        if (isset($message['email']) === false)
            return new JsonResponse(['error' => 'Required field email is missing']);
        if (isset($message['username']) === false)
            return new JsonResponse(['error' => 'Required field username is missing']);
        if (isset($message['password']) === false)
            return new JsonResponse(['error' => 'Required field password is missing']);
        if (isset($message['role']) === false)
            return new JsonResponse(['error' => 'Required field role is missing']);
        $roles = Roles::all();
        if (!array_key_exists($message['role'],$roles)) {
            return new JsonResponse(['error' => 'Unknown role']);
        }

        // get account
        $account = new Account();

        // encrypt password
        $unencryptedPassword = $message['password'];
        $encoder = $this->container->get('security.password_encoder');
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
        $this->getDoctrine()->getManager()->persist($account);
        $this->getDoctrine()->getManager()->flush();

        // return
        $result = $this->get('appapi.mapper.account')->singleEntityToModel($account);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Update passwords
     *
     * @Method("PUT")
     * @Route("/account_password/{id}")
     * @ApiDoc(
     *  section="Account",
     *  parameters={
     *      {"name"="password", "dataType"="string", "required"="true"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_SENIOR')")
     */
    public function updatePasswordAction(Request $request, $id)
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

        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository(Account::class);

        $account = $repo->find($id);
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
     * @Method("POST")
     * @Route("/account/unlock/{id}")
     * @ApiDoc(
     *  section="Account",
     *  parameters={
     *      {"name"="id", "dataType"="string", "required"="true"},
     *  },
     *  views = { "default", "1.1.0" }
     * )
     * @Security("has_role('ROLE_SENIOR')")
     */
    public function unlockAction(Request $request, $id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $accountRepo = $this->get('appapi.repository.account');

        /** @var Account $account */
        $account = $accountRepo->findOneById($id);

        if (null === $account) {
            return new JsonResponse([], Response::HTTP_OK);
        }

        $account->setAttempts(0);
        $account->setLocked(false);
        $em->flush();

        return new JsonResponse([], Response::HTTP_OK);
    }
}
