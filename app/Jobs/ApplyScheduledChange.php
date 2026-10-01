<?php

namespace App\Jobs;

use App\Models\SettingDraft;
use App\Models\User;
use App\Services\Admin\AdminChangeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * AG (audit L276) — Apply a scheduled setting change.
 *
 * Invoked by `settings:apply-scheduled` when a draft's scheduled_at
 * is due. Runs publish() in system context (no actor), which is the
 * audited server-side application path — never a browser assertion.
 */
class ApplyScheduledChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public readonly string $draftId,
    ) {}

    public function handle(AdminChangeService $service): void
    {
        $draft = SettingDraft::find($this->draftId);
        if (! $draft) {
            Log::warning('ApplyScheduledChange: draft not found', [
                'draft_id' => $this->draftId,
            ]);
            return;
        }

        if (! $draft->isDue()) {
            Log::info('ApplyScheduledChange: not due', [
                'draft_id' => $this->draftId,
                'scheduled_at' => optional($draft->scheduled_at)->toIso8601String(),
            ]);
            return;
        }

        // Publish uses system context (no actor). The draft's proposer
        // remains recorded on the immutable version row via source_draft_id.
        $systemActor = $this->systemActor();

        try {
            $service->publish(
                actor: $systemActor,
                draft: $draft,
                reason: 'Scheduled application of draft ' . $draft->id,
                expectedVersion: (int) ($draft->setting->version_number ?? 1),
            );

            $draft->scheduled_status       = SettingDraft::SCHEDULE_APPLIED;
            $draft->scheduled_processed_at = now();
            $draft->save();
        } catch (\Throwable $e) {
            $draft->scheduled_status       = SettingDraft::SCHEDULE_FAILED;
            $draft->scheduled_processed_at = now();
            $draft->save();

            Log::error('ApplyScheduledChange failed', [
                'draft_id' => $this->draftId,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function systemActor(): User
    {
        // A dedicated service user ensures the audit trail is complete.
        // If none is configured, fall back to the draft's proposer.
        $draft = SettingDraft::find($this->draftId);
        if ($draft && $draft->proposed_by) {
            $proposer = User::find($draft->proposed_by);
            if ($proposer) {
                return $proposer;
            }
        }
        throw new \RuntimeException('No actor available for scheduled application.');
    }
}
