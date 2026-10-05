<?php

declare(strict_types=1);

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class ChapaPaymentGateway implements PaymentGateway
{
    public function __construct(
        private readonly string $secretKey,
        private readonly string $baseUrl = 'https://api.chapa.co/v1',
        private readonly string $currency = 'ETB',
    ) {}

    public function name(): string { return 'chapa'; }

    public function charge(PaymentRequest $request): PaymentResult
    {
        $payload = [
            'amount'       => (string) ($request->amountMinor / 100),
            'currency'     => $request->currency ?: $this->currency,
            'email'        => $request->metadata['email']      ?? 'customer@felagi.et',
            'first_name'   => $request->metadata['first_name'] ?? 'Felagi',
            'last_name'    => $request->metadata['last_name']  ?? 'Customer',
            'phone_number' => $request->metadata['phone']      ?? '',
            'tx_ref'       => $request->idempotencyKey,
            'callback_url' => $request->metadata['callback_url'] ?? url('/api/v1/payments/chapa/webhook'),
            'return_url'   => $request->metadata['return_url']   ?? url('/payments/return'),
            'customization'=> [
                'title'       => $request->metadata['title']       ?? 'Felagi Payment',
                'description' => $request->metadata['description'] ?? 'Payment for Felagi',
            ],
        ];

        try {
            $response = Http::withToken($this->secretKey)->timeout(30)->acceptJson()
                ->post("{$this->baseUrl}/transaction/initialize", $payload);
            $body = $response->json() ?? [];

            if (! $response->successful() || ($body['status'] ?? '') !== 'success') {
                Log::error('Chapa charge failed', [
                    'status' => $response->status(),
                    'body'   => $body,
                    'raw'    => substr($response->body(), 0, 500),
                ]);

                // ─── Safe error extraction ───
                $msg = $body['message'] ?? null;
                if (is_array($msg)) {
                    $msg = json_encode($msg, JSON_UNESCAPED_UNICODE) ?: 'chapa_error_array';
                } elseif (! is_string($msg)) {
                    $msg = 'HTTP ' . $response->status() . ': chapa_initialization_failed';
                }

                return new PaymentResult(false, $request->idempotencyKey, $request->amountMinor,
                    $request->currency ?: $this->currency, 'failed', $body, $msg);
            }

            return new PaymentResult(true, $request->idempotencyKey, $request->amountMinor,
                $request->currency ?: $this->currency, 'succeeded', [
                    'driver' => 'chapa',
                    'checkout_url' => $body['data']['checkout_url'] ?? null,
                    'tx_ref' => $body['data']['tx_ref'] ?? $request->idempotencyKey,
                    'chapa_response' => $body,
                ]);
        } catch (\Throwable $e) {
            Log::error('Chapa charge exception', ['message' => $e->getMessage()]);
            return new PaymentResult(false, $request->idempotencyKey, $request->amountMinor,
                $request->currency ?: $this->currency, 'failed', [],
                'chapa_exception: ' . $e->getMessage());
        }
    }

    public function refund(string $transactionId, ?int $amountMinor = null): PaymentResult
    {
        $payload = $amountMinor !== null ? ['amount' => (string) ($amountMinor / 100)] : [];
        try {
            $response = Http::withToken($this->secretKey)->asForm()->timeout(30)
                ->post("{$this->baseUrl}/refund/{$transactionId}", $payload);
            $body = $response->json() ?? [];
            return new PaymentResult($response->successful(), $transactionId,
                $amountMinor ?? 0, $this->currency,
                $response->successful() ? 'refunded' : 'failed',
                ['driver' => 'chapa', 'chapa_response' => $body],
                $response->successful() ? null : ($body['message'] ?? 'chapa_refund_failed'));
        } catch (\Throwable $e) {
            return new PaymentResult(false, $transactionId, $amountMinor ?? 0,
                $this->currency, 'failed', [], 'chapa_refund_exception: ' . $e->getMessage());
        }
    }

    public function find(string $transactionId): ?PaymentResult
    {
        try {
            $response = Http::withToken($this->secretKey)->timeout(30)
                ->get("{$this->baseUrl}/transaction/verify/{$transactionId}");
            $body = $response->json() ?? [];
            if (! $response->successful()) return null;
            $status = $body['data']['status'] ?? 'unknown';
            return new PaymentResult($status === 'success', $transactionId,
                (int) round(((float) ($body['data']['amount'] ?? 0)) * 100),
                $body['data']['currency'] ?? $this->currency,
                $status === 'success' ? 'succeeded' : 'failed',
                ['driver' => 'chapa', 'chapa_response' => $body]);
        } catch (\Throwable $e) { return null; }
    }

    public static function verifyWebhook(string $rawBody, string $signature, string $secret): bool
    {
        return hash_equals(hash_hmac('sha256', $rawBody, $secret), $signature);
    }
}
