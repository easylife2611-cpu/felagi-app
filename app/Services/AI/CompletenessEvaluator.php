<?php

namespace App\Services\AI;

/**
 * AI-35 — Partial / uncertain result rules.
 *
 * Deterministic evaluation of how complete an AI score row is.
 * Does NOT block anything — it annotates results so the UI can
 * present PARTIAL / UNCERTAIN states explicitly.
 *
 * Rules (advisory only, per AI_Evaluation_Contract.md C04):
 *   COMPLETE  — all 4 criteria present with rationale and no missing info
 *   PARTIAL   — 1-2 criteria low/uncertain OR missing_information 1-2
 *   UNCERTAIN — 3+ criteria low/uncertain OR missing_information 3+
 *
 * "Low" = criterion value < LOW_THRESHOLD or explicitly flagged in
 * the AI's `missing_information` list.
 */
class CompletenessEvaluator
{
    public const COMPLETE  = 'COMPLETE';
    public const PARTIAL   = 'PARTIAL';
    public const UNCERTAIN = 'UNCERTAIN';

    public const LOW_THRESHOLD = 20;

    /**
     * @return array{
     *   completeness:string,
     *   missing_criteria:array<int,string>,
     *   uncertain_criteria:array<int,string>
     * }
     */
    public function evaluate(array $scoreRow): array
    {
        $criteria = $scoreRow['criteria'] ?? [];
        $missing  = $scoreRow['missing_information'] ?? [];
        $rationale = trim((string) ($scoreRow['rationale'] ?? ''));

        $missingCriteria   = [];
        $uncertainCriteria = [];

        foreach (ComparisonService::CRITERIA as $key => $meta) {
            if (! array_key_exists($key, $criteria) || ! is_numeric($criteria[$key])) {
                $missingCriteria[] = $key;
                continue;
            }
            if ((float) $criteria[$key] < self::LOW_THRESHOLD) {
                $uncertainCriteria[] = $key;
            }
        }

        $signals = count($missingCriteria) + count($uncertainCriteria)
                 + (is_array($missing) ? count($missing) : 0)
                 + (empty($rationale) ? 1 : 0);

        $completeness = match (true) {
            $signals === 0                                                          => self::COMPLETE,
            // 3+ criteria missing OR low OR total signals very high → UNCERTAIN
            count($missingCriteria) >= 3                                            => self::UNCERTAIN,
            count($uncertainCriteria) >= 3                                          => self::UNCERTAIN,
            $signals >= 5                                                           => self::UNCERTAIN,
            // 1+ signals but not enough for UNCERTAIN → PARTIAL
            $signals >= 1                                                           => self::PARTIAL,
            default                                                                 => self::COMPLETE,
        };

        return [
            'completeness'       => $completeness,
            'missing_criteria'   => $missingCriteria,
            'uncertain_criteria' => $uncertainCriteria,
        ];
    }
}
