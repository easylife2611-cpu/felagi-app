<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminFeaturesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeaturesStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        UserRole::create([
            'user_id'    => $u->id,
            'role'       => UserRole::ROLE_MAIN_ADMIN,
            'granted_at' => now(),
        ]);
        return $u;
    }

    private function seedFeature(string $key, bool $value, int $version = 1): Setting
    {
        return Setting::create([
            'key'            => $key,
            'group'          => 'FEATURE',
            'type'           => 'BOOLEAN',
            'value_json'     => ['value' => $value],
            'default_json'   => ['value' => true],
            'risk'           => 'HIGH',
            'is_secret'      => false,
            'version_number' => $version,
        ]);
    }

    public function test_service_returns_status_keys(): void
    {
        $status = app(AdminFeaturesService::class)->status();
        $this->assertArrayHasKey('total', $status);
        $this->assertArrayHasKey('features', $status);
        $this->assertArrayHasKey('computed_at', $status);
    }

    public function test_service_returns_four_canonical_features(): void
    {
        $status = app(AdminFeaturesService::class)->status();
        $this->assertSame(4, $status['total']);
        $this->assertCount(4, $status['features']);
    }

    public function test_service_uses_defaults_when_db_empty(): void
    {
        $status = app(AdminFeaturesService::class)->status();
        foreach ($status['features'] as $f) {
            $this->assertSame('default', $f['source']);
            $this->assertTrue($f['effective']);
            $this->assertSame(0, $f['version']);
        }
    }

    public function test_service_uses_db_values_when_present(): void
    {
        $this->seedFeature('feature.needs', false, 5);
        $status = app(AdminFeaturesService::class)->status();
        $needs = collect($status['features'])->firstWhere('key', 'feature.needs');
        $this->assertNotNull($needs);
        $this->assertFalse($needs['effective']);
        $this->assertSame(5, $needs['version']);
        $this->assertSame('db', $needs['source']);
    }

    public function test_service_reports_dependencies(): void
    {
        Setting::create([
            'key'            => 'feature.ai_compare',
            'group'          => 'FEATURE',
            'type'           => 'BOOLEAN',
            'value_json'     => ['value' => true],
            'default_json'   => ['value' => true],
            'risk'           => 'HIGH',
            'is_secret'      => false,
            'version_number' => 1,
            'dependencies'   => [['key' => 'ai.model_id', 'value' => 'non_empty', 'message' => 'Requires model']],
        ]);
        $status = app(AdminFeaturesService::class)->status();
        $ai = collect($status['features'])->firstWhere('key', 'feature.ai_compare');
        $this->assertIsArray($ai['dependencies']);
        $this->assertSame('ai.model_id', $ai['dependencies'][0]['key']);
    }

    public function test_features_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/features-status')->assertUnauthorized();
    }

    public function test_features_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/features-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['total', 'features', 'computed_at'],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A004')
            ->assertJsonPath('meta.source', 'live')
            ->assertJsonPath('data.total', 4);
    }

    public function test_features_status_reflects_db_value(): void
    {
        $admin = $this->admin();
        $this->seedFeature('feature.offers', false, 3);
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/features-status')
            ->assertOk()
            ->assertJsonFragment(['key' => 'feature.offers', 'effective' => false, 'version' => 3]);
    }

    public function test_features_status_returns_403_for_non_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/features-status')
            ->assertForbidden();
    }

    public function test_features_page_requires_auth(): void
    {
        $this->get('/admin/features')->assertRedirect('/admin/login');
    }

    public function test_features_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/features')
            ->assertOk()
            ->assertSee('features-stats', false)
            ->assertSee('features-table', false);
    }

    public function test_features_page_contains_fetch_endpoint(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/features')
            ->assertOk()
            ->assertSee('/api/v1/admin/features-status', false);
    }
}
