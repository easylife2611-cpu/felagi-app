<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Assigns a stable request_id to every API request.
 *
 * Used by BaseApiController::requestId() (reads from request attributes)
 * and included in every JSON response envelope.
 *
 * Also sets the X-Request-Id response header for observability.
 */
class RequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->attributes->has('request_id')) {
            $request->attributes->set('request_id', (string) Str::uuid());
        }

        $response = $next($request);

        $response->headers->set('X-Request-Id', (string) $request->attributes->get('request_id'));

        return $response;
    }
}
