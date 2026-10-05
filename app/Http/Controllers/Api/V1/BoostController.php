<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Boost;
use App\Models\BoostPackage;
use App\Models\Need;
use App\Models\Payment;
use App\Services\Payments\BoostPurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BoostController extends BaseApiController
{
    /**
     * GET /api/v1/boost-packages
     * List active boost packages (public).
     */
    public function packages(Request $request): JsonResponse
    {
        $packages = BoostPackage::query()
            ->where('active', true)
            ->orderBy('price')
            ->get();

        return $this->success($packages, 'Boost packages retrieved.');
    }

    /**
     * POST /api/v1/needs/{needId}/boosts
     * Create a boost for the authenticated user's need.
     */
    public function store(Request $request, string $needId): JsonResponse
    {
        $need = Need::find($needId);
        if (!$need) {
            return $this->error('NOT_FOUND', 'Need not found.', 404);
        }

        $user = $request->user();
        if ($need->requester_id !== $user->id) {
            return $this->error('FORBIDDEN', 'Not your Need.', 403);
        }

        if ($need->status !== Need::STATUS_OPEN) {
            return $this->error('STATE_CONFLICT', 'Only OPEN needs can be boosted.', 409);
        }

        // ── Idempotency pre-check (DFM §262) ──
        $idempotencyKey = $request->header('Idempotency-Key');
        if ($idempotencyKey) {
            $existing = Payment::where('payer_id', $user->id)
                ->where('idempotency_key', $idempotencyKey)
                ->where('purpose', Payment::PURPOSE_BOOST)
                ->with('boost.package')
                ->first();
            if ($existing && $existing->boost) {
                return $this->success([
                    'boost'   => $existing->boost,
                    'payment' => [
                        'id'           => $existing->id,
                        'status'       => $existing->status,
                        'provider'     => $existing->provider,
                        'amount'       => $existing->amount,
                        'currency'     => $existing->currency,
                        'checkout_url' => null,
                    ],
                ], 'Boost already created (idempotent).', 201);
            }
        }

        // ── 409: Active boost on same Need (DFM §127) ──
        $activeExists = Boost::where('need_id', $need->id)
            ->where('status', Boost::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
        if ($activeExists) {
            return $this->error('BOOST_ACTIVE', 'Need already has an active boost.', 409);
        }

        $validated = $request->validate([
            'package_id' => ['required', 'uuid', 'exists:boost_packages,id'],
        ]);

        $package = BoostPackage::find($validated['package_id']);
        if (!$package || !$package->active) {
            return $this->error('PACKAGE_UNAVAILABLE', 'Package unavailable.', 422);
        }

        // ── 503: Payments disabled (config missing/unsupported driver) ──
        //    Note: phpunit.xml `value="null"` → Laravel env() returns PHP null,
        //    while .env `PAYMENT_DRIVER=null` → string 'null'.
        //    Both are valid "null" gateway markers.
        $driver = config('payments.driver');
        $driverNorm = $driver === null ? 'null' : (string) $driver;
        if (!in_array($driverNorm, ['null', 'chapa'], true)) {
            return $this->error('PAYMENTS_DISABLED', 'Payments are not enabled.', 503);
        }

        $boost = DB::transaction(function () use ($need, $user, $package) {
            $boost = Boost::create([
                'id' => (string) Str::uuid(),
                'need_id' => $need->id,
                'requester_id' => $user->id,
                'package_id' => $package->id,
                'price_snapshot' => $package->price,
                'currency' => $package->currency ?? 'ETB',
                'duration_days' => $package->duration_days ?? 7,
                'status' => Boost::STATUS_PENDING,
                'starts_at' => null,
                'expires_at' => null,
            ]);

            return $boost;
        });

        // ── Wire BoostPurchaseService (DFM §265-268) ──
        $service = app(BoostPurchaseService::class);
        $result  = $service->initiate($boost, $idempotencyKey);

        $boost->refresh();
        $payment = $boost->payment;

        $paymentPayload = $payment ? [
            'id'           => $payment->id,
            'status'       => $payment->status,
            'provider'     => $payment->provider,
            'amount'       => $payment->amount,
            'currency'     => $payment->currency,
            'checkout_url' => $result->isOk()
                ? ($result->raw['checkout_url'] ?? null)
                : null,
        ] : null;

        return $this->success([
            'boost'   => $boost->fresh(['package']),
            'payment' => $paymentPayload,
        ], 'Boost created. Complete payment to activate.', 201);
    }

    /**
     * GET /api/v1/payments/{id}
     * Show a payment (owner only).
     */
    public function showPayment(Request $request, string $id): JsonResponse
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return $this->error('NOT_FOUND', 'Payment not found.', 404);
        }

        if (property_exists($payment, 'user_id') && $payment->user_id !== $request->user()->id) {
            return $this->error('FORBIDDEN', 'Not your payment.', 403);
        }

        return $this->success($payment, 'Payment retrieved.');
    }
}
