<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * G04C finding — Security headers.
 *
 * Adds the five recommended security headers that were missing at
 * production probe time (2026-10-01):
 *
 *   - Strict-Transport-Security
 *   - Content-Security-Policy
 *   - X-Frame-Options
 *   - X-Content-Type-Options
 *   - Referrer-Policy
 *
 * Additive only. Applied globally via bootstrap/app.php. This is
 * intentionally a small, focused change: it does not alter any
 * controller, route, or view.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // HSTS — only on HTTPS (never send over plain HTTP)
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        // CSP — includes Telegram widget requirements (unsafe-eval + frame-src) — needed by S002
        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; "
                . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://telegram.org https://zagcreativity.com https://www.zagcreativity.com; "
                . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
                . "img-src 'self' data: https: https://zagcreativity.com https://www.zagcreativity.com; "
                . "font-src 'self' data: https://fonts.gstatic.com; "
                . "connect-src 'self' https://zagcreativity.com https://www.zagcreativity.com https://api.telegram.org https://oauth.telegram.org https://generativelanguage.googleapis.com; "
                . "frame-src 'self' https://oauth.telegram.org https://telegram.org https://zagcreativity.com https://www.zagcreativity.com; "
                . " "
                . "frame-ancestors 'none'; "
                . "base-uri 'self'; "
                . "form-action 'self'"
            );
        }

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        return $response;
    }
}
