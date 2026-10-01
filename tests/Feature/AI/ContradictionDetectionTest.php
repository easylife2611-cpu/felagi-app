<?php

namespace Tests\Feature\AI;

use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use App\Services\AI\ContradictionDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AI-11 — Contradiction detection (deterministic pre-check).
 */
class ContradictionDetectionTest extends TestCase
{
    use RefreshDatabase;

    private function detector(): ContradictionDetector
    {
        return new ContradictionDetector();
    }

    private function need(array $overrides = []): Need
    {
        return Need::factory()->create(array_merge([
            'status'   => Need::STATUS_OPEN,
            'currency' => 'ETB',
            'budget_min' => 100,
            'budget_max' => 500,
        ], $overrides));
    }

    private function offer(Need $need, array $overrides = []): Offer
    {
        return Offer::factory()->create(array_merge([
            'need_id'          => $need->id,
            'status'           => 'PENDING',
            'offered_price'    => 300,
            'currency'         => 'ETB',
            'proposal_message' => 'ok',
            'delivery_time_text' => '3 days',
            'availability_text'  => 'immediate',
        ], $overrides));
    }

    public function test_no_contradictions_on_well_formed_offer(): void
    {
        $need = $this->need();
        $offer = $this->offer($need);

        $findings = $this->detector()->detect($need, [$offer]);

        $this->assertSame([], $findings);
    }

    public function test_budget_above_max_flagged(): void
    {
        $need = $this->need(['budget_max' => 500]);
        $offer = $this->offer($need, ['offered_price' => 999]);

        $findings = $this->detector()->detect($need, [$offer]);

        $this->assertNotEmpty($findings);
        $types = array_column($findings, 'type');
        $this->assertContains(ContradictionDetector::TYPE_BUDGET_ABOVE_MAX, $types);
    }

    public function test_budget_below_min_flagged(): void
    {
        $need = $this->need(['budget_min' => 100]);
        $offer = $this->offer($need, ['offered_price' => 10]);

        $findings = $this->detector()->detect($need, [$offer]);
        $types = array_column($findings, 'type');
        $this->assertContains(ContradictionDetector::TYPE_BUDGET_BELOW_MIN, $types);
    }

    public function test_currency_mismatch_flagged(): void
    {
        $need = $this->need(['currency' => 'ETB']);
        $offer = $this->offer($need, ['currency' => 'USD']);

        $findings = $this->detector()->detect($need, [$offer]);
        $types = array_column($findings, 'type');
        $this->assertContains(ContradictionDetector::TYPE_CURRENCY_MISMATCH, $types);
    }

    public function test_empty_delivery_flagged(): void
    {
        $need = $this->need();
        $offer = $this->offer($need, ['delivery_time_text' => null]);

        $findings = $this->detector()->detect($need, [$offer]);
        $types = array_column($findings, 'type');
        $this->assertContains(ContradictionDetector::TYPE_DELIVERY_UNSPECIFIED, $types);
    }

    public function test_zero_price_flagged(): void
    {
        // DB column is NOT NULL, so we use 0 as the "unspecified" sentinel.
        $need = $this->need();
        $offer = $this->offer($need, ['offered_price' => 0]);

        $findings = $this->detector()->detect($need, [$offer]);
        $types = array_column($findings, 'type');
        $this->assertContains(ContradictionDetector::TYPE_PRICE_UNSPECIFIED, $types);
    }

    public function test_summary_counts(): void
    {
        $need = $this->need();
        $offer = $this->offer($need, [
            'offered_price' => 9999,
            'currency' => 'USD',
            'delivery_time_text' => null,
        ]);

        $findings = $this->detector()->detect($need, [$offer]);
        $summary  = $this->detector()->summarise($findings);

        $this->assertGreaterThanOrEqual(3, $summary['total']);
        $this->assertArrayHasKey('by_severity', $summary);
        $this->assertArrayHasKey('by_offer', $summary);
    }

    public function test_detector_never_throws_on_empty_offer_list(): void
    {
        $need = $this->need();
        $findings = $this->detector()->detect($need, []);
        $this->assertSame([], $findings);
    }

    public function test_multiple_offers_independent_findings(): void
    {
        $need = $this->need();
        $offer1 = $this->offer($need, ['offered_price' => 300, 'currency' => 'ETB']);
        $offer2 = $this->offer($need, ['offered_price' => 5000, 'currency' => 'USD']);

        $findings = $this->detector()->detect($need, [$offer1, $offer2]);
        $offerIds = array_unique(array_column($findings, 'offer_id'));

        // only offer2 should trigger budget + currency issues
        $this->assertContains($offer2->id, $offerIds);
        $this->assertNotContains($offer1->id, $offerIds);
    }
}
