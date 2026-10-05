<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\Boost;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * BoostPurchaseService — initiates Chapa (or Null) payment for a Boost.
 *
 * Flow:
 *   1. Guard: boost must be PENDING
 *   2. Idempotency: reuse existing Payment if CONFIRMED
 *   3. Create Payment(PENDING) if not exists
 *   4. Link boost → payment
 *   5. Call gateway->charge() with sanitized metadata
 *   6. Update Payment.provider_reference or FAILED
 *   7. Audit via PaymentEvent
 *
 * Idempotency key = Boost.id (UUID, globally unique).
 */
final class BoostPurchaseService
{
    public function __construct(
        private readonly PaymentGateway $gateway,
    ) {
    }

    public function initiate(Boost $boost, ?string $idempotencyKey = null): PaymentResult
    {
        // ── 1. Guard ──
        if ($boost->status !== Boost::STATUS_PENDING) {
            return $this->fail($boost, 'boost_not_pending');
        }

        // ── 2. Idempotency check ──
        $key = $idempotencyKey ?? $boost->id;
        $existing = Payment::where('payer_id', $boost->requester_id)
            ->where('idempotency_key', $key)
            ->first();
        if ($existing && $existing->status === Payment::STATUS_CONFIRMED) {
            return new PaymentResult(
                ok: true,
                transactionId: (string) $existing->provider_reference,
                amountMinor: (int) round(((float) $existing->amount) * 100),
                currency: $existing->currency,
                status: 'succeeded',
                raw: ['existing' => true, 'payment_id' => $existing->id],
            );
        }

        // ── 3. Create or reuse Payment ──
        $payment = $existing ?? Payment::create([
            'payer_id'        => $boost->requester_id,
            'need_id'         => $boost->need_id,
            'purpose'         => Payment::PURPOSE_BOOST,
            'provider'        => $this->gateway->name(),
            'idempotency_key' => $key,
            'amount'          => $boost->price_snapshot,
            'currency'        => $boost->currency,
            'status'          => Payment::STATUS_PENDING,
        ]);

        // ── 4. Link boost → payment ──
        if ($boost->payment_id !== $payment->id) {
            $boost->update(['payment_id' => $payment->id]);
        }

        // ── 5. Build metadata (Chapa-safe) ──
        $user = $boost->requester;
        $email = filter_var((string) ($user?->email ?? ''), FILTER_VALIDATE_EMAIL)
            ? (string) $user->email
            : 'customer@felagi.et';

        $title = 'Boost ' . $boost->duration_days . 'd';
        if (strlen($title) > 16) {
            $title = substr($title, 0, 16);
        }

        $amountMinor = (int) round(((float) $boost->price_snapshot) * 100);

        $request = new PaymentRequest(
            amountMinor: $amountMinor,
            currency: $boost->currency,
            idempotencyKey: $boost->id,
            metadata: [
                'email'        => $email,
                'first_name'   => (string) ($user?->name ?? 'Felagi'),
                'last_name'    => 'Customer',
                'title'        => $title,
                'description'  => 'Boost ' . substr($boost->need_id, 0, 8),
                'callback_url' => url('/api/v1/payments/chapa/webhook'),
                'return_url'   => url('/needs/' . $boost->need_id . '/boost'),
                'boost_id'     => $boost->id,
                'need_id'      => $boost->need_id,
            ],
        );

        // ── 6. Charge ──
        try {
            $result = $this->gateway->charge($request);
        } catch (Throwable $e) {
            Log::error('BoostPurchaseService charge exception', [
                'boost_id' => $boost->id,
                'msg'      => $e->getMessage(),
            ]);
            $payment->update([
                'status'       => Payment::STATUS_FAILED,
                'failed_at'    => now(),
                'failure_code' => 'charge_exception',
            ]);
            throw $e;
        }

        if ($result->isOk()) {
            $payment->update([
                'provider_reference' => $result->transactionId,
            ]);
        } else {
            $payment->update([
                'status'       => Payment::STATUS_FAILED,
                'failed_at'    => now(),
                'failure_code' => substr((string) ($result->error ?? 'charge_failed'), 0, 80),
            ]);
        }

        // ── 7. Audit event ──
        try {
            PaymentEvent::create([
                'payment_id'         => $payment->id,
                'provider'           => $this->gateway->name(),
                'provider_event_id'  => 'init_' . $key,
                'payload_digest'     => hash('sha256', json_encode($request->metadata) ?: ''),
                'signature_valid'    => true,
                'event_type'         => 'initiate',
                'processing_status'  => $result->isOk()
                    ? PaymentEvent::STATUS_RECEIVED
                    : PaymentEvent::STATUS_REJECTED,
                'sanitized_metadata' => [
                    'amount_minor' => $amountMinor,
                    'currency'     => $boost->currency,
                ],
            ]);
        } catch (Throwable $e) {
            Log::warning('PaymentEvent record failed', [
                'payment_id' => $payment->id,
                'msg'        => $e->getMessage(),
            ]);
        }

        return $result;
    }

    private function fail(Boost $boost, string $code): PaymentResult
    {
        return new PaymentResult(
            ok: false,
            transactionId: '',
            amountMinor: (int) round(((float) $boost->price_snapshot) * 100),
            currency: $boost->currency,
            status: 'failed',
            error: $code,
        );
    }
}
