<?php

namespace Tests\Feature\AI;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GAP-AUD-02 — AI requirements audit integrity.
 */
class AiRequirementsAuditTest extends TestCase
{
    use RefreshDatabase;

    private function auditBody(): string
    {
        $path = base_path('docs/reports/AI_REQUIREMENTS_AUDIT_20261001.md');
        $this->assertFileExists($path);
        return file_get_contents($path);
    }

    public function test_audit_document_exists(): void
    {
        $this->assertFileExists(
            base_path('docs/reports/AI_REQUIREMENTS_AUDIT_20261001.md')
        );
    }

    public function test_audit_covers_all_51_requirements(): void
    {
        $body = $this->auditBody();
        $missing = [];
        for ($i = 1; $i <= 51; $i++) {
            $code = "AI-{$i}";
            if (! preg_match('/\|\s*' . preg_quote($code, '/') . '\s*\|/', $body)) {
                $missing[] = $code;
            }
        }
        $this->assertSame([], $missing, 'Missing rows: ' . implode(', ', $missing));
    }

    public function test_audit_references_existing_ai_services(): void
    {
        $files = [
            'app/Services/AI/ComparisonService.php',
            'app/Services/AI/GeminiClient.php',
            'app/Http/Controllers/Api/V1/ComparisonController.php',
        ];
        foreach ($files as $f) {
            $this->assertFileExists(base_path($f), "Missing: {$f}");
        }
    }

    public function test_audit_references_existing_comparison_models(): void
    {
        $models = [
            'Comparison', 'ComparisonResult',
            'ComparisonOffer', 'ComparisonAttempt',
        ];
        foreach ($models as $m) {
            $this->assertFileExists(
                base_path("app/Models/{$m}.php"),
                "Missing model: {$m}"
            );
        }
    }

    public function test_audit_references_contract_doc(): void
    {
        $body = $this->auditBody();
        $this->assertStringContainsString('AI_Evaluation_Contract.md', $body);
    }

    public function test_audit_summary_totals_to_51(): void
    {
        $body = $this->auditBody();
        $this->assertMatchesRegularExpression(
            '/\*\*Total\*\*\s*\|\s*\*\*51\*\*/',
            $body
        );
    }
}
