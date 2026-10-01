<?php

namespace App\Services\Auth;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

/**
 * Two-Factor Authentication (TOTP) — fresh implementation (L302)
 * Amharic + English messages supported at controller layer.
 */
class TwoFactorService
{
    private Google2FA $engine;

    public function __construct()
    {
        $this->engine = new Google2FA();
    }

    public function isEnabled(User $user): bool
    {
        return ! empty($user->totp_secret) && ! empty($user->totp_enabled_at);
    }

    public function generateSecret(): string
    {
        return $this->engine->generateSecretKey(32);
    }

    public function otpauthUrl(User $user, string $secret): string
    {
        return $this->engine->getQRCodeUrl(
            config('app.name', 'Felagi'),
            $user->email ?? $user->id,
            $secret
        );
    }

    public function qrSvg(string $otpauthUrl, int $size = 240): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        return $writer->writeString($otpauthUrl);
    }

    public function verifyCode(string $secret, string $code): bool
    {
        return (bool) $this->engine->verifyKey($secret, $code, 2);
    }

    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }
        return $codes;
    }
}
