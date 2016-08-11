<?php

namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\AccountRepository;
use GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\TokenRepository;

class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * @var GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\AccountRepository
     */
    protected $accountRepository;

    /**
     * @var GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\TokenRepository
     */
    protected $tokenRepository;

    public function __construct(AccountRepository $accountRepository, TokenRepository $tokenRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->tokenRepository = $tokenRepository;
    }

    public function getTokenByApiKey($apiKey)
    {
        return $this->tokenRepository->getByUuid($apiKey);
    }

    public function loadUserByUsername($username)
    {
        return $this->accountRepository->getByUsername($username);
    }

    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Entity\Account' === $class;
    }
}