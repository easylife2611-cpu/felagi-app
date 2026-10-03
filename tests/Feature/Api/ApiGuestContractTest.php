<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * L347-N — Guest access contract.
 *
 * Cross-cutting: EVERY route wrapped in auth (sanctum) MUST reject
 * unauthenticated requests with 401 before any controller logic.
 *
 * This complements per-feature behavioral tests (S011SubmitOfferTest,
 * BoostControllerTest, etc.) by providing a single regression net for
 * middleware wiring errors.
 *
 * Non-duplication: existing per-feature tests remain untouched.
 */
final class ApiGuestContractTest extends TestCase
{
    use RefreshDatabase;

    /** Expected public (unauthenticated) routes. */
    private const PUBLIC_ROUTES = [
        'GET api/health',
        'GET api/v1/categories',
        'GET api/v1/needs',
        'GET api/v1/needs/{id}',
        'GET api/ads/placements/{id}/delivery',
        'POST api/ads/events',
        'POST api/v1/auth/telegram/start',
        'POST api/v1/auth/telegram/widget/start',
        'GET api/v1/auth/telegram/widget/callback',
        'GET api/v1/auth/telegram/callback',
        'POST api/v1/auth/telegram/exchange',
        'POST api/v1/auth/refresh',
    ];

    /** @return array<int,array{method:string,uri:string}> */
    private function authenticatedRoutes(): array
    {
        $out = [];
        foreach (Route::getRoutes() as $r) {
            $hasAuth = false;
            foreach ($r->middleware() as $mw) {
                if (str_contains($mw, 'auth')) { $hasAuth = true; break; }
            }
            if (!$hasAuth) continue;

            foreach ($r->methods() as $m) {
                if (in_array($m, ['HEAD', 'OPTIONS'], true)) continue;
                $uri = preg_replace('/\{[^}]+\}/', '00000000-0000-0000-0000-000000000000', $r->uri());
                $out[] = ['method' => $m, 'uri' => '/' . $uri];
            }
        }
        return $out;
    }

    public function test_guest_gets_401_on_every_authenticated_route(): void
    {
        $failures = [];
        foreach ($this->authenticatedRoutes() as $route) {
            $status = $this->json($route['method'], $route['uri'])->status();
            if ($status !== 401) {
                $failures[] = "{$route['method']} {$route['uri']} → {$status}";
            }
        }

        $this->assertSame(
            [],
            $failures,
            "Expected 401 for guests on all auth routes. Non-conforming:\n" .
            implode("\n", $failures)
        );
    }

    public function test_public_route_whitelist_is_registered(): void
    {
        $registered = [];
        foreach (Route::getRoutes() as $r) {
            foreach ($r->methods() as $m) {
                if (in_array($m, ['HEAD', 'OPTIONS'], true)) continue;
                $registered[] = $m . ' ' . $r->uri();
            }
        }

        foreach (self::PUBLIC_ROUTES as $expected) {
            $this->assertContains($expected, $registered, "Missing public route: {$expected}");
        }
    }

    public function test_public_sample_endpoints_do_not_401_for_guest(): void
    {
        $this->json('GET', '/api/health')->assertStatus(200);
        $this->json('GET', '/api/v1/categories')->assertStatus(200);
        $this->json('GET', '/api/v1/needs')->assertStatus(200);
    }

    public function test_authenticated_user_reaches_me(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertStatus(200);
    }

    public function test_auth_route_count_is_significant(): void
    {
        $count = count($this->authenticatedRoutes());
        $this->assertGreaterThan(50, $count, "Expected >50 auth routes; found {$count}");
    }
}
