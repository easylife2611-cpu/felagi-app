<?php

declare(strict_types=1);

namespace Tests\Feature\Services\AI;

use App\Models\Category;
use App\Models\Comparison;
use App\Models\ComparisonOffer;
use App\Models\ComparisonResult;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use App\Services\AI\ComparisonService;
use App\Services\AI\NullGeminiClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

final class ComparisonServiceTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private User $owner;
    private Need $need;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::factory()->create();
        $this->category = Category::create([
            'id' => (string) Str::uuid(), 'slug' => 'cs-cat',
            'name_am' => 'ሙከራ', 'name_en' => 'Test',
            'active' => true, 'sort_order' => 1,
        ]);
        $this->need = Need::create([
            'id' => (string) Str::uuid(),
            'requester_id' => $this->owner->id,
            'category_id' => $this->category->id,
            'title' => 'CS test need',
            'description' => 'Test need for ComparisonService.',
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

    /**
     * Stub client that returns a fixed, valid AI payload.
     * Bypasses GeminiClient's network path entirely.
     */
    private function stubClient(array $scores): NullGeminiClient
    {
        return new class($scores) extends NullGeminiClient {
            public function __construct(private array $fixedScores) {}

            public function generateStructured(
                array $responseSchema,
                array $contents,
                ?string $systemInstruction = null,
                array $safetySettings = [],
            ): array {
                return [
                    'json'  => ['scores' => $this->fixedScores],
                    'raw'   => json_encode(['scores' => $this->fixedScores]),
                    'usage' => ['promptTokenCount' => 10, 'candidatesTokenCount' => 20],
                    'error' => null,
                ];
            }
        };
    }

    private function validScoreRow(int $index = 0): array
    {
        return [
            'offer_index' => $index,
            'total_score' => 80,
            'criteria' => [
                'price' => 80, 'delivery_time' => 75,
                'quality' => 85, 'reliability' => 90,
            ],
            'rationale' => 'Solid offer.',
            'strengths' => ['Fast delivery'],
            'weaknesses' => [],
            'missing_information' => [],
            'risk_notes' => [],
        ];
    }

    // ─────────────────────────────────────────
    // Constants
    // ─────────────────────────────────────────

    public function test_criteria_has_four_keys_with_quarter_weights(): void
    {
        $this->assertCount(4, ComparisonService::CRITERIA);
        foreach (ComparisonService::CRITERIA as $meta) {
            $this->assertSame(0.25, $meta['weight']);
        }
    }

    public function test_versions_are_declared(): void
    {
        $this->assertSame('1.0', ComparisonService::CRITERIA_VERSION);
        $this->assertSame('1.0', ComparisonService::PROMPT_VERSION);
        $this->assertSame('1.0', ComparisonService::OUTPUT_SCHEMA_VERSION);
    }

    // ─────────────────────────────────────────
    // No offers
    // ─────────────────────────────────────────

    public function test_evaluate_throws_when_no_offers(): void
    {
        $svc = new ComparisonService($this->stubClient([]));
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No eligible offers');
        $svc->evaluate($this->need);
    }

    // ─────────────────────────────────────────
    // AI error path
    // ─────────────────────────────────────────

    public function test_evaluate_throws_when_ai_returns_error(): void
    {
        $this->makeOffer();

        $client = new class extends NullGeminiClient {
            public function generateStructured(array $s, array $c, ?string $i = null, array $sa = []): array {
                return ['json' => null, 'raw' => '', 'usage' => [], 'error' => 'HTTP 503: unavailable'];
            }
        };

        $svc = new ComparisonService($client);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('AI comparison failed');
        $svc->evaluate($this->need);
    }

    // ─────────────────────────────────────────
    // Invalid payload
    // ─────────────────────────────────────────

    public function test_evaluate_throws_when_payload_missing_scores(): void
    {
        $this->makeOffer();

        $client = new class extends NullGeminiClient {
            public function generateStructured(array $s, array $c, ?string $i = null, array $sa = []): array {
                return ['json' => ['foo' => 'bar'], 'raw' => '', 'usage' => [], 'error' => null];
            }
        };

        $svc = new ComparisonService($client);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('invalid payload shape');
        $svc->evaluate($this->need);
    }

    public function test_evaluate_throws_when_score_count_mismatches(): void
    {
        $this->makeOffer();
        $this->makeOffer();

        // Only 1 score for 2 offers
        $svc = new ComparisonService($this->stubClient([$this->validScoreRow(0)]));
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('expected 2 scores');
        $svc->evaluate($this->need);
    }

    public function test_evaluate_throws_when_criterion_out_of_range(): void
    {
        $this->makeOffer();
        $bad = $this->validScoreRow(0);
        $bad['criteria']['price'] = 999;  // > 100

        $svc = new ComparisonService($this->stubClient([$bad]));
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('out of range');
        $svc->evaluate($this->need);
    }

    // ─────────────────────────────────────────
    // Happy path
    // ─────────────────────────────────────────

    public function test_evaluate_persists_comparison_offers_and_results(): void
    {
        $this->makeOffer();

        $svc = new ComparisonService($this->stubClient([$this->validScoreRow(0)]));
        $result = $svc->evaluate($this->need, $this->owner->id);

        $this->assertArrayHasKey('comparison_id', $result);
        $this->assertSame(1, $result['version_number']);
        $this->assertSame(1, $result['offers_evaluated']);

        $this->assertSame(1, Comparison::count());
        $this->assertSame(1, ComparisonOffer::count());
        $this->assertSame(1, ComparisonResult::count());
    }

    public function test_evaluate_version_increments_on_second_run(): void
    {
        $offer = $this->makeOffer();

        $svc = new ComparisonService($this->stubClient([$this->validScoreRow(0)]));
        $first = $svc->evaluate($this->need, $this->owner->id);
        $second = $svc->evaluate($this->need, $this->owner->id);

        $this->assertSame(1, $first['version_number']);
        $this->assertSame(2, $second['version_number']);
    }

    public function test_evaluate_persists_snapshot_hash(): void
    {
        $this->makeOffer();

        $svc = new ComparisonService($this->stubClient([$this->validScoreRow(0)]));
        $result = $svc->evaluate($this->need, $this->owner->id);

        $comparison = Comparison::find($result['comparison_id']);
        $this->assertNotNull($comparison->snapshot_hash);
        $this->assertSame(64, strlen($comparison->snapshot_hash));
    }

    public function test_evaluate_stores_usage_tokens(): void
    {
        $this->makeOffer();

        $svc = new ComparisonService($this->stubClient([$this->validScoreRow(0)]));
        $result = $svc->evaluate($this->need, $this->owner->id);

        $comparison = Comparison::find($result['comparison_id']);
        $this->assertSame(10, $comparison->input_token_count);
        $this->assertSame(20, $comparison->output_token_count);
    }

    public function test_evaluate_persists_criteria_versions(): void
    {
        $this->makeOffer();

        $svc = new ComparisonService($this->stubClient([$this->validScoreRow(0)]));
        $result = $svc->evaluate($this->need, $this->owner->id);

        $comparison = Comparison::find($result['comparison_id']);
        $this->assertSame(ComparisonService::CRITERIA_VERSION, $comparison->criteria_version);
        $this->assertSame(ComparisonService::PROMPT_VERSION, $comparison->prompt_version);
        $this->assertSame(ComparisonService::OUTPUT_SCHEMA_VERSION, $comparison->output_schema_version);
    }

    public function test_evaluate_returns_contradiction_summary(): void
    {
        // Offer above budget_max → contradiction present
        $this->makeOffer(['offered_price' => 99999]);

        $svc = new ComparisonService($this->stubClient([$this->validScoreRow(0)]));
        $result = $svc->evaluate($this->need, $this->owner->id);

        // Keys exist in the result envelope
        $this->assertArrayHasKey('contradictions', $result);
        $this->assertArrayHasKey('contradiction_summary', $result);

        // Real contradiction (price 99999 > budget_max 5000) must reach caller
        $this->assertGreaterThan(0, $result['contradiction_summary']['total']);
        $this->assertNotEmpty($result['contradictions']);
    }

    public function test_evaluate_stores_result_hash(): void
    {
        $this->makeOffer();

        $svc = new ComparisonService($this->stubClient([$this->validScoreRow(0)]));
        $svc->evaluate($this->need, $this->owner->id);

        $cr = ComparisonResult::first();
        $this->assertNotNull($cr->result_hash);
        $this->assertSame(64, strlen($cr->result_hash));
    }

    public function test_evaluate_status_is_completed(): void
    {
        $this->makeOffer();

        $svc = new ComparisonService($this->stubClient([$this->validScoreRow(0)]));
        $result = $svc->evaluate($this->need, $this->owner->id);

        $comparison = Comparison::find($result['comparison_id']);
        $this->assertSame(Comparison::STATUS_COMPLETED, $comparison->status);
    }
}
