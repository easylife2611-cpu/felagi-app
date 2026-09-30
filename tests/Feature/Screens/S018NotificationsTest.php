<?php

namespace Tests\Feature\Screens;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S018NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $res = $this->get('/notifications');
        $res->assertStatus(200);
        $res->assertSee('id="state-loading"', false);
    }

    public function test_page_has_filter_chips(): void
    {
        $res = $this->get('/notifications');
        $res->assertSee('data-unread=""', false);
        $res->assertSee('data-unread="1"', false);
    }

    public function test_api_requires_auth(): void
    {
        $this->getJson('/api/v1/notifications')->assertStatus(401);
    }

    public function test_api_returns_user_notifications(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        Notification::factory()->create([
            'recipient_user_id' => $me->id,
            'channel' => Notification::CHANNEL_IN_APP,
            'title' => 'Mine',
        ]);
        Notification::factory()->create([
            'recipient_user_id' => $other->id,
            'channel' => Notification::CHANNEL_IN_APP,
            'title' => 'Other',
        ]);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/notifications');
        $res->assertStatus(200);
        $this->assertCount(1, $res->json('data'));
    }

    public function test_api_returns_unread_count_meta(): void
    {
        $me = User::factory()->create();
        Notification::factory()->create([
            'recipient_user_id' => $me->id,
            'channel' => Notification::CHANNEL_IN_APP,
            'read_at' => null,
        ]);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/notifications');
        $res->assertStatus(200);
        $this->assertSame(1, $res->json('meta.unread_count'));
    }

    public function test_api_filters_unread_only(): void
    {
        $me = User::factory()->create();
        Notification::factory()->create(['recipient_user_id' => $me->id, 'channel' => Notification::CHANNEL_IN_APP, 'read_at' => null]);
        Notification::factory()->create(['recipient_user_id' => $me->id, 'channel' => Notification::CHANNEL_IN_APP, 'read_at' => now()]);

        $res = $this->actingAs($me, 'sanctum')->getJson('/api/v1/notifications?unread=1');
        $res->assertStatus(200);
        $this->assertCount(1, $res->json('data'));
    }

    public function test_mark_read_requires_auth(): void
    {
        $me = User::factory()->create();
        $n = Notification::factory()->create(['recipient_user_id' => $me->id, 'channel' => Notification::CHANNEL_IN_APP]);
        $this->postJson('/api/v1/notifications/'.$n->id.'/read')->assertStatus(401);
    }

    public function test_mark_read_succeeds(): void
    {
        $me = User::factory()->create();
        $n = Notification::factory()->create(['recipient_user_id' => $me->id, 'channel' => Notification::CHANNEL_IN_APP, 'read_at' => null]);

        $res = $this->actingAs($me, 'sanctum')->postJson('/api/v1/notifications/'.$n->id.'/read');
        $res->assertStatus(200);
        $this->assertNotNull($n->fresh()->read_at);
    }
}
