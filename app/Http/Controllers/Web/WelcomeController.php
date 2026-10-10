<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthAttemptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * WP-32 — Welcome / Entry Controller
 *
 * Extracted from routes/web.php closures:
 *   - GET /                (handoff + welcome)
 *   - GET /welcome         (alias)
 *   - GET /auth/telegram   (S002 alias)
 *
 * Behavior preserved — no changes to view names or handoff logic.
 */
class WelcomeController extends Controller
{
    /**
     * GET /
     *
     * Handles handoff_code consumption, then shows welcome-premium.
     */
    public function index(Request $request)
    {
        $handoff = $request->query('handoff_code');

        if ($handoff) {
            try {
                $service = app(AuthAttemptService::class);
                $result  = $service->consumeByHandoff($handoff);

                if ($result && ! empty($result['user'])) {
                    Auth::guard('web')->login($result['user']);
                    $request->session()->regenerate();
                    return redirect('/browse');
                }
            } catch (\Throwable $e) {
                Log::warning('Handoff consume failed', ['error' => $e->getMessage()]);
            }
        }

        return view('welcome-premium');
    }

    /**
     * GET /welcome
     *
     * S001 alias — design path. Canonical route is /.
     */
    public function welcome()
    {
        return view('welcome-premium');
    }

    /**
     * GET /auth/telegram
     *
     * S002 Telegram sign-in — alias for welcome.
     */
    public function telegram()
    {
        return view('telegram-premium');
    }
}
