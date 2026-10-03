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

final class OfferSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_uuid_primary_key(): void
    {
        $s = OfferSubmission::factory()->create();
        $this->assertTrue(Str::isUuid($s->id));
    }

    public function test_status_constants_match(): void
    {
        $this->assertSame('PENDING_PAYMENT', OfferSubmission::STATUS_PENDING_PAYMENT);
        $this->assertSame('UNLOCKED',        OfferSubmission::STATUS_UNLOCKED);
        $this->assertSame('EXPIRED',         OfferSubmission::STATUS_EXPIRED);
    }

    public function test_default_status_is_pending_payment(): void
    {
        $s = OfferSubmission::factory()->create();
        $this->assertSame(OfferSubmission::STATUS_PENDING_PAYMENT, $s->fresh()->status);
    }

    public function test_unlocked_state_sets_timestamps(): void
    {
        $s = OfferSubmission::factory()->unlocked()->create();
        $fresh = $s->fresh();
        $this->assertSame(OfferSubmission::STATUS_UNLOCKED, $fresh->status);
        $this->assertNotNull($fresh->unlocked_at);
        $this->assertNotNull($fresh->expires_at);
    }

    public function test_expired_state(): void
    {
        $s = OfferSubmission::factory()->expired()->create();
        $fresh = $s->fresh();
        $this->assertSame(OfferSubmission::STATUS_EXPIRED, $fresh->status);
        $this->assertTrue($fresh->expires_at->isPast());
    }

    public function test_unlocked_at_casts_to_carbon(): void
    {
        $s = OfferSubmission::factory()->unlocked()->create();
        $this->assertInstanceOf(Carbon::class, $s->fresh()->unlocked_at);
    }

    public function test_expires_at_casts_to_carbon(): void
    {
        $s = OfferSubmission::factory()->unlocked()->create();
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

    public function test_unique_constraint_on_need_and_provider(): void
    {
        $n = Need::factory()->create();
        $u = User::factory()->create();

        OfferSubmission::factory()->create(['need_id' => $n->id, 'provider_id' => $u->id]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        OfferSubmission::factory()->create(['need_id' => $n->id, 'provider_id' => $u->id]);
    }

    public function test_for_need_state(): void
    {
        $n = Need::factory()->create();
        $s = OfferSubmission::factory()->forNeed($n->id)->create();
        $this->assertSame($n->id, $s->fresh()->need_id);
    }

    public function test_for_provider_state(): void
    {
        $u = User::factory()->create();
        $s = OfferSubmission::factory()->forProvider($u->id)->create();
        $this->assertSame($u->id, $s->fresh()->provider_id);
    }

    public function test_payment_id_nullable(): void
    {
        $s = OfferSubmission::factory()->create();
        $this->assertNull($s->fresh()->payment_id);
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $s = new OfferSubmission();
        foreach ([
            'need_id', 'provider_id', 'status',
            'payment_id', 'unlocked_at', 'expires_at',
        ] as $f) {
            $this->assertContains($f, $s->getFillable());
        }
    }

    public function test_table_name_is_offer_submissions(): void
    {
        $s = new OfferSubmission();
        $this->assertSame('offer_submissions', $s->getTable());
    }
}
