<?php

namespace Tests\Feature\Screens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * GAP-TEST-01 — Screen contract tests (additive).
 *
 * Cross-cutting contract checks for all 23 user screens (S001-S023).
 * Complements the per-screen tests — verifies route registration,
 * HTTP health, and HTML5 document structure without assuming the
 * contents of any individual screen.
 *
 * Purely structural. No existing test modified.
 */
class ScreenContractTest extends TestCase
{
    use RefreshDatabase;

    /** S### => [URL, human-readable name] */
    private const SCREENS = [
        'S001' => ['/',                            'Welcome'],
        'S002' => ['/auth/telegram',               'Telegram sign-in'],
        'S003' => ['/profile',                     'Profile'],
        'S004' => ['/browse',                      'Browse Needs'],
        'S005' => ['/needs/new',                   'Create/Edit Need'],
        'S006' => ['/needs/new/public-preview',    'Public-post preview'],
        'S007' => ['/needs/test-id/created',       'Need-created confirmation'],
        'S008' => ['/needs/test-id',               'Need details'],
        'S009' => ['/my/needs',                    'My Needs'],
        'S010' => ['/needs/test-id/offers',        'Received Offers'],
        'S011' => ['/needs/test-id/offers/new',    'Submit/Edit Offer'],
        'S012' => ['/offers/test-id',              'Offer details'],
        'S013' => ['/my/offers',                   'My Offers'],
        'S014' => ['/needs/test-id/compare',       'Compare confirmation'],
        'S015' => ['/comparisons/test-id',         'AI comparison result'],
        'S016' => ['/needs/test-id/comparisons',   'Comparison history/export'],
        'S017' => ['/offers/test-id/messages',     'Messages'],
        'S018' => ['/notifications',               'Notifications'],
        'S019' => ['/needs/test-id/boost',         'Boost/Payments'],
        'S020' => ['/needs/test-id/rating',        'Rating'],
        'S021' => ['/support/report',              'Report/Support'],
        'S022' => ['/needs/test-id/publications',  'Telegram status/stop'],
        'S023' => ['/needs/test-id/offers/unlock', 'Offer Submission Unlock'],
    ];

    /** @var array<int,string> */
    private array $registeredUris = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach (Route::getRoutes() as $r) {
            $this->registeredUris[] = $r->uri();
        }
    }

    public function test_all_23_user_routes_are_registered(): void
    {
        $missing = [];
        foreach (self::SCREENS as $id => [$url, $name]) {
            $uri = ltrim($url, '/');
            if ($uri === '') {
                $uri = '/';
            }
            $pattern = preg_replace('#test-id#', '{id}', $uri);
            if (! in_array($pattern, $this->registeredUris, true)) {
                $missing[] = "{$id} ({$name}): {$pattern}";
            }
        }
        $this->assertSame(
            [],
            $missing,
            "Unregistered user routes: \n" . implode("\n", $missing)
        );
    }

    public function test_no_user_screen_returns_server_error(): void
    {
        foreach (self::SCREENS as $id => [$url, $name]) {
            $status = $this->get($url)->getStatusCode();
            $this->assertNotSame(404, $status, "404 on {$id} ({$name}): {$url}");
            $this->assertLessThan(500, $status, "5xx on {$id} ({$name}): HTTP {$status}");
        }
    }

    public function test_all_screens_are_valid_html5_documents(): void
    {
        foreach (self::SCREENS as $id => [$url, $name]) {
            $res = $this->get($url);
            if ($res->getStatusCode() !== 200) {
                continue; // redirects covered by test above
            }
            $body = $res->getContent();

            $this->assertMatchesRegularExpression(
                '/<!DOCTYPE html>/i', $body,
                "Missing <!DOCTYPE html> on {$id} ({$name})"
            );
            $this->assertMatchesRegularExpression(
                '/<html[^>]*\blang=/i', $body,
                "Missing <html lang=...> on {$id} ({$name})"
            );
            $this->assertMatchesRegularExpression(
                '/<meta[^>]*charset\s*=\s*["\']?utf-?8["\']?/i', $body,
                "Missing UTF-8 charset on {$id} ({$name})"
            );
            $this->assertMatchesRegularExpression(
                '/<meta[^>]*name=["\']viewport["\']/i', $body,
                "Missing viewport meta on {$id} ({$name})"
            );
            $this->assertMatchesRegularExpression(
                '/<title[^>]*>[^<]*<\/title>/i', $body,
                "Missing <title> on {$id} ({$name})"
            );
        }
    }

    public function test_no_screen_leaks_server_error_details(): void
    {
        $forbidden = ['Whoops', 'StackTrace', 'APP_DEBUG', 'Stack trace'];
        foreach (self::SCREENS as $id => [$url, $name]) {
            $res = $this->get($url);
            if ($res->getStatusCode() !== 200) {
                continue;
            }
            $body = $res->getContent();
            foreach ($forbidden as $needle) {
                $this->assertStringNotContainsString(
                    $needle, $body,
                    "Debug leak ({$needle}) on {$id} ({$name})"
                );
            }
        }
    }
}
