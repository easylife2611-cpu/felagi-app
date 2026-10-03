<?php

declare(strict_types=1);

namespace App\Services\Payments;

/**
 * Deterministic in-process gateway for tests and dev.
 *
 * No network. No credentials. Idempotent by key.
 * Transaction IDs are `null_<sha1(idempotencyKey)[:16]>`.
 */
final class NullPaymentGateway implements PaymentGateway
{
    /** @var array<string, PaymentResult> keyed by transaction_id */
    private array $store = [];

    /** @var array<string, string> idempotency_key → transaction_id */
    private array $idem = [];

    public function name(): string
    {
        return 'null';
    }

    public function charge(PaymentRequest $request): PaymentResult
    {
        // Idempotency: same key returns the original result
        if (isset($this->idem[$request->idempotencyKey])) {
            return $this->store[$this->idem[$request->idempotencyKey]];
        }

        $txnId = 'null_' . substr(sha1($request->idempotencyKey), 0, 16);

        $result = new PaymentResult(
            ok: true,
            transactionId: $txnId,
            amountMinor: $request->amountMinor,
            currency: $request->currency,
            status: 'succeeded',
            raw: [
                'driver'    => 'null',
                'metadata'  => $request->metadata,
                'idem_key'  => $request->idempotencyKey,
            ],
        );

        $this->store[$txnId] = $result;
        $this->idem[$request->idempotencyKey] = $txnId;

        return $result;
    }

    public function refund(string $transactionId, ?int $amountMinor = null): PaymentResult
    {
        $original = $this->store[$transactionId] ?? null;

        if ($original === null) {
            return new PaymentResult(
                ok: false,
                transactionId: $transactionId,
                amountMinor: 0,
                currency: 'UNKNOWN',
                status: 'failed',
                error: 'transaction_not_found',
            );
        }

        $refunded = new PaymentResult(
            ok: true,
            transactionId: $transactionId,
            amountMinor: $amountMinor ?? $original->amountMinor,
            currency: $original->currency,
            status: 'refunded',
            raw: ['driver' => 'null', 'refund_of' => $transactionId],
        );

        $this->store[$transactionId] = $refunded;

        return $refunded;
    }

    public function find(string $transactionId): ?PaymentResult
    {
        return $this->store[$transactionId] ?? null;
    }
}
