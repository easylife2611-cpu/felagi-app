<?php

namespace Database\Factories;

use App\Models\AdDelivery;
use App\Models\AdEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdEventFactory extends Factory
{
    protected $model = AdEvent::class;

    public function definition(): array
    {
        $eventId = 'evt_' . bin2hex(random_bytes(12));
        return [
            'event_id'    => $eventId,
            'delivery_id' => AdDelivery::factory(),
            'type'        => AdEvent::TYPE_IMPRESSION,
            'observed_at' => now(),
            'coverage'    => 0.75,
            'dedupe_key'  => $eventId . '_impression',
            'ingestion_outcome' => 'ACCEPTED',
        ];
    }

    public function click(): static
    {
        return $this->state(function () {
            $id = 'evt_' . bin2hex(random_bytes(12));
            return [
                'event_id'   => $id,
                'type'       => AdEvent::TYPE_CLICK,
                'dedupe_key' => $id . '_click',
                'coverage'   => null,
            ];
        });
    }
}
