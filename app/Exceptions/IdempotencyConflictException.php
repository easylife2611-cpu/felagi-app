<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when the same Idempotency-Key is reused with a different body.
 *
 * Maps to HTTP 409 with error code IDEMPOTENCY_CONFLICT.
 *
 * Per DFM-FDS-1.4.md §149:
 *   "idempotency_keys... A reused key with different body returns 409."
 * Per Admin_Authorization_Contract.md §4:
 *   "retry only failed eligible IDs using original operation idempotency keys."
 */
class IdempotencyConflictException extends Exception
{
    public function __construct(
        public readonly string $idempotencyKey,
        public readonly string $scope,
    ) {
        parent::__construct(
            "Idempotency key '{$idempotencyKey}' was already used for a different request body.",
        );
    }

    public function toArray(): array
    {
        return [
            'code'            => 'IDEMPOTENCY_CONFLICT',
            'idempotency_key' => $this->idempotencyKey,
            'scope'           => $this->scope,
        ];
    }
}
