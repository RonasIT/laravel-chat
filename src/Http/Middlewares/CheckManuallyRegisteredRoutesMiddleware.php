<?php

namespace RonasIT\Chat\Http\Middlewares;

use Closure;
use RonasIT\Chat\ChatRouter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckManuallyRegisteredRoutesMiddleware
{
    public function handle($request, Closure $next)
    {
        if (ChatRouter::$isBlockedBaseRoutes) {
            throw new NotFoundHttpException('Not found.');
        }

        return $next($request);
    }
}