<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetLocale Middleware (L338)
 *
 * Locale precedence (design-compliant — Localization/README.md):
 *   1. Session 'locale' key (explicit user choice)
 *   2. Accept-Language header (browser preference)
 *   3. NULL — keep current app()->getLocale()
 *      (config default 'am' + explicit app()->setLocale() calls)
 *
 * Design rules:
 *   - "Amharic `am` is default; English `en` is required at launch."
 *   - "Unsupported locale→Amharic"
 *   - "Locale changes preserve route/form state"
 *
 * IMPORTANT:
 *   Middleware does NOT pick the default locale. That is the job of
 *   config('app.locale') at boot. Middleware only overrides when it
 *   finds an explicit session preference or browser hint.
 */
class SetLocale
{
    public const SUPPORTED = ['am', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        // null → do NOT touch app()->getLocale() (keeps config default + explicit overrides)
        if ($locale !== null) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    protected function resolveLocale(Request $request): ?string
    {
        // 1. Session (explicit user choice)
        if ($request->hasSession()) {
            $session = $request->session()->get('locale');
            if ($this->isSupported($session)) {
                return $session;
            }
        }

        // 2. Accept-Language header
        $header = $this->parseAcceptLanguage($request->header('Accept-Language'));
        if ($this->isSupported($header)) {
            return $header;
        }

        // 3. null → keep current locale
        return null;
    }

    protected function isSupported(?string $locale): bool
    {
        return $locale !== null && in_array($locale, self::SUPPORTED, true);
    }

    protected function parseAcceptLanguage(?string $header): ?string
    {
        if (empty($header)) {
            return null;
        }

        $parts  = explode(',', $header);
        $parsed = [];

        foreach ($parts as $i => $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $sub  = explode(';', $part);
            $lang = strtolower(trim($sub[0]));

            $q = 1.0;
            if (isset($sub[1]) && preg_match('/q=([0-9.]+)/', $sub[1], $m)) {
                $q = (float) $m[1];
            }

            $parsed[] = ['lang' => $lang, 'q' => $q, 'i' => $i];
        }

        usort($parsed, fn($a, $b) => ($b['q'] <=> $a['q']) ?: ($a['i'] <=> $b['i']));

        foreach ($parsed as $p) {
            $base = explode('-', $p['lang'])[0];
            if (in_array($base, self::SUPPORTED, true)) {
                return $base;
            }
        }

        return null;
    }
}
