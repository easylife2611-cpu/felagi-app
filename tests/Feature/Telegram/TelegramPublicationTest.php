<?php

namespace Tests\Feature\Telegram;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramPublicationTest extends TestCase
{
    use RefreshDatabase;

    private function makeNeed(User $owner): Need
    {
        $cat = Category::factory()->create(['name_en'=>'L','name_am'=>'ሎ','slug'=>'c'.uniqid(),'active'=>true]);
        return Need::factory()->create([
            'category_id' => $cat->id, 'requester_id' => $owner->id,
            'title' => 'N', 'description' => 'D', 'status' => Need::STATUS_OPEN,
        ]);
    }

    public function test_index_requires_auth(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $this->getJson('/api/v1/needs/'.$need->id.'/telegram-publications')->assertStatus(401);
    }

    public function test_index_requires_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $need = $this->makeNeed($owner);
        $this->actingAs($other, 'sanctum')
            ->getJson('/api/v1/needs/'.$need->id.'/telegram-publications')->assertStatus(403);
    }

    public function test_owner_can_list(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $this->actingAs($owner, 'sanctum')
            ->getJson('/api/v1/needs/'.$need->id.'/telegram-publications')->assertStatus(200);
    }

    public function test_stop_requires_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $need = $this->makeNeed($owner);
        $this->actingAs($other, 'sanctum')
            ->postJson('/api/v1/needs/'.$need->id.'/telegram-publication/stop')->assertStatus(403);
    }

    public function test_stop_returns_404_if_no_publication(): void
    {
        $owner = User::factory()->create();
        $need = $this->makeNeed($owner);
        $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/'.$need->id.'/telegram-publication/stop')->assertStatus(404);
    }
}
