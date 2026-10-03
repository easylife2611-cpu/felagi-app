<?php

declare(strict_types=1);

namespace Tests\Feature\Screens;

use Tests\TestCase;

/**
 * L347-C — 2FA a11y improvements (closes GAP-L346E-2FA-A11Y).
 */
final class Profile2faA11yTest extends TestCase
{
    private string $html;

    protected function setUp(): void
    {
        parent::setUp();
        $this->html = file_get_contents(resource_path('views/profile/2fa.blade.php'));
    }

    public function test_status_badge_has_role_status(): void
    {
        $this->assertMatchesRegularExpression('/class="badge[^"]*"\s+role="status"/', $this->html);
    }

    public function test_status_badge_has_aria_live_polite(): void
    {
        $this->assertMatchesRegularExpression('/class="badge[^"]*"\s+role="status"\s+aria-live="polite"/', $this->html);
    }

    public function test_enroll_message_is_live_region(): void
    {
        $this->assertMatchesRegularExpression('/id="enroll-msg"[^>]*role="status"[^>]*aria-live="polite"/', $this->html);
    }

    public function test_disable_message_is_live_region(): void
    {
        $this->assertMatchesRegularExpression('/id="disable-msg"[^>]*role="status"[^>]*aria-live="polite"/', $this->html);
    }

    public function test_enroll_code_has_autocomplete_one_time_code(): void
    {
        $this->assertMatchesRegularExpression('/id="enroll-code"[^>]*autocomplete="one-time-code"/', $this->html);
    }

    public function test_disable_code_has_autocomplete_one_time_code(): void
    {
        $this->assertMatchesRegularExpression('/id="disable-code"[^>]*autocomplete="one-time-code"/', $this->html);
    }

    public function test_enroll_code_has_label_with_for_attribute(): void
    {
        $this->assertMatchesRegularExpression('/<label[^>]*for="enroll-code"/', $this->html);
    }

    public function test_disable_code_has_label_with_for_attribute(): void
    {
        $this->assertMatchesRegularExpression('/<label[^>]*for="disable-code"/', $this->html);
    }

    public function test_prefers_color_scheme_dark_present(): void
    {
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $this->html);
    }

    public function test_text_xs_contrast_darkened(): void
    {
        $this->assertStringContainsString('.text-xs { font-size: 12px; color: #4b5563; }', $this->html);
        $this->assertStringNotContainsString('.text-xs { font-size: 12px; color: #9ca3af; }', $this->html);
    }
}
