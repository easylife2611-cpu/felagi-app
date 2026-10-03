<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use App\Services\Payments\NullPaymentGateway;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentGatewayFactory;
use InvalidArgumentException;
use Tests\TestCase;

final class PaymentGatewayFactoryTest extends TestCase
{
    public function test_container_binding_resolves_to_null_driver(): void
    {
        $gw = app(PaymentGateway::class);
        $this->assertInstanceOf(NullPaymentGateway::class, $gw);
        $this->assertSame('null', $gw->name());
    }

    public function test_alias_payment_gateway_resolves(): void
    {
        $this->assertInstanceOf(NullPaymentGateway::class, app('payment.gateway'));
    }

    public function test_factory_makes_null_explicitly(): void
    {
        $factory = new PaymentGatewayFactory(['driver' => 'null']);
        $this->assertInstanceOf(NullPaymentGateway::class, $factory->make('null'));
    }

    public function test_factory_throws_on_unsupported_driver(): void
    {
        $factory = new PaymentGatewayFactory(['driver' => 'unknown']);
        $this->expectException(InvalidArgumentException::class);
        $factory->make('unknown');
    }
}
