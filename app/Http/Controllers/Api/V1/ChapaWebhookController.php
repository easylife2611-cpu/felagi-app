<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Services\Payments\BoostPaymentFulfillmentService;
use App\Services\Payments\ChapaPaymentGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ChapaWebhookController — thin receiver.
 *
 * Contract (DFM §268):
 *   - No user token. Signature verified in this controller.
 *   - Delegate state machine to BoostPaymentFulfillmentService.
 *   - Response: 200 acknowledged | 401 invalid signature | 400 missing | 404 unknown.
 */
final class ChapaWebhookController extends BaseApiController
{
    public function handle(Request $request, string $provider = 'chapa'): JsonResponse
    {
        // 0. Provider guard (canonical 1.3 — only chapa wired)
        if ($provider !== 'chapa') {
            Log::warning('Unsupported webhook provider', [
                'provider' => $provider,
                'ip'       => $request->ip(),
            ]);
            return response()->json(['message' => 'Unsupported provider'], 404);
        }

        // 1. Signature secret is mandatory (fail closed)
        $secret = (string) config('services.chapa.webhook_secret');
        if ($secret === '') {
            Log::error('Chapa webhook: webhook_secret not configured — refusing');
            return response()->json(['message' => 'Server misconfigured'], 500);
        }

        // 2. Verify HMAC signature
        $rawBody   = $request->getContent();
        $signature = (string) $request->header('Chapa-Signature', '');

        if (! ChapaPaymentGateway::verifyWebhook($rawBody, $signature, $secret)) {
            Log::warning('Chapa webhook: invalid signature', [
                'ip'        => $request->ip(),
                'signature' => substr($signature, 0, 20),
            ]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // 3. Delegate to canonical state machine
        $payload = (array) $request->json()->all();

        $result = app(BoostPaymentFulfillmentService::class)
            ->fulfill($payload, $provider, $rawBody);

        $http = (int) ($result['http'] ?? 200);

        return response()->json([
            'message'  => $result['outcome'] ?? 'ok',
            'event_id' => $result['event_id'] ?? null,
            'reason'   => $result['reason']  ?? null,
        ], $http);
    }
}
