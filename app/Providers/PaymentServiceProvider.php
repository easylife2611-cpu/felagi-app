<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentGatewayFactory;
use Illuminate\Support\ServiceProvider;

final class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/payments.php',
            'payments'
        );

        $this->app->singleton(PaymentGatewayFactory::class, function ($app): PaymentGatewayFactory {
            return new PaymentGatewayFactory($app['config']->get('payments', []));
        });

        $this->app->singleton(PaymentGateway::class, function ($app): PaymentGateway {
            return $app->make(PaymentGatewayFactory::class)->make();
        });

        $this->app->alias(PaymentGateway::class, 'payment.gateway');
    }
}
