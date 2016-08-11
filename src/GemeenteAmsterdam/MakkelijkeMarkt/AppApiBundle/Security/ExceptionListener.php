<?php
namespace GemeenteAmsterdam\MakkelijkeMarkt\AppApiBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof AuthenticationException) {
            $event->setResponse(new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED));
        }
        if ($exception instanceof AccessDeniedHttpException) {
            $event->setResponse(new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED));
        }
        if ($exception instanceof AccessDeniedException) {
            $event->setResponse(new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED));
        }
    }
}