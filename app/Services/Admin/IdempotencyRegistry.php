<?php

namespace App\Services\Admin;

use App\Exceptions\IdempotencyConflictException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Idempotency-Key registry.
 *
 * Design lock (DFM-FDS-1.4.md §149):
 *   - PK: (actor_scope, scope, key)
 *   - Same key + different body → 409 IDEMPOTENCY_CONFLICT
 *   - Response stored for replay
 *   - status: IN_PROGRESS | COMPLETE
 *
 * TTL: 24h (D-070). expires_at is set on insert.
 *
 * Usage:
 *   $registry->begin($request, $scope);          // returns existing response or null
 *   $response = $handler();                       // run real handler
 *   $registry->complete($request, $scope, $response);
 */
class IdempotencyRegistry
{
    /** TTL for idempotency records (D-070). */
    public const TTL_HOURS = 24;

    /** Header name (RFC draft). */
    public const HEADER = 'Idempotency-Key';

    /**
     * Compute actor_scope from authenticated user.
     * For webhook providers, this would be a signed namespace.
     */
    public function actorScope(?User $user): string
    {
        return $user ? "user:{$user->id}" : 'anonymous';
    }

    /**
     * Compute request hash (SHA-256 of method + path + normalized body).
     */
    public function requestHash(Request $request): string
    {
        $payload = [
            'method' => $request->method(),
            'path'   => $request->path(),
            'body'   => $this->canonicalJson($request->all()),
        ];

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES));
    }

    /**
     * Attempt to begin an idempotent operation.
     *
     * @return array{state: string, record?: object}
     *   state='new'      → first time, proceed with handler
     *   state='replay'   → return cached response
     *   state='progress' → same key still in flight (client retry too fast)
     *
     * @throws IdempotencyConflictException on same key, different body
     */
    public function begin(Request $request, string $scope, ?User $user = null): array
    {
        $key = $request->header(self::HEADER);
        if (empty($key)) {
            return ['state' => 'new', 'key' => null];
        }

        if (strlen($key) > 100) {
            throw new IdempotencyConflictException(substr($key, 0, 20) . '...', $scope);
        }

        $actorScope = $this->actorScope($user);
        $hash       = $this->requestHash($request);

        return DB::transaction(function () use ($actorScope, $scope, $key, $hash, $user) {
            $existing = DB::table('idempotency_keys')
                ->where('actor_scope', $actorScope)
                ->where('scope', $scope)
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                // Same key, different body → 409
                if ($existing->request_hash !== $hash) {
                    throw new IdempotencyConflictException($key, $scope);
                }

                // Replay only if COMPLETE
                if ($existing->status === 'COMPLETE') {
                    return ['state' => 'replay', 'key' => $key, 'record' => $existing];
                }

                // In flight
                return ['state' => 'progress', 'key' => $key, 'record' => $existing];
            }

            // New record
            DB::table('idempotency_keys')->insert([
                'actor_scope'   => $actorScope,
                'scope'         => $scope,
                'key'           => $key,
                'actor_id'      => $user?->id,
                'request_hash'  => $hash,
                'response_code' => 0,
                'response_body' => null,
                'status'        => 'IN_PROGRESS',
                'expires_at'    => now()->addHours(self::TTL_HOURS),
                'created_at'    => now(),
            ]);

            return ['state' => 'new', 'key' => $key];
        });
    }

    /**
     * Mark an idempotent operation as COMPLETE.
     */
    public function complete(Request $request, string $scope, int $code, ?array $body = null, ?User $user = null): void
    {
        $key = $request->header(self::HEADER);
        if (empty($key)) {
            return;
        }

        DB::table('idempotency_keys')
            ->where('actor_scope', $this->actorScope($user))
            ->where('scope', $scope)
            ->where('key', $key)
            ->update([
                'response_code' => $code,
                'response_body' => $body ? json_encode($body) : null,
                'status'        => 'COMPLETE',
            ]);
    }

    /**
     * Cleanup expired records (called by scheduler).
     */
    public function cleanup(): int
    {
        return DB::table('idempotency_keys')
            ->where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Canonical JSON — sort keys recursively for stable hashing.
     */
    protected function canonicalJson(array $data): array
    {
        ksort($data);
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = $this->canonicalJson($v);
            }
        }
        return $data;
    }
}
