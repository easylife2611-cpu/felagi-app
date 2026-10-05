<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Models\Boost;
use App\Models\Payment;
use App\Models\PaymentEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * BoostPaymentFulfillmentService — canonical webhook state machine.
 *
 * Contract (DFM-FDS-1.4 §202-204, §268):
 *   - Signature verified upstream (controller).
 *   - Dedupe by (provider, provider_event_id).
 *   - Match amount + currency + purpose against frozen Payment.
 *   - PENDING + success + match   → Payment CONFIRMED + Boost ACTIVE
 *   - PENDING + failed            → Payment FAILED
 *   - PENDING + success + mismatch → Payment REVIEW_REQUIRED
 *   - FAILED/CANCELLED + late success → Payment REVIEW_REQUIRED (no boost)
 *   - CONFIRMED + repeat          → DUPLICATE (no side effects)
 *   - Boost activation: PENDING → ACTIVE with starts_at/expires_at (server time).
 */
final class BoostPaymentFulfillmentService
{
    /**
     * @param array<string,mixed> $payload Raw webhook payload (already signature-verified)
     * @param string              $provider Provider key (e.g. 'chapa')
     * @param string              $rawBody  Raw request body (for digest)
     *
     * @return array{outcome:string,http:int,event_id?:string,reason?:string}
     */
    public function fulfill(array $payload, string $provider, string $rawBody): array
    {
        $txRef     = isset($payload['tx_ref']) ? (string) $payload['tx_ref'] : '';
        $status    = strtolower((string) ($payload['status']    ?? ''));
        $amountIn  = (string) ($payload['amount']   ?? '');
        $currency  = strtoupper((string) ($payload['currency'] ?? ''));
        $providerRef = isset($payload['reference']) ? (string) $payload['reference'] : null;

        if ($txRef === '') {
            return ['outcome' => 'REJECTED', 'http' => 400, 'reason' => 'missing_tx_ref'];
        }

        // ── 1. Payment lookup by frozen idempotency key ──
        $payment = Payment::where('idempotency_key', $txRef)->first();
        if (! $payment) {
            Log::warning('Webhook: unknown tx_ref', [
                'provider' => $provider,
                'tx_ref'   => $txRef,
                'status'   => $status,
            ]);
            return ['outcome' => 'REJECTED', 'http' => 404, 'reason' => 'unknown_tx_ref'];
        }

        // ── 2. Provider match (defense-in-depth) ──
        if ($payment->provider !== $provider) {
            Log::warning('Webhook: provider mismatch', [
                'payment_id'       => $payment->id,
                'payment_provider' => $payment->provider,
                'webhook_provider' => $provider,
            ]);
            return ['outcome' => 'REJECTED', 'http' => 404, 'reason' => 'provider_mismatch'];
        }

        // ── 3. Build event id + digest (idempotency) ──
        $eventId = $txRef . '|' . $status . '|' . ($providerRef ?? '');
        $digest  = hash('sha256', $rawBody);

        // ── 4. Dedupe ──
        $existing = PaymentEvent::where('provider', $provider)
            ->where('provider_event_id', $eventId)
            ->first();

        if ($existing && in_array($existing->processing_status, [
            PaymentEvent::STATUS_APPLIED,
            PaymentEvent::STATUS_DUPLICATE,
        ], true)) {
            return ['outcome' => 'DUPLICATE', 'http' => 200, 'event_id' => $existing->id];
        }

        // ── 5. Record (or refresh) event ──
        $event = $existing ?? PaymentEvent::create([
            'payment_id'         => $payment->id,
            'provider'           => $provider,
            'provider_event_id'  => $eventId,
            'payload_digest'     => $digest,
            'signature_valid'    => true,
            'event_type'         => 'chapa.' . ($status !== '' ? $status : 'unknown'),
            'processing_status'  => PaymentEvent::STATUS_RECEIVED,
            'sanitized_metadata' => [
                'status'   => $status,
                'amount'   => $amountIn,
                'currency' => $currency,
                'tx_ref'   => $txRef,
            ],
        ]);

        // ── 6. Late success after terminal failure → REVIEW_REQUIRED ──
        if (in_array($payment->status, [Payment::STATUS_FAILED, Payment::STATUS_CANCELLED], true)) {
            if ($status === 'success') {
                $payment->update([
                    'status'       => Payment::STATUS_REVIEW_REQUIRED,
                    'failure_code' => 'late_success_after_' . strtolower($payment->status),
                ]);
                $event->update(['processing_status' => PaymentEvent::STATUS_REVIEW_REQUIRED]);
                Log::warning('Webhook: late success after terminal', [
                    'payment_id' => $payment->id,
                    'prev_status'=> $payment->getOriginal('status'),
                ]);
                return ['outcome' => 'REVIEW_REQUIRED', 'http' => 200, 'event_id' => $event->id];
            }
            $event->update(['processing_status' => PaymentEvent::STATUS_DUPLICATE]);
            return ['outcome' => 'DUPLICATE', 'http' => 200, 'event_id' => $event->id];
        }

        // ── 7. Already CONFIRMED → idempotent ──
        if ($payment->status === Payment::STATUS_CONFIRMED) {
            $event->update(['processing_status' => PaymentEvent::STATUS_DUPLICATE]);
            return ['outcome' => 'DUPLICATE', 'http' => 200, 'event_id' => $event->id];
        }

        // ── 8. PENDING + failure event → FAILED ──
        if ($payment->status === Payment::STATUS_PENDING
            && in_array($status, ['failed', 'cancelled'], true)) {
            $payment->update([
                'status'       => Payment::STATUS_FAILED,
                'failed_at'    => now(),
                'failure_code' => substr($status, 0, 80),
            ]);
            $event->update(['processing_status' => PaymentEvent::STATUS_APPLIED]);
            return ['outcome' => 'APPLIED', 'http' => 200, 'event_id' => $event->id];
        }

        // ── 9. PENDING + success → verify + CONFIRM + activate ──
        if ($payment->status === Payment::STATUS_PENDING && $status === 'success') {
            $amountMatch   = $this->amountsEqual((string) $payment->amount, $amountIn);
            $currencyMatch = $currency === '' || strtoupper((string) $payment->currency) === $currency;

            if (! $amountMatch || ! $currencyMatch) {
                $payment->update([
                    'status'       => Payment::STATUS_REVIEW_REQUIRED,
                    'failure_code' => $amountMatch ? 'currency_mismatch' : 'amount_mismatch',
                ]);
                $event->update(['processing_status' => PaymentEvent::STATUS_REVIEW_REQUIRED]);
                Log::warning('Webhook: amount/currency mismatch', [
                    'payment_id'        => $payment->id,
                    'expected_amount'   => (string) $payment->amount,
                    'received_amount'   => $amountIn,
                    'expected_currency' => (string) $payment->currency,
                    'received_currency' => $currency,
                ]);
                return ['outcome' => 'REVIEW_REQUIRED', 'http' => 200, 'event_id' => $event->id];
            }

            try {
                DB::transaction(function () use ($payment, $providerRef, $event) {
                    $payment->update([
                        'status'             => Payment::STATUS_CONFIRMED,
                        'confirmed_at'       => now(),
                        'provider_reference' => $providerRef ?: $payment->provider_reference,
                    ]);

                    if ($payment->purpose === Payment::PURPOSE_BOOST) {
                        $boost = Boost::where('payment_id', $payment->id)->first();
                        if ($boost && $boost->status === Boost::STATUS_PENDING) {
                            $startsAt  = now();
                            $expiresAt = $startsAt->copy()->addDays((int) $boost->duration_days);
                            $boost->update([
                                'status'     => Boost::STATUS_ACTIVE,
                                'starts_at'  => $startsAt,
                                'expires_at' => $expiresAt,
                            ]);
                        }
                    }

                    $event->update(['processing_status' => PaymentEvent::STATUS_APPLIED]);
                });
            } catch (Throwable $e) {
                Log::error('Webhook: fulfillment transaction failed', [
                    'payment_id' => $payment->id,
                    'msg'        => $e->getMessage(),
                ]);
                $event->update(['processing_status' => PaymentEvent::STATUS_REVIEW_REQUIRED]);
                return ['outcome' => 'REVIEW_REQUIRED', 'http' => 200, 'event_id' => $event->id];
            }

            return ['outcome' => 'APPLIED', 'http' => 200, 'event_id' => $event->id];
        }

        // ── 10. Unrecognized combination → REVIEW_REQUIRED ──
        $event->update(['processing_status' => PaymentEvent::STATUS_REVIEW_REQUIRED]);
        return ['outcome' => 'REVIEW_REQUIRED', 'http' => 200, 'event_id' => $event->id];
    }

    private function amountsEqual(string $a, string $b): bool
    {
        return $this->toMinor($a) === $this->toMinor($b);
    }

    private function toMinor(string $v): int
    {
        return (int) round(((float) $v) * 100);
    }
}
