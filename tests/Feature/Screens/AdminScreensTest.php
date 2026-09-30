<?php

namespace Tests\Feature\Screens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AdminScreensTest extends TestCase
{
    use RefreshDatabase;

    /**
     * All 23 admin routes should render 200 (placeholders).
     * Map: route path => [page title key, spec ID]
     */
    public static function adminRouteProvider(): array
    {
        return [
            ['/admin/dashboard',                    'A001'],
            ['/admin/telegram',                     'A002'],
            ['/admin/health',                       'A003'],
            ['/admin/features',                     'A004'],
            ['/admin/marketplace',                  'A005'],
            ['/admin/ai',                           'A006'],
            ['/admin/payments',                     'A007'],
            ['/admin/users',                        'A008'],
            ['/admin/content',                      'A009'],
            ['/admin/notifications',                'A010'],
            ['/admin/files',                        'A011'],
            ['/admin/jobs',                         'A012'],
            ['/admin/backups',                      'A013'],
            ['/admin/integrity',                    'A014'],
            ['/admin/security',                     'A015'],
            ['/admin/audit',                        'A016'],
            ['/admin/settings',                     'A017'],
            ['/admin/recovery',                     'A018'],
            ['/admin/safe-mode',                    'A019'],
            ['/admin/monetization',                 'A020'],
            ['/admin/maintenance',                  'A021'],
            ['/admin/reports',                      'A022'],
            ['/admin/monetization/sponsored-ads',   'A023'],
        ];
    }

    #[DataProvider('adminRouteProvider')]
    public function test_admin_route_renders(string $path, string $spec): void
    {
        $res = $this->get($path);
        $res->assertStatus(200);
        // Every admin screen uses the shared admin layout shell
        $res->assertSee('class="admin-shell"', false);
        $res->assertSee('id="toast"', false);
    }

    #[DataProvider('adminRouteProvider')]
    public function test_admin_route_has_sidebar_nav(string $path, string $spec): void
    {
        $res = $this->get($path);
        $res->assertStatus(200);
        // Sidebar exists on all admin screens
        $res->assertSee('class="sidebar"', false);
        $res->assertSee('href="/admin/dashboard"', false);
    }

    #[DataProvider('adminRouteProvider')]
    public function test_admin_route_has_pending_notice(string $path, string $spec): void
    {
        // A023 (Sponsored Ads) is now a full UI — it is not a placeholder.
        if ($spec === 'A023') {
            $this->markTestSkipped('A023 graduated from placeholder (L267)');
        }

        $res = $this->get($path);
        $res->assertStatus(200);
        // Every admin screen has a pending notice until backend integration
        $res->assertSee('class="pending"', false);
    }

    public function test_all_23_admin_routes_are_registered(): void
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $adminRoutes = [];
        foreach ($routes as $r) {
            $uri = $r->uri();
            if (str_starts_with($uri, 'admin/')) {
                $adminRoutes[] = $uri;
            }
        }
        $this->assertCount(23, $adminRoutes);
    }

    public function test_admin_layout_is_extended_by_all_views(): void
    {
        $views = glob(base_path('resources/views/admin/*.blade.php'));
        $this->assertCount(23, $views);
        foreach ($views as $v) {
            $src = file_get_contents($v);
            $this->assertStringContainsString("@extends('layouts.admin')", $src,
                "Missing @extends in: " . basename($v));
        }
    }

    public function test_admin_layout_exists_and_compiles(): void
    {
        $layout = base_path('resources/views/layouts/admin.blade.php');
        $this->assertFileExists($layout);
        $compiled = app('blade.compiler')->compileString(file_get_contents($layout));
        $this->assertGreaterThan(5000, strlen($compiled));
    }
}
