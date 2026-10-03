<?php

declare(strict_types=1);

namespace App\Exceptions\OfferSubmission;

use RuntimeException;

/**
 * S023 — Same idempotency key with a different payload.
 * Per spec: returns 409 IDEMPOTENCY_CONFLICT.
 */
final class IdempotencyConflictException extends RuntimeException
{
    public function __construct(string $message = 'Same idempotency key with different payload.')
    {
        parent::__construct($message);
    }
}