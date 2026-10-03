<?php

declare(strict_types=1);

namespace App\Services\Payments;

final readonly class PaymentRequest
{
    public function __construct(
        public int $amountMinor,          // e.g. 4900 = 49.00 ETB
        public string $currency,
        public string $idempotencyKey,
        public array $metadata = [],
    ) {
    }
}
