<?php

namespace Tests\Feature\Models;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeNotification(array $overrides = []): Notification
    {
        $user = User::factory()->create();
        return Notification::create(array_merge([
            'recipient_user_id' => $user->id,
            'type' => 'test.type',
            'entity_type' => 'need',
            'entity_id' => (string) Str::uuid(),
            'title' => 'Test Title',
            'body' => 'Test body content',
            'channel' => Notification::CHANNEL_IN_APP,
            'delivery_status' => Notification::STATUS_PENDING,
            'available_at' => now(),
            'created_at' => now(),
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $n = $this->makeNotification();
        $this->assertNotNull($n->id);
        $this->assertTrue(Str::isUuid($n->id));
    }

    public function test_uses_no_timestamps(): void
    {
        $n = new Notification();
        $this->assertFalse($n->usesTimestamps());
    }

    public function test_channel_constants(): void
    {
        $this->assertSame('IN_APP', Notification::CHANNEL_IN_APP);
        $this->assertSame('TELEGRAM', Notification::CHANNEL_TELEGRAM);
    }

    public function test_status_constants(): void
    {
        $this->assertSame('PENDING', Notification::STATUS_PENDING);
        $this->assertSame('SENT', Notification::STATUS_SENT);
        $this->assertSame('FAILED', Notification::STATUS_FAILED);
        $this->assertSame('SKIPPED', Notification::STATUS_SKIPPED);
    }

    public function test_recipient_relationship(): void
    {
        $user = User::factory()->create();
        $n = $this->makeNotification(['recipient_user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $n->recipient);
        $this->assertSame($user->id, $n->recipient->id);
    }

    public function test_scope_unread(): void
    {
        $n1 = $this->makeNotification();
        $n2 = $this->makeNotification();
        $n2->markAsRead();

        $unread = Notification::unread()->get();
        $this->assertCount(1, $unread);
        $this->assertSame($n1->id, $unread->first()->id);
    }

    public function test_scope_pending(): void
    {
        $this->makeNotification(['delivery_status' => Notification::STATUS_PENDING]);
        $this->makeNotification(['delivery_status' => Notification::STATUS_SENT]);

        $pending = Notification::pending()->get();
        $this->assertCount(1, $pending);
    }

    public function test_is_read_false_when_no_read_at(): void
    {
        $n = $this->makeNotification();
        $this->assertFalse($n->isRead());
    }

    public function test_mark_as_read_sets_timestamp(): void
    {
        $n = $this->makeNotification();
        $n->markAsRead();
        $n->refresh();
        $this->assertTrue($n->isRead());
        $this->assertNotNull($n->read_at);
    }

    public function test_mark_as_read_is_idempotent(): void
    {
        $n = $this->makeNotification();
        $n->markAsRead();
        $firstRead = $n->read_at;
        sleep(1);
        $n->markAsRead();
        $n->refresh();
        $this->assertEquals($firstRead, $n->read_at);
    }

    public function test_datetime_casts(): void
    {
        $n = $this->makeNotification();
        $n->refresh();
        $this->assertInstanceOf(\Carbon\Carbon::class, $n->available_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $n->created_at);
    }
}
