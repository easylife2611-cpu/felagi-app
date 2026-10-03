<?php

declare(strict_types=1);

namespace Tests\Feature\Screens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AllScreensAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    private const USER_SCREENS = [
        ['S001', 'welcome.blade.php',               'screenS001'],
        ['S003', 'profile.blade.php',               'screenS003'],
        ['S004', 'browse.blade.php',                'screenS004'],
        ['S005', 'create-need.blade.php',           'screenS005'],
        ['S006', 'need-preview.blade.php',          'screenS006'],
        ['S007', 'need-created.blade.php',          'screenS007'],
        ['S008', 'show-need.blade.php',             'screenS008'],
        ['S009', 'my-needs.blade.php',              'screenS009'],
        ['S010', 'received-offers.blade.php',       'screenS010'],
        ['S012', 'offer-detail.blade.php',          'screenS012'],
        ['S013', 'my-offers.blade.php',             'screenS013'],
        ['S014', 'compare-offers.blade.php',        'screenS014'],
        ['S015', 'comparison-result.blade.php',     'screenS015'],
        ['S016', 'comparison-history.blade.php',    'screenS016'],
        ['S017', 'offer-messages.blade.php',        'screenS017'],
        ['S018', 'notifications.blade.php',         'screenS018'],
        ['S019', 'boost-need.blade.php',            'screenS019'],
        ['S020', 'rate-participant.blade.php',      'screenS020'],
        ['S021', 'report-support.blade.php',        'screenS021'],
    ];

    private const ADMIN_SCREENS = [
        ['A001', 'admin/dashboard.blade.php',       'screenA001'],
        ['A002', 'admin/telegram.blade.php',        'screenA002'],
        ['A003', 'admin/health.blade.php',          'screenA003'],
        ['A004', 'admin/features.blade.php',        'screenA004'],
        ['A005', 'admin/marketplace.blade.php',     'screenA005'],
        ['A006', 'admin/ai.blade.php',              'screenA006'],
        ['A007', 'admin/payments.blade.php',        'screenA007'],
        ['A008', 'admin/users.blade.php',           'screenA008'],
        ['A009', 'admin/content.blade.php',         'screenA009'],
        ['A010', 'admin/notifications.blade.php',   'screenA010'],
        ['A011', 'admin/files.blade.php',           'screenA011'],
        ['A012', 'admin/jobs.blade.php',            'screenA012'],
        ['A013', 'admin/backups.blade.php',         'screenA013'],
        ['A014', 'admin/integrity.blade.php',       'screenA014'],
        ['A015', 'admin/security.blade.php',        'screenA015'],
        ['A016', 'admin/audit.blade.php',           'screenA016'],
        ['A017', 'admin/settings.blade.php',        'screenA017'],
        ['A018', 'admin/recovery.blade.php',        'screenA018'],
        ['A019', 'admin/safe-mode.blade.php',       'screenA019'],
        ['A020', 'admin/monetization.blade.php',    'screenA020'],
        ['A021', 'admin/reports.blade.php',         'screenA021'],
        ['A022', 'admin/sponsored-ads.blade.php',   'screenA022'],
    ];

    public static function userScreens(): array { return self::USER_SCREENS; }
    public static function adminScreens(): array { return self::ADMIN_SCREENS; }

    private function en(): array
    {
        return json_decode(file_get_contents(base_path('lang/en.json')), true);
    }
    private function am(): array
    {
        return json_decode(file_get_contents(base_path('lang/am.json')), true);
    }

    /** @dataProvider userScreens */
    public function test_user_title_key_exists_in_en(string $sid, string $view, string $key): void
    {
        $en = $this->en();
        $this->assertArrayHasKey($key, $en, "Missing en key: {$key} ({$sid})");
        $this->assertNotEmpty($en[$key]);
    }

    /** @dataProvider userScreens */
    public function test_user_title_key_exists_in_am(string $sid, string $view, string $key): void
    {
        $am = $this->am();
        $this->assertArrayHasKey($key, $am, "Missing am key: {$key} ({$sid})");
        $this->assertNotEmpty($am[$key]);
    }

    /** @dataProvider adminScreens */
    public function test_admin_title_key_exists_in_en(string $sid, string $view, string $key): void
    {
        $en = $this->en();
        $this->assertArrayHasKey($key, $en, "Missing en key: {$key} ({$sid})");
        $this->assertNotEmpty($en[$key]);
    }

    /** @dataProvider adminScreens */
    public function test_admin_title_key_exists_in_am(string $sid, string $view, string $key): void
    {
        $am = $this->am();
        $this->assertArrayHasKey($key, $am, "Missing am key: {$key} ({$sid})");
        $this->assertNotEmpty($am[$key]);
    }

    /** @dataProvider userScreens */
    public function test_user_view_exists(string $sid, string $view, string $key): void
    {
        $this->assertFileExists(resource_path("views/{$view}"), "Missing view for {$sid}");
    }

    /** @dataProvider userScreens */
    public function test_user_view_uses_title_key(string $sid, string $view, string $key): void
    {
        $src = file_get_contents(resource_path("views/{$view}"));
        $this->assertMatchesRegularExpression(
            "/<title>\{\{\s*__\('{$key}'\)/",
            $src,
            "{$sid}: <title> does not use {$key}"
        );
    }

    /** @dataProvider userScreens */
    public function test_user_view_has_main_landmark(string $sid, string $view, string $key): void
    {
        $src = file_get_contents(resource_path("views/{$view}"));
        $this->assertMatchesRegularExpression(
            '/<main[^>]*role="main"[^>]*aria-labelledby="page-title"/',
            $src,
            "{$sid}: main landmark missing"
        );
    }

    /** @dataProvider userScreens */
    public function test_user_view_has_focusable_page_title(string $sid, string $view, string $key): void
    {
        $src = file_get_contents(resource_path("views/{$view}"));
        $this->assertMatchesRegularExpression(
            '/id="page-title"[^>]*tabindex="-1"/',
            $src,
            "{$sid}: page-title tabindex missing"
        );
    }

    /** @dataProvider userScreens */
    public function test_user_view_has_focus_visible_css(string $sid, string $view, string $key): void
    {
        $src = file_get_contents(resource_path("views/{$view}"));
        $this->assertStringContainsString(':focus-visible', $src, "{$sid}: focus-visible missing");
    }

    /** @dataProvider userScreens */
    public function test_user_view_uses_only_existing_keys(string $sid, string $view, string $key): void
    {
        $en = $this->en();
        $am = $this->am();
        $src = file_get_contents(resource_path("views/{$view}"));
        preg_match_all("/__\\('([a-zA-Z0-9_]+)'\\)/", $src, $m);
        foreach (array_unique($m[1]) as $k) {
            $this->assertArrayHasKey($k, $en, "{$sid}: en missing '{$k}'");
            $this->assertArrayHasKey($k, $am, "{$sid}: am missing '{$k}'");
        }
    }

    /** @dataProvider adminScreens */
    public function test_admin_view_exists(string $sid, string $view, string $key): void
    {
        $this->assertFileExists(resource_path("views/{$view}"), "Missing view for {$sid}");
    }

    /** @dataProvider adminScreens */
    public function test_admin_view_extends_admin_layout(string $sid, string $view, string $key): void
    {
        $src = file_get_contents(resource_path("views/{$view}"));
        $this->assertStringContainsString("@extends('layouts.admin')", $src, "{$sid}: no @extends");
    }

    /** @dataProvider adminScreens */
    public function test_admin_view_defines_title_section(string $sid, string $view, string $key): void
    {
        $src = file_get_contents(resource_path("views/{$view}"));
        $this->assertMatchesRegularExpression(
            "/@section\('title',\s*__\('{$key}'\)\)/",
            $src,
            "{$sid}: title section not using {$key}"
        );
    }

    /** @dataProvider adminScreens */
    public function test_admin_view_uses_only_existing_keys(string $sid, string $view, string $key): void
    {
        $en = $this->en();
        $am = $this->am();
        $src = file_get_contents(resource_path("views/{$view}"));
        preg_match_all("/__\\('([a-zA-Z0-9_]+)'\\)/", $src, $m);
        foreach (array_unique($m[1]) as $k) {
            $this->assertArrayHasKey($k, $en, "{$sid}: en missing '{$k}'");
            $this->assertArrayHasKey($k, $am, "{$sid}: am missing '{$k}'");
        }
    }

    public function test_admin_layout_has_main_landmark(): void
    {
        $src = file_get_contents(resource_path('views/layouts/admin.blade.php'));
        $this->assertMatchesRegularExpression(
            '/<main[^>]*role="main"[^>]*aria-labelledby="admin-page-title"/',
            $src
        );
    }

    public function test_admin_layout_has_focusable_page_title(): void
    {
        $src = file_get_contents(resource_path('views/layouts/admin.blade.php'));
        $this->assertMatchesRegularExpression('/id="admin-page-title"[^>]*tabindex="-1"/', $src);
    }

    public function test_admin_layout_has_focus_visible_css(): void
    {
        $src = file_get_contents(resource_path('views/layouts/admin.blade.php'));
        $this->assertStringContainsString(':focus-visible', $src);
    }

    public function test_s002_title_key_exists(): void
    {
        $en = $this->en();
        $am = $this->am();
        $this->assertArrayHasKey('screenS002', $en);
        $this->assertArrayHasKey('screenS002', $am);
    }
}
