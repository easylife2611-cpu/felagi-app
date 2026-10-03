<?php

declare(strict_types=1);

namespace App\Exceptions\OfferSubmission;

use RuntimeException;

/**
 * S023 — Unlock policy is missing or incomplete.
 * Per spec: missing policy is UNKNOWN and blocks submission while
 * preserving the draft. Do NOT substitute zero for a missing value.
 */
final class PolicyUnknownException extends RuntimeException
{
    public function __construct(string $message = 'Unlock policy is unknown or incomplete.')
    {
        parent::__construct($message);
    }
}