<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GAP-AUD-01 — Admin requirements audit integrity.
 *
 * Verifies the audit document exists and that every claimed production
 * artefact actually exists in the repo. Anchors the audit to facts.
 */
class AdminRequirementsAuditTest extends TestCase
{
    use RefreshDatabase;

    private function auditBody(): string
    {
        $path = base_path('docs/reports/ADMIN_REQUIREMENTS_AUDIT_20261001.md');
        $this->assertFileExists($path);
        return file_get_contents($path);
    }

    public function test_audit_document_exists(): void
    {
        $this->assertFileExists(
            base_path('docs/reports/ADMIN_REQUIREMENTS_AUDIT_20261001.md')
        );
    }

    public function test_audit_covers_all_55_requirements(): void
    {
        $body = $this->auditBody();
        $codes = [
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P',
            'Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
            'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
            'BA','BB','BC',
        ];
        $missing = [];
        foreach ($codes as $c) {
            if (! preg_match('/\|\s*' . preg_quote($c, '/') . '\s*\|/', $body)) {
                $missing[] = $c;
            }
        }
        $this->assertSame([], $missing, 'Missing rows: ' . implode(', ', $missing));
    }

    public function test_audit_marks_ar_as_missing(): void
    {
        $body = $this->auditBody();
        $this->assertMatchesRegularExpression(
            '/\|\s*AR\s*\|[^|]*\|[^|]*\|[^|]*\|\s*❌/u',
            $body
        );
    }

    public function test_audit_references_existing_controllers(): void
    {
        $files = [
            'app/Http/Controllers/Api/V1/Admin/AdminAdsController.php',
            'app/Http/Controllers/Api/V1/Admin/AdminChangeController.php',
            'app/Http/Controllers/Api/V1/Admin/AdminReadController.php',
            'app/Http/Controllers/Api/V1/Admin/AdminTelegramController.php',
            'app/Http/Controllers/Admin/Auth/AdminLoginController.php',
        ];
        foreach ($files as $f) {
            $this->assertFileExists(base_path($f), "Missing controller: {$f}");
        }
    }

    public function test_audit_references_existing_services_and_policies(): void
    {
        $files = [
            'app/Services/Admin/AdminChangeService.php',
            'app/Services/Admin/AuditWriter.php',
            'app/Services/Admin/IdempotencyRegistry.php',
            'app/Services/Admin/OutboxWriter.php',
            'app/Services/Admin/ReauthValidator.php',
            'app/Services/Admin/TotpService.php',
            'app/Policies/AdminReadPolicy.php',
            'app/Policies/SettingPolicy.php',
            'app/Http/Middleware/EnsureAdminRole.php',
            'app/Http/Middleware/IdempotencyKey.php',
            'app/Http/Middleware/RequestId.php',
            'app/Http/Middleware/RequireReauth.php',
        ];
        foreach ($files as $f) {
            $this->assertFileExists(base_path($f), "Missing: {$f}");
        }
    }

    public function test_audit_references_existing_admin_models(): void
    {
        $models = [
            'AuditLog', 'AuthAttempt', 'OutboxEvent',
            'SettingDraft', 'Setting', 'SettingVersion',
        ];
        foreach ($models as $m) {
            $this->assertFileExists(
                base_path("app/Models/{$m}.php"),
                "Missing model: {$m}"
            );
        }
    }

    public function test_audit_summary_totals_to_55(): void
    {
        $body = $this->auditBody();
        $this->assertMatchesRegularExpression(
            '/\*\*Total\*\*\s*\|\s*\*\*55\*\*/',
            $body
        );
    }
}
