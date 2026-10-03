<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BulkAction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BulkActionFactory extends Factory
{
    protected $model = BulkAction::class;

    public function definition(): array
    {
        return [
            'actor_id'          => User::factory(),
            'action_type'       => 'archive',
            'entity_type'       => 'need',
            'scope'             => 'admin',
            'selection_ids'     => ['id-1', 'id-2'],
            'selection_digest'  => hash('sha256', 'default-selection'),
            'expected_count'    => 2,
            'status'            => BulkAction::STATUS_PREVIEWED,
            'items'             => null,
            'retry_of_id'       => null,
            'created_at'        => now(),
            'executed_at'       => null,
            'completed_at'      => null,
        ];
    }

    public function executed(): static
    {
        return $this->state(fn () => [
            'status'      => BulkAction::STATUS_EXECUTED,
            'executed_at' => now(),
            'completed_at'=> now(),
            'items'       => [
                ['id' => 'id-1', 'status' => BulkAction::ITEM_SUCCEEDED],
                ['id' => 'id-2', 'status' => BulkAction::ITEM_SUCCEEDED],
            ],
        ]);
    }

    public function partial(): static
    {
        return $this->state(fn () => [
            'status'      => BulkAction::STATUS_PARTIAL,
            'executed_at' => now(),
            'completed_at'=> now(),
            'items'       => [
                ['id' => 'id-1', 'status' => BulkAction::ITEM_SUCCEEDED],
                ['id' => 'id-2', 'status' => BulkAction::ITEM_FAILED, 'reason' => 'conflict'],
            ],
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status'      => BulkAction::STATUS_FAILED,
            'executed_at' => now(),
            'completed_at'=> now(),
            'items'       => [
                ['id' => 'id-1', 'status' => BulkAction::ITEM_FAILED, 'reason' => 'unknown'],
                ['id' => 'id-2', 'status' => BulkAction::ITEM_FAILED, 'reason' => 'unknown'],
            ],
        ]);
    }

    public function retryOf(BulkAction $original): static
    {
        return $this->state(fn () => ['retry_of_id' => $original->id]);
    }
}
