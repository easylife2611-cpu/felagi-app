<?php

declare(strict_types=1);

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * SCREEN-S022 — Telegram status/stop acceptance tests.
 *
 * Spec: Acceptance_Cases, Accessibility_Matrix, Screen_by_Screen,
 *       Runtime_State_Matrix, Traceability_Matrix.
 *
 * Non-duplication:
 *   S022TelegramPublicationsTest.php (7 tests, structural ids) — untouched.
 *   TelegramPublicationTest.php (service) — untouched.
 *   This file asserts acceptance-level contracts only.
 */
final class S022AcceptanceTest extends TestCase
{
    use RefreshDatabase;

    private User $requester;
    private Need $need;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requester = User::factory()->create();
        $owner = User::factory()->create();

        $cat = Category::create([
            'slug'       => 's022-' . Str::lower(Str::random(6)),
            'name_am'    => 'ምድብ',
            'name_en'    => 'Cat',
            'active'     => true,
            'sort_order' => 1,
        ]);

        $this->need = Need::create([
            'requester_id' => $this->requester->id,
            'category_id'  => $cat->id,
            'title'        => 'Test Need',
            'description'  => 'Desc',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ]);
    }

    private function url(): string
    {
        return "/needs/{$this->need->id}/publications";
    }

    private function html(string $locale = 'en'): string
    {
        app()->setLocale($locale);
        return view('telegram-publications')->render();
    }

    // ─── Route + authorization ───

    public function test_canonical_route_is_registered(): void
    {
        $this->get($this->url())->assertStatus(200);
    }

    public function test_authorized_requester_reaches_shell(): void
    {
        $res = $this->actingAs($this->requester)->get($this->url());
        $res->assertStatus(200);
        $res->assertSee('id="publications-list"', false);
    }

    // ─── Locales ───

    public function test_renders_english_title(): void
    {
        $json = json_decode(file_get_contents(base_path('lang/en.json')), true);
        $this->assertStringContainsString($json['screenS022'], $this->html('en'));
    }

    public function test_renders_amharic_title(): void
    {
        $json = json_decode(file_get_contents(base_path('lang/am.json')), true);
        $this->assertStringContainsString($json['screenS022'], $this->html('am'));
    }

    public function test_html_lang_attribute_matches_locale(): void
    {
        $this->assertMatchesRegularExpression('/<html[^>]*lang="en"/', $this->html('en'));
        $this->assertMatchesRegularExpression('/<html[^>]*lang="am"/', $this->html('am'));
    }

    // ─── Title key ───

    public function test_title_key_screenS022_exists_in_en(): void
    {
        $json = json_decode(file_get_contents(base_path('lang/en.json')), true);
        $this->assertArrayHasKey('screenS022', $json);
        $this->assertNotEmpty($json['screenS022']);
    }

    public function test_title_key_screenS022_exists_in_am(): void
    {
        $json = json_decode(file_get_contents(base_path('lang/am.json')), true);
        $this->assertArrayHasKey('screenS022', $json);
        $this->assertNotEmpty($json['screenS022']);
    }

    // ─── a11y ───

    public function test_main_landmark_present_and_labelled(): void
    {
        $this->assertMatchesRegularExpression(
            '/<main[^>]*role="main"[^>]*aria-labelledby="page-title"/',
            $this->html()
        );
    }

    public function test_heading_is_focusable(): void
    {
        $this->assertMatchesRegularExpression(
            '/<h2[^>]*id="page-title"[^>]*tabindex="-1"/',
            $this->html()
        );
    }

    public function test_focus_visible_styles_present(): void
    {
        $this->assertStringContainsString(':focus-visible', $this->html());
    }

    public function test_sr_only_class_present(): void
    {
        $this->assertStringContainsString('sr-only', $this->html());
    }

    public function test_toast_is_polite_live_region(): void
    {
        $this->assertMatchesRegularExpression(
            '/<div[^>]*id="toast"[^>]*aria-live="polite"/',
            $this->html()
        );
    }

    public function test_notice_warn_has_alert_role(): void
    {
        $this->assertMatchesRegularExpression(
            '/<div[^>]*id="api-warn"[^>]*role="alert"/',
            $this->html()
        );
    }

    public function test_no_assertive_live_regions(): void
    {
        $this->assertStringNotContainsString('aria-live="assertive"', $this->html());
    }

    // ─── Responsive ───

    public function test_viewport_meta_present(): void
    {
        $html = $this->html();
        $this->assertStringContainsString('name="viewport"', $html);
        $this->assertStringContainsString('width=device-width', $html);
    }

    public function test_max_width_720_per_spec(): void
    {
        $this->assertStringContainsString('max-width:720px', $this->html());
    }

    // ─── Recovery paths ───

    public function test_401_recovery_present(): void
    {
        $this->assertStringContainsString('r.status===401', $this->html());
    }

    public function test_403_recovery_present(): void
    {
        $this->assertStringContainsString('r.status===403', $this->html());
    }

    public function test_404_recovery_present(): void
    {
        $this->assertStringContainsString('r.status===404', $this->html());
    }

    public function test_429_rate_limited_present(): void
    {
        $this->assertStringContainsString('r.status===429', $this->html());
    }

    public function test_409_conflict_present(): void
    {
        $this->assertStringContainsString('res.s===409', $this->html());
    }

    public function test_offline_banner_present(): void
    {
        $this->assertStringContainsString('offline-banner', $this->html());
    }

    // ─── States ───

    public function test_state_loading_present(): void
    {
        $this->assertStringContainsString('id="state-loading"', $this->html());
    }

    public function test_state_empty_present(): void
    {
        $this->assertStringContainsString('id="state-empty"', $this->html());
    }

    public function test_state_error_present(): void
    {
        $this->assertStringContainsString('id="state-error"', $this->html());
    }

    public function test_content_container_present(): void
    {
        $this->assertStringContainsString('id="content"', $this->html());
    }

    // ─── Status labels (5 visible) ───

    public function test_publication_status_labels_used(): void
    {
        $src = file_get_contents(resource_path('views/telegram-publications.blade.php'));
        foreach (['pubStatusSent', 'pubStatusPending', 'pubStatusFailed', 'pubStatusStopped', 'pubStatusStopping'] as $k) {
            $this->assertStringContainsString($k, $src, "Missing status key usage: {$k}");
        }
    }

    // ─── i18n integrity ───

    public function test_all_used_keys_exist_in_both_locales(): void
    {
        $en = json_decode(file_get_contents(base_path('lang/en.json')), true);
        $am = json_decode(file_get_contents(base_path('lang/am.json')), true);

        $html = file_get_contents(resource_path('views/telegram-publications.blade.php'));
        preg_match_all("/__\\('([a-zA-Z0-9_]+)'\\)/", $html, $m);

        foreach (array_unique($m[1]) as $key) {
            $this->assertArrayHasKey($key, $en, "Missing en key: {$key}");
            $this->assertArrayHasKey($key, $am, "Missing am key: {$key}");
        }
    }

    // ─── Primary action ───

    public function test_stop_action_present(): void
    {
        $src = file_get_contents(resource_path('views/telegram-publications.blade.php'));
        $this->assertStringContainsString('stopPublication', $src);
    }

    public function test_confirm_stop_present(): void
    {
        $src = file_get_contents(resource_path('views/telegram-publications.blade.php'));
        $this->assertStringContainsString('confirmStop', $src);
    }

    // ─── Ads ───

    public function test_no_ad_slots_rendered(): void
    {
        $this->assertStringNotContainsString('data-ad-slot', $this->html());
    }

    // ─── REQUIRES_EVIDENCE (spec states not in view yet) ───

    public function test_spec_state_queued(): void
    {
        $this->markTestIncomplete('S022.queued — not surfaced in view. WP pending.');
    }

    public function test_spec_state_retry(): void
    {
        $this->markTestIncomplete('S022.retry — not surfaced in view. WP pending.');
    }

    public function test_spec_state_uncertain(): void
    {
        $this->markTestIncomplete('S022.uncertain — not surfaced in view. WP pending.');
    }

    public function test_spec_state_skipped(): void
    {
        $this->markTestIncomplete('S022.skipped — not surfaced in view. WP pending.');
    }

    public function test_spec_state_paused(): void
    {
        $this->markTestIncomplete('S022.paused — not surfaced in view. WP pending.');
    }
}
