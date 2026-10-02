<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminSettingsStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminSettingsStatusTest extends TestCase
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

    private function seedSetting(string $key, mixed $value): Setting
    {
        return Setting::create([
            'key'            => $key,
            'group'          => 'FEATURE',
            'type'           => 'BOOLEAN',
            'value_json'     => ['value' => $value],
            'default_json'   => ['value' => false],
            'risk'           => 'HIGH',
            'is_secret'      => false,
            'version_number' => 1,
        ]);
    }

    public function test_service_returns_status_keys(): void
    {
        $status = app(AdminSettingsStatusService::class)->status();
        $this->assertArrayHasKey('total', $status);
        $this->assertArrayHasKey('freeze', $status);
        $this->assertArrayHasKey('settings', $status);
        $this->assertArrayHasKey('computed_at', $status);
    }

    public function test_service_returns_empty_list_when_no_settings(): void
    {
        $status = app(AdminSettingsStatusService::class)->status();
        $this->assertSame(0, $status['total']);
        $this->assertSame([], $status['settings']);
    }

    public function test_service_lists_settings_with_real_values(): void
    {
        $this->seedSetting('feature.needs', true);
        $this->seedSetting('feature.offers', false);
        $status = app(AdminSettingsStatusService::class)->status();
        $this->assertSame(2, $status['total']);
        $keys = array_column($status['settings'], 'key');
        $this->assertContains('feature.needs', $keys);
    }

    public function test_service_masks_secret_values(): void
    {
        Setting::create([
            'key' => 'payments.provider', 'group' => 'SECURITY', 'type' => 'STRING',
            'value_json' => ['value' => 'super-secret'],
            'default_json' => ['value' => null],
            'risk' => 'CRITICAL', 'is_secret' => true, 'version_number' => 1,
        ]);
        $status = app(AdminSettingsStatusService::class)->status();
        $this->assertSame('[secret]', $status['settings'][0]['effective']);
    }

    public function test_freeze_default_is_false(): void
    {
        $status = app(AdminSettingsStatusService::class)->status();
        $this->assertFalse($status['freeze']['enabled']);
    }

    public function test_freeze_reflects_cache_state(): void
    {
        Cache::forever(AdminSettingsStatusService::FREEZE_CACHE_KEY, true);
        Cache::forever(AdminSettingsStatusService::FREEZE_REASON_KEY, 'Testing');
        $status = app(AdminSettingsStatusService::class)->status();
        $this->assertTrue($status['freeze']['enabled']);
        $this->assertSame('Testing', $status['freeze']['reason']);
    }

    public function test_settings_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/settings-status')->assertUnauthorized();
    }

    public function test_settings_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->seedSetting('feature.needs', true);
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/settings-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['total', 'freeze' => ['enabled'], 'settings', 'computed_at'],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A017')
            ->assertJsonPath('meta.source', 'live')
            ->assertJsonPath('data.total', 1);
    }

    public function test_settings_status_returns_403_for_non_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/settings-status')
            ->assertForbidden();
    }

    public function test_settings_page_requires_auth(): void
    {
        $this->get('/admin/settings')->assertRedirect('/admin/login');
    }

    public function test_settings_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/settings')
            ->assertOk()
            ->assertSee('settings-stats', false)
            ->assertSee('settings-table', false);
    }

    public function test_settings_page_contains_fetch_endpoint(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/settings')
            ->assertOk()
            ->assertSee('/api/v1/admin/settings-status', false);
    }
}
