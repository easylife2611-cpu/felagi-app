<?php

namespace Tests\Feature\Models;

use App\Models\Need;
use App\Models\NeedAward;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Tests\TestCase;

/**
 * WP-B27 / R-TEST-04 — NeedAward model unit tests.
 * Schema: 2026_09_29_000005.
 * NOTE: Composite PK (need_id), $timestamps=false, $incrementing=false.
 * Composite FK (offer_id, need_id) → offers(id, need_id).
 */
class NeedAwardTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    private function makeNeedAndOffer(): array
    {
        $requester = User::factory()->create();
        $need = Need::create([
            'requester_id' => $requester->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Test Need',
            'description'  => 'Test Description',
            'status'       => 'OPEN',
        ]);
        $provider = User::factory()->create();
        $offer = Offer::create([
            'need_id'          => $need->id,
            'provider_id'      => $provider->id,
            'offered_price'    => '500.00',
            'currency'         => 'ETB',
            'proposal_message' => 'I can deliver.',
            'status'           => 'PENDING',
        ]);

        return [$need, $offer, $requester];
    }

    private function makeAward(array $overrides = []): NeedAward
    {
        [$need, $offer, $requester] = $this->makeNeedAndOffer();

        return NeedAward::create(array_merge([
            'need_id'     => $need->id,
            'offer_id'    => $offer->id,
            'accepted_by' => $requester->id,
            'accepted_at' => now(),
            'request_id'  => 'req-' . Str::uuid(),
        ], $overrides));
    }

    public function test_no_incrementing(): void
    {
        $this->assertFalse((new NeedAward())->getIncrementing());
    }

    public function test_no_timestamps(): void
    {
        $this->assertFalse((new NeedAward())->usesTimestamps());
    }

    public function test_primary_key_is_need_id(): void
    {
        $this->assertSame('need_id', (new NeedAward())->getKeyName());
    }

    public function test_primary_key_type_is_string(): void
    {
        $this->assertSame('string', (new NeedAward())->getKeyType());
    }

    public function test_persists_core_attributes(): void
    {
        $a = $this->makeAward();

        $this->assertDatabaseHas('need_awards', [
            'need_id'  => $a->need_id,
            'offer_id' => $a->offer_id,
        ]);
        $this->assertNotNull($a->fresh()->accepted_at);
    }

    public function test_belongs_to_need(): void
    {
        $a = $this->makeAward();
        $this->assertInstanceOf(Need::class, $a->need);
        $this->assertSame($a->need_id, $a->need->id);
    }

    public function test_belongs_to_offer(): void
    {
        $a = $this->makeAward();
        $this->assertInstanceOf(Offer::class, $a->offer);
        $this->assertSame($a->offer_id, $a->offer->id);
    }

    public function test_belongs_to_accepted_by_user(): void
    {
        $a = $this->makeAward();
        $this->assertInstanceOf(User::class, $a->acceptedBy);
        $this->assertSame($a->accepted_by, $a->acceptedBy->id);
    }

    public function test_accepted_at_casts_to_datetime(): void
    {
        $a = $this->makeAward(['accepted_at' => '2026-01-01 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $a->fresh()->accepted_at);
    }

    public function test_request_id_stored(): void
    {
        $reqId = 'req-' . Str::uuid();
        $a = $this->makeAward(['request_id' => $reqId]);
        $this->assertSame($reqId, $a->fresh()->request_id);
    }

    public function test_composite_pk_prevents_duplicate_need_award(): void
    {
        $a = $this->makeAward();

        $this->expectException(\Illuminate\Database\QueryException::class);

        // Same need_id → PK violation
        NeedAward::create([
            'need_id'     => $a->need_id,
            'offer_id'    => $a->offer_id,
            'accepted_by' => $a->accepted_by,
            'accepted_at' => now(),
            'request_id'  => 'req-dup',
        ]);
    }

    public function test_offer_id_is_unique(): void
    {
        $a = $this->makeAward();

        // Try to award a different need using the SAME offer_id
        // First create a second need
        $requester2 = User::factory()->create();
        $need2 = Need::create([
            'requester_id' => $requester2->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Test Need 2',
            'description'  => 'Desc 2',
            'status'       => 'OPEN',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        NeedAward::create([
            'need_id'     => $need2->id,
            'offer_id'    => $a->offer_id,  // same offer, unique violation
            'accepted_by' => $requester2->id,
            'accepted_at' => now(),
            'request_id'  => 'req-x',
        ]);
    }

    public function test_composite_fk_prevents_cross_need_offer(): void
    {
        // Need A with offer A — award to need A ✓
        // Need B with different offer — try to award need B using offer A's id
        // Composite FK (offer_id, need_id) should block
        [$needA, $offerA, $requesterA] = $this->makeNeedAndOffer();

        $requesterB = User::factory()->create();
        $needB = Need::create([
            'requester_id' => $requesterB->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Need B',
            'description'  => 'Desc B',
            'status'       => 'OPEN',
        ]);
        $providerB = User::factory()->create();
        Offer::create([
            'need_id'          => $needB->id,
            'provider_id'      => $providerB->id,
            'offered_price'    => '300.00',
            'currency'         => 'ETB',
            'proposal_message' => 'offer b',
            'status'           => 'PENDING',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        // offerA does NOT belong to needB → composite FK violation
        NeedAward::create([
            'need_id'     => $needB->id,
            'offer_id'    => $offerA->id,
            'accepted_by' => $requesterB->id,
            'accepted_at' => now(),
            'request_id'  => 'req-cross',
        ]);
    }
}
