<?php

namespace Tests\Feature\Boost;

use App\Models\BoostPackage;
use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebugBoostTest extends TestCase
{
    use RefreshDatabase;

    public function test_debug_boost_exception(): void
    {
        // 🔴 This reveals the ACTUAL exception instead of 500
        $this->withoutExceptionHandling();

        $owner = User::factory()->create();
        $cat = Category::factory()->create([
            'name_en' => 'L', 'name_am' => 'ሎ',
            'slug' => 'c' . uniqid(), 'active' => true,
        ]);
        $need = Need::factory()->create([
            'category_id'  => $cat->id,
            'requester_id' => $owner->id,
            'title'        => 'N',
            'description'  => 'D',
            'status'       => Need::STATUS_OPEN,
        ]);
        $pkg = BoostPackage::factory()->create(['active' => true, 'price' => 100]);

        $res = $this->actingAs($owner, 'sanctum')
            ->postJson('/api/v1/needs/' . $need->id . '/boosts', ['package_id' => $pkg->id]);

        $res->assertStatus(201);
    }
}
