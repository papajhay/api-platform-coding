<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class ApiAccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        if (!$this->isApiWriteRequest($request)) {
            return null;
        }

        return new JsonResponse([
            'message' => 'Access denied: you do not have permission to create, modify, or delete this resource.',
        ], Response::HTTP_FORBIDDEN);
    }

    private function isApiWriteRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api')
            && \in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }
}
