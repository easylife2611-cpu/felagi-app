<?php

namespace Tests\Feature\Payments;

use App\Models\Boost;
use App\Models\BoostPackage;
use App\Models\Category;
use App\Models\Need;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * ChapaWebhookTest — canonical webhook contract tests.
 *
 * Contract: DFM-FDS-1.4 §202-204, §268 + Monetization §Failure responses.
 * Uses CHAPA_WEBHOOK_SECRET=test_webhook_secret from phpunit.xml.
 */
class ChapaWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'test_webhook_secret';

    private function makeNeed(User $owner): Need
    {
        $cat = Category::factory()->create([
            'name_en' => 'L', 'name_am' => 'ሎ',
            'slug' => 'c' . uniqid(), 'active' => true,
        ]);

        return Need::factory()->create([
            'category_id'  => $cat->id,
            'requester_id' => $owner->id,
            'title'        => 'Webhook Test',
            'description'  => 'Description',
            'status'       => Need::STATUS_OPEN,
        ]);
    }

    /**
     * @return array{boost:Boost,payment:Payment,txRef:string}
     */
    private function makePendingPaymentWithBoost(User $owner): array
    {
        $need = $this->makeNeed($owner);
        $pkg  = BoostPackage::factory()->create(['active' => true, 'price' => 100]);

        $txRef = (string) Str::uuid();

        $payment = Payment::create([
            'payer_id'        => $owner->id,
            'need_id'         => $need->id,
            'purpose'         => Payment::PURPOSE_BOOST,
            'provider'        => 'chapa',
            'idempotency_key' => $txRef,
            'amount'          => '100.00',
            'currency'        => 'ETB',
            'status'          => Payment::STATUS_PENDING,
        ]);

        $boost = Boost::create([
            'need_id'        => $need->id,
            'requester_id'   => $owner->id,
            'package_id'     => $pkg->id,
            'payment_id'     => $payment->id,
            'price_snapshot' => $pkg->price,
            'currency'       => 'ETB',
            'duration_days'  => 7,
            'status'         => Boost::STATUS_PENDING,
        ]);

        return ['boost' => $boost, 'payment' => $payment, 'txRef' => $txRef];
    }

    /**
     * Send a signed webhook request.
     */
    private function postWebhook(array $payload, ?string $signature = null, string $provider = 'chapa')
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $sig  = $signature ?? hash_hmac('sha256', $body, self::SECRET);

        return $this->call(
            'POST',
            '/api/v1/payments/webhooks/' . $provider,
            [], [], [],
            [
                'CONTENT_TYPE'         => 'application/json',
                'HTTP_ACCEPT'          => 'application/json',
                'HTTP_CHAPA_SIGNATURE' => $sig,
            ],
            $body
        );
    }

    // ─────────────────────────────────────────
    // [1] Signature / provider / payload guards
    // ─────────────────────────────────────────

    public function test_invalid_signature_returns_401(): void
    {
        $res = $this->postWebhook(
            ['tx_ref' => 'x', 'status' => 'success', 'amount' => '100.00'],
            'wrong-signature'
        );

        $res->assertStatus(401);
    }

    public function test_unsupported_provider_returns_404(): void
    {
        $res = $this->postWebhook(
            ['tx_ref' => 'x', 'status' => 'success'],
            null,
            'stripe'
        );

        $res->assertStatus(404);
    }

    public function test_missing_tx_ref_returns_400(): void
    {
        $res = $this->postWebhook([
            'status' => 'success',
            'amount' => '100.00',
        ]);

        $res->assertStatus(400);
    }

    public function test_unknown_tx_ref_returns_404(): void
    {
        $res = $this->postWebhook([
            'tx_ref'   => (string) Str::uuid(),
            'status'   => 'success',
            'amount'   => '100.00',
            'currency' => 'ETB',
        ]);

        $res->assertStatus(404);
    }

    public function test_provider_mismatch_returns_404(): void
    {
        $owner = User::factory()->create();
        $ctx   = $this->makePendingPaymentWithBoost($owner);

        // Payment created with 'chapa'; flip to 'telebirr'
        $ctx['payment']->update(['provider' => 'telebirr']);

        $res = $this->postWebhook([
            'tx_ref'   => $ctx['txRef'],
            'status'   => 'success',
            'amount'   => '100.00',
            'currency' => 'ETB',
        ]);

        $res->assertStatus(404);
    }

    // ─────────────────────────────────────────
    // [2] Happy path — PENDING + success → CONFIRMED + ACTIVE
    // ─────────────────────────────────────────

    public function test_pending_success_confirms_payment_and_activates_boost(): void
    {
        $owner = User::factory()->create();
        $ctx   = $this->makePendingPaymentWithBoost($owner);

        $res = $this->postWebhook([
            'tx_ref'    => $ctx['txRef'],
            'status'    => 'success',
            'amount'    => '100.00',
            'currency'  => 'ETB',
            'reference' => 'chapa-ref-001',
        ]);

        $res->assertStatus(200);
        $res->assertJson(['message' => 'APPLIED']);

        $this->assertSame(
            Payment::STATUS_CONFIRMED,
            $ctx['payment']->fresh()->status
        );
        $this->assertNotNull($ctx['payment']->fresh()->confirmed_at);
        $this->assertSame(
            'chapa-ref-001',
            $ctx['payment']->fresh()->provider_reference
        );

        $boost = $ctx['boost']->fresh();
        $this->assertSame(Boost::STATUS_ACTIVE, $boost->status);
        $this->assertNotNull($boost->starts_at);
        $this->assertNotNull($boost->expires_at);
        $this->assertTrue($boost->expires_at->greaterThan($boost->starts_at));
    }

    // ─────────────────────────────────────────
    // [3] Failure path
    // ─────────────────────────────────────────

    public function test_pending_failed_marks_payment_failed(): void
    {
        $owner = User::factory()->create();
        $ctx   = $this->makePendingPaymentWithBoost($owner);

        $res = $this->postWebhook([
            'tx_ref'   => $ctx['txRef'],
            'status'   => 'failed',
            'amount'   => '100.00',
            'currency' => 'ETB',
        ]);

        $res->assertStatus(200);
        $this->assertSame(
            Payment::STATUS_FAILED,
            $ctx['payment']->fresh()->status
        );
        $this->assertSame(
            Boost::STATUS_PENDING,
            $ctx['boost']->fresh()->status
        );
    }

    // ─────────────────────────────────────────
    // [4] Idempotency — duplicate webhook
    // ─────────────────────────────────────────

    public function test_duplicate_webhook_is_idempotent(): void
    {
        $owner = User::factory()->create();
        $ctx   = $this->makePendingPaymentWithBoost($owner);

        $payload = [
            'tx_ref'    => $ctx['txRef'],
            'status'    => 'success',
            'amount'    => '100.00',
            'currency'  => 'ETB',
            'reference' => 'chapa-ref-002',
        ];

        $res1 = $this->postWebhook($payload);
        $res1->assertStatus(200)->assertJson(['message' => 'APPLIED']);

        $res2 = $this->postWebhook($payload);
        $res2->assertStatus(200)->assertJson(['message' => 'DUPLICATE']);

        // State unchanged
        $this->assertSame(
            Payment::STATUS_CONFIRMED,
            $ctx['payment']->fresh()->status
        );
        $this->assertSame(
            Boost::STATUS_ACTIVE,
            $ctx['boost']->fresh()->status
        );

        // Exactly ONE PaymentEvent for this payment
        $this->assertSame(
            1,
            PaymentEvent::where('payment_id', $ctx['payment']->id)->count()
        );
    }

    // ─────────────────────────────────────────
    // [5] Mismatch → REVIEW_REQUIRED
    // ─────────────────────────────────────────

    public function test_amount_mismatch_marks_review_required(): void
    {
        $owner = User::factory()->create();
        $ctx   = $this->makePendingPaymentWithBoost($owner);

        $res = $this->postWebhook([
            'tx_ref'   => $ctx['txRef'],
            'status'   => 'success',
            'amount'   => '999.99',   // ≠ 100.00
            'currency' => 'ETB',
        ]);

        $res->assertStatus(200);
        $this->assertSame(
            Payment::STATUS_REVIEW_REQUIRED,
            $ctx['payment']->fresh()->status
        );
        $this->assertSame(
            Boost::STATUS_PENDING,
            $ctx['boost']->fresh()->status
        );
    }

    public function test_currency_mismatch_marks_review_required(): void
    {
        $owner = User::factory()->create();
        $ctx   = $this->makePendingPaymentWithBoost($owner);

        $res = $this->postWebhook([
            'tx_ref'   => $ctx['txRef'],
            'status'   => 'success',
            'amount'   => '100.00',
            'currency' => 'USD',      // ≠ ETB
        ]);

        $res->assertStatus(200);
        $this->assertSame(
            Payment::STATUS_REVIEW_REQUIRED,
            $ctx['payment']->fresh()->status
        );
    }

    // ─────────────────────────────────────────
    // [6] Late success after FAILED — no boost
    // ─────────────────────────────────────────

    public function test_late_success_after_failed_marks_review_required_no_boost(): void
    {
        $owner = User::factory()->create();
        $ctx   = $this->makePendingPaymentWithBoost($owner);

        // Step 1 — mark FAILED
        $this->postWebhook([
            'tx_ref'   => $ctx['txRef'],
            'status'   => 'failed',
            'amount'   => '100.00',
            'currency' => 'ETB',
        ]);
        $this->assertSame(
            Payment::STATUS_FAILED,
            $ctx['payment']->fresh()->status
        );

        // Step 2 — late success arrives
        $res = $this->postWebhook([
            'tx_ref'    => $ctx['txRef'],
            'status'    => 'success',
            'amount'    => '100.00',
            'currency'  => 'ETB',
            'reference' => 'late-ref',
        ]);

        $res->assertStatus(200);
        $res->assertJson(['message' => 'REVIEW_REQUIRED']);

        $this->assertSame(
            Payment::STATUS_REVIEW_REQUIRED,
            $ctx['payment']->fresh()->status
        );
        // 🔴 CRITICAL — Boost stays PENDING (no auto-activation)
        $this->assertSame(
            Boost::STATUS_PENDING,
            $ctx['boost']->fresh()->status
        );
    }
}
