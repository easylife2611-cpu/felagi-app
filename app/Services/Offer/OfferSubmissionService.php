<?php

declare(strict_types=1);

namespace App\Services\Offer;

use App\Exceptions\OfferSubmission\IdempotencyConflictException;
use App\Exceptions\OfferSubmission\PolicyUnknownException;
use App\Models\Need;
use App\Models\Offer;
use App\Models\OfferSubmission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * S023 — Offer Submission Unlock service.
 *
 * Contract: System_Specification/Monetization_Payment_Specification.md
 *
 *   OFF = FREE. ON + 0 ETB = FREE. ON + positive amount = PAYMENT REQUIRED.
 *   CLIENT SUCCESS != VERIFIED PAYMENT.
 *   Free policy creates exactly one Offer.
 *   Paid policy creates exactly one submission aggregate + one payment intent.
 *
 * G2 scope: free path fully functional; paid path creates aggregate only
 * (payment intent creation deferred to G3, needs gateway integration).
 */
class OfferSubmissionService
{
    public const POLICY_FREE = 'free';
    public const POLICY_PAID = 'paid';

    /**
     * Resolve unlock policy from config.
     *
     * @return array{kind:string, policy_version:string, amount_minor:int, currency:string}
     * @throws PolicyUnknownException
     */
    public function resolvePolicy(): array
    {
        $config = config('payments.unlock');

        if (!is_array($config)) {
            throw new PolicyUnknownException('Unlock policy config missing.');
        }

        $enabled  = $config['feature_enabled'] ?? null;
        $amount   = $config['amount_minor'] ?? null;
        $version  = $config['policy_version'] ?? null;
        $currency = config('payments.currency', 'ETB');

        if ($enabled === null || $amount === null || $version === null) {
            throw new PolicyUnknownException('Unlock policy fields incomplete.');
        }

        $enabled = (bool) $enabled;
        $amount  = (int) $amount;

        if ($enabled && $amount > 0) {
            return [
                'kind'           => self::POLICY_PAID,
                'policy_version' => (string) $version,
                'amount_minor'   => $amount,
                'currency'       => (string) $currency,
            ];
        }

        return [
            'kind'           => self::POLICY_FREE,
            'policy_version' => (string) $version,
            'amount_minor'   => 0,
            'currency'       => (string) $currency,
        ];
    }

    /**
     * Submit an Offer for a Need.
     *
     * Idempotent on (provider_id, idempotency_key). Same key + same payload
     * returns the existing aggregate. Same key + different payload throws
     * IdempotencyConflictException.
     *
     * @param array<string,mixed> $offerPayload
     * @throws InvalidArgumentException|IdempotencyConflictException|PolicyUnknownException
     */
    public function submit(
        User $provider,
        Need $need,
        array $offerPayload,
        string $draftId,
        int $draftVersion,
        string $draftHash,
        string $idempotencyKey,
    ): OfferSubmission {
        $this->assertEligible($provider, $need);

        $policy = $this->resolvePolicy();

        return DB::transaction(function () use (
            $provider, $need, $offerPayload,
            $draftId, $draftVersion, $draftHash, $idempotencyKey, $policy
        ) {
            // Idempotency: same key
            $existing = OfferSubmission::query()
                ->where('provider_id', $provider->id)
                ->where('idempotency_key', $idempotencyKey)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                // Same key + different payload → 409
                if ($existing->draft_hash !== $draftHash) {
                    throw new IdempotencyConflictException();
                }
                return $existing;
            }

            if ($policy['kind'] === self::POLICY_FREE) {
                return $this->createFreeSubmission(
                    $provider, $need, $offerPayload,
                    $draftId, $draftVersion, $draftHash, $idempotencyKey, $policy
                );
            }

            return $this->createPaidSubmission(
                $provider, $need, $offerPayload,
                $draftId, $draftVersion, $draftHash, $idempotencyKey, $policy
            );
        });
    }

    /**
     * Look up an existing submission by (provider, idempotency_key).
     */
    public function getByIdempotencyKey(User $provider, string $idempotencyKey): ?OfferSubmission
    {
        return OfferSubmission::query()
            ->where('provider_id', $provider->id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();
    }

    private function assertEligible(User $provider, Need $need): void
    {
        if ($need->requester_id === $provider->id) {
            throw new InvalidArgumentException('Cannot unlock own Need.');
        }
        if ($need->status !== Need::STATUS_OPEN) {
            throw new InvalidArgumentException('Need is not OPEN.');
        }
        if ($need->offer_deadline_at && now()->gte($need->offer_deadline_at)) {
            throw new InvalidArgumentException('Offer deadline has passed.');
        }
    }

    /**
     * Free policy path: create exactly one Offer + one submission (state=submitted).
     */
    private function createFreeSubmission(
        User $provider, Need $need, array $payload,
        string $draftId, int $draftVersion, string $draftHash,
        string $idempotencyKey, array $policy
    ): OfferSubmission {
        if ($need->offers()->where('provider_id', $provider->id)->exists()) {
            throw new InvalidArgumentException('You already have an Offer on this Need.');
        }

        $offer = Offer::create([
            'id'                 => (string) Str::uuid(),
            'need_id'            => $need->id,
            'provider_id'        => $provider->id,
            'offered_price'      => $payload['offered_price'] ?? 0,
            'currency'           => $payload['currency'] ?? $policy['currency'],
            'proposal_message'   => $payload['proposal_message'] ?? '',
            'delivery_time_text' => $payload['delivery_time_text'] ?? null,
            'availability_text'  => $payload['availability_text'] ?? null,
            'additional_notes'   => $payload['additional_notes'] ?? null,
            'status'             => Offer::STATUS_PENDING,
            'version'            => 1,
        ]);

        $submission = OfferSubmission::create([
            'id'              => (string) Str::uuid(),
            'need_id'         => $need->id,
            'provider_id'     => $provider->id,
            'state'           => OfferSubmission::STATE_SUBMITTED,
            'draft_id'        => $draftId,
            'draft_version'   => $draftVersion,
            'draft_hash'      => $draftHash,
            'idempotency_key' => $idempotencyKey,
            'policy_version'  => $policy['policy_version'],
            'amount_minor'    => 0,
            'currency'        => $policy['currency'],
            'offer_id'        => $offer->id,
            'unlocked_at'     => now(),
        ]);

        return $submission->fresh(['offer', 'need']);
    }

    /**
     * Paid policy path: create submission aggregate (state=payment-required).
     * Payment intent creation is G3 (requires gateway integration).
     */
    private function createPaidSubmission(
        User $provider, Need $need, array $payload,
        string $draftId, int $draftVersion, string $draftHash,
        string $idempotencyKey, array $policy
    ): OfferSubmission {
        $submission = OfferSubmission::create([
            'id'              => (string) Str::uuid(),
            'need_id'         => $need->id,
            'provider_id'     => $provider->id,
            'state'           => OfferSubmission::STATE_PAYMENT_REQUIRED,
            'draft_id'        => $draftId,
            'draft_version'   => $draftVersion,
            'draft_hash'      => $draftHash,
            'idempotency_key' => $idempotencyKey,
            'policy_version'  => $policy['policy_version'],
            'amount_minor'    => $policy['amount_minor'],
            'currency'        => $policy['currency'],
            'offer_id'        => null,
        ]);

        return $submission->fresh(['need']);
    }
}