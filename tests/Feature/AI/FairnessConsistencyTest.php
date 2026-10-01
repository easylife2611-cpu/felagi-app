<?php

namespace Tests\Feature\AI;

use App\Models\Comparison;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use App\Services\AI\ComparisonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * AI-47 — Fairness consistency.
 *
 * Provider-blind, criteria-stable, schema-gated.
 */
class FairnessConsistencyTest extends TestCase
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

    private function needWithOffers(User $owner, int $count = 3): array
    {
        $need = Need::factory()->create([
            'requester_id' => $owner->id,
            'status'       => Need::STATUS_OPEN,
            'title'        => 'Fairness Need',
            'description'  => 'Need a service',
            'currency'     => 'ETB',
            'budget_min'   => 100,
            'budget_max'   => 500,
        ]);

        $offers = [];
        for ($i = 0; $i < $count; $i++) {
            $offers[] = Offer::factory()->create([
                'need_id'          => $need->id,
                'provider_id'      => $this->user()->id,
                'status'           => 'PENDING',
                'offered_price'    => 200 + ($i * 50),
                'currency'         => 'ETB',
                'proposal_message' => 'Offer ' . ($i + 1),
                'delivery_time_text' => '3 days',
                'availability_text'  => 'immediate',
            ]);
        }

        return [$need, $offers];
    }

    /**
     * Build a canonical AI response text for N offers.
     */
    private function aiResponseText(int $count, array $criteriaOverride = []): string
    {
        $scores = [];
        for ($i = 0; $i < $count; $i++) {
            $criteria = array_merge([
                'price'         => 80,
                'delivery_time' => 80,
                'quality'       => 80,
                'reliability'   => 80,
            ], $criteriaOverride);

            $scores[] = [
                'offer_index' => $i,
                'total_score' => 80,
                'criteria'    => $criteria,
                'rationale'   => 'ok',
                'strengths'   => [],
                'weaknesses'  => [],
                'missing_information' => [],
                'risk_notes'  => [],
            ];
        }
        return json_encode(['scores' => $scores]);
    }

    /**
     * Fake Gemini response and capture the outgoing request body.
     */
    private function fakeGemini(string $responseText, array &$captured = []): void
    {
        Http::fake(function ($request) use ($responseText, &$captured) {
            $captured['body']    = (string) $request->body();
            $captured['headers'] = $request->headers();

            return Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => $responseText]],
                    ],
                ]],
                'usageMetadata' => [
                    'promptTokenCount'     => 100,
                    'candidatesTokenCount' => 80,
                ],
            ], 200);
        });
    }

    // ─── Provider blindness ───

    public function test_prompt_does_not_contain_provider_id(): void
    {
        $owner = $this->user();
        [$need, $offers] = $this->needWithOffers($owner, 3);

        $captured = [];
        $this->fakeGemini($this->aiResponseText(3), $captured);

        app(ComparisonService::class)->evaluate($need, $owner->id);

        $body = $captured['body'] ?? '';
        foreach ($offers as $o) {
            $this->assertStringNotContainsString(
                $o->provider_id,
                $body,
                'Prompt leaked provider_id ' . $o->provider_id
            );
        }
    }

    public function test_prompt_uses_offer_index(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 3);

        $captured = [];
        $this->fakeGemini($this->aiResponseText(3), $captured);

        app(ComparisonService::class)->evaluate($need, $owner->id);

        $body = $captured['body'] ?? '';
        $this->assertStringContainsString('offer_index=0', $body);
        $this->assertStringContainsString('offer_index=1', $body);
        $this->assertStringContainsString('offer_index=2', $body);
    }

    public function test_system_instruction_forbids_identity_reference(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);

        $captured = [];
        $this->fakeGemini($this->aiResponseText(2), $captured);

        app(ComparisonService::class)->evaluate($need, $owner->id);

        $body = $captured['body'] ?? '';
        $this->assertStringContainsString('Never reference competitor identity', $body);
    }

    // ─── Criteria stability ───

    public function test_criteria_weights_sum_to_one(): void
    {
        $sum = 0.0;
        foreach (ComparisonService::CRITERIA as $c) {
            $sum += $c['weight'];
        }
        $this->assertEqualsWithDelta(1.0, $sum, 0.0001);
    }

    public function test_criteria_are_canonical_four(): void
    {
        $this->assertSame(
            ['price', 'delivery_time', 'quality', 'reliability'],
            array_keys(ComparisonService::CRITERIA)
        );
    }

    public function test_prompt_states_criteria_weights(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);

        $captured = [];
        $this->fakeGemini($this->aiResponseText(2), $captured);

        app(ComparisonService::class)->evaluate($need, $owner->id);

        $body = $captured['body'] ?? '';
        $this->assertStringContainsString('0.30', $body);
        $this->assertStringContainsString('0.25', $body);
        $this->assertStringContainsString('0.15', $body);
    }

    // ─── Schema gate ───

    public function test_rejects_wrong_score_count(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 3);

        // 3 offers → return only 2 scores
        $this->fakeGemini($this->aiResponseText(2));

        $this->expectException(\RuntimeException::class);
        app(ComparisonService::class)->evaluate($need, $owner->id);
    }

    public function test_rejects_out_of_range_criterion(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 1);

        $this->fakeGemini($this->aiResponseText(1, ['price' => 150]));

        $this->expectException(\RuntimeException::class);
        app(ComparisonService::class)->evaluate($need, $owner->id);
    }

    public function test_rejects_missing_criterion(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 1);

        // Remove quality
        $txt = json_encode([
            'scores' => [[
                'offer_index' => 0,
                'total_score' => 80,
                'criteria'    => [
                    'price'         => 80,
                    'delivery_time' => 80,
                    'reliability'   => 80,
                ],
                'rationale' => 'a',
            ]],
        ]);
        $this->fakeGemini($txt);

        $this->expectException(\RuntimeException::class);
        app(ComparisonService::class)->evaluate($need, $owner->id);
    }

    // ─── Determinism ───

    public function test_identical_offers_share_criteria_shape(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 3);

        $captured = [];
        $this->fakeGemini($this->aiResponseText(3), $captured);

        app(ComparisonService::class)->evaluate($need, $owner->id);

        $comparison = Comparison::where('need_id', $need->id)->first();
        $this->assertNotNull($comparison);
        $this->assertSame(3, $comparison->results()->count());

        $canonical = ['price', 'delivery_time', 'quality', 'reliability'];
        foreach ($comparison->results as $r) {
            $this->assertSame($canonical, array_keys($r->criterion_scores));
        }
    }

    public function test_version_number_is_monotonic(): void
    {
        $owner = $this->user();
        [$need] = $this->needWithOffers($owner, 2);

        $this->fakeGemini($this->aiResponseText(2));
        app(ComparisonService::class)->evaluate($need, $owner->id);

        $this->fakeGemini($this->aiResponseText(2));
        app(ComparisonService::class)->evaluate($need, $owner->id);

        $versions = Comparison::where('need_id', $need->id)
            ->orderBy('version_number')
            ->pluck('version_number')
            ->toArray();

        $this->assertSame([1, 2], $versions);
    }
}
