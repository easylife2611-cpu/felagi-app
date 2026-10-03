<?php

declare(strict_types=1);

namespace Tests\Feature\Screens;

use Tests\TestCase;

final class S023OfferUnlockUiTest extends TestCase
{
    private string $html;

    protected function setUp(): void
    {
        parent::setUp();
        $this->html = file_get_contents(resource_path('views/offer-unlock.blade.php'));
    }

    public function test_view_exists(): void
    {
        $this->assertNotEmpty($this->html);
    }

    public function test_reads_submission_id_from_query(): void
    {
        $this->assertStringContainsString('submission_id', $this->html);
    }

    public function test_fetches_submission_via_get(): void
    {
        $this->assertStringContainsString('/api/v1/offer-submissions/', $this->html);
        $this->assertStringContainsString("credentials:'same-origin'", $this->html);
    }

    public function test_posts_to_resume_endpoint(): void
    {
        $this->assertStringContainsString('/resume', $this->html);
        $this->assertStringContainsString("method:'POST'", $this->html);
    }

    public function test_sends_csrf_token(): void
    {
        $this->assertStringContainsString('X-CSRF-TOKEN', $this->html);
    }

    public function test_has_state_badge(): void
    {
        $this->assertStringContainsString('state-badge', $this->html);
        $this->assertStringContainsString('id="state-badge"', $this->html);
    }

    public function test_has_all_nine_states_in_state_meta(): void
    {
        foreach ([
            "'free'", "'payment-required'", "'pending'", "'payment-verified'",
            "'submission-recovery'", "'submitted'", "'refund-pending'",
            "'failed'", "'unknown'",
        ] as $state) {
            $this->assertStringContainsString($state, $this->html, "Missing state: {$state}");
        }
    }

    public function test_no_legacy_post_to_offer_submissions_collection(): void
    {
        // Must not POST directly to the collection endpoint (that is S011's job)
        $this->assertStringNotContainsString(
            "fetch('/api/v1/offer-submissions',{",
            $this->html
        );
    }

    public function test_outdated_comment_removed(): void
    {
        $this->assertStringNotContainsString('// No unlock API exists yet', $this->html);
    }

    public function test_has_refresh_and_back_buttons(): void
    {
        $this->assertStringContainsString('id="unlock-btn"', $this->html);
        $this->assertStringContainsString('id="cancel-btn"', $this->html);
        $this->assertStringContainsString('id="back-btn"', $this->html);
    }

    public function test_has_offline_banner(): void
    {
        $this->assertStringContainsString('offline-banner', $this->html);
    }

    public function test_has_empty_state(): void
    {
        $this->assertStringContainsString('id="state-empty"', $this->html);
    }
}
