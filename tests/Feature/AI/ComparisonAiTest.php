<?php

namespace Tests\Feature\AI;

use App\Models\Comparison;
use App\Models\ComparisonOffer;
use App\Models\ComparisonResult;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ComparisonAiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['ai.gemini.api_key' => 'test-key']);
        config(['ai.gemini.model' => 'gemini-flash-latest']);
    }

    private function user(): User
    {
        return User::factory()->create();
    }

    private function needWithOffers(User $owner, int $count = 2): array
    {
        $need = Need::factory()->create([
            'requester_id' => $owner->id,
            'status'       => Need::STATUS_OPEN,
            'title'        => 'Test Need',
            'description'  => 'Need a service',
            'currency'     => 'ETB',
        ]);

        $offers = [];
        for ($i = 0; $i < $count; $i++) {
            $provider = $this->user();
            $offers[] = Offer::factory()->create([
                'need_id'        => $need->id,
                'provider_id'    => $provider->id,
                'status'         => 'PENDING',
                'offered_price'  => 100 + ($i * 50),
                'currency'       => 'ETB',
                'proposal_message' => 'Offer ' . ($i + 1) . ' details',
            ]);
        }

        return [$need, $offers];
    }

    private function fakeGeminiSuccess(int $offerCount): void
    {
        $scores = [];
        for ($i = 0; $i < $offerCount; $i++) {
            $scores[] = [
                'offer_index' => $i,
                'total_score' => 80 - ($i * 5),
                'criteria' => [
                    'price'         => 75,
                    'delivery_time' => 80,
                    'quality'       => 85,
                    'reliability'   => 80,
                ],
                'rationale'            => 'Offer ' . ($i + 1) . ' evaluation',
                'strengths'            => ['Good price'],
                'weaknesses'           => [],
                'missing_information'  => [],
                'risk_notes'           => [],
            ];
        }

        $body = [
            'candidates' => [[
                'content' => [
                    'parts' => [[
                        'text' => json_encode(['scores' => $scores]),
                    ]],
                ],
            ]],
            'usageMetadata' => [
                'promptTokenCount'     => 120,
                'candidatesTokenCount' => 80,
                'totalTokenCount'      => 200,
            ],
        ];

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response($body, 200),
        ]);
    }

    // ─── Auth ───

    public function test_store_requires_auth(): void
    {
        $need = Need::factory()->create();
        $this->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(401);
    }

    public function test_store_forbidden_for_non_owner(): void
    {
        $owner = $this->user();
        $stranger = $this->user();
        [$need] = $this->needWithOffers($owner);

        $this->actingAs($stranger, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(403);
    }

    // ─── Preconditions ───

    public function test_store_404_for_unknown_need(): void
    {
        $user = $this->user();
        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/needs/nonexistent-id/comparisons")
            ->assertStatus(404);
    }

    public function test_store_409_when_need_not_open(): void
    {
        $owner = $this->user();
        $need = Need::factory()->create([
            'requester_id' => $owner->id,
            'status'       => 'COMPLETED',
        ]);

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(409);
    }

    public function test_store_422_when_no_offers(): void
    {
        $owner = $this->user();
        $need = Need::factory()->create([
            'requester_id' => $owner->id,
            'status'       => Need::STATUS_OPEN,
        ]);

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(422);
    }

    // ─── Successful comparison ───

    public function test_store_runs_ai_comparison_successfully(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);
        $this->fakeGeminiSuccess(2);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons");

        $res->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.offers_evaluated', 2)
            ->assertJsonStructure([
                'data' => ['comparison_id', 'version_number', 'offers_evaluated'],
            ]);
    }

    public function test_comparison_persisted_with_correct_schema(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);
        $this->fakeGeminiSuccess(2);

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(201);

        $comparison = Comparison::where('need_id', $need->id)->first();
        $this->assertNotNull($comparison);
        $this->assertEquals('COMPLETED', $comparison->status);
        $this->assertEquals(2, $comparison->eligible_offer_count);
        $this->assertEquals(2, $comparison->included_offer_count);
        $this->assertEquals('gemini', $comparison->ai_provider);
        $this->assertEquals($owner->id, $comparison->triggered_by);
        $this->assertNotNull($comparison->need_snapshot);
        $this->assertNotNull($comparison->snapshot_hash);
        $this->assertNotNull($comparison->completed_at);
    }

    public function test_comparison_offers_snapshot_created(): void
    {
        $owner = $this->user();
        [$need, $offers] = $this->needWithOffers($owner, 2);
        $this->fakeGeminiSuccess(2);

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(201);

        $comparison = Comparison::where('need_id', $need->id)->first();
        $this->assertEquals(2, ComparisonOffer::where('comparison_id', $comparison->id)->count());
    }

    public function test_comparison_results_created_with_scores(): void
    {
        $owner = $this->user();
        [$need, $offers] = $this->needWithOffers($owner, 2);
        $this->fakeGeminiSuccess(2);

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(201);

        $comparison = Comparison::where('need_id', $need->id)->first();
        $results = ComparisonResult::where('comparison_id', $comparison->id)->get();
        $this->assertCount(2, $results);

        foreach ($results as $r) {
            $this->assertGreaterThan(0, (float) $r->score);
            $this->assertIsArray($r->criterion_scores);
            $this->assertArrayHasKey('price', $r->criterion_scores);
            $this->assertNotNull($r->comparison_offer_id);
            $this->assertNotNull($r->fit_explanation);
        }
    }

    // ─── AI failure handling (rule C20) ───

    public function test_store_returns_503_on_ai_http_error(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response([
                'error' => ['code' => 500, 'message' => 'Internal error'],
            ], 500),
        ]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons");

        $res->assertStatus(503)
            ->assertJsonPath('error.code', 'AI_COMPARISON_FAILED');
    }

    public function test_no_comparison_persisted_on_ai_failure(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response(['error' => ['message' => 'fail']], 500),
        ]);

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(503);

        $this->assertEquals(0, Comparison::where('need_id', $need->id)->count());
    }

    public function test_store_returns_503_on_malformed_ai_response(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'not json at all']]],
                ]],
                'usageMetadata' => [],
            ], 200),
        ]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons");

        $res->assertStatus(503);
        $this->assertEquals(0, Comparison::where('need_id', $need->id)->count());
    }

    public function test_store_returns_503_on_schema_mismatch(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 3); // 3 offers

        // Return only 2 scores (schema expects 3)
        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => json_encode([
                        'scores' => [
                            ['offer_index' => 0, 'total_score' => 80, 'criteria' => ['price' => 80, 'delivery_time' => 80, 'quality' => 80, 'reliability' => 80], 'rationale' => 'a'],
                            ['offer_index' => 1, 'total_score' => 70, 'criteria' => ['price' => 70, 'delivery_time' => 70, 'quality' => 70, 'reliability' => 70], 'rationale' => 'b'],
                        ],
                    ])]]],
                ]],
                'usageMetadata' => [],
            ], 200),
        ]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons");

        $res->assertStatus(503);
    }

    // ─── Version increment ───

    public function test_second_comparison_increments_version(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);
        $this->fakeGeminiSuccess(2);

        // First
        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(201);

        // Second
        $this->fakeGeminiSuccess(2);
        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(201);

        $versions = Comparison::where('need_id', $need->id)
            ->orderBy('version_number')
            ->pluck('version_number')
            ->toArray();

        $this->assertEquals([1, 2], $versions);
    }

    // ─── History + show ───

    public function test_index_returns_comparison_history_for_owner(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);
        $this->fakeGeminiSuccess(2);

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(201);

        $res = $this->actingAs($owner, 'sanctum')
            ->getJson("/api/v1/needs/{$need->id}/comparisons");

        $res->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_show_returns_comparison_for_owner(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);
        $this->fakeGeminiSuccess(2);

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(201);

        $comparison = Comparison::where('need_id', $need->id)->first();

        $this->actingAs($owner, 'sanctum')
            ->getJson("/api/v1/comparisons/{$comparison->id}")
            ->assertStatus(200);
    }

    public function test_show_404_for_unrelated_user(): void
    {
        $owner = $this->user();
        $stranger = $this->user();
        [$need] = $this->needWithOffers($owner, 2);
        $this->fakeGeminiSuccess(2);

        $this->actingAs($owner, 'sanctum')
            ->postJson("/api/v1/needs/{$need->id}/comparisons")
            ->assertStatus(201);

        $comparison = Comparison::where('need_id', $need->id)->first();

        $this->actingAs($stranger, 'sanctum')
            ->getJson("/api/v1/comparisons/{$comparison->id}")
            ->assertStatus(404);
    }
}
