<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when a CRITICAL publish is attempted without 2FA setup.
 *
 * Maps to HTTP 403 with error code TWO_FACTOR_REQUIRED.
 *
 * Per Admin_Authorization_Contract.md §3:
 *   "CRITICAL requires second factor."
 */
class TwoFactorRequiredException extends Exception
{
    public function __construct(
        public readonly string $settingKey,
    ) {
        parent::__construct(
            "Two-factor authentication is required for CRITICAL setting '{$settingKey}'. "
            . 'Please enable 2FA in your account settings before publishing.',
        );
    }

    public function toArray(): array
    {
        return [
            'setting_key' => $this->settingKey,
            'action'      => 'enroll_2fa',
        ];
    }
}
