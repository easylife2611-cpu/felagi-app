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

    public function test_posts_to_offer_submissions(): void
    {
        $this->assertStringContainsString('/api/v1/offer-submissions', $this->html);
    }

    public function test_uses_post_method(): void
    {
        $this->assertStringContainsString("method:'POST'", $this->html);
    }

    public function test_sends_csrf_token(): void
    {
        $this->assertStringContainsString('X-CSRF-TOKEN', $this->html);
    }

    public function test_sends_need_id(): void
    {
        $this->assertStringContainsString('need_id:needId', $this->html);
    }

    public function test_outdated_comment_removed(): void
    {
        $this->assertStringNotContainsString('// No unlock API exists yet', $this->html);
    }

    public function test_button_handler_registered(): void
    {
        $this->assertStringContainsString("addEventListener('click',doUnlock)", $this->html);
    }

    public function test_pending_notice_present(): void
    {
        $this->assertStringContainsString('notice-warn', $this->html);
    }
}
