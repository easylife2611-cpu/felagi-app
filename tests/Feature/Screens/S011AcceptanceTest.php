<?php

declare(strict_types=1);

namespace Tests\Feature\Screens;

use App\Models\Category;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class S011AcceptanceTest extends TestCase
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
            'slug'       => 's011-' . Str::lower(Str::random(6)),
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
        return "/needs/{$this->need->id}/offers/new";
    }

    private function html(string $locale = 'en'): string
    {
        app()->setLocale($locale);
        return view('submit-offer')->render();
    }

    public function test_canonical_route_is_registered(): void
    {
        $this->get($this->url())->assertStatus(200);
    }

    public function test_authorized_provider_reaches_shell(): void
    {
        $res = $this->actingAs($this->provider)->get($this->url());
        $res->assertStatus(200);
        $res->assertSee('id="offer-form"', false);
    }

    public function test_owner_reaches_shell_but_js_will_reject(): void
    {
        $res = $this->actingAs($this->owner)->get($this->url());
        $res->assertStatus(200);
    }

    public function test_renders_english_title(): void
    {
        $this->assertStringContainsString('Submit Offer', $this->html('en'));
    }

    public function test_renders_amharic_title(): void
    {
        $this->assertStringContainsString('ቅናሽ አስገባ', $this->html('am'));
    }

    public function test_html_lang_attribute_matches_locale(): void
    {
        $this->assertMatchesRegularExpression('/<html[^>]*lang="en"/', $this->html('en'));
        $this->assertMatchesRegularExpression('/<html[^>]*lang="am"/', $this->html('am'));
    }

    public function test_title_key_screenS011_exists_in_en(): void
    {
        $json = json_decode(file_get_contents(base_path('lang/en.json')), true);
        $this->assertArrayHasKey('screenS011', $json);
        $this->assertNotEmpty($json['screenS011']);
    }

    public function test_title_key_screenS011_exists_in_am(): void
    {
        $json = json_decode(file_get_contents(base_path('lang/am.json')), true);
        $this->assertArrayHasKey('screenS011', $json);
        $this->assertNotEmpty($json['screenS011']);
    }

    public function test_required_form_fields_present(): void
    {
        $html = $this->html();
        foreach ([
            'id="offered_price"',
            'id="currency"',
            'id="proposal_message"',
            'id="delivery_time_text"',
            'id="availability_text"',
            'id="additional_notes"',
        ] as $marker) {
            $this->assertStringContainsString($marker, $html);
        }
    }

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

    public function test_status_is_polite_live_region(): void
    {
        $this->assertMatchesRegularExpression(
            '/<div[^>]*id="status"[^>]*aria-live="polite"/',
            $this->html()
        );
    }

    public function test_focus_visible_styles_present(): void
    {
        $this->assertStringContainsString(':focus-visible', $this->html());
    }

    public function test_bdi_wraps_amounts_in_js(): void
    {
        $this->assertStringContainsString('<bdi>', $this->html());
    }

    public function test_aria_invalid_support_in_js(): void
    {
        $html = $this->html();
        $this->assertStringContainsString('aria-invalid', $html);
        $this->assertStringContainsString('aria-describedby', $html);
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

    public function test_form_max_width_720_per_spec(): void
    {
        $this->assertStringContainsString('max-width:720px', $this->html());
    }

    public function test_401_recovery_present(): void
    {
        $this->assertStringContainsString('r.status===401', $this->html());
    }

    public function test_404_recovery_present(): void
    {
        $this->assertStringContainsString('r.status===404', $this->html());
    }

    public function test_409_recovery_present(): void
    {
        $this->assertStringContainsString('409', $this->html());
    }

    public function test_422_validation_handling_present(): void
    {
        $this->assertStringContainsString('422', $this->html());
    }

    public function test_503_policy_unknown_present(): void
    {
        $this->assertStringContainsString('status===503', $this->html());
    }

    public function test_offline_banner_present(): void
    {
        $this->assertStringContainsString('offline-banner', $this->html());
    }

    public function test_all_used_keys_exist_in_both_locales(): void
    {
        $en = json_decode(file_get_contents(base_path('lang/en.json')), true);
        $am = json_decode(file_get_contents(base_path('lang/am.json')), true);

        $html = file_get_contents(resource_path('views/submit-offer.blade.php'));
        preg_match_all("/__\\('([a-zA-Z0-9_]+)'\\)/", $html, $m);

        foreach (array_unique($m[1]) as $key) {
            $this->assertArrayHasKey($key, $en, "Missing en key: {$key}");
            $this->assertArrayHasKey($key, $am, "Missing am key: {$key}");
        }
    }

    public function test_state_loading_present(): void
    {
        $this->assertStringContainsString('id="state-loading"', $this->html());
    }

    public function test_state_need_error_present(): void
    {
        $this->assertStringContainsString('id="state-need-error"', $this->html());
    }

    public function test_state_form_present(): void
    {
        $this->assertStringContainsString('id="state-form"', $this->html());
    }

    public function test_duplicate_state_handling_present(): void
    {
        $src = file_get_contents(resource_path('views/submit-offer.blade.php'));
        $this->assertStringContainsString('offerAlreadyExists', $src);
    }

    public function test_deadline_passed_state_present(): void
    {
        $src = file_get_contents(resource_path('views/submit-offer.blade.php'));
        $this->assertStringContainsString('offerDeadlinePassed', $src);
    }

    public function test_idempotency_key_generation_present(): void
    {
        $this->assertStringContainsString('idempotency_key', $this->html());
    }

    public function test_draft_fields_present(): void
    {
        $html = $this->html();
        $this->assertStringContainsString('draft_id', $html);
        $this->assertStringContainsString('draft_version', $html);
        $this->assertStringContainsString('draft_hash', $html);
    }

    public function test_no_ad_slots_rendered(): void
    {
        $html = $this->html();
        $this->assertStringNotContainsString('data-ad-slot', $html);
    }
}
