<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when a HIGH/CRITICAL publish is attempted without
 * fresh authentication within the 5-minute window.
 *
 * Maps to HTTP 401 with error code REAUTH_REQUIRED.
 *
 * Per Admin_Authorization_Contract.md §3:
 *   "HIGH/CRITICAL publication requires fresh authenticated
 *    reauthentication within five minutes, reason and exact
 *    impact/version confirmation."
 *
 * Per DFM-FDS-1.4.md §8.3:
 *   "HIGH/CRITICAL changes require Main Admin, recent re-auth,
 *    second factor, reason and explicit confirmation."
 */
class ReauthRequiredException extends Exception
{
    public const WINDOW_MINUTES = 5;

    public function __construct(
        public readonly string $settingKey,
        public readonly string $risk,
        public readonly ?\DateTimeInterface $lastAuthAt = null,
    ) {
        $message = sprintf(
            "Re-authentication required for %s setting '%s'. "
            . "Please re-authenticate to proceed (window: %d minutes).",
            $risk,
            $settingKey,
            self::WINDOW_MINUTES,
        );

        parent::__construct($message);
    }

    public function toArray(): array
    {
        return [
            'setting_key'    => $this->settingKey,
            'risk'           => $this->risk,
            'window_minutes' => self::WINDOW_MINUTES,
            'last_auth_at'   => $this->lastAuthAt?->format('Y-m-d\TH:i:s\Z'),
        ];
    }
}
