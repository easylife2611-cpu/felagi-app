<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Privacy;

use App\Models\User;
use App\Services\Privacy\DataRightsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class DataRightsServiceTest extends TestCase
{
    use RefreshDatabase;

    private DataRightsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataRightsService();
    }

    private function request(): Request
    {
        return Request::create('/api/v1/privacy/erasure', 'POST', [], [], [], [
            'REMOTE_ADDR' => '203.0.113.42',
        ]);
    }

    // ─────────────────────────────────────────
    // Art. 34 — show()
    // ─────────────────────────────────────────

    public function test_show_returns_expected_keys(): void
    {
        $user = User::factory()->create();
        $result = $this->service->show($user);

        foreach (['id', 'name', 'username', 'email', 'created_at'] as $key) {
            $this->assertArrayHasKey($key, $result);
        }
    }

    public function test_show_returns_user_values(): void
    {
        $user = User::factory()->create([
            'name'     => 'Bob',
            'username' => 'bob-test',
            'email'    => 'bob@example.com',
        ]);

        $result = $this->service->show($user);

        $this->assertSame($user->id, $result['id']);
        $this->assertSame('Bob', $result['name']);
        $this->assertSame('bob-test', $result['username']);
        $this->assertSame('bob@example.com', $result['email']);
    }

    public function test_show_does_not_leak_password_or_totp(): void
    {
        $user = User::factory()->create();
        $result = $this->service->show($user);

        $this->assertArrayNotHasKey('password', $result);
        $this->assertArrayNotHasKey('totp_secret', $result);
        $this->assertArrayNotHasKey('totp_recovery_codes', $result);
        $this->assertArrayNotHasKey('remember_token', $result);
    }

    // ─────────────────────────────────────────
    // Art. 35 — rectify()
    // ─────────────────────────────────────────

    public function test_rectify_updates_user_name(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $result = $this->service->rectify($user, ['name' => 'New Name']);

        $this->assertSame('New Name', $result['name']);
        $this->assertSame('New Name', $user->fresh()->name);
    }

    public function test_rectify_returns_fresh_show_payload(): void
    {
        $user = User::factory()->create();

        $result = $this->service->rectify($user, ['name' => 'Rectified']);

        foreach (['id', 'name', 'username', 'email', 'created_at'] as $key) {
            $this->assertArrayHasKey($key, $result);
        }
    }

    public function test_rectify_ignores_non_fillable_fields(): void
    {
        $user = User::factory()->create(['name' => 'Original']);
        $originalId = $user->id;

        // id is not in $fillable — fill() should ignore it
        $this->service->rectify($user, ['id' => 'malicious-id', 'name' => 'Changed']);

        $this->assertSame($originalId, $user->fresh()->id);
        $this->assertSame('Changed', $user->fresh()->name);
    }

    // ─────────────────────────────────────────
    // Art. 36 — requestErasure()
    // ─────────────────────────────────────────

    public function test_request_erasure_returns_pending_with_grace(): void
    {
        $user = User::factory()->create();

        $result = $this->service->requestErasure($user, $this->request());

        $this->assertArrayHasKey('request_id', $result);
        $this->assertSame('pending', $result['status']);
        $this->assertSame(7, $result['grace_days']);
    }

    public function test_request_erasure_persists_row(): void
    {
        $user = User::factory()->create();

        $this->service->requestErasure($user, $this->request());

        $row = DB::table('data_requests')
            ->where('user_id', $user->id)
            ->where('type', 'erasure')
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('pending', $row->status);
        $this->assertSame('203.0.113.42', $row->ip_address);
    }

    public function test_request_erasure_payload_contains_grace_days(): void
    {
        $user = User::factory()->create();

        $this->service->requestErasure($user, $this->request());

        $row = DB::table('data_requests')->where('user_id', $user->id)->first();
        $payload = json_decode($row->payload, true);

        $this->assertIsArray($payload);
        $this->assertSame(7, $payload['grace_days']);
    }

    // ─────────────────────────────────────────
    // Art. 37 — restrict()
    // ─────────────────────────────────────────

    public function test_restrict_returns_pending_with_scope(): void
    {
        $user = User::factory()->create();

        $result = $this->service->restrict($user, ['scope' => 'marketing'], $this->request());

        $this->assertSame('pending', $result['status']);
        $this->assertSame('marketing', $result['scope']);
        $this->assertArrayHasKey('request_id', $result);
    }

    public function test_restrict_persists_row(): void
    {
        $user = User::factory()->create();

        $this->service->restrict($user, ['scope' => 'analytics'], $this->request());

        $row = DB::table('data_requests')
            ->where('user_id', $user->id)
            ->where('type', 'restriction')
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('pending', $row->status);
    }

    // ─────────────────────────────────────────
    // Art. 39 — object()
    // ─────────────────────────────────────────

    public function test_object_returns_pending_with_purpose(): void
    {
        $user = User::factory()->create();

        $result = $this->service->object($user, ['purpose' => 'profiling'], $this->request());

        $this->assertSame('pending', $result['status']);
        $this->assertSame('profiling', $result['purpose']);
        $this->assertArrayHasKey('request_id', $result);
    }

    public function test_object_persists_row(): void
    {
        $user = User::factory()->create();

        $this->service->object($user, ['purpose' => 'profiling'], $this->request());

        $row = DB::table('data_requests')
            ->where('user_id', $user->id)
            ->where('type', 'objection')
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('pending', $row->status);
    }

    // ─────────────────────────────────────────
    // Cross-cutting
    // ─────────────────────────────────────────

    public function test_requests_are_isolated_per_user(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $this->service->requestErasure($a, $this->request());
        $this->service->requestErasure($b, $this->request());

        $this->assertSame(1, DB::table('data_requests')->where('user_id', $a->id)->count());
        $this->assertSame(1, DB::table('data_requests')->where('user_id', $b->id)->count());
    }

    public function test_request_captures_ip_address(): void
    {
        $user = User::factory()->create();

        $this->service->requestErasure($user, $this->request());

        $row = DB::table('data_requests')->where('user_id', $user->id)->first();
        $this->assertSame('203.0.113.42', $row->ip_address);
    }

    public function test_request_ids_are_distinct(): void
    {
        $user = User::factory()->create();

        $a = $this->service->requestErasure($user, $this->request());
        $b = $this->service->object($user, ['purpose' => 'x'], $this->request());

        $this->assertNotSame($a['request_id'], $b['request_id']);
    }
}
