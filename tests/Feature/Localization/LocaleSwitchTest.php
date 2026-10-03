<?php

namespace Tests\Feature\Localization;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * L338 — Locale resolution tests
 *
 * Design rules (Localization/README.md):
 *   - "Amharic `am` is default; English `en` is required at launch."
 *   - "Fallback: chosen locale→Amharic"
 *   - "Unsupported locale→Amharic"
 *
 * Precedence:
 *   1. Session 'locale' (explicit user choice)
 *   2. Accept-Language header
 *   3. Config default (am)
 *   4. FALLBACK constant (am)
 */
class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_locale_is_amharic(): void
    {
        // L338 — config default 'am' per design (Localization/README.md).
        // NOTE: Symfony's TestClient always injects default Accept-Language
        // (en-us,en;q=0.5). So we assert config default directly.
        $this->assertSame('am', config('app.locale'));
        $this->assertSame('am', config('app.fallback_locale'));
    }

    public function test_accept_language_am_selects_amharic(): void
    {
        $this->withHeader('Accept-Language', 'am-ET,am;q=0.9')
             ->get('/browse');
        $this->assertSame('am', app()->getLocale());
    }

    public function test_accept_language_en_selects_english(): void
    {
        $this->withHeader('Accept-Language', 'en-US,en;q=0.9')
             ->get('/browse');
        $this->assertSame('en', app()->getLocale());
    }

    public function test_unsupported_locale_falls_back_to_amharic(): void
    {
        $this->withHeader('Accept-Language', 'fr-FR,fr;q=0.9')
             ->get('/browse');
        $this->assertSame('am', app()->getLocale(), 'Unsupported → am');
    }

    public function test_session_locale_overrides_header(): void
    {
        $this->withSession(['locale' => 'en'])
             ->withHeader('Accept-Language', 'am-ET')
             ->get('/browse');
        $this->assertSame('en', app()->getLocale(), 'Session wins over header');
    }

    public function test_q_value_respected(): void
    {
        // en has higher q than am → en wins
        $this->withHeader('Accept-Language', 'am;q=0.5,en;q=0.9')
             ->get('/browse');
        $this->assertSame('en', app()->getLocale());
    }

    public function test_empty_accept_language_uses_default(): void
    {
        $this->withHeader('Accept-Language', '')
             ->get('/browse');
        $this->assertSame('am', app()->getLocale());
    }

    public function test_amharic_ui_renders_amharic_string(): void
    {
        $response = $this->withHeader('Accept-Language', 'am-ET,am;q=0.9')
             ->get('/browse');
        $response->assertStatus(200);
        $response->assertSee('ፍላጎቶች', false); // browseNeeds Amharic
    }

    public function test_english_ui_renders_english_string(): void
    {
        $response = $this->withHeader('Accept-Language', 'en-US,en;q=0.9')
             ->get('/browse');
        $response->assertStatus(200);
        $response->assertSee('Browse', false);
    }
}
