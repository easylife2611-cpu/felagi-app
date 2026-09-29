<?php

namespace Tests\Feature\Models;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_uuid_auto_generated_on_create(): void
    {
        $log = AuditLog::create([
            'action' => 'TEST_ACTION',
            'entity_type' => 'setting',
            'entity_id' => (string) Str::uuid(),
            'occurred_at' => now(),
            'request_id' => 'test-request',
        ]);

        $this->assertNotNull($log->id);
        $this->assertTrue(Str::isUuid($log->id));
    }

    public function test_uses_no_timestamps(): void
    {
        $log = new AuditLog();
        $this->assertFalse($log->usesTimestamps());
    }

    public function test_safe_metadata_casts_to_array(): void
    {
        $log = AuditLog::create([
            'action' => 'TEST_ACTION',
            'entity_type' => 'setting',
            'entity_id' => (string) Str::uuid(),
            'safe_metadata' => ['k1' => 'v1', 'k2' => 42],
            'occurred_at' => now(),
            'request_id' => 'test-request',
        ]);

        $log->refresh();
        $this->assertIsArray($log->safe_metadata);
        $this->assertSame('v1', $log->safe_metadata['k1']);
    }

    public function test_occurred_at_casts_to_datetime(): void
    {
        $log = AuditLog::create([
            'action' => 'TEST_ACTION',
            'entity_type' => 'setting',
            'entity_id' => (string) Str::uuid(),
            'occurred_at' => '2026-09-29 10:00:00',
            'request_id' => 'test-request',
        ]);

        $log->refresh();
        $this->assertInstanceOf(\Carbon\Carbon::class, $log->occurred_at);
    }

    public function test_scope_for_entity_filters_correctly(): void
    {
        $entityId = (string) Str::uuid();
        AuditLog::create([
            'action' => 'A1',
            'entity_type' => 'setting',
            'entity_id' => $entityId,
            'occurred_at' => now(),
            'request_id' => 'test-request',
        ]);
        AuditLog::create([
            'action' => 'A2',
            'entity_type' => 'other',
            'entity_id' => $entityId,
            'occurred_at' => now(),
            'request_id' => 'test-request',
        ]);

        $result = AuditLog::forEntity('setting', $entityId)->get();
        $this->assertCount(1, $result);
        $this->assertSame('A1', $result->first()->action);
    }

    public function test_scope_by_actor_filters_correctly(): void
    {
        $actor = User::factory()->create();
        AuditLog::create([
            'actor_id' => $actor->id,
            'action' => 'A1',
            'entity_type' => 'setting',
            'entity_id' => (string) Str::uuid(),
            'occurred_at' => now(),
            'request_id' => 'test-request',
        ]);

        $result = AuditLog::byActor($actor->id)->get();
        $this->assertCount(1, $result);
        $this->assertSame('A1', $result->first()->action);
    }

    public function test_actor_relationship(): void
    {
        $user = User::factory()->create();
        $log = AuditLog::create([
            'actor_id' => $user->id,
            'action' => 'A1',
            'entity_type' => 'setting',
            'entity_id' => (string) Str::uuid(),
            'occurred_at' => now(),
            'request_id' => 'test-request',
        ]);

        $this->assertInstanceOf(User::class, $log->actor);
        $this->assertSame($user->id, $log->actor->id);
    }

    public function test_hash_chain_fields_are_fillable(): void
    {
        $prev = str_repeat('a', 64);
        $curr = str_repeat('b', 64);

        $log = AuditLog::create([
            'action' => 'A1',
            'entity_type' => 'setting',
            'entity_id' => (string) Str::uuid(),
            'occurred_at' => now(),
            'request_id' => 'test-request',
            'prev_hash' => $prev,
            'hash' => $curr,
        ]);

        $log->refresh();
        $this->assertSame($prev, $log->prev_hash);
        $this->assertSame($curr, $log->hash);
    }
}
