<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AdminLoginController;

Route::get('/', function (\Illuminate\Http\Request $request) {
    $handoff = $request->query('handoff_code');

    if ($handoff) {
        try {
            $service = app(\App\Services\Auth\AuthAttemptService::class);
            $result  = $service->consumeByHandoff($handoff);

            if ($result && ! empty($result['user'])) {
                \Auth::guard('web')->login($result['user']);
                $request->session()->regenerate();
                return redirect('/browse');
            }
        } catch (\Throwable $e) {
            \Log::warning('Handoff consume failed', ['error' => $e->getMessage()]);
        }
    }

    return view('welcome-premium');
});

// S002 Telegram sign-in (alias for welcome — admin login redirects here)
Route::get('/auth/telegram', function () {
    return view('telegram-premium');
});

// S001 Welcome alias (design path: /welcome; canonical route is /)
Route::get('/welcome', function () {
    return view('welcome-premium');
});

Route::get('/profile', function () {
    return view('profile-premium');
});

Route::get('/browse', function () {
    return view('browse-premium');
});

Route::get('/needs/new', function () {
    return view('create-need-premium');
});

Route::get('/needs/{id}', function ($id) {
    return view('show-need');
});

Route::get('/my/needs', function () {
    return view('my-needs');
});

Route::get('/needs/{id}/offers/new', function ($id) {
    return view('submit-offer');
});

Route::get('/needs/{id}/offers', function ($id) {
    return view('received-offers');
});

Route::get('/offers/{id}', function ($id) {
    return view('offer-detail');
});

Route::get('/my/offers', function () {
    return view('my-offers');
});

Route::get('/notifications', function () {
    return view('notifications');
});

Route::get('/offers/{id}/messages', function ($id) {
    return view('offer-messages');
});

Route::get('/needs/{id}/compare', function ($id) {
    return view('compare-offers');
});

Route::get('/needs/{id}/created', function ($id) {
    return view('need-created-premium');
});

Route::get('/needs/new/public-preview', function () {
    return view('need-preview-premium');
});

Route::get('/needs/{id}/rating', function ($id) {
    return view('rate-participant');
});

Route::get('/comparisons/{id}', function ($id) {
    return view('comparison-result');
});

Route::get('/needs/{id}/comparisons', function ($id) {
    return view('comparison-history');
});

Route::get('/needs/{id}/boost', function ($id) {
    return view('boost-need');
});

Route::get('/support/report', function () {
    return view('report-support');
});

Route::get('/needs/{id}/publications', function ($id) {
    return view('telegram-publications');
});

Route::get('/needs/{id}/offers/unlock', function ($id) {
    return view('offer-unlock');
});

// ─── Admin Routes (A001-A023) ───
// Login is public; all other admin routes require auth + admin role.
Route::prefix('admin')->group(function () {

    // Public: login page
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])
        ->name('admin.login');

    // Public: Telegram widget callback for admin login (creates web session)
    Route::post('/login/telegram', [AdminLoginController::class, 'telegramCallback'])
        ->middleware('throttle:10,1')
        ->name('admin.login.telegram');

        // L291 — OIDC direct flow (replaces the iframe widget)
        Route::get('/login/oidc/start',    [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'oidcStart'])
            ->name('admin.login.oidc.start');
        Route::get('/login/oidc/callback', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'oidcCallback'])
            ->name('admin.login.oidc.callback');

    // Protected: logout + admin panel (auth + admin role)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])
            ->name('admin.logout');

        Route::get('/dashboard',                  fn() => view('admin.dashboard'));
        Route::get('/telegram',                   fn() => view('admin.telegram'));
        Route::get('/health',                     fn() => view('admin.health'));
        Route::get('/features',                   fn() => view('admin.features'));
        Route::get('/marketplace',                fn() => view('admin.marketplace'));
        Route::get('/ai',                         fn() => view('admin.ai'));
        Route::get('/payments',                   fn() => view('admin.payments'));
        Route::get('/users',                      fn() => view('admin.users'));
        Route::get('/content',                    fn() => view('admin.content'));
        Route::get('/notifications',              fn() => view('admin.notifications'));
        Route::get('/files',                      fn() => view('admin.files'));
        Route::get('/jobs',                       fn() => view('admin.jobs'));
        Route::get('/backups',                    fn() => view('admin.backups'));
        Route::get('/integrity',                  fn() => view('admin.integrity'));
        Route::get('/security',                   fn() => view('admin.security'));
        Route::get('/audit',                      fn() => view('admin.audit'));
        Route::get('/settings',                   fn() => view('admin.settings'));
        Route::get('/recovery',                   fn() => view('admin.recovery'));
        Route::get('/safe-mode',                  fn() => view('admin.safe-mode'));
        Route::get('/monetization',               fn() => view('admin.monetization'));
        Route::get('/maintenance',                fn() => view('admin.maintenance'));
        Route::get('/reports',                    fn() => view('admin.reports'));
        Route::get('/monetization/sponsored-ads', fn() => view('admin.sponsored-ads'));
    });
});

// 2FA profile page — L302
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/profile/2fa', [\App\Http\Controllers\V1\TwoFactorWebController::class, 'index'])
        ->name('profile.2fa');
});

// Fallback login route (redirects to Telegram sign-in)
Route::get('/login', function () {
    return redirect('/auth/telegram');
})->name('login');


// L302 — Email OTP web verify (creates web session for browser flow)
Route::post('/auth/email/verify-web', [\App\Http\Controllers\V1\EmailAuthController::class, 'verifyWeb'])->middleware('throttle:10,1')->name('auth.email.verify-web');

// L304 — Email Verification + Password Reset
Route::get('/verify-email/{token}', [\App\Http\Controllers\V1\EmailVerificationController::class, 'verify'])
    ->where('token', '[A-Za-z0-9_-]+')
    ->name('email.verify');

Route::get('/forgot-password', [\App\Http\Controllers\V1\PasswordResetController::class, 'showForgotForm'])
    ->name('password.request');

Route::post('/forgot-password', [\App\Http\Controllers\V1\PasswordResetController::class, 'sendLink'])
    ->middleware('throttle:5,1')
    ->name('password.email');

Route::get('/reset-password/{token}', [\App\Http\Controllers\V1\PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [\App\Http\Controllers\V1\PasswordResetController::class, 'reset'])
    ->middleware('throttle:5,1')
    ->name('password.update');

// ─── QA Login Route (env-gated, production-safe) ───
// Enables automated deep QA via Playwright authenticated sessions.
// SECURITY: only active when QA_LOGIN_ENABLED=true (default: false).
// Never enable in production.
if (env('QA_LOGIN_ENABLED', false) === true) {
    Route::get('/_qa/login', function (\Illuminate\Http\Request $request) {
        $userName = (string) $request->query('user', 'So');
        $user = \App\Models\User::where('full_name', $userName)->firstOrFail();

        \Illuminate\Support\Facades\Auth::guard('web')->login($user);

        return response()->json([
            'success'    => true,
            'user'       => $user->full_name,
            'user_id'    => $user->id,
            'session_id' => session()->getId(),
            'cookie'     => config('session.cookie'),
        ]);
    })->middleware(['web']);
}

// ─── L342: Locale switcher ───
Route::get('/lang/{locale}', function (string $locale) {
    if (! in_array($locale, ['am', 'en'], true)) {
        abort(404);
    }
    session(['locale' => $locale]);
    return redirect()->back();
})->name('lang.switch');
