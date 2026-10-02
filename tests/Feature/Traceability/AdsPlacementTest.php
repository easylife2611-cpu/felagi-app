<?php

namespace Tests\Feature\Traceability;

use App\Models\AdCampaign;
use Tests\TestCase;

/**
 * T5 — Ads placement traceability.
 *
 * Design_Data/placement-registry.json declares 3 placements:
 *   AD_BROWSE_INLINE_01
 *   AD_SEARCH_RESULTS_INLINE_01
 *   AD_NEED_DETAIL_BOTTOM_01
 *
 * Verifies each is:
 *   1. declared as an AdCampaign constant
 *   2. referenced in a Blade ad slot (data-ad-slot)
 *   3. covered by an admin toggle
 */
class AdsPlacementTest extends TestCase
{
    private array $designPlacements;
    private array $appPlacements;

    protected function setUp(): void
    {
        parent::setUp();

        $designPath = '/home/zagcreht/felagi_extracted/Felagi_Design_Package/Design_Data/placement-registry.json';
        if (! file_exists($designPath)) {
            $this->markTestSkipped("Design placement registry not found");
        }

        $design = json_decode(file_get_contents($designPath), true);
        $placements = $design['placements'] ?? $design;
        $this->designPlacements = array_map(
            fn($p) => $p['placement_id'] ?? $p['id'] ?? null,
            $placements
        );
        $this->designPlacements = array_filter($this->designPlacements);

        // App-side: pull from AdCampaign::PLACEMENTS
        $this->appPlacements = AdCampaign::PLACEMENTS ?? [];
    }

    public function test_design_declares_3_placements(): void
    {
        $this->assertCount(3, $this->designPlacements);
    }

    public function test_all_design_placements_exist_in_app(): void
    {
        $missing = [];
        foreach ($this->designPlacements as $pid) {
            if (! in_array($pid, $this->appPlacements, true)) {
                $missing[] = $pid;
            }
        }
        $this->assertEmpty($missing,
            'Design placements missing in AdCampaign::PLACEMENTS: ' . implode(', ', $missing));
    }

    public function test_each_placement_has_blade_slot(): void
    {
        $missing = [];
        foreach ($this->designPlacements as $pid) {
            $found = false;
            foreach (['browse', 'show-need', 'index', 'welcome'] as $view) {
                $path = resource_path("views/{$view}.blade.php");
                if (! file_exists($path)) continue;
                if (str_contains(file_get_contents($path), "data-ad-slot=\"{$pid}\"")) {
                    $found = true;
                    break;
                }
            }
            if (! $found) {
                $missing[] = $pid;
            }
        }
        $this->assertEmpty($missing,
            'Placements without Blade ad slot: ' . implode(', ', $missing));
    }

    public function test_each_placement_has_admin_toggle(): void
    {
        $path = resource_path('views/admin/sponsored-ads.blade.php');
        if (! file_exists($path)) {
            $this->markTestSkipped("admin sponsored-ads view missing");
        }
        $content = file_get_contents($path);

        $missing = [];
        foreach ($this->designPlacements as $pid) {
            if (! str_contains($content, "data-placement=\"{$pid}\"")) {
                $missing[] = $pid;
            }
        }
        $this->assertEmpty($missing,
            'Placements without admin toggle: ' . implode(', ', $missing));
    }

    public function test_placement_constants_are_declared(): void
    {
        $this->assertTrue(defined(AdCampaign::class . '::PLACEMENT_BROWSE'));
        $this->assertTrue(defined(AdCampaign::class . '::PLACEMENT_SEARCH'));
        $this->assertTrue(defined(AdCampaign::class . '::PLACEMENT_NEED_DETAIL'));
    }
}
