<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * G04C — Security headers.
 */
class SecurityHeadersTest extends TestCase
{
    public function test_x_content_type_options_present(): void
    {
        $res = $this->get('/');
        $this->assertSame('nosniff', $res->headers->get('X-Content-Type-Options'));
    }

    public function test_x_frame_options_present(): void
    {
        $res = $this->get('/');
        $this->assertSame('DENY', $res->headers->get('X-Frame-Options'));
    }

    public function test_referrer_policy_present(): void
    {
        $res = $this->get('/');
        $this->assertSame(
            'strict-origin-when-cross-origin',
            $res->headers->get('Referrer-Policy')
        );
    }

    public function test_csp_present(): void
    {
        $res = $this->get('/');
        $csp = $res->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp);
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString('telegram.org', $csp);
    }

    public function test_headers_present_on_api_routes(): void
    {
        $res = $this->getJson('/api/health');
        $this->assertSame('nosniff', $res->headers->get('X-Content-Type-Options'));
        $this->assertSame('DENY', $res->headers->get('X-Frame-Options'));
    }
}
