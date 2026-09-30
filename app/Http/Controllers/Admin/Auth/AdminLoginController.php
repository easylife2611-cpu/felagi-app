<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\TelegramWidgetService;
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

}
