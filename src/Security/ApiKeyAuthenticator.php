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

namespace App\Security;

use App\Repository\TokenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiKeyAuthenticator extends AbstractGuardAuthenticator
{

    private $mmAppKey;
    private $security;
    private $tokenRepository;

    public function __construct(string $mmAppKey, Security $security, TokenRepository $tokenRepository)
    {
        $this->mmAppKey = $mmAppKey;
        $this->security = $security;
        $this->tokenRepository = $tokenRepository;
    }
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        if ($this->security->getUser()) {
            return false;
        }
        return true;
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        if ($request->headers->get('MmAppKey') !== $this->mmAppKey) {
            throw new AuthenticationException('Invalid application key');
        }

        $authorizationHeader = $request->headers->get('Authorization');
        $header = explode(' ', $authorizationHeader);
        if ($header[0] !== 'Bearer') {
            throw new AuthenticationException('Invalid authorization header');
        }

        $token = $this->tokenRepository->getByUuid($header[1] ?? '');
        if (!$token) {
            throw new AuthenticationException('Invalid token uuid');
        }

        $timeLeft = $token->getCreationDate()->getTimestamp() + $token->getLifeTime() - time();
        if ($timeLeft < 0) {
            throw new AuthenticationException('Invalid token time');
        }

        $account = $token->getAccount();
        if (!$account) {
            throw new AuthenticationException('Invalid token account');
        }

        return $account->getUsername();
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        // The "username" in this case is the apiToken, see the key `property`
        // of `your_db_provider` in `security.yaml`.
        // If this returns a user, checkCredentials() is called next:
        return $userProvider->loadUserByUsername($credentials);
    }

    public function checkCredentials($credentials, UserInterface $account)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response($exception->getMessage(), Response::HTTP_PRECONDITION_FAILED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response('Authentication Required', Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
