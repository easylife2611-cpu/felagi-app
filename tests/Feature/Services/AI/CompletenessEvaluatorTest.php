<?php

declare(strict_types=1);

namespace Tests\Feature\Services\AI;

use App\Services\AI\CompletenessEvaluator;
use Tests\TestCase;

final class CompletenessEvaluatorTest extends TestCase
{
    private CompletenessEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new CompletenessEvaluator();
    }

    private function fullRow(): array
    {
        return [
            'offer_index' => 0,
            'total_score' => 80,
            'criteria' => [
                'price'         => 80,
                'delivery_time' => 75,
                'quality'       => 85,
                'reliability'   => 90,
            ],
            'rationale' => 'Solid offer with all details provided.',
            'missing_information' => [],
        ];
    }

    public function test_constants_match(): void
    {
        $this->assertSame('COMPLETE',  CompletenessEvaluator::COMPLETE);
        $this->assertSame('PARTIAL',   CompletenessEvaluator::PARTIAL);
        $this->assertSame('UNCERTAIN', CompletenessEvaluator::UNCERTAIN);
        $this->assertSame(20, CompletenessEvaluator::LOW_THRESHOLD);
    }

    public function test_full_row_is_complete(): void
    {
        $result = $this->evaluator->evaluate($this->fullRow());
        $this->assertSame(CompletenessEvaluator::COMPLETE, $result['completeness']);
        $this->assertSame([], $result['missing_criteria']);
        $this->assertSame([], $result['uncertain_criteria']);
    }

    public function test_missing_one_criterion_is_partial(): void
    {
        $row = $this->fullRow();
        unset($row['criteria']['price']);

        $result = $this->evaluator->evaluate($row);
        $this->assertSame(CompletenessEvaluator::PARTIAL, $result['completeness']);
        $this->assertContains('price', $result['missing_criteria']);
    }

    public function test_missing_three_criteria_is_uncertain(): void
    {
        $row = $this->fullRow();
        $row['criteria'] = ['price' => 80];  // 3 missing

        $result = $this->evaluator->evaluate($row);
        $this->assertSame(CompletenessEvaluator::UNCERTAIN, $result['completeness']);
        $this->assertCount(3, $result['missing_criteria']);
    }

    public function test_low_scores_mark_uncertain_criteria(): void
    {
        $row = $this->fullRow();
        $row['criteria']['price'] = 5;      // below threshold
        $row['criteria']['quality'] = 10;   // below threshold

        $result = $this->evaluator->evaluate($row);
        $this->assertContains('price', $result['uncertain_criteria']);
        $this->assertContains('quality', $result['uncertain_criteria']);
    }

    public function test_three_low_scores_is_uncertain(): void
    {
        $row = $this->fullRow();
        $row['criteria']['price'] = 5;
        $row['criteria']['delivery_time'] = 5;
        $row['criteria']['quality'] = 5;

        $result = $this->evaluator->evaluate($row);
        $this->assertSame(CompletenessEvaluator::UNCERTAIN, $result['completeness']);
    }

    public function test_missing_information_triggers_partial(): void
    {
        $row = $this->fullRow();
        $row['missing_information'] = ['Provider did not state warranty'];

        $result = $this->evaluator->evaluate($row);
        $this->assertSame(CompletenessEvaluator::PARTIAL, $result['completeness']);
    }

    public function test_many_missing_information_items_is_uncertain(): void
    {
        $row = $this->fullRow();
        $row['missing_information'] = [
            'No warranty stated',
            'No delivery plan',
            'No references',
            'No contact preference',
            'No payment terms',
        ];

        $result = $this->evaluator->evaluate($row);
        $this->assertSame(CompletenessEvaluator::UNCERTAIN, $result['completeness']);
    }

    public function test_empty_rationale_counts_as_signal(): void
    {
        $row = $this->fullRow();
        $row['rationale'] = '';

        $result = $this->evaluator->evaluate($row);
        // Empty rationale alone = 1 signal → PARTIAL
        $this->assertSame(CompletenessEvaluator::PARTIAL, $result['completeness']);
    }

    public function test_non_numeric_criterion_treated_as_missing(): void
    {
        $row = $this->fullRow();
        $row['criteria']['price'] = 'high';

        $result = $this->evaluator->evaluate($row);
        $this->assertContains('price', $result['missing_criteria']);
    }

    public function test_result_shape_is_stable(): void
    {
        $result = $this->evaluator->evaluate($this->fullRow());
        $this->assertArrayHasKey('completeness', $result);
        $this->assertArrayHasKey('missing_criteria', $result);
        $this->assertArrayHasKey('uncertain_criteria', $result);
        $this->assertIsString($result['completeness']);
        $this->assertIsArray($result['missing_criteria']);
        $this->assertIsArray($result['uncertain_criteria']);
    }

    public function test_low_threshold_boundary_is_kept(): void
    {
        // 20 is the threshold — exactly 20 is NOT low (uses < comparison)
        $row = $this->fullRow();
        $row['criteria']['price'] = CompletenessEvaluator::LOW_THRESHOLD;

        $result = $this->evaluator->evaluate($row);
        $this->assertNotContains('price', $result['uncertain_criteria']);
    }

    public function test_one_below_threshold_is_flagged(): void
    {
        $row = $this->fullRow();
        $row['criteria']['price'] = CompletenessEvaluator::LOW_THRESHOLD - 1;

        $result = $this->evaluator->evaluate($row);
        $this->assertContains('price', $result['uncertain_criteria']);
    }

    public function test_missing_and_low_combine(): void
    {
        $row = $this->fullRow();
        unset($row['criteria']['price']);        // 1 missing
        $row['criteria']['quality'] = 5;          // 1 low

        $result = $this->evaluator->evaluate($row);
        $this->assertContains('price', $result['missing_criteria']);
        $this->assertContains('quality', $result['uncertain_criteria']);
        // 2 signals → PARTIAL
        $this->assertSame(CompletenessEvaluator::PARTIAL, $result['completeness']);
    }
}
