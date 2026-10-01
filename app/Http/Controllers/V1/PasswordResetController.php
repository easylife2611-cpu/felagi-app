<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Password Reset Controller — L304
 */
class PasswordResetController extends Controller
{
    public function __construct(private PasswordResetService $service) {}

    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate(['email' => 'required|email|max:255']);

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $locale = str_starts_with($request->header('Accept-Language', 'en'), 'am') ? 'am' : 'en';
            $this->service->send($user, $locale);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'If the email exists, a reset link was sent.']);
        }

        return back()->with('status', 'If the email exists, a reset link was sent.');
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'email'    => 'required|email|max:255',
            'token'    => 'required|string|max:128',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $this->service->reset($data['email'], $data['token'], $data['password']);

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid or expired reset link'], 422);
            }
            return back()->withErrors(['email' => 'Invalid or expired reset link']);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Password reset successful']);
        }

        return redirect('/')->with('status', 'Password reset successful. Please sign in.');
    }
}
