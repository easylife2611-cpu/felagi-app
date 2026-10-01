<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\SettingDraft;
use App\Models\SettingVersion;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\ControlRegistrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AG (audit L276) — Scheduled admin changes.
 */
class ScheduledChangeTest extends TestCase
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

    public function test_schedule_requires_auth(): void
    {
        $this->postJson('/api/v1/admin/changes/00000000-0000-0000-0000-000000000000/schedule', [
            'scheduled_at' => now()->addHour()->toIso8601String(),
        ])->assertStatus(401);
    }

    public function test_schedule_sets_pending_status(): void
    {
        $admin = $this->admin();
        $draft = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 99,
        ])->json('data.id');

        $res = $this->actingAs($admin)->postJson("/api/v1/admin/changes/{$draft}/schedule", [
            'scheduled_at' => now()->addHour()->toIso8601String(),
        ]);

        $res->assertStatus(202)
            ->assertJsonPath('data.scheduled_status', 'PENDING');

        $d = SettingDraft::find($draft);
        $this->assertNotNull($d->scheduled_at);
        $this->assertTrue($d->isScheduled());
    }

    public function test_schedule_rejects_past_time(): void
    {
        $admin = $this->admin();
        $draft = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 99,
        ])->json('data.id');

        $this->actingAs($admin)->postJson("/api/v1/admin/changes/{$draft}/schedule", [
            'scheduled_at' => now()->subHour()->toIso8601String(),
        ])->assertStatus(422);
    }

    public function test_unschedule_cancels_pending(): void
    {
        $admin = $this->admin();
        $draft = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 99,
        ])->json('data.id');

        $this->actingAs($admin)->postJson("/api/v1/admin/changes/{$draft}/schedule", [
            'scheduled_at' => now()->addHour()->toIso8601String(),
        ])->assertStatus(202);

        $this->actingAs($admin)->deleteJson("/api/v1/admin/changes/{$draft}/schedule")
            ->assertStatus(200);

        $d = SettingDraft::find($draft);
        $this->assertSame(SettingDraft::SCHEDULE_CANCELLED, $d->scheduled_status);
    }

    public function test_command_applies_due_scheduled_changes(): void
    {
        $admin = $this->admin();
        $draftId = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 77,
        ])->json('data.id');

        // Schedule 2 seconds in past so isDue() returns true
        $draft = SettingDraft::find($draftId);
        $draft->scheduled_at     = now()->subSecond();
        $draft->scheduled_status = SettingDraft::SCHEDULE_PENDING;
        $draft->save();

        $this->artisan('settings:apply-scheduled')->assertExitCode(0);

        $draft->refresh();
        $this->assertSame(SettingDraft::SCHEDULE_APPLIED, $draft->scheduled_status);
        $this->assertEquals(77, Setting::find('marketplace.max_open_needs_per_user')->value_json['value']);
    }

    public function test_command_is_noop_when_nothing_due(): void
    {
        $admin = $this->admin();
        $draftId = $this->actingAs($admin)->postJson('/api/v1/admin/changes', [
            'setting_key'    => 'marketplace.max_open_needs_per_user',
            'proposed_value' => 88,
        ])->json('data.id');

        // Schedule in future
        SettingDraft::where('id', $draftId)->update([
            'scheduled_at'     => now()->addHours(2),
            'scheduled_status' => SettingDraft::SCHEDULE_PENDING,
        ]);

        $this->artisan('settings:apply-scheduled')->assertExitCode(0);

        $d = SettingDraft::find($draftId);
        $this->assertSame(SettingDraft::SCHEDULE_PENDING, $d->scheduled_status);
    }
}
