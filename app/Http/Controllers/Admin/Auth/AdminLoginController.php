<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\TelegramWidgetService;
use App\Services\Auth\AuthAttemptService;
use App\Services\Auth\TelegramOidcService;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OidcExchangeException;
use Illuminate\View\View;

/**
 * Admin browser login — Telegram-first (Felagi identity model).
 *
 * Per DFM-FDS-1.4.md §5.1: Telegram login uses OIDC authorization-code flow.
 * Per Admin_Authorization_Contract.md: browser Admin session, Admin role check.
 *
 * There is NO email/password for Felagi users (User has no email/password
 * columns; telegram_subject is the primary identity).
 *
 * Flow:
 *   1. GET  /admin/login  → login page with Telegram sign-in button
 *   2. User signs in via /auth/telegram (existing S002 flow)
 *   3. Callback redirects here with an authenticated session
 *   4. GET  /admin/dashboard → EnsureAdminRole verifies role
 *      - Has admin role → 200
 *      - No role → 403
 */
class AdminLoginController extends Controller
{
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        // If already authenticated with an admin role → skip to dashboard
        $user = $request->user();
        if ($user && $this->hasAdminRole($user)) {
            return redirect('/admin/dashboard');
        }

        return view('admin.auth.login', [
            'authenticated' => (bool) $user,
            'hasAdminRole'  => $user ? $this->hasAdminRole($user) : false,
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    private function hasAdminRole($user): bool
    {
        return $user->roles()
            ->whereIn('role', [
                UserRole::ROLE_MAIN_ADMIN,
                UserRole::ROLE_ADMIN,
                UserRole::ROLE_MODERATOR,
            ])
            ->whereNull('revoked_at')
            ->exists();
    }
    /**
     * POST /admin/login/telegram
     * Verify Telegram Widget data and create a web session.
     *
     * Widget JS POSTs the signed payload here (id, first_name, ..., hash).
     * We verify with TelegramWidgetService then Auth::login() → web session.
     */
    public function telegramCallback(Request $request, TelegramWidgetService $widgetService): RedirectResponse
    {
        try {
            $verified = $widgetService->verify($request->all());
        } catch (OidcExchangeException $e) {
            return back()->withErrors([
                "telegram" => "Telegram verification failed: " . $e->getMessage(),
            ]);
        }

        $result = $widgetService->upsertUser($verified);
        $user = $result["user"];

        // Must have an active admin role
        if (!$this->hasAdminRole($user)) {
            return back()->withErrors([
                "telegram" => __("admin.auth.no_admin_role_hint"),
            ]);
        }

        // Create web session
        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()->intended("/admin/dashboard");
    }

    /**
     * GET /admin/login/oidc/start
     *
     * L291 — OIDC direct flow (replaces the Telegram iframe widget).
     *
     * Creates an auth attempt with return_uri = admin OIDC callback,
     * then redirects to Telegram's authorization endpoint with PKCE.
     */
    public function oidcStart(Request $request, AuthAttemptService $attempts): RedirectResponse
    {
        $returnUri = url('/admin/login/oidc/callback');

        // Return host must be in the allow-list (configured in config/auth.php)
        $allowedHosts = config('auth.telegram_allowed_hosts', []);
        $returnHost = parse_url($returnUri, PHP_URL_HOST);
        if (!empty($allowedHosts) && !in_array($returnHost, $allowedHosts, true)) {
            Log::error('admin.oidc_start: return host not in allow-list', [
                'return_host' => $returnHost,
                'allowed'     => $allowedHosts,
            ]);
            return redirect('/admin/login')->withErrors([
                'telegram' => 'Configuration error: admin callback host not allowed.',
            ]);
        }

        $created = $attempts->create($returnUri);

        $authUrl = config('services.telegram.oidc.authorization_url') . '?' . http_build_query([
            'client_id'             => config('services.telegram.client_id', ''),
            'bot_id'                => config('services.telegram.bot_id', config('services.telegram.client_id', '')),
            'origin'                => parse_url(config('app.url'), PHP_URL_HOST) ?: 'zagcreativity.com',
            'redirect_uri'          => config('services.telegram.redirect_uri', ''),
            'response_type'         => 'code',
            'scope'                 => 'openid profile',
            'state'                 => $created['state'],
            'nonce'                 => $created['nonce'],
            'code_challenge'        => $created['code_challenge'],
            'code_challenge_method' => $created['code_challenge_method'],
        ]);

        return redirect()->away($authUrl);
    }

    /**
     * GET /admin/login/oidc/callback
     *
     * L291 — receives handoff_code from the API's OIDC callback,
     * verifies the user has an active admin role, creates a web session.
     */
    public function oidcCallback(Request $request, AuthAttemptService $attempts): RedirectResponse
    {
        $handoffCode = $request->query('handoff_code');

        if (!$handoffCode) {
            return redirect('/admin/login')->withErrors([
                'telegram' => 'Missing handoff code from Telegram.',
            ]);
        }

        $result = $attempts->consumeByHandoff($handoffCode);
        if (!$result || empty($result['user'])) {
            return redirect('/admin/login')->withErrors([
                'telegram' => 'Invalid or expired sign-in link. Please try again.',
            ]);
        }

        $user = $result['user'];

        if (!$this->hasAdminRole($user)) {
            return redirect('/admin/login')->withErrors([
                'telegram' => __('admin.auth.no_admin_role_hint'),
            ]);
        }

        Auth::login($user, false);
        $request->session()->regenerate();

        return redirect()->intended('/admin/dashboard');
    }

}
