<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * L347-F — Offer model unit tests (B18 item 6).
 */
class OfferTest extends TestCase
{
    use RefreshDatabase;

    private function makeOffer(array $overrides = []): Offer
    {
        $provider = User::factory()->create();
        $owner    = User::factory()->create();
        $cat      = Category::create([
            'slug'       => 'offer-cat-' . Str::lower(Str::random(6)),
            'name_am'    => 'ምድብ',
            'name_en'    => 'Cat',
            'active'     => true,
            'sort_order' => 1,
        ]);
        $need = Need::create([
            'requester_id' => $owner->id,
            'category_id'  => $cat->id,
            'title'        => 'Host need ' . Str::random(4),
            'description'  => 'Host',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ]);

        return Offer::create(array_merge([
            'need_id'          => $need->id,
            'provider_id'      => $provider->id,
            'offered_price'    => '500.00',
            'currency'         => 'ETB',
            'proposal_message' => 'I can do this.',
            'status'           => Offer::STATUS_PENDING,
            'version'          => 1,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $o = $this->makeOffer();
        $this->assertNotNull($o->id);
        $this->assertTrue(Str::isUuid($o->id));
    }

    public function test_soft_deletes(): void
    {
        $o = $this->makeOffer();
        $o->delete();
        $this->assertSoftDeleted('offers', ['id' => $o->id]);
    }

    public function test_persists_core_attributes(): void
    {
        $o = $this->makeOffer([
            'offered_price'    => '750.50',
            'currency'         => 'USD',
            'proposal_message' => 'Best offer',
        ]);

        $this->assertDatabaseHas('offers', [
            'id'            => $o->id,
            'offered_price' => '750.50',
            'currency'      => 'USD',
            'status'        => Offer::STATUS_PENDING,
        ]);
    }

    public function test_offered_price_casts_decimal_2(): void
    {
        $o = $this->makeOffer(['offered_price' => '123.45']);
        $this->assertSame('123.45', $o->fresh()->offered_price);
    }

    public function test_version_casts_integer(): void
    {
        $o = $this->makeOffer(['version' => 3]);
        $this->assertSame(3, $o->fresh()->version);
    }

    public function test_accepted_at_casts_datetime(): void
    {
        $o = $this->makeOffer(['accepted_at' => '2026-12-31 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $o->fresh()->accepted_at);
    }

    public function test_withdrawn_at_casts_datetime(): void
    {
        $o = $this->makeOffer(['withdrawn_at' => '2026-12-31 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $o->fresh()->withdrawn_at);
    }

    public function test_status_constants(): void
    {
        $this->assertSame('PENDING', Offer::STATUS_PENDING);
        $this->assertSame('ACCEPTED', Offer::STATUS_ACCEPTED);
        $this->assertSame('REJECTED', Offer::STATUS_REJECTED);
        $this->assertSame('WITHDRAWN', Offer::STATUS_WITHDRAWN);
    }

    public function test_belongs_to_need(): void
    {
        $o = $this->makeOffer();
        $this->assertInstanceOf(Need::class, $o->need);
    }

    public function test_belongs_to_provider(): void
    {
        $provider = User::factory()->create();
        $o = $this->makeOffer(['provider_id' => $provider->id]);
        $this->assertInstanceOf(User::class, $o->provider);
        $this->assertSame($provider->id, $o->provider->id);
    }

    public function test_has_one_award_initially_null(): void
    {
        $o = $this->makeOffer();
        $this->assertNull($o->award);
    }

    public function test_has_many_messages(): void
    {
        $o = $this->makeOffer();
        $this->assertCount(0, $o->messages);
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $o->messages
        );
    }

    public function test_has_many_attachments(): void
    {
        $o = $this->makeOffer();
        $this->assertCount(0, $o->attachments);
    }

    public function test_scope_pending_filters(): void
    {
        $this->makeOffer(['status' => Offer::STATUS_PENDING]);
        $this->makeOffer(['status' => Offer::STATUS_PENDING]);
        $this->makeOffer(['status' => Offer::STATUS_ACCEPTED]);

        $this->assertSame(2, Offer::pending()->count());
    }

    public function test_is_pending_returns_true_for_pending(): void
    {
        $o = $this->makeOffer(['status' => Offer::STATUS_PENDING]);
        $this->assertTrue($o->isPending());
    }

    public function test_is_pending_returns_false_for_accepted(): void
    {
        $o = $this->makeOffer(['status' => Offer::STATUS_ACCEPTED]);
        $this->assertFalse($o->isPending());
    }

    public function test_is_owned_by_returns_true_for_provider(): void
    {
        $provider = User::factory()->create();
        $o = $this->makeOffer(['provider_id' => $provider->id]);
        $this->assertTrue($o->isOwnedBy($provider));
    }

    public function test_is_owned_by_returns_false_for_other(): void
    {
        $o = $this->makeOffer();
        $other = User::factory()->create();
        $this->assertFalse($o->isOwnedBy($other));
    }

    public function test_can_accept_offer(): void
    {
        $o = $this->makeOffer();
        $o->update([
            'status'      => Offer::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        $o->refresh();
        $this->assertSame(Offer::STATUS_ACCEPTED, $o->status);
        $this->assertNotNull($o->accepted_at);
    }

    public function test_can_withdraw_offer(): void
    {
        $o = $this->makeOffer();
        $o->update([
            'status'       => Offer::STATUS_WITHDRAWN,
            'withdrawn_at' => now(),
        ]);

        $o->refresh();
        $this->assertSame(Offer::STATUS_WITHDRAWN, $o->status);
        $this->assertNotNull($o->withdrawn_at);
    }
}
