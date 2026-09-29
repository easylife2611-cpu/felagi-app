<?php

namespace App\Http\Middleware;

use App\Exceptions\IdempotencyConflictException;
use App\Services\Admin\IdempotencyRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Idempotency-Key middleware for POST/PATCH/PUT/DELETE.
 *
 * - Reads `Idempotency-Key` header
 * - If absent → proceed normally (no idempotency)
 * - If present:
 *     * new key      → proceed; record response after
 *     * same key     → return cached response (replay)
 *     * same key + different body → 409 (via exception)
 *     * in flight    → 409 CONFLICT
 *
 * Scope derived from route name or URL path.
 */
class IdempotencyKey
{
    public function __construct(
        private readonly IdempotencyRegistry $registry,
    ) {}

    public function handle(Request $request, Closure $next, ?string $scope = null): Response
    {
        // Only for state-changing verbs
        if (!in_array($request->method(), ['POST', 'PATCH', 'PUT', 'DELETE'], true)) {
            return $next($request);
        }

        // No header → no idempotency
        if (!$request->hasHeader(IdempotencyRegistry::HEADER)) {
            return $next($request);
        }

        $scope = $scope ?: $this->defaultScope($request);
        $user  = $request->user();

        try {
            $state = $this->registry->begin($request, $scope, $user);
        } catch (IdempotencyConflictException $e) {
            return response()->json([
                'success'    => false,
                'error'      => $e->toArray() + ['message' => $e->getMessage()],
                'request_id' => $request->attributes->get('request_id'),
            ], 409);
        }

        // Replay: return cached response
        if ($state['state'] === 'replay') {
            $record = $state['record'];
            $body   = $record->response_body ? json_decode($record->response_body, true) : null;
            return response()->json($body, $record->response_code)
                ->header('X-Idempotency-Replayed', 'true');
        }

        // In flight: same key still processing
        if ($state['state'] === 'progress') {
            return response()->json([
                'success'    => false,
                'error'      => [
                    'code'    => 'IDEMPOTENCY_IN_PROGRESS',
                    'message' => 'A request with this Idempotency-Key is still being processed.',
                ],
                'request_id' => $request->attributes->get('request_id'),
            ], 409);
        }

        // New: proceed
        $response = $next($request);

        // Record COMPLETE
        $bodyArray = null;
        if ($response->headers->get('Content-Type') === 'application/json') {
            $decoded = json_decode($response->getContent(), true);
            if (is_array($decoded)) {
                $bodyArray = $decoded;
            }
        }

        $this->registry->complete(
            $request,
            $scope,
            $response->getStatusCode(),
            $bodyArray,
            $user,
        );

        return $response;
    }

    protected function defaultScope(Request $request): string
    {
        // Derive from route name (e.g. "admin.changes.publish") or path
        $route = $request->route();
        if ($route && $route->getName()) {
            return $route->getName();
        }

        return 'route:' . $request->method() . ':' . $request->path();
    }
}
