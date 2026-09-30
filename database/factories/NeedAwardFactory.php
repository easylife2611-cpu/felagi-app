<?php

namespace Database\Factories;

use App\Models\Need;
use App\Models\NeedAward;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NeedAwardFactory extends Factory
{
    protected $model = NeedAward::class;

    public function definition(): array
    {
        return [
            'need_id'     => function () {
                return Need::factory()->create()->id;
            },
            'offer_id'    => function (array $attrs) {
                $need = Need::find($attrs['need_id']);
                $provider = User::factory()->create();
                return Offer::create([
                    'need_id'          => $need->id,
                    'provider_id'      => $provider->id,
                    'offered_price'    => '500.00',
                    'currency'         => 'ETB',
                    'proposal_message' => 'I can deliver.',
                    'status'           => 'PENDING',
                ])->id;
            },
            'accepted_by' => function (array $attrs) {
                return Need::find($attrs['need_id'])->requester_id;
            },
            'accepted_at' => now(),
            'request_id'  => 'req-' . Str::uuid(),
        ];
    }
}
