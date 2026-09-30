<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Need;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * GAP-71b — Attachment factory infrastructure.
 *
 * Schema: attachments (2026_09_29_000017)
 * NOTE: SoftDeletes, no timestamps (created_at auto at DB level).
 */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'uploaded_by'   => User::factory(),
            'need_id'       => null,
            'offer_id'      => null,
            'message_id'    => null,
            'purpose'       => Attachment::PURPOSE_PROFILE,
            'storage_disk'  => 'local',
            'storage_key'   => 'attachments/' . Str::uuid() . '.pdf',
            'original_name' => 'document.pdf',
            'detected_mime' => 'application/pdf',
            'byte_size'     => 12345,
            'sha256'        => hash('sha256', 'test-content-' . Str::random(16)),
            'visibility'    => Attachment::VISIBILITY_PRIVATE,
            'scan_status'   => Attachment::SCAN_PENDING,
        ];
    }

    public function clean(): static
    {
        return $this->state(fn () => ['scan_status' => Attachment::SCAN_CLEAN]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['scan_status' => Attachment::SCAN_REJECTED]);
    }

    public function publicVisibility(): static
    {
        return $this->state(fn () => ['visibility' => Attachment::VISIBILITY_PUBLIC]);
    }

    public function forNeed(Need $need): static
    {
        return $this->state(fn () => [
            'need_id' => $need->id,
            'purpose' => Attachment::PURPOSE_NEED,
        ]);
    }
}
