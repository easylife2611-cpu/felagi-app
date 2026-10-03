<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // G04C — security headers on every response
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // L338 — locale resolution (Amharic default per design)
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\RequestId::class,
        ]);

        $middleware->alias([
            'reauth' => \App\Http\Middleware\RequireReauth::class,
            'idempotent' => \App\Http\Middleware\IdempotencyKey::class,
            'admin' => \App\Http\Middleware\EnsureAdminRole::class,
        ]);

        // Redirect unauthenticated users to /admin/login (for /admin/* routes)
        // Otherwise fall back to the named 'login' route if it exists.
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return '/admin/login';
            }
            return null;
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // T23: consistent JSON envelope with request_id for auth errors

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'UNAUTHENTICATED',
                        'message' => 'Authentication required.',
                    ],
                    'request_id' => $request->attributes->get('request_id') ?? (string) Str::uuid(),
                ], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'FORBIDDEN',
                        'message' => 'Insufficient permissions.',
                    ],
                    'request_id' => $request->attributes->get('request_id') ?? (string) Str::uuid(),
                ], 403);
            }
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Resource not found.',
                    ],
                    'request_id' => $request->attributes->get('request_id') ?? (string) Str::uuid(),
                ], 404);
            }
        });
    })->create();
