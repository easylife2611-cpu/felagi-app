<?php

namespace Tests\Feature\Models;

use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    public function test_uuid_auto_generated(): void
    {
        $user = User::factory()->create();
        $this->assertNotNull($user->id);
        $this->assertTrue(Str::isUuid($user->id));
    }

    public function test_soft_deletes(): void
    {
        $user = User::factory()->create();
        $id = $user->id;
        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $id]);
        $this->assertNull(User::find($id));
    }

    public function test_status_constants(): void
    {
        $this->assertSame('ACTIVE', User::STATUS_ACTIVE);
        $this->assertSame('SUSPENDED', User::STATUS_SUSPENDED);
        $this->assertSame('BANNED', User::STATUS_BANNED);
    }

    public function test_scope_active(): void
    {
        User::factory()->create(['status' => User::STATUS_ACTIVE]);
        User::factory()->create(['status' => User::STATUS_SUSPENDED]);

        $active = User::active()->get();
        $this->assertCount(1, $active);
        $this->assertSame(User::STATUS_ACTIVE, $active->first()->status);
    }

    public function test_roles_relationship(): void
    {
        $user = User::factory()->create();
        UserRole::create([
            'user_id' => $user->id,
            'role' => 'ADMIN',
            'granted_at' => now(),
        ]);

        $this->assertCount(1, $user->roles);
        $this->assertSame('ADMIN', $user->roles->first()->role);
    }

    public function test_scope_with_role(): void
    {
        $admin = User::factory()->create();
        UserRole::create([
            'user_id' => $admin->id,
            'role' => 'ADMIN',
            'granted_at' => now(),
        ]);

        User::factory()->create();

        $admins = User::withRole('ADMIN')->get();
        $this->assertCount(1, $admins);
        $this->assertSame($admin->id, $admins->first()->id);
    }

    public function test_needs_relationship(): void
    {
        $user = User::factory()->create();
        Need::create([
            'requester_id' => $user->id,
            'category_id' => $this->makeCategory(),
            'title' => 'Test Need',
            'description' => 'Desc',
            'status' => 'OPEN',
        ]);

        $this->assertCount(1, $user->needs);
    }

    public function test_offers_relationship(): void
    {
        $user = User::factory()->create();
        $requester = User::factory()->create();
        $need = Need::create([
            'requester_id' => $requester->id,
            'category_id' => $this->makeCategory(),
            'title' => 'Test Need',
            'description' => 'Desc',
            'status' => 'OPEN',
        ]);
        Offer::create([
            'provider_id' => $user->id,
            'need_id' => $need->id,
            'offered_price' => 100,
            'currency' => 'ETB',
            'proposal_message' => 'Test proposal',
            'status' => 'PENDING',
        ]);

        $this->assertCount(1, $user->offers);
    }

    public function test_totp_secret_encrypted_cast(): void
    {
        $user = User::factory()->create([
            'totp_secret' => 'ABCDEFGHIJKLMNOP',
        ]);

        $raw = \DB::table('users')->where('id', $user->id)->value('totp_secret');
        $this->assertNotSame('ABCDEFGHIJKLMNOP', $raw);

        $user->refresh();
        $this->assertSame('ABCDEFGHIJKLMNOP', $user->totp_secret);
    }

    public function test_hidden_attributes(): void
    {
        $user = User::factory()->create(['phone_number' => '+251911234567']);
        $array = $user->toArray();
        $this->assertArrayNotHasKey('phone_number', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }
}
