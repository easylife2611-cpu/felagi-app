<?php

declare(strict_types=1);

namespace App\Services\Payments;

use InvalidArgumentException;

final class PaymentGatewayFactory
{
    public function __construct(
        private readonly array $config,
    ) {
    }

    public function make(?string $driver = null): PaymentGateway
    {
        $driver ??= $this->config['driver'] ?? 'null';

        return match ($driver) {
            'null'   => new NullPaymentGateway(),
            default  => throw new InvalidArgumentException(
                "Unsupported payment driver [{$driver}]. Supported: null."
            ),
        };
    }
}
