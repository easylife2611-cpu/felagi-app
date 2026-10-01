<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\ControlDependencyService;
use Database\Seeders\ControlRegistrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * J (audit L276) — Dependency-aware controls.
 */
class ControlDependencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ControlRegistrySeeder::class);
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        UserRole::create(['user_id' => $u->id, 'role' => 'MAIN_ADMIN']);
        $u->recently_authenticated_at = now();
        $u->save();
        return $u;
    }

    public function test_migration_added_column(): void
    {
        $this->assertTrue(
            \Schema::hasColumn('settings', 'dependencies'),
            'settings.dependencies column missing'
        );
    }

    public function test_seeder_populates_dependencies_for_boosts(): void
    {
        $boosts = Setting::find('feature.boosts');
        $this->assertNotNull($boosts);
        $this->assertIsArray($boosts->dependencies);
        $this->assertNotEmpty($boosts->dependencies);
        $keys = array_column($boosts->dependencies, 'key');
        $this->assertContains('feature.payments', $keys);
    }

    public function test_seeder_populates_dependencies_for_ai_compare(): void
    {
        $ai = Setting::find('feature.ai_compare');
        $this->assertNotNull($ai);
        $this->assertIsArray($ai->dependencies);
        $keys = array_column($ai->dependencies, 'key');
        $this->assertContains('ai.model_id', $keys);
    }

    public function test_service_reports_violation_when_dependency_off(): void
    {
        // feature.payments is OFF by default → boosts dependency VIOLATED
        $boosts = Setting::find('feature.boosts');
        $report = app(ControlDependencyService::class)->inspect($boosts);

        $this->assertFalse($report['satisfied']);
        $this->assertNotEmpty($report['violations']);
        $this->assertSame('feature.boosts', $report['setting_key']);
    }

    public function test_service_reports_satisfied_when_dependency_on(): void
    {
        $payments = Setting::find('feature.payments');
        $payments->value_json = ['value' => true];
        $payments->save();

        $boosts = Setting::find('feature.boosts');
        $report = app(ControlDependencyService::class)->inspect($boosts);

        $this->assertTrue($report['satisfied']);
        $this->assertEmpty($report['violations']);
    }

    public function test_service_handles_non_empty_rule(): void
    {
        // ai.model_id is null by default → ai_compare VIOLATED
        $ai = Setting::find('feature.ai_compare');
        $report = app(ControlDependencyService::class)->inspect($ai);
        $this->assertFalse($report['satisfied']);

        // Set model_id → satisfied
        $model = Setting::find('ai.model_id');
        $model->value_json = ['value' => 'gemini-flash-latest'];
        $model->save();

        $report2 = app(ControlDependencyService::class)->inspect(Setting::find('feature.ai_compare'));
        $this->assertTrue($report2['satisfied']);
    }

    public function test_endpoint_returns_404_for_unknown_key(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/controls/does.not.exist/dependencies')
            ->assertStatus(404);
    }

    public function test_endpoint_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/controls/feature.boosts/dependencies')
            ->assertStatus(401);
    }

    public function test_endpoint_returns_dependency_report(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/controls/feature.boosts/dependencies');

        $res->assertStatus(200)
            ->assertJsonPath('data.setting_key', 'feature.boosts')
            ->assertJsonStructure([
                'data' => ['setting_key', 'dependencies', 'satisfied', 'violations'],
            ]);
    }
}
