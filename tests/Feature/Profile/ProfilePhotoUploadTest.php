<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $file = UploadedFile::fake()->image('me.jpg', 200, 200);
        $this->postJson('/api/v1/profile/photo', ['photo' => $file])->assertStatus(401);
    }

    public function test_uploads_valid_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('me.jpg', 200, 200);

        $res = $this->actingAs($user, 'sanctum')->postJson('/api/v1/profile/photo', ['photo' => $file]);
        $res->assertStatus(200);
        $this->assertNotNull($user->fresh()->profile_photo_path);
    }

    public function test_rejects_non_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/profile/photo', ['photo' => $file])->assertStatus(422);
    }

    public function test_rejects_oversize(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('big.jpg', 200, 200)->size(6000);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/profile/photo', ['photo' => $file])->assertStatus(422);
    }

    public function test_replaces_existing_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/profile/photo', ['photo' => UploadedFile::fake()->image('a.jpg', 200, 200)]);
        $first = $user->fresh()->profile_photo_path;
        $this->assertNotNull($first);

        sleep(1); // ensure distinct timestamp

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/profile/photo', ['photo' => UploadedFile::fake()->image('b.jpg', 200, 200)]);
        $second = $user->fresh()->profile_photo_path;

        $this->assertNotNull($second);
        $this->assertNotSame($first, $second);
    }
}
