<?php

namespace Tests\Feature\Admin;

use App\Models\Comparison;
use App\Models\ComparisonAttempt;
use App\Models\Need;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminAiStatusTest extends TestCase
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

    private function seedSetting(string $key, mixed $value, string $group = 'CONFIG', string $type = 'INTEGER'): Setting
    {
        return Setting::create([
            'key'            => $key,
            'group'          => $group,
            'type'           => $type,
            'value_json'     => ['value' => $value],
            'default_json'   => ['value' => null],
            'risk'           => 'MEDIUM',
            'is_secret'      => false,
            'version_number' => 1,
        ]);
    }

    private function seedComparison(string $status = Comparison::STATUS_COMPLETED, int $inTokens = 100, int $outTokens = 50, float $cost = 0.01): Comparison
    {
        // need_id is a FK — must create a real Need first.
        $need = Need::factory()->create();
        return Comparison::create([
            'id'                  => (string) Str::uuid(),
            'need_id'             => $need->id,
            'version_number'      => 1,
            'triggered_by'        => User::factory()->create()->id,
            'status'              => $status,
            'criteria_version'    => 'v1',
            'prompt_version'      => 'v1',
            'output_schema_version' => 'v1',
            'ai_provider'         => 'gemini',
            'model_id'            => 'gemini-2.5-flash',
            'need_snapshot'       => [],
            'snapshot_hash'       => str_repeat('a', 64),
            'eligible_offer_count'=> 0,
            'included_offer_count'=> 0,
            'input_token_count'   => $inTokens,
            'output_token_count'  => $outTokens,
            'estimated_cost'      => $cost,
            'attempt_count'       => 1,
            'requested_at'        => now(),
        ]);
    }

    public function test_service_returns_status_keys(): void
    {
        $status = app(AdminAiService::class)->status();
        $this->assertArrayHasKey('config', $status);
        $this->assertArrayHasKey('evaluation', $status);
        $this->assertArrayHasKey('token_limits', $status);
        $this->assertArrayHasKey('usage_stats', $status);
        $this->assertArrayHasKey('recent_failures', $status);
        $this->assertArrayHasKey('computed_at', $status);
    }

    public function test_service_defaults_when_db_empty(): void
    {
        $s = app(AdminAiService::class)->status();
        $this->assertSame(60, $s['config']['timeout_seconds']);
        $this->assertSame('v1', $s['config']['criteria_version']);
        $this->assertSame('v1', $s['config']['prompt_version']);
        $this->assertSame('default', $s['config']['source']);
        $this->assertSame(3, $s['evaluation']['max_attempts']);
        $this->assertSame(10, $s['evaluation']['user_daily_cap']);
        $this->assertSame(3, $s['evaluation']['need_daily_cap']);
        $this->assertSame(4000, $s['token_limits']['max_input_tokens']);
        $this->assertSame(1000, $s['token_limits']['max_output_tokens']);
    }

    public function test_service_reads_db_settings_when_present(): void
    {
        $this->seedSetting('ai.model_id', 'gemini-2.5-pro', 'CONFIG', 'STRING');
        $this->seedSetting('ai.timeout_seconds', 120);
        $s = app(AdminAiService::class)->status();
        $this->assertSame('gemini-2.5-pro', $s['config']['model_id']);
        $this->assertSame(120, $s['config']['timeout_seconds']);
        $this->assertSame('db', $s['config']['source']);
    }

    public function test_service_reports_zero_stats_without_data(): void
    {
        $s = app(AdminAiService::class)->status();
        $this->assertSame(0, $s['usage_stats']['total_comparisons']);
        $this->assertSame(0, $s['usage_stats']['completed']);
        $this->assertSame(0, $s['usage_stats']['failed']);
        $this->assertSame(0, $s['usage_stats']['processing']);
        $this->assertSame(0, $s['usage_stats']['total_input_tokens']);
        $this->assertSame(0, $s['usage_stats']['total_output_tokens']);
        $this->assertSame(0.0, (float) $s['usage_stats']['total_cost']);
    }

    public function test_service_counts_comparisons_by_status(): void
    {
        $this->seedComparison(Comparison::STATUS_COMPLETED);
        $this->seedComparison(Comparison::STATUS_COMPLETED);
        $this->seedComparison(Comparison::STATUS_FAILED);
        $this->seedComparison(Comparison::STATUS_PROCESSING);
        $s = app(AdminAiService::class)->status();
        $this->assertSame(4, $s['usage_stats']['total_comparisons']);
        $this->assertSame(2, $s['usage_stats']['completed']);
        $this->assertSame(1, $s['usage_stats']['failed']);
        $this->assertSame(1, $s['usage_stats']['processing']);
    }

    public function test_service_sums_token_usage_and_cost(): void
    {
        $this->seedComparison(Comparison::STATUS_COMPLETED, 100, 50, 0.01);
        $this->seedComparison(Comparison::STATUS_COMPLETED, 200, 100, 0.02);
        $s = app(AdminAiService::class)->status();
        $this->assertSame(300, $s['usage_stats']['total_input_tokens']);
        $this->assertSame(150, $s['usage_stats']['total_output_tokens']);
        $this->assertEqualsWithDelta(0.03, (float) $s['usage_stats']['total_cost'], 0.0001);
    }

    public function test_service_returns_recent_failures(): void
    {
        $comparison = $this->seedComparison(Comparison::STATUS_FAILED);
        ComparisonAttempt::create([
            'id'            => (string) Str::uuid(),
            'comparison_id' => $comparison->id,
            'attempt_number'=> 1,
            'status'        => 'failed',
            'failure_code'  => 'SCHEMA_MISMATCH',
            'started_at'    => now(),
            'finished_at'   => now(),
            'token_usage'   => [],
        ]);
        $s = app(AdminAiService::class)->status();
        $this->assertCount(1, $s['recent_failures']);
        $this->assertSame('SCHEMA_MISMATCH', $s['recent_failures'][0]['failure_code']);
    }

    public function test_ai_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/ai-status')->assertUnauthorized();
    }

    public function test_ai_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/ai-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'config' => ['model_id', 'timeout_seconds', 'criteria_version', 'prompt_version', 'source'],
                    'evaluation' => ['max_attempts', 'user_daily_cap', 'need_daily_cap'],
                    'token_limits' => ['max_input_tokens', 'max_output_tokens'],
                    'usage_stats' => ['total_comparisons', 'completed', 'failed', 'processing', 'total_input_tokens', 'total_output_tokens', 'total_cost'],
                    'recent_failures',
                    'computed_at',
                ],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A006')
            ->assertJsonPath('meta.source', 'live');
    }

    public function test_ai_status_reflects_db_setting(): void
    {
        $admin = $this->admin();
        $this->seedSetting('ai.timeout_seconds', 90);
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/ai-status')
            ->assertOk()
            ->assertJsonPath('data.config.timeout_seconds', 90);
    }

    public function test_ai_status_returns_403_for_non_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/ai-status')
            ->assertForbidden();
    }

    public function test_ai_page_requires_auth(): void
    {
        $this->get('/admin/ai')->assertRedirect('/admin/login');
    }

    public function test_ai_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/ai')
            ->assertOk()
            ->assertSee('ai-stats', false)
            ->assertSee('ai-failures', false);
    }

    public function test_ai_page_contains_fetch_endpoint(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/ai')
            ->assertOk()
            ->assertSee('/api/v1/admin/ai-status', false);
    }

    public function test_ai_page_has_pending_notice(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/ai')
            ->assertOk()
            ->assertSee('class="pending"', false);
    }
}
