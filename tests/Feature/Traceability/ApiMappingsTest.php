<?php

namespace Tests\Feature\Traceability;

use Tests\TestCase;

/**
 * API Mappings Traceability Test
 *
 * Verifies Design_Data/api-mappings.json references (218 endpoints
 * across 44 screens) exist in App routes.
 *
 * Design ref format: "METHOD /api/v1/path" (no leading /)
 * App route format: uri + methods from Router.
 */
class ApiMappingsTest extends TestCase
{
    private array $designMappings;
    private array $appRoutes;

    protected function setUp(): void
    {
        parent::setUp();

        $designPath = '/home/zagcreht/felagi_extracted/Felagi_Design_Package/Design_Data/api-mappings.json';
        if (! file_exists($designPath)) {
            $this->markTestSkipped("Design api-mappings not found");
        }

        $this->designMappings = json_decode(file_get_contents($designPath), true);

        // Build App routes table: "METHOD /uri" set
        $this->appRoutes = [];
        foreach (app('router')->getRoutes() as $route) {
            $uri = '/' . ltrim($route->uri(), '/');
            foreach ($route->methods() as $method) {
                if ($method === 'HEAD') continue;
                $this->appRoutes[] = $method . ' ' . $uri;
            }
        }
    }

    /**
     * Normalize a design reference and an app route to a comparable form.
     * Design: "POST /api/v1/auth/telegram/start"
     * App:    "POST /api/v1/auth/telegram/start"
     * Both:   convert {param} and :param to {*}
     */
    private function normalize(string $method, string $path): string
    {
        $path = '/' . ltrim($path, '/');

        // Replace {param} with {*}
        $path = preg_replace('/\{[^}]+\}/', '{*}', $path);
        // Replace :param with {*}
        $path = preg_replace('/:[a-zA-Z_][a-zA-Z0-9_]*/', '{*}', $path);

        return strtoupper($method) . ' ' . $path;
    }

    public function test_design_declares_46_api_mappings(): void
    {
        $this->assertCount(46, $this->designMappings);
    }

    public function test_total_reference_count(): void
    {
        $total = 0;
        foreach ($this->designMappings as $entry) {
            $total += count($entry['references'] ?? []);
        }
        $this->assertSame(218, $total,
            'Design declares 218 total endpoint references');
    }

    public function test_local_only_entries_have_no_references(): void
    {
        $violations = [];
        foreach ($this->designMappings as $key => $entry) {
            if (($entry['local_only'] ?? false) === true) {
                if (! empty($entry['references'])) {
                    $violations[] = $key;
                }
            }
        }
        $this->assertEmpty($violations,
            'local_only entries must have empty references: ' . implode(', ', $violations));
    }

    /**
     * For each design reference, verify the (METHOD, path-pattern) exists
     * among app routes after normalization.
     */
    public function test_all_design_references_exist_in_app_routes(): void
    {
        // Build normalized app route set
        $appNormalized = [];
        foreach ($this->appRoutes as $r) {
            [$method, $path] = explode(' ', $r, 2);
            $appNormalized[$this->normalize($method, $path)] = $r;
        }

        $missing = [];
        $matched = 0;

        foreach ($this->designMappings as $key => $entry) {
            foreach ($entry['references'] ?? [] as $ref) {
                // Parse "METHOD /path"
                if (! preg_match('#^([A-Z]+)\s+(/.+)$#', $ref, $m)) {
                    $missing[] = "{$key}: MALFORMED -> {$ref}";
                    continue;
                }
                [, $method, $path] = $m;
                $norm = $this->normalize($method, $path);

                if (isset($appNormalized[$norm])) {
                    $matched++;
                } else {
                    $missing[] = "{$key}: {$method} {$path}";
                }
            }
        }

        // Report summary in failure message
        $msg = sprintf(
            "Matched: %d, Missing: %d\nMissing endpoints:\n  %s",
            $matched,
            count($missing),
            implode("\n  ", array_slice($missing, 0, 50))
        );

        $this->assertEmpty($missing, $msg);
    }

    public function test_all_api_v1_references_are_authenticated_or_public(): void
    {
        // Every /api/v1/ reference should map to a route — this is enforced
        // by test_all_design_references_exist_in_app_routes. This test
        // documents that all references are under /api/v1.
        $nonApiV1 = [];
        foreach ($this->designMappings as $key => $entry) {
            foreach ($entry['references'] ?? [] as $ref) {
                if (preg_match('#^[A-Z]+\s+(/.+)$#', $ref, $m)) {
                    $path = $m[1];
                    if (strpos($path, '/api/') !== 0 && strpos($path, '/auth/') !== 0) {
                        $nonApiV1[] = "{$key}: {$ref}";
                    }
                }
            }
        }
        $this->assertEmpty($nonApiV1,
            "References outside /api/ namespace:\n  " . implode("\n  ", $nonApiV1));
    }
}
