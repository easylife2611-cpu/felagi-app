<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Auth\EmailOtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Email Authentication — OTP-based login (Amharic + English)
 */
class EmailAuthController extends Controller
{
    public function __construct(private EmailOtpService $otp) {}

    public function requestCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $this->otp->send($data['email'], $request);

        $locale = $this->locale($request);
        $message = $locale === 'am'
            ? 'የመግቢያ ኮድ ወደ ኢሜልዎ ተልኳል።'
            : 'Login code has been sent to your email.';

        return response()->json([
            'message' => $message,
            'expires_in_minutes' => 10,
        ]);
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'code'  => 'required|string|size:6',
        ]);

        $user = $this->otp->verify($data['email'], $data['code']);

        if (! $user) {
            $locale = $this->locale($request);
            $message = $locale === 'am'
                ? 'ኮዱ ልክ አይደለም ወይም ጊዜው አልፎበታል።'
                : 'Invalid or expired code.';

            return response()->json(['message' => $message], 401);
        }

        Auth::login($user);
        $token = $user->createToken('email-otp')->plainTextToken;

        $locale = $this->locale($request);
        $message = $locale === 'am'
            ? 'በተሳካ ሁኔታ ገብተዋል።'
            : 'Logged in successfully.';

        return response()->json([
            'message' => $message,
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    private function locale(Request $request): string
    {
        $header = $request->header('Accept-Language', 'en');
        return str_starts_with($header, 'am') ? 'am' : 'en';
    }
}
