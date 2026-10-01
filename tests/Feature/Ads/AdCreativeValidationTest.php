<?php

namespace Tests\Feature\Ads;

use App\Models\AdCreative;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdCreativeValidationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $u = User::factory()->create();
        UserRole::create(['user_id' => $u->id, 'role' => UserRole::ROLE_MAIN_ADMIN]);
        return $u;
    }

    private function creative(array $overrides = []): AdCreative
    {
        return AdCreative::factory()->create($overrides);
    }

    public function test_requires_auth(): void
    {
        $c = $this->creative();
        $this->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate")
             ->assertStatus(401);
    }

    public function test_404_for_unknown_creative(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum')
             ->postJson('/api/v1/admin/ads/creatives/00000000-0000-0000-0000-000000000000/validate')
             ->assertStatus(404);
    }

    public function test_valid_text_only_creative_passes(): void
    {
        $admin = $this->admin();
        $c = $this->creative();

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(200)
            ->assertJsonPath('data.status', 'VALID')
            ->assertJsonPath('data.spec', 'ADS-17')
            ->assertJsonPath('data.has_media', false);

        $c->refresh();
        $this->assertSame('VALID', $c->validation_receipt['status']);
    }

    public function test_missing_amharic_title_fails(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['copy_am_title' => '']);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_FAILED')
            ->assertJsonStructure(['error' => ['details' => ['copy_am_title']]]);
    }

    public function test_missing_english_body_fails(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['copy_en_body' => '']);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(422)
            ->assertJsonStructure(['error' => ['details' => ['copy_en_body']]]);
    }

    public function test_title_exceeding_code_points_fails(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['copy_am_title' => str_repeat('ሀ', 81)]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(422)
            ->assertJsonStructure(['error' => ['details' => ['copy_am_title']]]);
    }

    public function test_body_exceeding_code_points_fails(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['copy_en_body' => str_repeat('a', 241)]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(422)
            ->assertJsonStructure(['error' => ['details' => ['copy_en_body']]]);
    }

    public function test_cta_exceeding_code_points_fails(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['copy_en_cta' => str_repeat('x', 31)]);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(422)
            ->assertJsonStructure(['error' => ['details' => ['copy_en_cta']]]);
    }

    public function test_html_markup_rejected(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['copy_en_title' => '<b>Buy now</b>']);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(422)
            ->assertJsonStructure(['error' => ['details' => ['copy_en_title']]]);
    }

    public function test_script_markup_rejected(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['copy_en_body' => '<script>alert(1)</script>']);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(422)
            ->assertJsonStructure(['error' => ['details' => ['copy_en_body']]]);
    }

    public function test_javascript_scheme_rejected(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['copy_en_cta' => 'javascript:alert(1)']);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(422)
            ->assertJsonStructure(['error' => ['details' => ['copy_en_cta']]]);
    }

    public function test_amharic_unicode_title_counts_by_code_point(): void
    {
        $admin = $this->admin();
        // 80 Amharic characters should pass (each is 1 code point)
        $c = $this->creative(['copy_am_title' => str_repeat('ሀ', 80)]);

        $this->actingAs($admin, 'sanctum')
             ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate")
             ->assertStatus(200);
    }

    public function test_media_asset_returns_requires_evidence(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['media_asset_id' => 'nonexistent-asset.png']);

        $res = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate");

        $res->assertStatus(422)
            ->assertJsonStructure(['error' => ['details' => ['media_asset_id']]]);
    }

    public function test_invalid_receipt_is_persisted(): void
    {
        $admin = $this->admin();
        $c = $this->creative(['copy_en_title' => '<b>x</b>']);

        $this->actingAs($admin, 'sanctum')
             ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate")
             ->assertStatus(422);

        $c->refresh();
        $this->assertSame('INVALID', $c->validation_receipt['status']);
        $this->assertArrayHasKey('copy_en_title', $c->validation_receipt['errors']);
    }

    public function test_all_three_formats_accept_valid_text(): void
    {
        $admin = $this->admin();
        foreach (AdCreative::FORMATS as $format) {
            $c = $this->creative(['format' => $format]);
            $this->actingAs($admin, 'sanctum')
                 ->postJson("/api/v1/admin/ads/creatives/{$c->id}/validate")
                 ->assertStatus(200)
                 ->assertJsonPath('data.format', $format);
        }
    }
}
