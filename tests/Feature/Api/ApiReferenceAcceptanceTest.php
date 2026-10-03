<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * L347-M — API Reference Acceptance.
 *
 * Spec: Acceptance_Cases.md — every SCREEN-XXX requires "API reference".
 * Source of truth: Design_Data/api-mappings.json.
 *
 * Verifies: every endpoint declared in api.SXXX / api.AXXX is registered
 *           in the application's route table with the correct HTTP method.
 *
 * Non-duplication:
 *   S011SubmitOfferTest.php — behavioral HTTP tests (auth/status/validation)
 *   AllScreensAcceptanceTest.php — view structure only
 *   This file — registry vs routes parity only.
 */
final class ApiReferenceAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    private static ?array $mappings = null;

    private function mappings(): array
    {
        if (self::$mappings === null) {
            $path = base_path('../felagi_extracted/Felagi_Design_Package/Design_Data/api-mappings.json');
            if (!file_exists($path)) {
                $path = getenv('HOME') . '/felagi_extracted/Felagi_Design_Package/Design_Data/api-mappings.json';
            }
            self::$mappings = json_decode(file_get_contents($path), true) ?: [];
        }
        return self::$mappings;
    }

    /** Registered routes as method|uri pairs. */
    private function registeredRoutes(): array
    {
        $out = [];
        foreach (Route::getRoutes() as $r) {
            foreach ($r->methods() as $m) {
                if ($m === 'HEAD') continue;
                $out[] = $m . ' ' . $r->uri();
            }
        }
        return array_unique($out);
    }

    /** Normalize {param} placeholders to {id} for comparison. */
    private function normalize(string $uri): string
    {
        return preg_replace('/\{[^}]+\}/', '{id}', $uri);
    }

    /** @return array<string,array{0:string,1:string}> */
    public static function screenProvider(): array
    {
        $path = getenv('HOME') . '/felagi_extracted/Felagi_Design_Package/Design_Data/api-mappings.json';
        $data = file_exists($path) ? json_decode(file_get_contents($path), true) : [];

        $cases = [];
        foreach ($data as $key => $entry) {
            if (!empty($entry['local_only'])) continue;
            foreach ($entry['references'] ?? [] as $ref) {
                $cases["{$key} :: {$ref}"] = [$key, $ref];
            }
        }
        return $cases;
    }

    /** @dataProvider screenProvider */
    public function test_declared_api_reference_is_registered(string $screenKey, string $reference): void
    {
        // Parse "METHOD /uri"
        if (!preg_match('#^(GET|POST|PUT|PATCH|DELETE)\s+(/.*)$#', $reference, $m)) {
            $this->fail("Malformed reference: {$reference}");
        }

        [$method, $uri] = [$m[1], $m[2]];

        // Keep full URI: routes carry both 'api' (Laravel 11 default)
        // and 'v1' (Route::prefix in routes/api.php) prefixes.
        $expectedUri = ltrim($uri, '/');
        $expected = $this->normalize($expectedUri);

        $registered = array_map(
            fn($r) => $this->normalize(explode(' ', $r, 2)[1]),
            $this->registeredRoutes()
        );

        $this->assertContains(
            $expected,
            $registered,
            "{$screenKey}: route {$method} {$expectedUri} not registered (from {$reference})"
        );
    }

    public function test_s001_is_local_only(): void
    {
        $m = $this->mappings();
        $this->assertTrue($m['api.S001']['local_only'] ?? false, 'S001 must be local_only');
        $this->assertEmpty($m['api.S001']['references'] ?? []);
    }

    public function test_s006_is_local_only(): void
    {
        $m = $this->mappings();
        $this->assertTrue($m['api.S006']['local_only'] ?? false, 'S006 must be local_only');
        $this->assertEmpty($m['api.S006']['references'] ?? []);
    }

    public function test_all_references_use_api_v1_prefix(): void
    {
        foreach ($this->mappings() as $key => $entry) {
            foreach ($entry['references'] ?? [] as $ref) {
                $this->assertStringContainsString(
                    '/api/v1/',
                    $ref,
                    "{$key}: reference must use /api/v1/: {$ref}"
                );
            }
        }
    }

    public function test_all_screens_have_mapping_entry(): void
    {
        $m = $this->mappings();

        for ($i = 1; $i <= 23; $i++) {
            $sid = sprintf('S%03d', $i);
            $this->assertArrayHasKey("api.{$sid}", $m, "Missing api.{$sid}");
        }

        for ($i = 1; $i <= 23; $i++) {
            $aid = sprintf('A%03d', $i);
            $this->assertArrayHasKey("api.{$aid}", $m, "Missing api.{$aid}");
        }
    }

    public function test_total_screens_mapped(): void
    {
        $this->assertCount(46, $this->mappings(), 'Expect 46 screen mappings (S001-S023 + A001-A023)');
    }

    public function test_offer_submission_endpoints_registered(): void
    {
        $registered = $this->registeredRoutes();

        $this->assertContains('POST api/v1/offer-submissions', $registered);
        $this->assertContains('GET api/v1/offer-submissions/{id}', $registered);
        $this->assertContains('POST api/v1/offer-submissions/{id}/resume', $registered);
    }
}
