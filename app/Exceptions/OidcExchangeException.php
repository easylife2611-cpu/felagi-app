<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when Telegram OIDC code exchange or token validation fails.
 *
 * Maps to HTTP 401 with error code OIDC_EXCHANGE_FAILED.
 *
 * Per DFM-FDS-1.4.md §218:
 *   "exchanges the code server-side, validates Telegram signature/JWKS
 *    and issuer/audience/expiry/nonce"
 */
class OidcExchangeException extends Exception
{
    public const REASON_CODE_EXCHANGE_FAILED  = 'code_exchange_failed';
    public const REASON_INVALID_TOKEN         = 'invalid_token';
    public const REASON_ISSUER_MISMATCH       = 'issuer_mismatch';
    public const REASON_AUDIENCE_MISMATCH     = 'audience_mismatch';
    public const REASON_EXPIRED               = 'token_expired';
    public const REASON_NONCE_MISMATCH        = 'nonce_mismatch';
    public const REASON_JWKS_UNAVAILABLE      = 'jwks_unavailable';
    public const REASON_MISSING_SUBJECT       = 'missing_subject';
    public const REASON_ATTEMPT_NOT_FOUND     = 'attempt_not_found';
    public const REASON_ATTEMPT_EXPIRED       = 'attempt_expired';
    public const REASON_HANDOFF_INVALID       = 'handoff_invalid';

    public function __construct(
        public readonly string $reason,
        ?string $detail = null,
    ) {
        $message = match ($reason) {
            self::REASON_CODE_EXCHANGE_FAILED => 'Failed to exchange authorization code with Telegram.',
            self::REASON_INVALID_TOKEN        => 'Telegram ID token signature is invalid.',
            self::REASON_ISSUER_MISMATCH      => 'Token issuer does not match expected value.',
            self::REASON_AUDIENCE_MISMATCH    => 'Token audience does not match client_id.',
            self::REASON_EXPIRED              => 'Token has expired.',
            self::REASON_NONCE_MISMATCH       => 'Token nonce does not match auth attempt.',
            self::REASON_JWKS_UNAVAILABLE     => 'Telegram JWKS endpoint is unavailable.',
            self::REASON_MISSING_SUBJECT      => 'Token is missing required sub (subject) claim.',
            self::REASON_ATTEMPT_NOT_FOUND    => 'Auth attempt not found or already consumed.',
            self::REASON_ATTEMPT_EXPIRED      => 'Auth attempt has expired.',
            self::REASON_HANDOFF_INVALID      => 'Handoff code is invalid or already used.',
            default                           => 'OIDC exchange failed.',
        };

        if ($detail) {
            $message .= ' ' . $detail;
        }

        parent::__construct($message);
    }

    public function toArray(): array
    {
        return [
            'code'   => 'OIDC_EXCHANGE_FAILED',
            'reason' => $this->reason,
        ];
    }
}
