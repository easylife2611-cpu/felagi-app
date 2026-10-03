<?php

declare(strict_types=1);

namespace App\Services\Payments;

final readonly class PaymentResult
{
    public function __construct(
        public bool $ok,
        public string $transactionId,
        public int $amountMinor,
        public string $currency,
        public string $status,          // succeeded | failed | refunded
        public array $raw = [],
        public ?string $error = null,
    ) {
    }

    public function isOk(): bool
    {
        return $this->ok && $this->status === 'succeeded';
    }

    public function toArray(): array
    {
        return [
            'ok'             => $this->ok,
            'transaction_id' => $this->transactionId,
            'amount_minor'   => $this->amountMinor,
            'currency'       => $this->currency,
            'status'         => $this->status,
            'error'          => $this->error,
        ];
    }
}
