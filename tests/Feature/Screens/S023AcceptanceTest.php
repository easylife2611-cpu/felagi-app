<?php

declare(strict_types=1);

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class S023AcceptanceTest extends TestCase
{
    use RefreshDatabase;

    private User $provider;
    private User $owner;
    private Need $need;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = User::factory()->create();
        $this->owner    = User::factory()->create();

        $cat = Category::create([
            'slug'       => 'acc-' . Str::lower(Str::random(6)),
            'name_am'    => 'ምድብ',
            'name_en'    => 'Cat',
            'active'     => true,
            'sort_order' => 1,
        ]);

        $this->need = Need::create([
            'requester_id' => $this->owner->id,
            'category_id'  => $cat->id,
            'title'        => 'Test Need',
            'description'  => 'Desc',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ]);
    }

    private function url(): string
    {
        return "/needs/{$this->need->id}/offers/unlock";
    }

    /**
     * Render the Blade view directly with the given locale.
     * HTTP-based locale detection is unreliable in tests; direct render
     * is deterministic and exercises the same Blade template.
     */
    private function html(string $locale = 'en'): string
    {
        app()->setLocale($locale);
        return view('offer-unlock')->render();
    }

    public function test_canonical_route_is_registered(): void
    {
        $this->get($this->url())->assertStatus(200);
    }

    public function test_authorized_provider_reaches_static_shell(): void
    {
        $res = $this->actingAs($this->provider)->get($this->url());
        $res->assertStatus(200);
        $res->assertSee('id="unlock-btn"', false);
    }

    public function test_owner_reaches_shell_but_api_will_reject(): void
    {
        $res = $this->actingAs($this->owner)->get($this->url());
        $res->assertStatus(200);
    }

    public function test_renders_english_title(): void
    {
        $this->assertStringContainsString('Unlock offer submission', $this->html('en'));
    }

    public function test_renders_amharic_title(): void
    {
        $this->assertStringContainsString('የቅናሽ ማስገቢያ ክፈት', $this->html('am'));
    }

    public function test_html_lang_attribute_matches_locale(): void
    {
        $this->assertMatchesRegularExpression('/<html[^>]*lang="en"/', $this->html('en'));
        $this->assertMatchesRegularExpression('/<html[^>]*lang="am"/', $this->html('am'));
    }

    public function test_title_key_screenS023_exists_in_en(): void
    {
        $json = json_decode(file_get_contents(base_path('lang/en.json')), true);
        $this->assertArrayHasKey('screenS023', $json);
        $this->assertNotEmpty($json['screenS023']);
    }

    public function test_title_key_screenS023_exists_in_am(): void
    {
        $json = json_decode(file_get_contents(base_path('lang/am.json')), true);
        $this->assertArrayHasKey('screenS023', $json);
        $this->assertNotEmpty($json['screenS023']);
    }

    public function test_401_response_redirects_to_root(): void
    {
        $html = $this->html();
        $this->assertStringContainsString('r.status===401', $html);
        $this->assertStringContainsString("window.location.href='/'", $html);
    }

    public function test_404_response_triggers_error_state(): void
    {
        $html = $this->html();
        $this->assertStringContainsString('r.status===404', $html);
        $this->assertStringContainsString("showState('error')", $html);
    }

    public function test_offline_recovery_listeners_present(): void
    {
        $html = $this->html();
        $this->assertStringContainsString("addEventListener('online'", $html);
        $this->assertStringContainsString("addEventListener('offline'", $html);
    }

    public function test_pending_status_uses_polite_live_region(): void
    {
        $this->assertMatchesRegularExpression(
            '/id="state-desc"[^>]*aria-live="polite"/',
            $this->html()
        );
    }

    public function test_amount_is_wrapped_in_bdi(): void
    {
        $this->assertStringContainsString('<bdi', $this->html());
    }

    public function test_heading_is_focusable_for_route_change(): void
    {
        $this->assertMatchesRegularExpression(
            '/<h1[^>]*id="page-title"[^>]*tabindex="-1"/',
            $this->html()
        );
    }

    public function test_main_landmark_present_and_labelled(): void
    {
        $this->assertMatchesRegularExpression(
            '/<main[^>]*aria-labelledby="page-title"/',
            $this->html()
        );
    }

    public function test_focus_visible_styles_present(): void
    {
        $this->assertStringContainsString(':focus-visible', $this->html());
    }

    public function test_no_assertive_live_regions(): void
    {
        $this->assertStringNotContainsString('aria-live="assertive"', $this->html());
    }

    public function test_viewport_meta_present(): void
    {
        $html = $this->html();
        $this->assertStringContainsString('name="viewport"', $html);
        $this->assertStringContainsString('width=device-width', $html);
    }

    public function test_form_max_width_is_720_per_spec(): void
    {
        $this->assertStringContainsString('max-width:720px', $this->html());
    }

    public function test_state_titles_are_translated_not_hardcoded(): void
    {
        $html = $this->html('en');
        $this->assertStringNotContainsString("'Free submission'", $html);
        $this->assertStringNotContainsString("'Payment pending'", $html);
        $this->assertStringContainsString('Free submission', $html);
    }

    public function test_amharic_state_text_rendered_in_am_locale(): void
    {
        $html = $this->html('am');
        $this->assertStringContainsString('ነጻ ማስገቢያ', $html);
    }

    public function test_pol_05_repeated_verified_creates_one_offer(): void
    {
        $this->markTestIncomplete(
            'POL-05 — repeated verified payment must produce exactly one Offer. ' .
            'Blocked on WP-11 (Stripe integration).'
        );
    }

    public function test_pol_06_interruption_resume_no_recharge(): void
    {
        $this->markTestIncomplete(
            'POL-06 — interrupted resume must not charge twice. ' .
            'Blocked on WP-11 (Stripe integration).'
        );
    }

    public function test_pol_07_expired_need_refunds(): void
    {
        $this->markTestIncomplete(
            'POL-07 — payment on expired/closed Need must refund. ' .
            'Blocked on WP-11 (Stripe integration).'
        );
    }

    public function test_pol_11_no_commission(): void
    {
        $this->markTestIncomplete(
            'POL-11 — no commission applied. Blocked on contract owner decision.'
        );
    }
}
