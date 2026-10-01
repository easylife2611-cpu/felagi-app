<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AM (audit L276) — Bulk action safety.
 *
 * Persists the frozen selection digest and per-item execution results
 * so retry can reuse the original idempotency keys and the audit trail
 * is complete.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('actor_id')->constrained('users')->restrictOnDelete();
            $table->string('action_type', 80);
            $table->string('entity_type', 60);
            $table->string('scope', 120);
            $table->json('selection_ids');
            $table->char('selection_digest', 64);
            $table->unsignedInteger('expected_count');
            $table->string('status', 40)->default('PREVIEWED');
            $table->json('items')->nullable();
            $table->uuid('retry_of_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->index(['actor_id', 'created_at']);
            $table->index('status');
            $table->index('action_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_actions');
    }
};
