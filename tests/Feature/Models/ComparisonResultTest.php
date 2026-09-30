<?php

namespace Tests\Feature\Models;

use App\Models\Comparison;
use App\Models\ComparisonOffer;
use App\Models\ComparisonResult;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Tests\TestCase;

/**
 * WP-B26 / R-TEST-02 — ComparisonResult model unit tests.
 * Schema: 2026_09_29_000008. No HasFactory. No timestamps.
 */
class ComparisonResultTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    private function makeComparisonOffer(): ComparisonOffer
    {
        $requester = User::factory()->create();
        $need = Need::create([
            'requester_id' => $requester->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Test Need',
            'description'  => 'Test Description',
            'status'       => 'OPEN',
        ]);
        $comparison = Comparison::create([
            'need_id'        => $need->id,
            'version_number' => 1,
            'triggered_by'   => $requester->id,
            'need_snapshot'  => ['title' => 'Test Need'],
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

        return ComparisonOffer::create([
            'comparison_id'        => $comparison->id,
            'offer_id'             => $offer->id,
            'provider_id'          => $provider->id,
            'offer_snapshot'       => ['price' => 500],
            'credibility_snapshot' => ['rating' => 4.5],
            'offer_snapshot_hash'  => hash('sha256', 'x'),
        ]);
    }

    private function makeResult(array $overrides = []): ComparisonResult
    {
        $co = $this->makeComparisonOffer();

        return ComparisonResult::create(array_merge([
            'comparison_id'        => $co->comparison_id,
            'comparison_offer_id'  => $co->id,
            'score'                => '8.50',
            'criterion_scores'     => ['price' => 9, 'speed' => 8],
            'strengths'            => ['Fast delivery', 'Fair price'],
            'weaknesses'           => ['No portfolio'],
            'missing_information'  => ['No delivery date'],
            'risk_notes'           => ['First-time provider'],
            'fit_explanation'      => 'Good match for the budget.',
            'result_hash'          => hash('sha256', 'result-1'),
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $r = $this->makeResult();
        $this->assertTrue(Str::isUuid($r->id));
    }

    public function test_no_timestamps(): void
    {
        $this->assertFalse((new ComparisonResult())->usesTimestamps());
    }

    public function test_belongs_to_comparison(): void
    {
        $r = $this->makeResult();
        $this->assertInstanceOf(Comparison::class, $r->comparison);
        $this->assertSame($r->comparison_id, $r->comparison->id);
    }

    public function test_belongs_to_comparison_offer(): void
    {
        $r = $this->makeResult();
        $this->assertInstanceOf(ComparisonOffer::class, $r->comparisonOffer);
        $this->assertSame($r->comparison_offer_id, $r->comparisonOffer->id);
    }

    public function test_score_casts_decimal_2(): void
    {
        $r = $this->makeResult(['score' => '7.5']);
        $this->assertSame('7.50', (string) $r->fresh()->score);
    }

    public function test_score_nullable(): void
    {
        $r = $this->makeResult(['score' => null]);
        $this->assertNull($r->fresh()->score);
    }

    public function test_criterion_scores_casts_to_array(): void
    {
        $r = $this->makeResult([
            'criterion_scores' => ['price' => 10, 'quality' => 9, 'speed' => 8],
        ]);
        $fresh = $r->fresh();

        $this->assertIsArray($fresh->criterion_scores);
        $this->assertSame(10, $fresh->criterion_scores['price']);
    }

    public function test_strengths_casts_to_array(): void
    {
        $r = $this->makeResult(['strengths' => ['A', 'B', 'C']]);
        $this->assertSame(['A', 'B', 'C'], $r->fresh()->strengths);
    }

    public function test_weaknesses_casts_to_array(): void
    {
        $r = $this->makeResult(['weaknesses' => ['W1', 'W2']]);
        $this->assertSame(['W1', 'W2'], $r->fresh()->weaknesses);
    }

    public function test_missing_information_casts_to_array(): void
    {
        $r = $this->makeResult(['missing_information' => ['M1']]);
        $this->assertSame(['M1'], $r->fresh()->missing_information);
    }

    public function test_risk_notes_casts_to_array(): void
    {
        $r = $this->makeResult(['risk_notes' => ['R1', 'R2']]);
        $this->assertSame(['R1', 'R2'], $r->fresh()->risk_notes);
    }

    public function test_fit_explanation_stored_as_text(): void
    {
        $text = str_repeat('Good match. ', 50);
        $r = $this->makeResult(['fit_explanation' => $text]);
        $this->assertSame($text, $r->fresh()->fit_explanation);
    }

    public function test_result_hash_is_64_chars(): void
    {
        $r = $this->makeResult();
        $this->assertSame(64, strlen($r->fresh()->result_hash));
    }

    public function test_created_at_casts_to_datetime(): void
    {
        $r = $this->makeResult();
        $this->assertInstanceOf(\Carbon\Carbon::class, $r->fresh()->created_at);
    }

    public function test_unique_comparison_id_and_comparison_offer_id(): void
    {
        $co = $this->makeComparisonOffer();
        ComparisonResult::create([
            'comparison_id'       => $co->comparison_id,
            'comparison_offer_id' => $co->id,
            'criterion_scores'    => ['x' => 1],
            'strengths'           => [],
            'weaknesses'          => [],
            'missing_information' => [],
            'risk_notes'          => [],
            'fit_explanation'     => 'OK',
            'result_hash'         => hash('sha256', 'a'),
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        ComparisonResult::create([
            'comparison_id'       => $co->comparison_id,
            'comparison_offer_id' => $co->id,
            'criterion_scores'    => ['x' => 2],
            'strengths'           => [],
            'weaknesses'          => [],
            'missing_information' => [],
            'risk_notes'          => [],
            'fit_explanation'     => 'Again',
            'result_hash'         => hash('sha256', 'b'),
        ]);
    }
}
