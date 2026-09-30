<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Boost;
use App\Models\BoostPackage;
use App\Models\Need;
use App\Models\Payment;
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

        $validated = $request->validate([
            'package_id' => ['required', 'uuid', 'exists:boost_packages,id'],
        ]);

        $package = BoostPackage::find($validated['package_id']);
        if (!$package || !$package->active) {
            return $this->error('PACKAGE_UNAVAILABLE', 'Package unavailable.', 422);
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

        return $this->success($boost->fresh(['package']), 'Boost created. Complete payment to activate.', 201);
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
