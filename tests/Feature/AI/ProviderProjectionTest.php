<?php

namespace Tests\Feature\AI;

use App\Models\Comparison;
use App\Models\ComparisonOffer;
use App\Models\ComparisonResult;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AI-21 + AI-22 — Provider projection + feedback.
 */
class ProviderProjectionTest extends TestCase
{
    use RefreshDatabase;

    private function scenario(): array
    {
        $owner = User::factory()->create();
        $p1 = User::factory()->create();
        $p2 = User::factory()->create();

        $need = Need::factory()->create([
            'requester_id' => $owner->id,
            'status'       => Need::STATUS_OPEN,
        ]);

        $offer1 = Offer::factory()->create([
            'need_id' => $need->id, 'provider_id' => $p1->id,
            'status' => 'PENDING', 'offered_price' => 300, 'currency' => 'ETB',
        ]);
        $offer2 = Offer::factory()->create([
            'need_id' => $need->id, 'provider_id' => $p2->id,
            'status' => 'PENDING', 'offered_price' => 400, 'currency' => 'ETB',
        ]);

        $comparison = Comparison::factory()->create([
            'need_id' => $need->id,
            'status'  => Comparison::STATUS_COMPLETED,
            'triggered_by' => $owner->id,
            'version_number' => 1,
        ]);

        $co1 = ComparisonOffer::create([
            'comparison_id' => $comparison->id,
            'offer_id' => $offer1->id, 'provider_id' => $p1->id,
            'offer_snapshot' => [], 'credibility_snapshot' => [],
            'offer_snapshot_hash' => hash('sha256', '{}'),
        ]);
        $co2 = ComparisonOffer::create([
            'comparison_id' => $comparison->id,
            'offer_id' => $offer2->id, 'provider_id' => $p2->id,
            'offer_snapshot' => [], 'credibility_snapshot' => [],
            'offer_snapshot_hash' => hash('sha256', '{}'),
        ]);

        foreach ([$co1, $co2] as $i => $co) {
            ComparisonResult::create([
                'comparison_id' => $comparison->id,
                'comparison_offer_id' => $co->id,
                'score' => 80 - $i * 10,
                'criterion_scores' => ['price' => 80, 'delivery_time' => 80, 'quality' => 80, 'reliability' => 80],
                'strengths' => [], 'weaknesses' => [],
                'missing_information' => [], 'risk_notes' => [],
                'fit_explanation' => 'ok',
                'completeness' => 'COMPLETE',
                'missing_criteria' => [], 'uncertain_criteria' => [],
                'result_hash' => hash('sha256', 'r' . $i),
                'created_at' => now(),
            ]);
        }

        return [$owner, $p1, $p2, $comparison];
    }

    // ─── AI-21 ───

    public function test_provider_projection_requires_auth(): void
    {
        [$owner, $p1, $p2, $comparison] = $this->scenario();
        $this->getJson("/api/v1/comparisons/{$comparison->id}/provider-projection")
            ->assertStatus(401);
    }

    public function test_provider_projection_403_for_unrelated_user(): void
    {
        [$owner, $p1, $p2, $comparison] = $this->scenario();
        $stranger = User::factory()->create();
        $this->actingAs($stranger, 'sanctum')
            ->getJson("/api/v1/comparisons/{$comparison->id}/provider-projection")
            ->assertStatus(403);
    }

    public function test_provider_projection_returns_own_result_only(): void
    {
        [$owner, $p1, $p2, $comparison] = $this->scenario();

        $res = $this->actingAs($p1, 'sanctum')
            ->getJson("/api/v1/comparisons/{$comparison->id}/provider-projection");

        $res->assertStatus(200)
            ->assertJsonPath('data.comparison_id', $comparison->id)
            ->assertJsonPath('data.completeness', 'COMPLETE');
        // The score for p1 should NOT equal the score for p2
        $this->assertNotEquals(70, $res->json('data.score'));
    }

    // ─── AI-22 ───

    public function test_feedback_requires_auth(): void
    {
        [$owner, $p1, $p2, $comparison] = $this->scenario();
        $this->postJson("/api/v1/comparisons/{$comparison->id}/feedback", [
            'rating' => 'FAIR',
        ])->assertStatus(401);
    }

    public function test_feedback_403_for_unrelated_user(): void
    {
        [$owner, $p1, $p2, $comparison] = $this->scenario();
        $stranger = User::factory()->create();
        $this->actingAs($stranger, 'sanctum')
            ->postJson("/api/v1/comparisons/{$comparison->id}/feedback", [
                'rating' => 'FAIR',
            ])->assertStatus(403);
    }

    public function test_provider_can_submit_feedback(): void
    {
        [$owner, $p1, $p2, $comparison] = $this->scenario();

        $res = $this->actingAs($p1, 'sanctum')
            ->postJson("/api/v1/comparisons/{$comparison->id}/feedback", [
                'rating'  => 'FAIR',
                'comment' => 'Result looked reasonable.',
            ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.rating', 'FAIR')
            ->assertJsonPath('data.provider_id', $p1->id);
    }

    public function test_feedback_rejects_duplicate(): void
    {
        [$owner, $p1, $p2, $comparison] = $this->scenario();

        $this->actingAs($p1, 'sanctum')
            ->postJson("/api/v1/comparisons/{$comparison->id}/feedback", ['rating' => 'FAIR'])
            ->assertStatus(201);

        $this->actingAs($p1, 'sanctum')
            ->postJson("/api/v1/comparisons/{$comparison->id}/feedback", ['rating' => 'UNCLEAR'])
            ->assertStatus(409)
            ->assertJsonPath('error.code', 'FEEDBACK_ALREADY_SUBMITTED');
    }

    public function test_feedback_rejects_invalid_rating(): void
    {
        [$owner, $p1, $p2, $comparison] = $this->scenario();
        $this->actingAs($p1, 'sanctum')
            ->postJson("/api/v1/comparisons/{$comparison->id}/feedback", ['rating' => 'BAD'])
            ->assertStatus(422);
    }
}
