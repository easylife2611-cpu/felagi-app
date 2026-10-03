<?php

declare(strict_types=1);

namespace Tests\Feature\Audit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class FinalAuditAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    private const USER_VIEWS = [
        'S001'=>'welcome.blade.php','S003'=>'profile.blade.php','S004'=>'browse.blade.php',
        'S005'=>'create-need.blade.php','S006'=>'need-preview.blade.php','S007'=>'need-created.blade.php',
        'S008'=>'show-need.blade.php','S009'=>'my-needs.blade.php','S010'=>'received-offers.blade.php',
        'S011'=>'submit-offer.blade.php','S012'=>'offer-detail.blade.php','S013'=>'my-offers.blade.php',
        'S014'=>'compare-offers.blade.php','S015'=>'comparison-result.blade.php','S016'=>'comparison-history.blade.php',
        'S017'=>'offer-messages.blade.php','S018'=>'notifications.blade.php','S019'=>'boost-need.blade.php',
        'S020'=>'rate-participant.blade.php','S021'=>'report-support.blade.php',
        'S022'=>'telegram-publications.blade.php','S023'=>'offer-unlock.blade.php',
    ];

    private const ADMIN_VIEWS = [
        'A001'=>'admin/dashboard.blade.php','A002'=>'admin/telegram.blade.php',
        'A003'=>'admin/health.blade.php','A004'=>'admin/features.blade.php',
        'A005'=>'admin/marketplace.blade.php','A006'=>'admin/ai.blade.php',
        'A007'=>'admin/payments.blade.php','A008'=>'admin/users.blade.php',
        'A009'=>'admin/content.blade.php','A010'=>'admin/notifications.blade.php',
        'A011'=>'admin/files.blade.php','A012'=>'admin/jobs.blade.php',
        'A013'=>'admin/backups.blade.php','A014'=>'admin/integrity.blade.php',
        'A015'=>'admin/security.blade.php','A016'=>'admin/audit.blade.php',
        'A017'=>'admin/settings.blade.php','A018'=>'admin/recovery.blade.php',
        'A019'=>'admin/safe-mode.blade.php','A020'=>'admin/monetization.blade.php',
        'A021'=>'admin/reports.blade.php','A022'=>'admin/sponsored-ads.blade.php',
    ];

    public static function userViews(): array
    {
        $out = [];
        foreach (self::USER_VIEWS as $sid => $view) {
            $out[$sid] = [$sid, $view];
        }
        return $out;
    }

    public static function adminViews(): array
    {
        $out = [];
        foreach (self::ADMIN_VIEWS as $sid => $view) {
            $out[$sid] = [$sid, $view];
        }
        return $out;
    }

    private function en(): array { return json_decode(file_get_contents(base_path('lang/en.json')), true); }
    private function am(): array { return json_decode(file_get_contents(base_path('lang/am.json')), true); }

    /** @dataProvider userViews */
    public function test_user_screen_has_view(string $sid, string $view): void
    {
        $this->assertFileExists(resource_path("views/{$view}"));
    }

    /** @dataProvider userViews */
    public function test_user_screen_title_key_in_both_locales(string $sid, string $view): void
    {
        $key = "screen{$sid}";
        $this->assertArrayHasKey($key, $this->en(), "en missing {$key}");
        $this->assertArrayHasKey($key, $this->am(), "am missing {$key}");
    }

    /** @dataProvider adminViews */
    public function test_admin_screen_has_view(string $sid, string $view): void
    {
        $this->assertFileExists(resource_path("views/{$view}"));
    }

    /** @dataProvider adminViews */
    public function test_admin_screen_title_key_in_both_locales(string $sid, string $view): void
    {
        $key = "screen{$sid}";
        $this->assertArrayHasKey($key, $this->en(), "en missing {$key}");
        $this->assertArrayHasKey($key, $this->am(), "am missing {$key}");
    }

    public function test_source_of_truth_has_recent_work_packages(): void
    {
        $body = file_get_contents(base_path('public/handoff/SOURCE_OF_TRUTH.md'));
        foreach (['L347-I','L347-J','L347-L','L347-M','L347-N'] as $section) {
            $this->assertStringContainsString($section, $body, "SOURCE_OF_TRUTH missing {$section}");
        }
    }

    public function test_screen_contract_test_covers_23_user_routes(): void
    {
        $body = file_get_contents(base_path('tests/Feature/Screens/ScreenContractTest.php'));
        for ($i = 1; $i <= 23; $i++) {
            $sid = sprintf('S%03d', $i);
            $this->assertStringContainsString("'{$sid}'", $body, "ScreenContractTest missing {$sid}");
        }
    }

    public function test_acceptance_tests_present_for_s011_s022_s023(): void
    {
        foreach (['S011','S022','S023'] as $sid) {
            $this->assertFileExists(base_path("tests/Feature/Screens/{$sid}AcceptanceTest.php"));
        }
    }

    public function test_en_and_am_key_counts_match(): void
    {
        $this->assertSame(count($this->en()), count($this->am()));
    }

    public function test_no_key_in_en_missing_from_am(): void
    {
        $missing = array_values(array_diff(array_keys($this->en()), array_keys($this->am())));
        $this->assertSame([], $missing, 'en->am missing: ' . implode(', ', $missing));
    }

    public function test_no_key_in_am_missing_from_en(): void
    {
        $missing = array_values(array_diff(array_keys($this->am()), array_keys($this->en())));
        $this->assertSame([], $missing, 'am->en missing: ' . implode(', ', $missing));
    }

    public function test_all_screen_keys_non_empty_in_both_locales(): void
    {
        $en = $this->en(); $am = $this->am();
        $bad = [];
        foreach ($en as $k => $v) {
            if (!preg_match('/^screen[SA]\d{3}$/', $k)) continue;
            if (trim((string)$v) === '') $bad[] = "en:{$k}";
            if (trim((string)($am[$k] ?? '')) === '') $bad[] = "am:{$k}";
        }
        $this->assertSame([], $bad, 'Empty screen keys: ' . implode(', ', $bad));
    }

    public function test_am_json_is_valid_utf8(): void
    {
        $this->assertTrue(mb_check_encoding(file_get_contents(base_path('lang/am.json')), 'UTF-8'));
    }

    public function test_screen_keys_cover_all_lettered_screens(): void
    {
        $en = $this->en();
        $found = 0;
        for ($i = 1; $i <= 23; $i++) if (isset($en[sprintf('screenS%03d', $i)])) $found++;
        for ($i = 1; $i <= 22; $i++) if (isset($en[sprintf('screenA%03d', $i)])) $found++;
        $this->assertSame(45, $found, "Expected 45 screen keys; found {$found}");
    }

    public function test_auth_routes_have_throttle_middleware(): void
    {
        $throttled = 0;
        foreach (Route::getRoutes() as $r) {
            if (!str_starts_with($r->uri(), 'api/v1/auth/')) continue;
            foreach ($r->middleware() as $mw) {
                if (str_contains($mw, 'throttle')) { $throttled++; break; }
            }
        }
        $this->assertGreaterThan(5, $throttled, 'Expected >5 throttled auth routes');
    }

    public function test_2fa_routes_have_tight_throttle(): void
    {
        $tight = 0;
        foreach (Route::getRoutes() as $r) {
            if (!str_contains($r->uri(), 'auth/2fa/')) continue;
            foreach ($r->middleware() as $mw) {
                if (preg_match('/throttle:(\d+),/', $mw, $m) && (int)$m[1] <= 10) {
                    $tight++; break;
                }
            }
        }
        $this->assertGreaterThan(0, $tight, 'Expected some 2FA routes with tight throttle');
    }

    public function test_guest_gets_401_on_high_value_routes(): void
    {
        $checks = [
            ['GET', '/api/v1/auth/me'],
            ['GET', '/api/v1/my/needs'],
            ['GET', '/api/v1/my/offers'],
            ['GET', '/api/v1/notifications'],
            ['GET', '/api/v1/admin/dashboard'],
        ];
        foreach ($checks as [$method, $uri]) {
            $this->json($method, $uri)->assertStatus(401, "Guest should get 401 for {$method} {$uri}");
        }
    }

    public function test_web_file_has_post_routes(): void
    {
        $web = file_get_contents(base_path('routes/web.php'));
        $this->assertMatchesRegularExpression('/Route::post\(/', $web);
    }

    public function test_security_headers_test_exists(): void
    {
        $this->assertFileExists(base_path('tests/Feature/SecurityHeadersTest.php'));
    }
}
