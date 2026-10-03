<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use App\Services\Payments\NullPaymentGateway;
use App\Services\Payments\PaymentRequest;
use Tests\TestCase;

final class NullPaymentGatewayTest extends TestCase
{
    private function makeRequest(string $key = 'idem-1', int $amount = 4900): PaymentRequest
    {
        return new PaymentRequest(
            amountMinor: $amount,
            currency: 'ETB',
            idempotencyKey: $key,
            metadata: ['need_id' => 'need-abc'],
        );
    }

    public function test_name_is_null(): void
    {
        $this->assertSame('null', (new NullPaymentGateway())->name());
    }

    public function test_charge_returns_succeeded(): void
    {
        $gw = new NullPaymentGateway();
        $result = $gw->charge($this->makeRequest());

        $this->assertTrue($result->isOk());
        $this->assertSame('succeeded', $result->status);
        $this->assertSame(4900, $result->amountMinor);
        $this->assertSame('ETB', $result->currency);
        $this->assertStringStartsWith('null_', $result->transactionId);
    }

    public function test_charge_is_idempotent_by_key(): void
    {
        $gw = new NullPaymentGateway();
        $a = $gw->charge($this->makeRequest('idem-xyz'));
        $b = $gw->charge($this->makeRequest('idem-xyz'));

        $this->assertSame($a->transactionId, $b->transactionId);
    }

    public function test_different_keys_produce_different_transactions(): void
    {
        $gw = new NullPaymentGateway();
        $a = $gw->charge($this->makeRequest('idem-a'));
        $b = $gw->charge($this->makeRequest('idem-b'));

        $this->assertNotSame($a->transactionId, $b->transactionId);
    }

    public function test_find_returns_stored_transaction(): void
    {
        $gw = new NullPaymentGateway();
        $result = $gw->charge($this->makeRequest());
        $found = $gw->find($result->transactionId);

        $this->assertNotNull($found);
        $this->assertSame($result->transactionId, $found->transactionId);
    }

    public function test_find_returns_null_for_unknown(): void
    {
        $gw = new NullPaymentGateway();
        $this->assertNull($gw->find('null_does_not_exist'));
    }

    public function test_refund_succeeds_for_known_transaction(): void
    {
        $gw = new NullPaymentGateway();
        $charge = $gw->charge($this->makeRequest());
        $refund = $gw->refund($charge->transactionId);

        $this->assertTrue($refund->ok);
        $this->assertSame('refunded', $refund->status);
        $this->assertSame($charge->amountMinor, $refund->amountMinor);
    }

    public function test_refund_fails_for_unknown_transaction(): void
    {
        $gw = new NullPaymentGateway();
        $refund = $gw->refund('null_missing');

        $this->assertFalse($refund->ok);
        $this->assertSame('failed', $refund->status);
        $this->assertSame('transaction_not_found', $refund->error);
    }
}
