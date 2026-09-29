<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when a submitted TOTP code is invalid or expired.
 *
 * Maps to HTTP 422 with error code INVALID_2FA_CODE.
 *
 * Per Admin_Authorization_Contract.md §3:
 *   "CRITICAL requires second factor. If session/second factor
 *    is unavailable, deny publish while retaining draft."
 */
class InvalidTotpCodeException extends Exception
{
    public function __construct(
        public readonly string $reason = 'invalid',
    ) {
        parent::__construct(
            $reason === 'replay'
                ? 'This TOTP code has already been used. Please wait for a new code.'
                : 'The TOTP code is invalid or expired.',
        );
    }

    public function toArray(): array
    {
        return [
            'code'   => 'INVALID_2FA_CODE',
            'reason' => $this->reason,
        ];
    }
}
