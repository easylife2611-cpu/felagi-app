<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Auth\EmailVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Email Verification Controller — L304
 */
class EmailVerificationController extends Controller
{
    public function __construct(private EmailVerificationService $service) {}

    public function verify(string $token): RedirectResponse
    {
        $user = $this->service->verify($token);

        if (! $user) {
            return redirect('/?verify_error=1');
        }

        if (! Auth::guard('web')->check()) {
            Auth::guard('web')->login($user);
            request()->session()->regenerate();
        }

        return redirect('/browse');
    }

    public function resend(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($this->service->isVerified($user)) {
            return response()->json(['message' => 'Email already verified'], 409);
        }

        $locale = str_starts_with($request->header('Accept-Language', 'en'), 'am') ? 'am' : 'en';
        $this->service->send($user, $locale);

        $msg = $locale === 'am'
            ? 'የማረጋገጫ ኢሜል ተልኳል።'
            : 'Verification email sent.';

        return response()->json(['message' => $msg]);
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json(['data' => [
            'verified'    => $this->service->isVerified($user),
            'verified_at' => $user->email_verified_at,
        ]]);
    }
}
