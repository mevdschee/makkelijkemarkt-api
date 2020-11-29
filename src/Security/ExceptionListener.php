<?php
// /*
//  *  Copyright (c) 2020 X Gemeente
//  *                     X Amsterdam
//  *                     X Onderzoek, Informatie en Statistiek
//  *
//  *  This Source Code Form is subject to the terms of the Mozilla Public
//  *  License, v. 2.0. If a copy of the MPL was not distributed with this
//  *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
//  */

// namespace App\Security;

// use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpKernel\Event\ExceptionEvent;
// use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
// use Symfony\Component\HttpKernel\KernelEvents;
// use Symfony\Component\Security\Core\Exception\AccessDeniedException;
// use Symfony\Component\Security\Core\Exception\AuthenticationException;

// class ExceptionListener implements EventSubscriberInterface
// {
//     public static function getSubscribedEvents(): array
//     {
//         return [
//             // the priority must be greater than the Security HTTP
//             // ExceptionListener, to make sure it's called before
//             // the default exception listener
//             KernelEvents::EXCEPTION => ['onKernelException', 2],
//         ];
//     }

//     public function onKernelException(ExceptionEvent $event): void
//     {
//         $exception = $event->getThrowable();
//         if ($exception instanceof AuthenticationException) {
//             $event->setResponse(new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED));
//         }
//         if ($exception instanceof AccessDeniedHttpException) {
//             $event->setResponse(new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED));
//         }
//         if ($exception instanceof AccessDeniedException) {
//             $event->setResponse(new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED));
//         }
//     }
// }
