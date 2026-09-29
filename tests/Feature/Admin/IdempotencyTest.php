<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\UserRole;
use App\Services\Admin\IdempotencyRegistry;
use Database\Seeders\ControlRegistrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class IdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ControlRegistrySeeder::class);
    }

    private function mainAdmin(): User
    {
        $u = User::factory()->create();
        UserRole::create(['user_id' => $u->id, 'role' => 'MAIN_ADMIN']);
        return $u;
    }

    /** T01: requestHash() stable across key order */
    public function test_request_hash_is_order_independent(): void
    {
        $registry = app(IdempotencyRegistry::class);

        $r1 = Request::create('/api/v1/x', 'POST', ['a' => 1, 'b' => 2]);
        $r2 = Request::create('/api/v1/x', 'POST', ['b' => 2, 'a' => 1]);

        $this->assertEquals(
            $registry->requestHash($r1),
            $registry->requestHash($r2),
        );
    }

    /** T02: requestHash() differs on body change */
    public function test_request_hash_differs_on_body(): void
    {
        $registry = app(IdempotencyRegistry::class);

        $r1 = Request::create('/api/v1/x', 'POST', ['a' => 1]);
        $r2 = Request::create('/api/v1/x', 'POST', ['a' => 99]);

        $this->assertNotEquals(
            $registry->requestHash($r1),
            $registry->requestHash($r2),
        );
    }

    /** T03: begin() with no header returns 'new' */
    public function test_begin_without_header_returns_new(): void
    {
        $registry = app(IdempotencyRegistry::class);
        $user = $this->mainAdmin();
        $req = Request::create('/api/v1/x', 'POST');

        $state = $registry->begin($req, 'test.scope', $user);
        $this->assertEquals('new', $state['state']);
        $this->assertNull($state['key']);
    }

    /** T04: begin() with header returns 'new' first time */
    public function test_begin_first_time_returns_new(): void
    {
        $registry = app(IdempotencyRegistry::class);
        $user = $this->mainAdmin();
        $req = Request::create('/api/v1/x', 'POST', ['a' => 1]);
        $req->headers->set('Idempotency-Key', 'key-abc-001');

        $state = $registry->begin($req, 'test.scope', $user);
        $this->assertEquals('new', $state['state']);
        $this->assertEquals('key-abc-001', $state['key']);
        $this->assertDatabaseHas('idempotency_keys', [
            'scope' => 'test.scope',
            'key' => 'key-abc-001',
            'status' => 'IN_PROGRESS',
        ]);
    }

    /** T05: begin() replay after complete() */
    public function test_begin_replay_after_complete(): void
    {
        $registry = app(IdempotencyRegistry::class);
        $user = $this->mainAdmin();
        $req = Request::create('/api/v1/x', 'POST', ['a' => 1]);
        $req->headers->set('Idempotency-Key', 'key-abc-002');

        $registry->begin($req, 'test.scope', $user);
        $registry->complete($req, 'test.scope', 200, ['ok' => true], $user);

        $state = $registry->begin($req, 'test.scope', $user);
        $this->assertEquals('replay', $state['state']);
        $this->assertEquals(200, $state['record']->response_code);
    }

    /** T06: begin() 409 on same key + different body */
    public function test_begin_conflicts_on_different_body(): void
    {
        $registry = app(IdempotencyRegistry::class);
        $user = $this->mainAdmin();

        $req1 = Request::create('/api/v1/x', 'POST', ['a' => 1]);
        $req1->headers->set('Idempotency-Key', 'key-abc-003');
        $registry->begin($req1, 'test.scope', $user);

        // Same key, different body
        $req2 = Request::create('/api/v1/x', 'POST', ['a' => 999]);
        $req2->headers->set('Idempotency-Key', 'key-abc-003');

        $this->expectException(\App\Exceptions\IdempotencyConflictException::class);
        $registry->begin($req2, 'test.scope', $user);
    }

    /** T07: begin() returns 'progress' for in-flight same key */
    public function test_begin_progress_for_in_flight(): void
    {
        $registry = app(IdempotencyRegistry::class);
        $user = $this->mainAdmin();

        $req = Request::create('/api/v1/x', 'POST', ['a' => 1]);
        $req->headers->set('Idempotency-Key', 'key-abc-004');

        // First call → new (IN_PROGRESS)
        $s1 = $registry->begin($req, 'test.scope', $user);
        $this->assertEquals('new', $s1['state']);

        // Second call (not yet completed) → progress
        $s2 = $registry->begin($req, 'test.scope', $user);
        $this->assertEquals('progress', $s2['state']);
    }

    /** T08: cleanup() removes expired keys */
    public function test_cleanup_removes_expired(): void
    {
        $registry = app(IdempotencyRegistry::class);
        $user = $this->mainAdmin();

        $req = Request::create('/api/v1/x', 'POST', ['a' => 1]);
        $req->headers->set('Idempotency-Key', 'expired-key');
        $registry->begin($req, 'test.scope', $user);

        // Manually expire
        \DB::table('idempotency_keys')
            ->where('key', 'expired-key')
            ->update(['expires_at' => now()->subHour()]);

        $deleted = $registry->cleanup();
        $this->assertGreaterThanOrEqual(1, $deleted);
        $this->assertDatabaseMissing('idempotency_keys', ['key' => 'expired-key']);
    }
}
