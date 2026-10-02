<?php

namespace Tests\Feature\Traceability;

use Tests\TestCase;

/**
 * T4 — Localization traceability.
 *
 * 1. EN/AM key parity (identical key sets)
 * 2. Every __('key') in resources/ + app/ has a translation in both files
 */
class LocalizationTest extends TestCase
{
    private array $en;
    private array $am;

    protected function setUp(): void
    {
        parent::setUp();
        $this->en = json_decode(file_get_contents(lang_path('en.json')), true);
        $this->am = json_decode(file_get_contents(lang_path('am.json')), true);
    }

    public function test_en_am_key_parity(): void
    {
        $en = array_keys($this->en);
        $am = array_keys($this->am);

        $onlyEn = array_diff($en, $am);
        $onlyAm = array_diff($am, $en);

        $this->assertEmpty($onlyEn, 'Keys only in EN: ' . implode(', ', array_slice($onlyEn, 0, 20)));
        $this->assertEmpty($onlyAm, 'Keys only in AM: ' . implode(', ', array_slice($onlyAm, 0, 20)));
    }

    public function test_all_used_translation_keys_exist(): void
    {
        $pattern = '/(?:__|@lang)\s*\(\s*[\'"]([^\'"]+)[\'"]/';
        $used = [];

        foreach (['resources', 'app', 'routes'] as $dir) {
            if (! is_dir(base_path($dir))) continue;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(base_path($dir), \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if (! $file->isFile()) continue;
                if (! preg_match('/\.php$/', $file->getFilename())) continue;
                $content = file_get_contents($file->getPathname());
                if (preg_match_all($pattern, $content, $m)) {
                    foreach ($m[1] as $key) {
                        $used[$key] = true;
                    }
                }
            }
        }

        $usedKeys = array_keys($used);
        $missingEn = array_values(array_filter($usedKeys, fn($k) => ! isset($this->en[$k])));
        $missingAm = array_values(array_filter($usedKeys, fn($k) => ! isset($this->am[$k])));

        $this->assertEmpty($missingEn,
            "Used keys missing in EN: " . implode(', ', $missingEn));
        $this->assertEmpty($missingAm,
            "Used keys missing in AM: " . implode(', ', $missingAm));
    }

    public function test_admin_setting_labels_present(): void
    {
        foreach (['adminSettingTotal', 'adminSettingFreeze', 'adminSettingRisk', 'adminSettingVersion'] as $k) {
            $this->assertArrayHasKey($k, $this->en, "EN missing {$k}");
            $this->assertArrayHasKey($k, $this->am, "AM missing {$k}");
        }
    }
}
