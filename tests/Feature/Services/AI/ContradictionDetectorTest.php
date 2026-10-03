<?php

declare(strict_types=1);

namespace Tests\Feature\Services\AI;

use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use App\Services\AI\ContradictionDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class ContradictionDetectorTest extends TestCase
{
    use RefreshDatabase;

    private ContradictionDetector $detector;
    private Category $category;
    private Need $need;
    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new ContradictionDetector();
        $this->owner = User::factory()->create();
        $this->category = Category::create([
            'id' => (string) Str::uuid(), 'slug' => 'cd-cat',
            'name_am' => 'ሙከራ', 'name_en' => 'Test',
            'active' => true, 'sort_order' => 1,
        ]);
        $this->need = Need::create([
            'id' => (string) Str::uuid(),
            'requester_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'CD test need',
            'description' => 'Test need for contradiction detection.',
            'status' => Need::STATUS_OPEN,
            'version' => 1,
            'budget_min' => 1000,
            'budget_max' => 5000,
            'currency' => 'ETB',
        ]);
    }

    private function makeOffer(array $overrides = []): Offer
    {
        $provider = User::factory()->create();
        return Offer::create(array_merge([
            'id' => (string) Str::uuid(),
            'need_id' => $this->need->id,
            'provider_id' => $provider->id,
            'offered_price' => 3000,
            'currency' => 'ETB',
            'proposal_message' => 'Valid offer',
            'delivery_time_text' => '3 days',
            'availability_text' => 'Available now',
            'status' => Offer::STATUS_PENDING,
            'version' => 1,
        ], $overrides));
    }

    public function test_constants_match(): void
    {
        $this->assertSame('BUDGET_ABOVE_MAX', ContradictionDetector::TYPE_BUDGET_ABOVE_MAX);
        $this->assertSame('BUDGET_BELOW_MIN', ContradictionDetector::TYPE_BUDGET_BELOW_MIN);
        $this->assertSame('CURRENCY_MISMATCH', ContradictionDetector::TYPE_CURRENCY_MISMATCH);
        $this->assertSame('DELIVERY_UNSPECIFIED', ContradictionDetector::TYPE_DELIVERY_UNSPECIFIED);
        $this->assertSame('PRICE_UNSPECIFIED', ContradictionDetector::TYPE_PRICE_UNSPECIFIED);
        $this->assertSame('AVAILABILITY_UNSPECIFIED', ContradictionDetector::TYPE_AVAILABILITY_UNSPECIFIED);
    }

    public function test_clean_offer_produces_no_findings(): void
    {
        $offer = $this->makeOffer();
        $findings = $this->detector->detect($this->need, [$offer]);
        $this->assertSame([], $findings);
    }

    public function test_budget_above_max_detected(): void
    {
        $offer = $this->makeOffer(['offered_price' => 9999]);
        $findings = $this->detector->detect($this->need, [$offer]);
        $this->assertCount(1, $findings);
        $this->assertSame(ContradictionDetector::TYPE_BUDGET_ABOVE_MAX, $findings[0]['type']);
        $this->assertSame('MEDIUM', $findings[0]['severity']);
        $this->assertSame($offer->id, $findings[0]['offer_id']);
        $this->assertSame(0, $findings[0]['offer_index']);
    }

    public function test_budget_below_min_detected(): void
    {
        $offer = $this->makeOffer(['offered_price' => 500]);
        $findings = $this->detector->detect($this->need, [$offer]);
        $this->assertCount(1, $findings);
        $this->assertSame(ContradictionDetector::TYPE_BUDGET_BELOW_MIN, $findings[0]['type']);
        $this->assertSame('LOW', $findings[0]['severity']);
    }

    public function test_currency_mismatch_detected(): void
    {
        $offer = $this->makeOffer(['currency' => 'USD']);
        $findings = $this->detector->detect($this->need, [$offer]);
        $this->assertCount(1, $findings);
        $this->assertSame(ContradictionDetector::TYPE_CURRENCY_MISMATCH, $findings[0]['type']);
        $this->assertSame('HIGH', $findings[0]['severity']);
    }

    public function test_price_null_produces_price_unspecified(): void
    {
        // DB enforces NOT NULL on offered_price — use unsaved Offer to
        // exercise the detector's null branch without violating schema.
        $provider = User::factory()->create();
        $offer = new Offer([
            'need_id'            => $this->need->id,
            'provider_id'        => $provider->id,
            'offered_price'      => null,
            'currency'           => 'ETB',
            'proposal_message'   => 'Valid offer',
            'delivery_time_text' => '3 days',
            'availability_text'  => 'Available now',
            'status'             => Offer::STATUS_PENDING,
            'version'            => 1,
        ]);
        $offer->id = (string) Str::uuid();

        $findings = $this->detector->detect($this->need, [$offer]);
        $types = array_column($findings, 'type');
        $this->assertContains(ContradictionDetector::TYPE_PRICE_UNSPECIFIED, $types);
    }

    public function test_price_zero_produces_price_unspecified(): void
    {
        $offer = $this->makeOffer(['offered_price' => 0]);
        $findings = $this->detector->detect($this->need, [$offer]);
        $types = array_column($findings, 'type');
        $this->assertContains(ContradictionDetector::TYPE_PRICE_UNSPECIFIED, $types);
    }

    public function test_delivery_unspecified_detected(): void
    {
        $offer = $this->makeOffer(['delivery_time_text' => '']);
        $findings = $this->detector->detect($this->need, [$offer]);
        $types = array_column($findings, 'type');
        $this->assertContains(ContradictionDetector::TYPE_DELIVERY_UNSPECIFIED, $types);
    }

    public function test_availability_unspecified_detected(): void
    {
        $offer = $this->makeOffer(['availability_text' => '']);
        $findings = $this->detector->detect($this->need, [$offer]);
        $types = array_column($findings, 'type');
        $this->assertContains(ContradictionDetector::TYPE_AVAILABILITY_UNSPECIFIED, $types);
    }

    public function test_multiple_offers_get_distinct_indices(): void
    {
        $o1 = $this->makeOffer(['offered_price' => 9999]);
        $o2 = $this->makeOffer(['offered_price' => 9999]);
        $findings = $this->detector->detect($this->need, [$o1, $o2]);
        $this->assertCount(2, $findings);
        $this->assertSame(0, $findings[0]['offer_index']);
        $this->assertSame(1, $findings[1]['offer_index']);
    }

    public function test_summarise_empty_returns_zeros(): void
    {
        $summary = $this->detector->summarise([]);
        $this->assertSame(0, $summary['total']);
        $this->assertSame(0, $summary['by_severity']['HIGH']);
        $this->assertSame(0, $summary['by_severity']['MEDIUM']);
        $this->assertSame(0, $summary['by_severity']['LOW']);
    }

    public function test_summarise_counts_by_severity(): void
    {
        $offer = $this->makeOffer([
            'offered_price' => 0,          // zero = PRICE_UNSPECIFIED
            'currency' => 'USD',           // HIGH
            'delivery_time_text' => '',    // MEDIUM
            'availability_text' => '',     // LOW
        ]);
        $findings = $this->detector->detect($this->need, [$offer]);
        $summary = $this->detector->summarise($findings);
        $this->assertSame(count($findings), $summary['total']);
        $this->assertGreaterThan(0, $summary['by_severity']['HIGH']);
        $this->assertGreaterThan(0, $summary['by_severity']['MEDIUM']);
    }

    public function test_summarise_counts_by_offer(): void
    {
        $o1 = $this->makeOffer(['offered_price' => 9999]);
        $o2 = $this->makeOffer(['offered_price' => 9999]);
        $findings = $this->detector->detect($this->need, [$o1, $o2]);
        $summary = $this->detector->summarise($findings);
        $this->assertArrayHasKey($o1->id, $summary['by_offer']);
        $this->assertArrayHasKey($o2->id, $summary['by_offer']);
    }
}
