<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ApiAccessDeniedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        if (!$this->isApiWriteRequest($request)) {
            return;
        }

        $exception = $event->getThrowable();
        if (!$exception instanceof AccessDeniedException && !$exception instanceof AccessDeniedHttpException) {
            return;
        }

        $event->setResponse(new JsonResponse([
            'message' => 'Access denied: you do not have permission to create, modify, or delete this resource.',
        ], Response::HTTP_FORBIDDEN));
    }

    private function isApiWriteRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api')
            && \in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }
}
