<?php

namespace App\Http\Middleware;

use App\Exceptions\ReauthRequiredException;
use App\Services\Admin\ReauthValidator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RequireReauth middleware.
 *
 * Design lock (Auth Contract §3):
 *   "HIGH/CRITICAL publication requires fresh authenticated
 *    reauthentication within five minutes."
 *
 * Attach to routes: ->middleware('reauth')
 *
 * The middleware only checks the SESSION timestamp — actual setting-level
 * risk checks (LOW/MEDIUM vs HIGH/CRITICAL) happen in the controller
 * via ReauthValidator::require($user, $setting).
 *
 * Usage:
 *   Route::post('.../publish', ...)->middleware(['auth:sanctum', 'reauth']);
 */
class RequireReauth
{
    public function __construct(
        private readonly ReauthValidator $validator,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success'    => false,
                'error'      => [
                    'code'    => 'UNAUTHENTICATED',
                    'message' => 'Authentication required.',
                ],
                'request_id' => $request->attributes->get('request_id'),
            ], 401);
        }

        // Route-level check: is user fresh at all?
        // Controller-level check will re-verify against specific setting risk.
        if (!$this->validator->isFresh($user)) {
            $age = $this->validator->ageInMinutes($user);

            return response()->json([
                'success'    => false,
                'error'      => [
                    'code'    => 'REAUTH_REQUIRED',
                    'message' => sprintf(
                        'Re-authentication required. Window: %d minutes. Last auth: %s.',
                        ReauthValidator::WINDOW_MINUTES,
                        $age === null ? 'never' : round($age, 1) . ' min ago',
                    ),
                    'details' => [
                        'window_minutes' => ReauthValidator::WINDOW_MINUTES,
                        'age_minutes'    => $age,
                    ],
                ],
                'request_id' => $request->attributes->get('request_id'),
            ], 401);
        }

        return $next($request);
    }
}
