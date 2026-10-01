<?php

namespace Tests\Feature\Ads;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ADS-55 — Privacy / Consent documentation.
 */
class SponsoredAdsPrivacyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        UserRole::create(['user_id' => $u->id, 'role' => UserRole::ROLE_MAIN_ADMIN]);
        return $u;
    }

    public function test_canonical_privacy_doc_exists(): void
    {
        $path = base_path('docs/privacy/SPONSORED_ADS_PRIVACY_CONSENT.md');
        $this->assertFileExists($path);
        $body = file_get_contents($path);
        $this->assertStringContainsString('ADS-55', $body);
        $this->assertStringContainsString('Contextual', $body);
        $this->assertStringContainsString('30 days', $body);
        $this->assertStringContainsString('180 days', $body);
        $this->assertStringContainsString('No third-party tracking pixels', $body);
    }

    public function test_a023_renders_privacy_section(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'web')
            ->get('/admin/monetization/sponsored-ads');

        $res->assertStatus(200)
            ->assertSee('id="ads-privacy"', false)
            ->assertSee('Privacy', false);
    }

    public function test_a023_shows_contextual_signals_list(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'web')
            ->get('/admin/monetization/sponsored-ads');

        $res->assertStatus(200)
            ->assertSee('Placement ID')
            ->assertSee('Public marketplace category')
            ->assertSee('Explicitly coarse region')
            ->assertSee('Campaign schedule');
    }

    public function test_a023_shows_never_used_list(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'web')
            ->get('/admin/monetization/sponsored-ads');

        $res->assertStatus(200)
            ->assertSee('Messages or attachments')
            ->assertSee('Phone number or address')
            ->assertSee('Payment or wallet records')
            ->assertSee('AI comparison results')
            ->assertSee('Inferred sensitive traits');
    }

    public function test_a023_shows_retention_values(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'web')
            ->get('/admin/monetization/sponsored-ads');

        $res->assertStatus(200)
            ->assertSee('30 days')
            ->assertSee('180 days');
    }

    public function test_a023_shows_tracking_prohibitions(): void
    {
        $admin = $this->admin();
        $res = $this->actingAs($admin, 'web')
            ->get('/admin/monetization/sponsored-ads');

        $res->assertStatus(200)
            ->assertSee('No third-party tracking pixels or scripts')
            ->assertSee('No cross-service or cross-app identity');
    }

    public function test_en_lang_keys_exist(): void
    {
        $en = json_decode(file_get_contents(base_path('lang/en.json')), true);
        foreach ([
            'admin.ads.privacy.title',
            'admin.ads.privacy.subtitle',
            'admin.ads.privacy.uses_placement',
            'admin.ads.privacy.never_messages',
            'admin.ads.privacy.retention_raw',
            'admin.ads.privacy.tracking_pixels',
            'admin.ads.privacy.doc_ref',
        ] as $k) {
            $this->assertArrayHasKey($k, $en, "Missing EN key: {$k}");
        }
    }

    public function test_am_lang_keys_exist(): void
    {
        $am = json_decode(file_get_contents(base_path('lang/am.json')), true);
        foreach ([
            'admin.ads.privacy.title',
            'admin.ads.privacy.subtitle',
            'admin.ads.privacy.uses_placement',
            'admin.ads.privacy.never_messages',
            'admin.ads.privacy.retention_raw',
            'admin.ads.privacy.tracking_pixels',
            'admin.ads.privacy.doc_ref',
        ] as $k) {
            $this->assertArrayHasKey($k, $am, "Missing AM key: {$k}");
        }
    }

    public function test_privacy_section_renders_in_amharic(): void
    {
        $admin = $this->admin();
        app()->setLocale('am');

        $res = $this->actingAs($admin, 'web')
            ->get('/admin/monetization/sponsored-ads');

        $res->assertStatus(200)
            ->assertSee('ግላዊነት እና ፈቃድ', false);
    }
}
