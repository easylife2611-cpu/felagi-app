<?php

declare(strict_types=1);

namespace Tests\Feature\Screens;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * L346-E — /profile/2fa UI structure + i18n + auth contract.
 *
 * These tests verify the SHIPPED view. They do NOT change the view.
 * A11y gaps are documented in docs/reports/L346E_FINDINGS_20261003.md.
 */
final class Profile2faUiTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    // ─────────────────────────────────────────
    // Auth
    // ─────────────────────────────────────────

    public function test_guest_is_redirected(): void
    {
        $res = $this->get('/profile/2fa');
        // Some setups redirect, some return 401. Both acceptable.
        $this->assertContains($res->status(), [302, 401]);
    }

    public function test_authenticated_user_gets_200(): void
    {
        $res = $this->actingAs($this->user())->get('/profile/2fa');
        $res->assertStatus(200);
    }

    // ─────────────────────────────────────────
    // Structure
    // ─────────────────────────────────────────

    public function test_html_has_lang_attribute(): void
    {
        $res = $this->actingAs($this->user())->get('/profile/2fa');
        $res->assertStatus(200);
        $this->assertMatchesRegularExpression('/<html\s+lang="[^"]+"/i', $res->getContent());
    }

    public function test_viewport_meta_present(): void
    {
        $res = $this->actingAs($this->user())->get('/profile/2fa');
        $this->assertStringContainsString(
            'name="viewport"',
            $res->getContent()
        );
    }

    public function test_title_uses_translation_key(): void
    {
        $res = $this->actingAs($this->user())->get('/profile/2fa');
        $this->assertStringContainsString(
            __('2fa.title'),
            $res->getContent()
        );
    }

    public function test_csrf_token_is_present(): void
    {
        $res = $this->actingAs($this->user())->get('/profile/2fa');
        $this->assertMatchesRegularExpression(
            "/csrf\s*=\s*'[^']+'/",
            $res->getContent()
        );
    }

    // ─────────────────────────────────────────
    // i18n
    // ─────────────────────────────────────────

    public function test_en_am_2fa_key_parity(): void
    {
        $en = json_decode(file_get_contents(lang_path('en.json')), true);
        $am = json_decode(file_get_contents(lang_path('am.json')), true);

        $enKeys = array_filter(array_keys($en), fn($k) => str_starts_with($k, '2fa.'));
        $amKeys = array_filter(array_keys($am), fn($k) => str_starts_with($k, '2fa.'));

        sort($enKeys);
        sort($amKeys);

        $this->assertSame($enKeys, $amKeys, 'EN/AM 2fa key sets must match');
    }

    public function test_all_2fa_keys_used_in_view_exist(): void
    {
        $view = file_get_contents(resource_path('views/profile/2fa.blade.php'));

        preg_match_all("/__\('(2fa\.[a-z_]+)'/", $view, $m);
        $used = array_unique($m[1]);

        $en = json_decode(file_get_contents(lang_path('en.json')), true);

        foreach ($used as $key) {
            $this->assertArrayHasKey($key, $en, "Missing EN translation for {$key}");
        }
    }

    public function test_am_locale_renders_amharic_strings(): void
    {
        // SetLocale middleware (L338) reads Accept-Language at request time;
        // app()->setLocale() alone is overwritten. Use the header instead.
        $res = $this->actingAs($this->user())
            ->withHeader('Accept-Language', 'am')
            ->get('/profile/2fa');
        $res->assertStatus(200);
        $this->assertStringContainsString('የሁለት-ደረጃ ማረጋገጫ', $res->getContent());
    }

    public function test_en_locale_renders_english_strings(): void
    {
        app()->setLocale('en');
        $res = $this->actingAs($this->user())->get('/profile/2fa');
        $res->assertStatus(200);
        $this->assertStringContainsString('Two-Factor Authentication', $res->getContent());
    }

    // ─────────────────────────────────────────
    // A11y markers (verify what IS shipped)
    // ─────────────────────────────────────────

    public function test_otp_input_has_inputmode_numeric(): void
    {
        $res = $this->actingAs($this->user())->get('/profile/2fa');
        $this->assertStringContainsString('inputmode="numeric"', $res->getContent());
    }

    public function test_otp_input_has_maxlength_6(): void
    {
        $res = $this->actingAs($this->user())->get('/profile/2fa');
        $this->assertStringContainsString('maxlength="6"', $res->getContent());
    }

    public function test_at_least_one_label_is_present(): void
    {
        $res = $this->actingAs($this->user())->get('/profile/2fa');
        $this->assertMatchesRegularExpression('/<label[^>]*>/i', $res->getContent());
    }

    public function test_lang_attribute_matches_app_locale(): void
    {
        // SetLocale middleware (L338) reads Accept-Language at request time.
        $res = $this->actingAs($this->user())
            ->withHeader('Accept-Language', 'am')
            ->get('/profile/2fa');
        $this->assertMatchesRegularExpression('/<html\s+lang="am"/i', $res->getContent());

        $res = $this->actingAs($this->user())
            ->withHeader('Accept-Language', 'en')
            ->get('/profile/2fa');
        $this->assertMatchesRegularExpression('/<html\s+lang="en"/i', $res->getContent());
    }
}
