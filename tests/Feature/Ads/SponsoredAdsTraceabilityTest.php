<?php

namespace Tests\Feature\Ads;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ADS-57 / ADS-61 — Traceability + Final Execution.
 */
class SponsoredAdsTraceabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_traceability_matrix_exists(): void
    {
        $this->assertFileExists(
            base_path('docs/reports/ADS_TRACEABILITY_MATRIX_20261001.md')
        );
    }

    public function test_traceability_matrix_covers_key_requirements(): void
    {
        $body = file_get_contents(
            base_path('docs/reports/ADS_TRACEABILITY_MATRIX_20261001.md')
        );
        foreach ([
            'ADS-1', 'ADS-17', 'ADS-55', 'ADS-57', 'ADS-58', 'ADS-61',
            'ADS-S008-PLACEMENT',
        ] as $req) {
            $this->assertStringContainsString($req, $body, "Missing: {$req}");
        }
    }

    public function test_traceability_marks_ads_58_not_started(): void
    {
        $body = file_get_contents(
            base_path('docs/reports/ADS_TRACEABILITY_MATRIX_20261001.md')
        );
        $this->assertStringContainsString('NOT STARTED', $body);
        $this->assertStringContainsString('ADS-58', $body);
    }

    public function test_final_execution_doc_exists(): void
    {
        $this->assertFileExists(
            base_path('docs/reports/ADS_FINAL_EXECUTION_20261001.md')
        );
    }

    public function test_final_execution_records_evidence_boundary(): void
    {
        $body = file_get_contents(
            base_path('docs/reports/ADS_FINAL_EXECUTION_20261001.md')
        );
        $this->assertStringContainsString('Published', $body);
        $this->assertStringContainsString('Applied', $body);
        $this->assertStringContainsString('Verified', $body);
    }

    public function test_final_execution_records_core_metrics(): void
    {
        $body = file_get_contents(
            base_path('docs/reports/ADS_FINAL_EXECUTION_20261001.md')
        );
        $this->assertStringContainsString('18 admin', $body);
        $this->assertStringContainsString('A023', $body);
        $this->assertStringContainsString('9', $body);
        $this->assertStringContainsString('3 placements', $body);
    }
}
