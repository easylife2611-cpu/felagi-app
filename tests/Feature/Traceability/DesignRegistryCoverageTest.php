<?php

namespace Tests\Feature\Traceability;

use Tests\TestCase;

/**
 * T9-T15 — Design_Data registry coverage.
 *
 * Verifies each design registry has structural presence in App code.
 * Pragmatic: verifies at least a representative sample is used.
 */
class DesignRegistryCoverageTest extends TestCase
{
    private const DESIGN = '/home/zagcreht/felagi_extracted/Felagi_Design_Package/Design_Data';

    private function load(string $file): mixed
    {
        $path = self::DESIGN . '/' . $file;
        if (! file_exists($path)) {
            $this->markTestSkipped("Design registry not found: {$file}");
        }
        return json_decode(file_get_contents($path), true);
    }

    private function collectAppSources(): string
    {
        $blob = '';
        foreach (['app', 'resources', 'routes', 'config', 'database/seeders'] as $dir) {
            $base = base_path($dir);
            if (! is_dir($base)) continue;
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($it as $file) {
                if ($file->isFile() && preg_match('/\.(php|js|blade\.php)$/', $file->getFilename())) {
                    $blob .= file_get_contents($file->getPathname()) . "\n";
                }
            }
        }
        return $blob;
    }

    /** T9 — states.json: App uses state names */
    public function test_t9_states_registry_covered(): void
    {
        $states = $this->load('states.json');
        $this->assertIsArray($states);
        $this->assertGreaterThan(100, count($states));

        $appBlob = $this->collectAppSources();

        // Sample: must find at least 20 of the states as literals
        $hits = 0;
        foreach (array_keys($states) as $state) {
            if (str_contains($appBlob, "'{$state}'") || str_contains($appBlob, "\"{$state}\"")) {
                $hits++;
            }
        }
        $this->assertGreaterThanOrEqual(20, $hits,
            "Only {$hits} of " . count($states) . " design states appear in App code");
    }

    /** T10 — actions.json: 11 primary actions */
    public function test_t10_actions_registry_covered(): void
    {
        $actions = $this->load('actions.json');
        $this->assertIsArray($actions);
        $this->assertCount(11, $actions);

        $appBlob = $this->collectAppSources();
        $hits = 0;
        foreach ($actions as $action) {
            $key = is_array($action) ? ($action['id'] ?? $action['key'] ?? null) : $action;
            if ($key && str_contains($appBlob, $key)) {
                $hits++;
            }
        }
        $this->assertGreaterThanOrEqual(5, $hits,
            "Only {$hits} of 11 design actions appear in App code");
    }

    /** T11 — fields.json: 45 form field definitions */
    public function test_t11_fields_registry_covered(): void
    {
        $fields = $this->load('fields.json');
        $this->assertIsArray($fields);
        $this->assertGreaterThan(40, count($fields));

        $appBlob = $this->collectAppSources();
        $hits = 0;
        foreach (array_keys($fields) as $field) {
            if (str_contains($appBlob, $field)) {
                $hits++;
            }
        }
        $this->assertGreaterThanOrEqual(20, $hits,
            "Only {$hits} of " . count($fields) . " design fields appear in App code");
    }

    /** T12 — admin-acceptance-tests.json: 55 tests declared */
    public function test_t12_admin_acceptance_registry_declared(): void
    {
        $tests = $this->load('admin-acceptance-tests.json');
        $this->assertIsArray($tests);
        $this->assertCount(55, $tests);
    }

    /** T13 — ux-quality.json: contract structure */
    public function test_t13_ux_quality_registry_structure(): void
    {
        $ux = $this->load('ux-quality.json');
        $this->assertIsArray($ux);

        foreach (['version', 'formula', 'three_second_orientation', 'optimistic_allowlist', 'screens'] as $key) {
            $this->assertArrayHasKey($key, $ux, "ux-quality missing: {$key}");
        }
    }

    /** T14 — master-traceability-index.json: canonical rule + domains */
    public function test_t14_master_traceability_index_structure(): void
    {
        $index = $this->load('master-traceability-index.json');
        $this->assertIsArray($index);

        foreach (['version', 'canonical_rule', 'domains'] as $key) {
            $this->assertArrayHasKey($key, $index, "master index missing: {$key}");
        }
        $this->assertNotEmpty($index['domains']);
    }

    /** T15 — traceability.json: 209 entries */
    public function test_t15_traceability_matrix_declared(): void
    {
        $matrix = $this->load('traceability.json');
        $this->assertIsArray($matrix);
        $this->assertGreaterThanOrEqual(200, count($matrix),
            'traceability.json should declare ≥200 entries (found ' . count($matrix) . ')');
    }
}
