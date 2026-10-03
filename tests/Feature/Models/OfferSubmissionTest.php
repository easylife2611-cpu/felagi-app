<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Need;
use App\Models\OfferSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * L347-G — OfferSubmission model unit tests (S023 spec).
 */
final class OfferSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_uuid_primary_key(): void
    {
        $s = OfferSubmission::factory()->create();
        $this->assertTrue(Str::isUuid($s->id));
    }

    public function test_state_constants_match_s023_spec(): void
    {
        $this->assertSame('free',                OfferSubmission::STATE_FREE);
        $this->assertSame('payment-required',    OfferSubmission::STATE_PAYMENT_REQUIRED);
        $this->assertSame('pending',             OfferSubmission::STATE_PENDING);
        $this->assertSame('payment-verified',    OfferSubmission::STATE_PAYMENT_VERIFIED);
        $this->assertSame('submission-recovery', OfferSubmission::STATE_SUBMISSION_RECOVERY);
        $this->assertSame('submitted',           OfferSubmission::STATE_SUBMITTED);
        $this->assertSame('refund-pending',      OfferSubmission::STATE_REFUND_PENDING);
        $this->assertSame('failed',              OfferSubmission::STATE_FAILED);
        $this->assertSame('unknown',             OfferSubmission::STATE_UNKNOWN);
    }

    public function test_default_state_is_free(): void
    {
        $s = OfferSubmission::factory()->create();
        $this->assertSame(OfferSubmission::STATE_FREE, $s->fresh()->state);
    }

    public function test_default_amount_minor_is_zero(): void
    {
        $s = OfferSubmission::factory()->create();
        $this->assertSame(0, $s->fresh()->amount_minor);
    }

    public function test_default_currency_is_etb(): void
    {
        $s = OfferSubmission::factory()->create();
        $this->assertSame('ETB', $s->fresh()->currency);
    }

    public function test_draft_version_casts_integer(): void
    {
        $s = OfferSubmission::factory()->create(['draft_version' => 3]);
        $this->assertSame(3, $s->fresh()->draft_version);
    }

    public function test_amount_minor_casts_integer(): void
    {
        $s = OfferSubmission::factory()->create(['amount_minor' => 5000]);
        $this->assertSame(5000, $s->fresh()->amount_minor);
    }

    public function test_unlocked_at_casts_datetime(): void
    {
        $s = OfferSubmission::factory()->create(['unlocked_at' => '2026-12-31 12:00:00']);
        $this->assertInstanceOf(Carbon::class, $s->fresh()->unlocked_at);
    }

    public function test_expires_at_casts_datetime(): void
    {
        $s = OfferSubmission::factory()->create(['expires_at' => '2026-12-31 12:00:00']);
        $this->assertInstanceOf(Carbon::class, $s->fresh()->expires_at);
    }

    public function test_belongs_to_need(): void
    {
        $n = Need::factory()->create();
        $s = OfferSubmission::factory()->create(['need_id' => $n->id]);
        $this->assertSame($n->id, $s->fresh()->need->id);
    }

    public function test_belongs_to_provider(): void
    {
        $u = User::factory()->create();
        $s = OfferSubmission::factory()->create(['provider_id' => $u->id]);
        $this->assertSame($u->id, $s->fresh()->provider->id);
    }

    public function test_offer_relation_nullable_by_default(): void
    {
        $s = OfferSubmission::factory()->create();
        $this->assertNull($s->fresh()->offer);
    }

    public function test_payment_relation_nullable_by_default(): void
    {
        $s = OfferSubmission::factory()->create();
        $this->assertNull($s->fresh()->payment);
    }

    public function test_unique_constraint_on_provider_and_idempotency_key(): void
    {
        $u = User::factory()->create();
        OfferSubmission::factory()->create(['provider_id' => $u->id, 'idempotency_key' => 'idem-fixed-123']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        OfferSubmission::factory()->create(['provider_id' => $u->id, 'idempotency_key' => 'idem-fixed-123']);
    }

    public function test_null_idempotency_key_allows_multiple(): void
    {
        $u = User::factory()->create();
        OfferSubmission::factory()->create(['provider_id' => $u->id, 'idempotency_key' => null]);
        OfferSubmission::factory()->create(['provider_id' => $u->id, 'idempotency_key' => null]);

        $this->assertSame(2, OfferSubmission::where('provider_id', $u->id)->count());
    }

    public function test_scope_pending_includes_in_flight_states(): void
    {
        OfferSubmission::factory()->create(['state' => OfferSubmission::STATE_FREE]);
        OfferSubmission::factory()->create(['state' => OfferSubmission::STATE_PENDING]);
        OfferSubmission::factory()->create(['state' => OfferSubmission::STATE_SUBMITTED]);
        OfferSubmission::factory()->create(['state' => OfferSubmission::STATE_FAILED]);

        $this->assertSame(2, OfferSubmission::pending()->count());
    }

    public function test_scope_submitted_filters(): void
    {
        OfferSubmission::factory()->create(['state' => OfferSubmission::STATE_SUBMITTED]);
        OfferSubmission::factory()->create(['state' => OfferSubmission::STATE_SUBMITTED]);
        OfferSubmission::factory()->create(['state' => OfferSubmission::STATE_PENDING]);

        $this->assertSame(2, OfferSubmission::submitted()->count());
    }

    public function test_is_submitted_returns_true_only_for_submitted(): void
    {
        $s = OfferSubmission::factory()->create(['state' => OfferSubmission::STATE_SUBMITTED]);
        $this->assertTrue($s->isSubmitted());

        $p = OfferSubmission::factory()->create(['state' => OfferSubmission::STATE_PENDING]);
        $this->assertFalse($p->isSubmitted());
    }

    public function test_is_pending_returns_true_for_in_flight_states(): void
    {
        foreach ([
            OfferSubmission::STATE_PENDING,
            OfferSubmission::STATE_PAYMENT_VERIFIED,
            OfferSubmission::STATE_SUBMISSION_RECOVERY,
        ] as $state) {
            $s = OfferSubmission::factory()->create(['state' => $state]);
            $this->assertTrue($s->isPending(), "Expected isPending for state {$state}");
        }
    }

    public function test_factory_states_work(): void
    {
        $this->assertSame(OfferSubmission::STATE_FREE, OfferSubmission::factory()->free()->create()->state);
        $this->assertSame(OfferSubmission::STATE_PAYMENT_REQUIRED, OfferSubmission::factory()->paymentRequired()->create()->state);
        $this->assertSame(OfferSubmission::STATE_PENDING, OfferSubmission::factory()->pending()->create()->state);
        $this->assertSame(OfferSubmission::STATE_PAYMENT_VERIFIED, OfferSubmission::factory()->paymentVerified()->create()->state);
        $this->assertSame(OfferSubmission::STATE_SUBMITTED, OfferSubmission::factory()->submitted()->create()->state);
        $this->assertSame(OfferSubmission::STATE_FAILED, OfferSubmission::factory()->failed()->create()->state);
        $this->assertSame(OfferSubmission::STATE_REFUND_PENDING, OfferSubmission::factory()->refundPending()->create()->state);
    }

    public function test_fillable_contains_all_spec_fields(): void
    {
        $s = new OfferSubmission();
        $expected = [
            'need_id', 'provider_id', 'state',
            'draft_id', 'draft_version', 'draft_hash',
            'idempotency_key', 'policy_version',
            'amount_minor', 'currency', 'payment_id', 'offer_id', 'refund_reference',
            'unlocked_at', 'expires_at',
        ];
        foreach ($expected as $f) {
            $this->assertContains($f, $s->getFillable(), "Missing fillable: {$f}");
        }
    }

    public function test_table_name_is_offer_submissions(): void
    {
        $s = new OfferSubmission();
        $this->assertSame('offer_submissions', $s->getTable());
    }

    public function test_no_status_field_in_fillable(): void
    {
        $fillable = (new OfferSubmission())->getFillable();
        $this->assertNotContains('status', $fillable);
    }
}
