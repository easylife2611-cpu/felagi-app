<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

abstract class BaseApiController extends Controller
{
    use AuthorizesRequests;

    /**
     * Success response envelope.
     */
    protected function success(
        mixed $data = null,
        string $message = '',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        return response()->json([
            'success'    => true,
            'data'       => $data,
            'message'    => $message,
            'request_id' => $this->requestId(),
            'meta'       => $meta,
        ], $status);
    }

    /**
     * Error response envelope.
     */
    protected function error(
        string $code,
        string $message,
        int $status = 400,
        ?array $details = null
    ): JsonResponse {
        $payload = [
            'success'    => false,
            'error'      => [
                'code'    => $code,
                'message' => $message,
            ],
            'request_id' => $this->requestId(),
        ];

        if ($details !== null) {
            $payload['error']['details'] = $details;
        }

        return response()->json($payload, $status);
    }

    /**
     * Validation error with field-specific details.
     */
    protected function validationError(array $errors): JsonResponse
    {
        return $this->error(
            'VALIDATION_FAILED',
            'The submitted data is invalid.',
            422,
            $errors
        );
    }

    /**
     * Generate or retrieve request ID for correlation.
     */
    protected function requestId(): string
    {
        $request = request();
        if (!$request->attributes->has('request_id')) {
            $request->attributes->set('request_id', (string) Str::uuid());
        }
        return $request->attributes->get('request_id');
    }
}
