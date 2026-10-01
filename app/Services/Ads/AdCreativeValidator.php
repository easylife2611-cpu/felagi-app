<?php

namespace App\Services\Ads;

use App\Models\AdCreative;

/**
 * ADS-17 — Creative Validation
 *
 * LOCKED spec: System_Specification/Sponsored_Advertising_Contract.md
 *   §Data, validation and versioning
 *
 * - No advertiser HTML/JS/CSS/iframe, executable or animated media.
 * - PNG/JPEG/WebP only; decoded MIME must match; max 1 MiB; each dim 320-2048.
 * - CARD 16:9, BANNER 3:1, COMPACT 1:1; ratio tolerance 0.02.
 * - Both locales required: title 80, body 240, CTA 30, alt 200 code points.
 * - Store escaped plain text; reject markup.
 * - Optional text-only creative is valid.
 * - Automated checks do NOT replace human content review.
 *
 * Constitution: never guess. When media asset store is not available
 * server-side, return REQUIRES_EVIDENCE instead of fabricated PASS/FAIL.
 */
class AdCreativeValidator
{
    public const FORMAT_RATIOS = [
        AdCreative::FORMAT_CARD    => 16 / 9,
        AdCreative::FORMAT_BANNER  => 3 / 1,
        AdCreative::FORMAT_COMPACT => 1 / 1,
    ];

    public const RATIO_TOLERANCE = 0.02;

    public const MAX_TITLE_CODE_POINTS = 80;
    public const MAX_BODY_CODE_POINTS  = 240;
    public const MAX_CTA_CODE_POINTS   = 30;
    public const MAX_ALT_CODE_POINTS   = 200;

    public const ALLOWED_MIME_TYPES = ['image/png', 'image/jpeg', 'image/webp'];
    public const MAX_MEDIA_BYTES    = 1048576;
    public const MIN_DIMENSION_PX   = 320;
    public const MAX_DIMENSION_PX   = 2048;

    /**
     * @return array<string,string> field path => error message (empty = valid)
     */
    public function validate(AdCreative $c): array
    {
        $errors = [];

        if (! in_array($c->format, AdCreative::FORMATS, true)) {
            $errors['format'] = 'Unsupported format. Allowed: '
                . implode(', ', AdCreative::FORMATS) . '.';
        }

        $this->validateLocale($errors, 'am', [
            'title' => $c->copy_am_title,
            'body'  => $c->copy_am_body,
            'cta'   => $c->copy_am_cta,
            'alt'   => $c->copy_am_alt,
        ]);
        $this->validateLocale($errors, 'en', [
            'title' => $c->copy_en_title,
            'body'  => $c->copy_en_body,
            'cta'   => $c->copy_en_cta,
            'alt'   => $c->copy_en_alt,
        ]);

        // Media is optional. Text-only creative is valid.
        if (! empty($c->media_asset_id)) {
            $this->validateMediaReference($errors, (string) $c->media_asset_id);
        }

        return $errors;
    }

    private function validateLocale(array &$errors, string $loc, array $fields): void
    {
        $this->validateTextField($errors, "copy_{$loc}_title",
            $fields['title'], self::MAX_TITLE_CODE_POINTS, true);
        $this->validateTextField($errors, "copy_{$loc}_body",
            $fields['body'],  self::MAX_BODY_CODE_POINTS,  true);
        $this->validateTextField($errors, "copy_{$loc}_cta",
            $fields['cta'],   self::MAX_CTA_CODE_POINTS,   true);
        if ($fields['alt'] !== null && $fields['alt'] !== '') {
            $this->validateTextField($errors, "copy_{$loc}_alt",
                $fields['alt'], self::MAX_ALT_CODE_POINTS, false);
        }
    }

    private function validateTextField(
        array &$errors,
        string $path,
        ?string $value,
        int $maxCodePoints,
        bool $required
    ): void {
        if ($value === null || $value === '') {
            if ($required) {
                $errors[$path] = 'Required.';
            }
            return;
        }

        if ($this->containsMarkup($value)) {
            $errors[$path] = 'Markup is not allowed. Store plain text only.';
            return;
        }

        $codePoints = mb_strlen($value, 'UTF-8');
        if ($codePoints > $maxCodePoints) {
            $errors[$path] = "Exceeds {$maxCodePoints} Unicode code points (got {$codePoints}).";
        }
    }

    private function containsMarkup(string $v): bool
    {
        if (preg_match('/<\s*\/?\s*[a-zA-Z][^>]*>/', $v)) {
            return true;
        }
        if (preg_match('/<\s*(script|style|iframe|object|embed|svg|math)/i', $v)) {
            return true;
        }
        if (stripos($v, 'javascript:') !== false) {
            return true;
        }
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $v)) {
            return true;
        }
        return false;
    }

    /**
     * Media asset references are opaque strings. Server-side asset store is
     * not implemented in this codebase yet (see OPEN_GAPS / GAP-12).
     * Per Constitution (UNKNOWN != MISSING), return REQUIRES_EVIDENCE
     * rather than fabricating a PASS or FAIL.
     */
    private function validateMediaReference(array &$errors, string $mediaAssetId): void
    {
        $errors['media_asset_id'] =
            'REQUIRES_EVIDENCE: media asset store not implemented server-side '
            . '(see GAP-12 - Ads media scanning). Automated file checks cannot '
            . 'replace this verification (ADS-17).';
    }
}
