<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('actor_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('action', 100);
            $table->string('entity_type', 60);
            $table->uuid('entity_id')->nullable();
            $table->string('request_id', 100);
            $table->text('reason')->nullable();
            $table->char('before_digest', 64)->nullable();
            $table->char('after_digest', 64)->nullable();
            $table->json('safe_metadata')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->char('prev_hash', 64)->nullable();
            $table->char('hash', 64)->nullable();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['actor_id', 'occurred_at']);
            $table->index('action');
            $table->index('request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
