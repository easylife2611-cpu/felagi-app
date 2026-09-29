<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_publications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('need_id')->constrained('needs')->restrictOnDelete();
            $table->foreignUuid('destination_id')->constrained('telegram_destinations')->restrictOnDelete();
            $table->unsignedInteger('need_publication_version');
            $table->char('content_hash', 64);
            $table->json('payload_snapshot');
            $table->enum('state', [
                'QUEUED', 'SENDING', 'POSTED', 'RETRY', 'UNCERTAIN',
                'FAILED', 'SKIPPED', 'REMOVAL_PENDING', 'REMOVED'
            ])->default('QUEUED');
            $table->bigInteger('telegram_message_id')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->string('last_error_code', 80)->nullable();
            $table->timestamps();

            $table->unique(['need_id', 'destination_id', 'need_publication_version'], 'telegram_pub_need_dest_version_unique');
            $table->index(['state', 'next_attempt_at']);
            $table->index('need_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_publications');
    }
};
