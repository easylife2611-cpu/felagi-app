<?php

declare(strict_types=1);

namespace App\Services\Payments;

use InvalidArgumentException;

final class PaymentGatewayFactory
{
    public function __construct(private readonly array $config) {}

    public function make(?string $driver = null): PaymentGateway
    {
        $driver ??= $this->config['payment']['driver'] ?? $this->config['driver'] ?? 'null';

        return match ($driver) {
            'null'  => new NullPaymentGateway(),
            'chapa' => new ChapaPaymentGateway(
                secretKey: $this->config['chapa']['secret_key']
                    ?? throw new InvalidArgumentException('CHAPA_SECRET_KEY not configured'),
                baseUrl:  $this->config['chapa']['base_url'] ?? 'https://api.chapa.co/v1',
                currency: $this->config['chapa']['currency'] ?? 'ETB',
            ),
            default => throw new InvalidArgumentException(
                "Unsupported payment driver [{$driver}]. Supported: null, chapa."
            ),
        };
    }
}
