<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var string
     */
    protected $mmApiKey;

    public function __construct($mmApiKey)
    {
        $this->mmApiKey = $mmApiKey;
    }

    public function createToken(Request $request, $providerKey)
    {
        $appKey = $request->headers->get('MmAppKey');
        if ($appKey !== $this->mmApiKey) {
            throw new AuthenticationException('Invalid application key');
        }

        $authorizationHeader = $request->headers->get('Authorization');
        if ($authorizationHeader === null)
            return null;

        $header = explode(' ', $authorizationHeader);
        if ($header[0] !== 'Bearer')
            return null;

        if (isset($header[1]) === false)
            return null;

        return new PreAuthenticatedToken(
            'anon.',
            $header[1],
            $providerKey
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        try {
            $this->createToken($request, 'testKey');
        } catch (AuthenticationException $e) {
            return new Response(
                'Invalid application key',
                412
            );
        }
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiKeyUserProvider) {
            throw new \InvalidArgumentException(sprintf('The user provider must be an instance of ApiKeyUserProvider (%s was given).', get_class($userProvider)));
        }

        $apiKey = $token->getCredentials();

        $token = $userProvider->getTokenByApiKey($apiKey);
        if ($token === null)
            return null;

        $timeLeft = $token->getCreationDate()->getTimestamp() + $token->getLifeTime() - time();
        if ($timeLeft < 0)
            return null;

        $user = $token->getAccount();

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}