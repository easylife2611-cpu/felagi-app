<?php

namespace Tests\Feature\Handoff;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GAP-ADS-58 / Handoff refresh integrity.
 *
 * The SOURCE_OF_TRUTH.md is refreshed at the end of each work package,
 * just before the ledger commit. After the commit lands, HEAD advances
 * by one. The test therefore accepts current HEAD or immediate parent.
 */
class HandoffRefreshTest extends TestCase
{
    use RefreshDatabase;

    public function test_ads_canonical_artifacts_update_exists(): void
    {
        $this->assertFileExists(
            base_path('docs/reports/ADS_CANONICAL_ARTIFACTS_UPDATE_20261001.md')
        );
    }

    public function test_ads_canonical_artifacts_lists_production_updates(): void
    {
        $body = file_get_contents(
            base_path('docs/reports/ADS_CANONICAL_ARTIFACTS_UPDATE_20261001.md')
        );
        $this->assertStringContainsString('ADS-58', $body);
        $this->assertStringContainsString('design owner', $body);
        $this->assertStringContainsString('SPONSORED_ADS_PRIVACY_CONSENT.md', $body);
        $this->assertStringContainsString('Release gate alignment', $body);
    }

    public function test_source_of_truth_head_is_current_or_parent(): void
    {
        $current = trim(shell_exec('git rev-parse --short HEAD') ?? '');
        $parent  = trim(shell_exec('git rev-parse --short HEAD~1') ?? '');

        $body = file_get_contents(base_path('public/handoff/SOURCE_OF_TRUTH.md'));

        $found = ($current !== '' && str_contains($body, "`{$current}`"))
              || ($parent  !== '' && str_contains($body, "`{$parent}`"));

        $this->assertTrue(
            $found,
            "SOURCE_OF_TRUTH HEAD not current ({$current}) or parent ({$parent})"
        );
    }

    public function test_completion_matrix_refreshed_exists(): void
    {
        $this->assertFileExists(
            base_path('docs/reports/COMPLETION_MATRIX_20261001.md')
        );
    }

    public function test_completion_matrix_has_l271_addendum(): void
    {
        $body = file_get_contents(
            base_path('docs/reports/COMPLETION_MATRIX_20261001.md')
        );
        $this->assertStringContainsString('L271', $body);
        $this->assertStringContainsString('L277', $body);
        $this->assertStringContainsString('889', $body);
    }

    public function test_index_lists_recent_ledgers(): void
    {
        $body = file_get_contents(
            base_path('public/handoff/ledgers/INDEX.md')
        );
        foreach (['L271', 'L272', 'L273', 'L274', 'L275', 'L276', 'L277'] as $l) {
            $this->assertStringContainsString($l, $body, "INDEX missing: {$l}");
        }
    }
}
