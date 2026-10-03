<?php

declare(strict_types=1);

namespace App\Services\Payments;

interface PaymentGateway
{
    public function charge(PaymentRequest $request): PaymentResult;

    public function refund(string $transactionId, ?int $amountMinor = null): PaymentResult;

    public function find(string $transactionId): ?PaymentResult;

    public function name(): string;
}
