<?php

namespace Tests\Feature\AI;

use App\Services\AI\CompletenessEvaluator;
use Tests\TestCase;

/**
 * AI-35 — Partial / uncertain result rules.
 */
class CompletenessEvaluatorTest extends TestCase
{
    private function eval(): CompletenessEvaluator
    {
        return new CompletenessEvaluator();
    }

    public function test_complete_when_all_criteria_and_rationale_present(): void
    {
        $r = $this->eval()->evaluate([
            'criteria' => ['price' => 80, 'delivery_time' => 80, 'quality' => 80, 'reliability' => 80],
            'missing_information' => [],
            'rationale' => 'All good',
        ]);
        $this->assertSame(CompletenessEvaluator::COMPLETE, $r['completeness']);
        $this->assertSame([], $r['missing_criteria']);
        $this->assertSame([], $r['uncertain_criteria']);
    }

    public function test_partial_when_one_criterion_missing(): void
    {
        $r = $this->eval()->evaluate([
            'criteria' => ['price' => 80, 'delivery_time' => 80, 'quality' => 80],
            'rationale' => 'ok',
        ]);
        $this->assertSame(CompletenessEvaluator::PARTIAL, $r['completeness']);
        $this->assertContains('reliability', $r['missing_criteria']);
    }

    public function test_partial_when_missing_information_present(): void
    {
        $r = $this->eval()->evaluate([
            'criteria' => ['price' => 80, 'delivery_time' => 80, 'quality' => 80, 'reliability' => 80],
            'missing_information' => ['delivery'],
            'rationale' => 'ok',
        ]);
        $this->assertSame(CompletenessEvaluator::PARTIAL, $r['completeness']);
    }

    public function test_uncertain_when_three_criteria_missing(): void
    {
        $r = $this->eval()->evaluate([
            'criteria' => ['price' => 80],
            'rationale' => 'ok',
        ]);
        $this->assertSame(CompletenessEvaluator::UNCERTAIN, $r['completeness']);
    }

    public function test_uncertain_when_low_criteria_present(): void
    {
        $r = $this->eval()->evaluate([
            'criteria' => ['price' => 5, 'delivery_time' => 5, 'quality' => 5, 'reliability' => 80],
            'rationale' => 'ok',
        ]);
        $this->assertSame(CompletenessEvaluator::UNCERTAIN, $r['completeness']);
        $this->assertContains('price', $r['uncertain_criteria']);
    }

    public function test_partial_when_one_low_criterion(): void
    {
        $r = $this->eval()->evaluate([
            'criteria' => ['price' => 5, 'delivery_time' => 80, 'quality' => 80, 'reliability' => 80],
            'rationale' => 'ok',
        ]);
        $this->assertSame(CompletenessEvaluator::PARTIAL, $r['completeness']);
    }
}
