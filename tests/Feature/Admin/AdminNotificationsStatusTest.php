<?php

namespace Tests\Feature\Admin;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\AdminNotificationsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminNotificationsStatusTest extends TestCase
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

    private function seedNotification(string $status = Notification::STATUS_SENT, string $channel = Notification::CHANNEL_IN_APP): Notification
    {
        $recipient = User::factory()->create();
        return Notification::create([
            'id'                => (string) Str::uuid(),
            'recipient_user_id' => $recipient->id,
            'type'              => 'need.created',
            'entity_type'       => 'Need',
            'entity_id'         => (string) Str::uuid(),
            'title'             => 'Test',
            'body'              => 'Test body',
            'channel'           => $channel,
            'delivery_status'   => $status,
            'available_at'      => now(),
            'created_at'        => now(),
        ]);
    }

    public function test_service_returns_status_keys(): void
    {
        $status = app(AdminNotificationsService::class)->status();
        $this->assertArrayHasKey('by_status', $status);
        $this->assertArrayHasKey('by_channel', $status);
        $this->assertArrayHasKey('recent', $status);
        $this->assertArrayHasKey('totals', $status);
        $this->assertArrayHasKey('computed_at', $status);
    }

    public function test_service_reports_zero_when_empty(): void
    {
        $s = app(AdminNotificationsService::class)->status();
        $this->assertSame(0, $s['by_status']['pending']);
        $this->assertSame(0, $s['by_status']['sent']);
        $this->assertSame(0, $s['by_status']['failed']);
        $this->assertSame(0, $s['by_status']['skipped']);
        $this->assertSame(0, $s['by_channel']['in_app']);
        $this->assertSame(0, $s['by_channel']['telegram']);
        $this->assertSame(0, $s['totals']['total']);
        $this->assertSame([], $s['recent']);
    }

    public function test_service_counts_by_status(): void
    {
        $this->seedNotification(Notification::STATUS_SENT);
        $this->seedNotification(Notification::STATUS_SENT);
        $this->seedNotification(Notification::STATUS_PENDING);
        $this->seedNotification(Notification::STATUS_FAILED);
        $this->seedNotification(Notification::STATUS_SKIPPED);
        $s = app(AdminNotificationsService::class)->status();
        $this->assertSame(2, $s['by_status']['sent']);
        $this->assertSame(1, $s['by_status']['pending']);
        $this->assertSame(1, $s['by_status']['failed']);
        $this->assertSame(1, $s['by_status']['skipped']);
        $this->assertSame(5, $s['totals']['total']);
    }

    public function test_service_counts_by_channel(): void
    {
        $this->seedNotification(Notification::STATUS_SENT, Notification::CHANNEL_IN_APP);
        $this->seedNotification(Notification::STATUS_SENT, Notification::CHANNEL_TELEGRAM);
        $this->seedNotification(Notification::STATUS_SENT, Notification::CHANNEL_TELEGRAM);
        $s = app(AdminNotificationsService::class)->status();
        $this->assertSame(1, $s['by_channel']['in_app']);
        $this->assertSame(2, $s['by_channel']['telegram']);
    }

    public function test_service_returns_recent(): void
    {
        $this->seedNotification(Notification::STATUS_SENT);
        $s = app(AdminNotificationsService::class)->status();
        $this->assertCount(1, $s['recent']);
        $this->assertSame('need.created', $s['recent'][0]['type']);
    }

    public function test_service_counts_unread(): void
    {
        $this->seedNotification(Notification::STATUS_SENT);
        $this->seedNotification(Notification::STATUS_SENT);
        $s = app(AdminNotificationsService::class)->status();
        $this->assertSame(2, $s['totals']['unread']);
    }

    public function test_notifications_status_requires_auth(): void
    {
        $this->getJson('/api/v1/admin/notifications-status')->assertUnauthorized();
    }

    public function test_notifications_status_returns_real_data(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/notifications-status')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'by_status' => ['pending', 'sent', 'failed', 'skipped'],
                    'by_channel' => ['in_app', 'telegram'],
                    'recent',
                    'totals' => ['total', 'unread'],
                    'computed_at',
                ],
                'meta' => ['screen', 'area', 'source'],
            ])
            ->assertJsonPath('meta.screen', 'A010')
            ->assertJsonPath('meta.source', 'live');
    }

    public function test_notifications_status_reflects_data(): void
    {
        $admin = $this->admin();
        $this->seedNotification(Notification::STATUS_SENT, Notification::CHANNEL_TELEGRAM);
        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/admin/notifications-status')
            ->assertOk()
            ->assertJsonPath('data.by_status.sent', 1)
            ->assertJsonPath('data.by_channel.telegram', 1);
    }

    public function test_notifications_status_returns_403_for_non_admin(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/notifications-status')
            ->assertForbidden();
    }

    public function test_notifications_page_requires_auth(): void
    {
        $this->get('/admin/notifications')->assertRedirect('/admin/login');
    }

    public function test_notifications_page_renders_for_admin(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/notifications')
            ->assertOk()
            ->assertSee('notifications-stats', false)
            ->assertSee('nf-recent', false);
    }

    public function test_notifications_page_contains_fetch_endpoint(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'web')
            ->get('/admin/notifications')
            ->assertOk()
            ->assertSee('/api/v1/admin/notifications-status', false)
            ->assertSee('class="pending"', false);
    }
}
