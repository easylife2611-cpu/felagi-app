<?php

namespace App\Services\AI;

use App\Models\Comparison;
use App\Models\ComparisonOffer;
use App\Models\ComparisonResult;
use App\Models\Need;
use App\Models\Offer;
use App\Services\AI\ContradictionDetector;
use App\Services\AI\CompletenessEvaluator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * WP-10 — AI-powered offer comparison.
 *
 * 4 canonical criteria (from AI_Evaluation_Contract.md):
 *   price (0.25), delivery_time (0.25), quality (0.25), reliability (0.25)
 *
 * Rule C04: AI analyzes, never decides.
 * Rule C20: AI failure creates no fake result.
 *
 * Writes to existing schema:
 *   - comparisons: triggered_by, criteria_version, prompt_version, ai_provider,
 *     model_id, need_snapshot, snapshot_hash, eligible/included counts,
 *     input/output token counts, estimated_cost, status, completed_at
 *   - comparison_offers: offer_id, provider_id, offer_snapshot, offer_snapshot_hash
 *   - comparison_results: comparison_offer_id, score, criterion_scores,
 *     strengths/weaknesses/missing_information/risk_notes, fit_explanation, result_hash
 */
class ComparisonService
{
    public const CRITERIA_VERSION = '1.0';
    public const PROMPT_VERSION = '1.0';
    public const OUTPUT_SCHEMA_VERSION = '1.0';

    /** Canonical criteria — LOCKED by AI_Evaluation_Contract.md */
    public const CRITERIA = [
        'price'         => ['weight' => 0.25, 'label' => 'Price'],
        'delivery_time' => ['weight' => 0.25, 'label' => 'Delivery time'],
        'quality'       => ['weight' => 0.25, 'label' => 'Quality'],
        'reliability'   => ['weight' => 0.25, 'label' => 'Reliability'],
    ];

    public function __construct(
        private readonly GeminiClient $client,
        private readonly ContradictionDetector $contradictionDetector = new ContradictionDetector(),
        private readonly CompletenessEvaluator $completenessEvaluator = new CompletenessEvaluator(),
    ) {}

    /**
     * Evaluate all PENDING offers for a Need.
     *
     * @return array{comparison_id: string, version_number: int, offers_evaluated: int}
     */
    public function evaluate(Need $need, ?string $userId = null): array
    {
        $offers = Offer::where('need_id', $need->id)
            ->where('status', 'PENDING')
            ->orderBy('created_at', 'asc')
            ->limit((int) config('ai.comparison.max_offers', 20))
            ->get();

        if ($offers->isEmpty()) {
            throw new \RuntimeException('No eligible offers to compare.');
        }

        $eligibleCount = $offers->count();

        $offerPayloads = $offers->map(fn (Offer $o, int $i) => [
            'index'              => $i,
            'offered_price'      => $o->offered_price,
            'currency'           => $o->currency,
            'proposal_message'   => $o->proposal_message,
            'delivery_time_text' => $o->delivery_time_text,
            'availability_text'  => $o->availability_text,
        ])->values()->all();

        $needSnapshot = [
            'id'          => $need->id,
            'title'       => $need->title,
            'description' => $need->description,
            'budget_min'  => $need->budget_min ?? null,
            'budget_max'  => $need->budget_max ?? null,
            'currency'    => $need->currency ?? 'ETB',
            'category_id' => $need->category_id ?? null,
        ];

        // AI-11: deterministic pre-check (advisory only — never blocks)
        $contradictions = $this->contradictionDetector->detect($need, $offers);
        $contradictionSummary = $this->contradictionDetector->summarise($contradictions);

        $startedAt = now();

        $result = $this->client->generateStructured(
            $this->responseSchema(),
            [[
                'role'  => 'user',
                'parts' => [['text' => $this->buildPrompt($needSnapshot, $offerPayloads)]],
            ]],
            $this->systemInstruction()
        );

        if ($result['error'] !== null) {
            Log::error('Comparison AI failed', [
                'need_id' => $need->id,
                'error'   => $result['error'],
            ]);
            throw new \RuntimeException('AI comparison failed: ' . $result['error']);
        }

        $payload = $result['json'];
        if (!is_array($payload) || !isset($payload['scores']) || !is_array($payload['scores'])) {
            throw new \RuntimeException('AI returned invalid payload shape.');
        }

        $this->validatePayload($payload, $eligibleCount);

        $usage = $result['usage'];

        return DB::transaction(function () use ($need, $offers, $payload, $userId, $usage, $startedAt, $eligibleCount, $needSnapshot) {
            $version = (int) (Comparison::where('need_id', $need->id)->max('version_number') ?? 0) + 1;

            // AI-11: persist the findings inside the need_snapshot (additive;
            // does not require a migration). The snapshot_hash stays the same
            // because it was computed before — findings are post-hoc advisory.
            $comparison = Comparison::create([
                'need_id'                 => $need->id,
                'version_number'          => $version,
                'triggered_by'            => $userId,
                'status'                  => Comparison::STATUS_COMPLETED,
                'criteria_version'        => self::CRITERIA_VERSION,
                'prompt_version'          => self::PROMPT_VERSION,
                'output_schema_version'   => self::OUTPUT_SCHEMA_VERSION,
                'ai_provider'             => config('ai.provider', 'gemini'),
                'model_id'                => config('ai.gemini.model', 'gemini-flash-latest'),
                'need_snapshot'           => $needSnapshot,
                'snapshot_hash'           => hash('sha256', json_encode($needSnapshot)),
                'eligible_offer_count'    => $eligibleCount,
                'included_offer_count'    => $eligibleCount,
                'input_token_count'       => $usage['promptTokenCount'] ?? null,
                'output_token_count'      => $usage['candidatesTokenCount'] ?? null,
                'estimated_cost'          => null, // TODO: compute from pricing table
                'attempt_count'           => 1,
                'requested_at'            => $startedAt,
                'started_at'              => $startedAt,
                'completed_at'            => now(),
            ]);

            // Snapshot offers → comparison_offers
            $comparisonOffersByIndex = [];
            foreach ($offers as $i => $offer) {
                $snapshot = [
                    'offered_price'      => $offer->offered_price,
                    'currency'           => $offer->currency,
                    'proposal_message'   => $offer->proposal_message,
                    'delivery_time_text' => $offer->delivery_time_text,
                    'availability_text'  => $offer->availability_text,
                ];

                $co = ComparisonOffer::create([
                    'comparison_id'       => $comparison->id,
                    'offer_id'            => $offer->id,
                    'provider_id'         => $offer->provider_id,
                    'offer_snapshot'      => $snapshot,
                    'credibility_snapshot'=> [],
                    'offer_snapshot_hash' => hash('sha256', json_encode($snapshot)),
                ]);

                $comparisonOffersByIndex[$i] = $co;
            }

            // Persist scores → comparison_results
            foreach ($payload['scores'] as $scoreRow) {
                $idx = $scoreRow['offer_index'] ?? null;
                if ($idx === null || !isset($comparisonOffersByIndex[$idx])) {
                    continue;
                }

                $co = $comparisonOffersByIndex[$idx];

                $criterionScores = $scoreRow['criteria'] ?? [];
                $fitExplanation = $scoreRow['rationale'] ?? '';

                $resultPayload = [
                    'score'               => $scoreRow['total_score'] ?? 0,
                    'criterion_scores'    => $criterionScores,
                    'strengths'           => $scoreRow['strengths'] ?? [],
                    'weaknesses'          => $scoreRow['weaknesses'] ?? [],
                    'missing_information' => $scoreRow['missing_information'] ?? [],
                    'risk_notes'          => $scoreRow['risk_notes'] ?? [],
                    'fit_explanation'     => $fitExplanation,
                ];

                $completeness = $this->completenessEvaluator->evaluate($scoreRow);

                ComparisonResult::create([
                    'comparison_id'       => $comparison->id,
                    'comparison_offer_id' => $co->id,
                    'score'               => $resultPayload['score'],
                    'criterion_scores'    => $resultPayload['criterion_scores'],
                    'strengths'           => $resultPayload['strengths'],
                    'weaknesses'          => $resultPayload['weaknesses'],
                    'missing_information' => $resultPayload['missing_information'],
                    'risk_notes'          => $resultPayload['risk_notes'],
                    'fit_explanation'     => $resultPayload['fit_explanation'],
                    'completeness'        => $completeness['completeness'],
                    'missing_criteria'    => $completeness['missing_criteria'],
                    'uncertain_criteria'  => $completeness['uncertain_criteria'],
                    'result_hash'         => hash('sha256', json_encode($resultPayload)),
                    'created_at'          => now(),
                ]);
            }

            return [
                'comparison_id'      => $comparison->id,
                'version_number'     => $version,
                'offers_evaluated'   => $eligibleCount,
                'contradictions'     => $contradictions ?? [],
                'contradiction_summary' => $contradictionSummary ?? ['total' => 0],
            ];
        });
    }

    /**
     * JSON schema for Gemini structured output.
     */
    private function responseSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'scores' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'offer_index' => ['type' => 'integer'],
                            'total_score' => ['type' => 'number'],
                            'criteria' => [
                                'type' => 'object',
                                'properties' => [
                                    'price'         => ['type' => 'number'],
                                    'delivery_time' => ['type' => 'number'],
                                    'quality'       => ['type' => 'number'],
                                    'reliability'   => ['type' => 'number'],
                                ],
                                'required' => ['price', 'delivery_time', 'quality', 'reliability'],
                            ],
                            'rationale' => ['type' => 'string'],
                            'strengths' => ['type' => 'array', 'items' => ['type' => 'string']],
                            'weaknesses' => ['type' => 'array', 'items' => ['type' => 'string']],
                            'missing_information' => ['type' => 'array', 'items' => ['type' => 'string']],
                            'risk_notes' => ['type' => 'array', 'items' => ['type' => 'string']],
                        ],
                        'required' => ['offer_index', 'total_score', 'criteria', 'rationale'],
                    ],
                ],
            ],
            'required' => ['scores'],
        ];
    }

    private function systemInstruction(): string
    {
        return 'You are an advisory AI comparing service offers for a marketplace. '
            . 'Rules: (1) You MUST analyze — you MUST NOT decide; output is advisory only. '
            . '(2) Score each offer on four criteria: price, delivery_time, quality, reliability. '
            . '(3) Each criterion score is 0-100 (higher = better for requester). '
            . '(4) total_score = price*0.25 + delivery_time*0.25 + quality*0.25 + reliability*0.25. '
            . '(5) If a fact is unknown, state so in missing_information — do NOT invent facts. '
            . '(6) Never reference competitor identity; use offer_index only. '
            . '(7) Respond ONLY in the JSON schema. No prose, no markdown. '
            . '(8) Use evidence from proposal text; be conservative on unknown data.';
    }

    private function buildPrompt(array $need, array $offers): string
    {
        $lines = [];
        $lines[] = 'NEED:';
        $lines[] = '  Title: ' . ($need['title'] ?? '');
        $lines[] = '  Description: ' . ($need['description'] ?? '');
        if (isset($need['budget_min']) || isset($need['budget_max'])) {
            $lines[] = '  Budget: ' . ($need['budget_min'] ?? '?')
                . ' - ' . ($need['budget_max'] ?? '?')
                . ' ' . ($need['currency'] ?? 'ETB');
        }
        $lines[] = '';
        $lines[] = 'OFFERS (evaluate ALL, respond with scores for EACH):';
        foreach ($offers as $o) {
            $lines[] = '--- offer_index=' . $o['index'] . ' ---';
            $lines[] = '  offered_price: ' . ($o['offered_price'] ?? 'unknown') . ' ' . ($o['currency'] ?? '');
            $lines[] = '  delivery_time_text: ' . ($o['delivery_time_text'] ?? 'unknown');
            $lines[] = '  availability_text: ' . ($o['availability_text'] ?? 'unknown');
            $lines[] = '  proposal_message: ' . ($o['proposal_message'] ?? '');
            $lines[] = '';
        }
        $lines[] = 'Return scores for offer_index in 0..' . (count($offers) - 1) . '.';
        return implode("\n", $lines);
    }

    /**
     * Schema gate — enforce C20 ("AI failure creates no fake result").
     */
    private function validatePayload(array $payload, int $expectedCount): void
    {
        $scores = $payload['scores'];
        if (count($scores) !== $expectedCount) {
            throw new \RuntimeException(
                "Schema validation failed: expected {$expectedCount} scores, got " . count($scores)
            );
        }

        foreach ($scores as $row) {
            foreach (['offer_index', 'total_score', 'criteria', 'rationale'] as $key) {
                if (!array_key_exists($key, $row)) {
                    throw new \RuntimeException("Schema validation failed: missing {$key}");
                }
            }
            foreach (array_keys(self::CRITERIA) as $c) {
                if (!array_key_exists($c, $row['criteria'])) {
                    throw new \RuntimeException("Schema validation failed: missing criteria.{$c}");
                }
                $v = $row['criteria'][$c];
                if (!is_numeric($v) || $v < 0 || $v > 100) {
                    throw new \RuntimeException("Schema validation failed: criteria.{$c} out of range");
                }
            }
        }
    }
}
