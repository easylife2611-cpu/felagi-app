<?php

namespace Tests\Feature\Traceability;

use Tests\TestCase;

class ControlRegistryTest extends TestCase
{
    private array $designControls;
    private array $mapping;
    private array $seederKeys;

    protected function setUp(): void
    {
        parent::setUp();

        $designPath = '/home/zagcreht/felagi_extracted/Felagi_Design_Package/Design_Data/control-registry.json';
        $mappingPath = base_path('docs/traceability/control-mapping.json');
        $seederPath = base_path('database/seeders/ControlRegistrySeeder.php');

        if (! file_exists($designPath)) {
            $this->markTestSkipped("Design registry not found");
        }
        if (! file_exists($mappingPath)) {
            $this->markTestSkipped("Mapping not found");
        }

        $design = json_decode(file_get_contents($designPath), true);
        $this->designControls = $design['controls'] ?? $design;

        $this->mapping = json_decode(file_get_contents($mappingPath), true)['mappings'];

        $this->seederKeys = [];
        if (file_exists($seederPath)) {
            $content = file_get_contents($seederPath);
            if (preg_match_all("/['\"]([a-z][a-z0-9._]+)['\"]\s*=>/i", $content, $m)) {
                $this->seederKeys = array_unique($m[1]);
            }
        }
    }

    private function flattenMapping(): array
    {
        $flat = [];
        foreach ($this->mapping as $section => $items) {
            if (! is_array($items)) continue;
            foreach ($items as $controlId => $entry) {
                if (str_starts_with($controlId, '_')) continue;
                $flat[$controlId] = $entry;
            }
        }
        return $flat;
    }

    public function test_design_declares_55_controls(): void
    {
        $this->assertCount(55, $this->designControls);
    }

    public function test_every_design_control_has_mapping(): void
    {
        $flat = $this->flattenMapping();
        $unmapped = [];
        foreach ($this->designControls as $control) {
            $cid = $control['control_id'] ?? null;
            if ($cid && ! isset($flat[$cid])) {
                $unmapped[] = $cid;
            }
        }
        $this->assertEmpty($unmapped, 'Missing from mapping: ' . implode(', ', $unmapped));
    }

    public function test_mapping_count_matches_design_count(): void
    {
        $flat = $this->flattenMapping();
        $this->assertCount(55, $flat, 'Mapping must cover all 55 (found ' . count($flat) . ')');
    }

    public function test_mapping_covers_all_storage_types(): void
    {
        $types = ['db', 'config', 'operation', 'env', 'ads'];
        $found = [];
        foreach ($this->mapping as $section => $items) {
            if (! is_array($items)) continue;
            foreach ($items as $controlId => $entry) {
                if (str_starts_with($controlId, '_')) continue;
                $found[] = $entry['storage'] ?? 'unknown';
            }
        }
        foreach ($types as $type) {
            $this->assertContains($type, $found, "No controls mapped to: {$type}");
        }
    }

    public function test_operations_have_endpoint(): void
    {
        $missing = [];
        foreach ($this->mapping as $section => $items) {
            if (! is_array($items)) continue;
            foreach ($items as $controlId => $entry) {
                if (str_starts_with($controlId, '_')) continue;
                if (($entry['storage'] ?? '') !== 'operation') continue;
                if (empty($entry['endpoint'])) $missing[] = $controlId;
            }
        }
        $this->assertEmpty($missing, 'Operations missing endpoint: ' . implode(', ', $missing));
    }

    public function test_secrets_have_env_var(): void
    {
        $missing = [];
        foreach ($this->mapping as $section => $items) {
            if (! is_array($items)) continue;
            foreach ($items as $controlId => $entry) {
                if (str_starts_with($controlId, '_')) continue;
                if (($entry['storage'] ?? '') !== 'env') continue;
                if (empty($entry['env_var'])) $missing[] = $controlId;
            }
        }
        $this->assertEmpty($missing, 'Secrets missing env_var: ' . implode(', ', $missing));
    }

    public function test_mapping_has_no_duplicate_control_ids(): void
    {
        $seen = [];
        $dupes = [];
        foreach ($this->mapping as $section => $items) {
            if (! is_array($items)) continue;
            foreach ($items as $controlId => $entry) {
                if (str_starts_with($controlId, '_')) continue;
                if (isset($seen[$controlId])) {
                    $dupes[] = $controlId;
                }
                $seen[$controlId] = true;
            }
        }
        $this->assertEmpty($dupes, 'Duplicate control IDs: ' . implode(', ', $dupes));
    }
}
