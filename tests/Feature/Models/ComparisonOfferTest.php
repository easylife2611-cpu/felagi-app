<?php

namespace Tests\Feature\Models;

use App\Models\Comparison;
use App\Models\ComparisonOffer;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Tests\TestCase;

/**
 * WP-B26 / R-TEST-02 — ComparisonOffer model unit tests.
 * Schema: 2026_09_29_000007. No HasFactory.
 */
class ComparisonOfferTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    private function makeComparison(): Comparison
    {
        $requester = User::factory()->create();
        $need = Need::create([
            'requester_id' => $requester->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Test Need',
            'description'  => 'Test Description',
            'status'       => 'OPEN',
        ]);

        return Comparison::create([
            'need_id'        => $need->id,
            'version_number' => 1,
            'triggered_by'   => $requester->id,
            'need_snapshot'  => ['title' => 'Test Need'],
        ]);
    }

    private function makeOffer(Need $need): array
    {
        $provider = User::factory()->create();
        $offer = Offer::create([
            'need_id'          => $need->id,
            'provider_id'      => $provider->id,
            'offered_price'    => '500.00',
            'currency'         => 'ETB',
            'proposal_message' => 'I can deliver this.',
            'status'           => 'PENDING',
        ]);

        return [$offer, $provider];
    }

    private function makeComparisonOffer(array $overrides = []): ComparisonOffer
    {
        $comparison = $this->makeComparison();
        [$offer, $provider] = $this->makeOffer($comparison->need);

        return ComparisonOffer::create(array_merge([
            'comparison_id'         => $comparison->id,
            'offer_id'              => $offer->id,
            'provider_id'           => $provider->id,
            'offer_snapshot'        => ['price' => 500, 'notes' => 'OK'],
            'credibility_snapshot'  => ['rating' => 4.5, 'count' => 10],
            'offer_snapshot_hash'   => hash('sha256', 'offer-test'),
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $co = $this->makeComparisonOffer();
        $this->assertTrue(Str::isUuid($co->id));
    }

    public function test_belongs_to_comparison(): void
    {
        $co = $this->makeComparisonOffer();
        $this->assertInstanceOf(Comparison::class, $co->comparison);
        $this->assertSame($co->comparison_id, $co->comparison->id);
    }

    public function test_belongs_to_offer(): void
    {
        $co = $this->makeComparisonOffer();
        $this->assertInstanceOf(Offer::class, $co->offer);
        $this->assertSame($co->offer_id, $co->offer->id);
    }

    public function test_belongs_to_provider(): void
    {
        $co = $this->makeComparisonOffer();
        $this->assertInstanceOf(User::class, $co->provider);
        $this->assertSame($co->provider_id, $co->provider->id);
    }

    public function test_has_one_result_initially_null(): void
    {
        $co = $this->makeComparisonOffer();
        $this->assertNull($co->result);
    }

    public function test_offer_snapshot_casts_to_array(): void
    {
        $co = $this->makeComparisonOffer([
            'offer_snapshot' => ['price' => 123, 'currency' => 'ETB', 'tags' => ['a']],
        ]);
        $fresh = $co->fresh();

        $this->assertIsArray($fresh->offer_snapshot);
        $this->assertSame(123, $fresh->offer_snapshot['price']);
        $this->assertSame(['a'], $fresh->offer_snapshot['tags']);
    }

    public function test_credibility_snapshot_casts_to_array(): void
    {
        $co = $this->makeComparisonOffer([
            'credibility_snapshot' => ['rating' => 5.0, 'jobs' => 100],
        ]);
        $fresh = $co->fresh();

        $this->assertIsArray($fresh->credibility_snapshot);
        $this->assertEquals(5, $fresh->credibility_snapshot["rating"]);
    }

    public function test_offer_snapshot_hash_stores_64_hex(): void
    {
        $hash = hash('sha256', 'specific-payload');
        $co = $this->makeComparisonOffer(['offer_snapshot_hash' => $hash]);

        $this->assertSame(64, strlen($co->fresh()->offer_snapshot_hash));
        $this->assertSame($hash, $co->fresh()->offer_snapshot_hash);
    }

    public function test_unique_comparison_id_and_offer_id(): void
    {
        $comparison = $this->makeComparison();
        [$offer, $provider] = $this->makeOffer($comparison->need);

        ComparisonOffer::create([
            'comparison_id'        => $comparison->id,
            'offer_id'             => $offer->id,
            'provider_id'          => $provider->id,
            'offer_snapshot'       => ['price' => 100],
            'credibility_snapshot' => ['rating' => 4.0],
            'offer_snapshot_hash'  => hash('sha256', 'a'),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        ComparisonOffer::create([
            'comparison_id'        => $comparison->id,
            'offer_id'             => $offer->id,
            'provider_id'          => $provider->id,
            'offer_snapshot'       => ['price' => 200],
            'credibility_snapshot' => ['rating' => 4.5],
            'offer_snapshot_hash'  => hash('sha256', 'b'),
        ]);
    }

    public function test_can_add_multiple_offers_to_one_comparison(): void
    {
        $comparison = $this->makeComparison();
        [$offer1, $provider1] = $this->makeOffer($comparison->need);
        [$offer2, $provider2] = $this->makeOffer($comparison->need);

        ComparisonOffer::create([
            'comparison_id'        => $comparison->id,
            'offer_id'             => $offer1->id,
            'provider_id'          => $provider1->id,
            'offer_snapshot'       => ['price' => 100],
            'credibility_snapshot' => ['rating' => 4.0],
            'offer_snapshot_hash'  => hash('sha256', 'a'),
        ]);
        ComparisonOffer::create([
            'comparison_id'        => $comparison->id,
            'offer_id'             => $offer2->id,
            'provider_id'          => $provider2->id,
            'offer_snapshot'       => ['price' => 200],
            'credibility_snapshot' => ['rating' => 4.5],
            'offer_snapshot_hash'  => hash('sha256', 'b'),
        ]);

        $this->assertSame(2, ComparisonOffer::where('comparison_id', $comparison->id)->count());
    }

    public function test_uses_timestamps(): void
    {
        $this->assertTrue((new ComparisonOffer())->usesTimestamps());
    }

    public function test_created_at_populated(): void
    {
        $co = $this->makeComparisonOffer();
        $this->assertNotNull($co->fresh()->created_at);
    }

    public function test_updated_at_populated(): void
    {
        $co = $this->makeComparisonOffer();
        $this->assertNotNull($co->fresh()->updated_at);
    }
}
