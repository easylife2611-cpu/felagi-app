<?php

namespace Tests\Feature\Models;

use App\Models\OutboxEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OutboxEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_uuid_auto_generated_on_create(): void
    {
        $event = OutboxEvent::create([
            'event_type' => 'setting.published',
            'aggregate_type' => 'setting_version',
            'aggregate_id' => (string) Str::uuid(),
            'event_key' => 'evt-' . Str::random(8),
            'payload_json' => ['k' => 'v'],
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        $this->assertNotNull($event->id);
        $this->assertTrue(Str::isUuid($event->id));
    }

    public function test_uses_no_timestamps(): void
    {
        $event = new OutboxEvent();
        $this->assertFalse($event->usesTimestamps());
    }

    public function test_status_constants_defined(): void
    {
        $this->assertSame('PENDING', OutboxEvent::STATUS_PENDING);
        $this->assertSame('PROCESSING', OutboxEvent::STATUS_PROCESSING);
        $this->assertSame('DONE', OutboxEvent::STATUS_DONE);
        $this->assertSame('FAILED', OutboxEvent::STATUS_FAILED);
    }

    public function test_payload_json_casts_to_array(): void
    {
        $event = OutboxEvent::create([
            'event_type' => 'setting.published',
            'aggregate_type' => 'setting_version',
            'aggregate_id' => (string) Str::uuid(),
            'event_key' => 'evt-' . Str::random(8),
            'payload_json' => ['setting_key' => 'x', 'n' => 42],
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        $event->refresh();
        $this->assertIsArray($event->payload_json);
        $this->assertSame('x', $event->payload_json['setting_key']);
    }

    public function test_attempts_casts_to_integer(): void
    {
        $event = OutboxEvent::create([
            'event_type' => 'test',
            'aggregate_type' => 'setting_version',
            'aggregate_id' => (string) Str::uuid(),
            'event_key' => 'evt-' . Str::random(8),
            'payload_json' => [],
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 5,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        $event->refresh();
        $this->assertIsInt($event->attempts);
        $this->assertSame(5, $event->attempts);
    }

    public function test_scope_pending_filters_correctly(): void
    {
        OutboxEvent::create([
            'event_type' => 'test',
            'aggregate_type' => 'setting_version',
            'aggregate_id' => (string) Str::uuid(),
            'event_key' => 'evt-pending',
            'payload_json' => [],
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 0,
            'available_at' => now()->subMinute(),
            'created_at' => now(),
        ]);
        OutboxEvent::create([
            'event_type' => 'test',
            'aggregate_type' => 'setting_version',
            'aggregate_id' => (string) Str::uuid(),
            'event_key' => 'evt-done',
            'payload_json' => [],
            'status' => OutboxEvent::STATUS_DONE,
            'attempts' => 0,
            'available_at' => now()->subMinute(),
            'created_at' => now(),
        ]);

        $pending = OutboxEvent::pending()->get();
        $this->assertCount(1, $pending);
        $this->assertSame('evt-pending', $pending->first()->event_key);
    }

    public function test_scope_pending_excludes_future_available_at(): void
    {
        OutboxEvent::create([
            'event_type' => 'test',
            'aggregate_type' => 'setting_version',
            'aggregate_id' => (string) Str::uuid(),
            'event_key' => 'evt-future',
            'payload_json' => [],
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 0,
            'available_at' => now()->addHour(),
            'created_at' => now(),
        ]);

        $pending = OutboxEvent::pending()->get();
        $this->assertCount(0, $pending);
    }

    public function test_mark_done_updates_status_and_completed_at(): void
    {
        $event = OutboxEvent::create([
            'event_type' => 'test',
            'aggregate_type' => 'setting_version',
            'aggregate_id' => (string) Str::uuid(),
            'event_key' => 'evt-' . Str::random(8),
            'payload_json' => [],
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        $event->markDone();
        $event->refresh();

        $this->assertSame(OutboxEvent::STATUS_DONE, $event->status);
        $this->assertNotNull($event->completed_at);
    }

    public function test_mark_failed_increments_attempts(): void
    {
        $event = OutboxEvent::create([
            'event_type' => 'test',
            'aggregate_type' => 'setting_version',
            'aggregate_id' => (string) Str::uuid(),
            'event_key' => 'evt-' . Str::random(8),
            'payload_json' => [],
            'status' => OutboxEvent::STATUS_PENDING,
            'attempts' => 2,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        $event->markFailed();
        $event->refresh();

        $this->assertSame(OutboxEvent::STATUS_FAILED, $event->status);
        $this->assertSame(3, $event->attempts);
    }
}
