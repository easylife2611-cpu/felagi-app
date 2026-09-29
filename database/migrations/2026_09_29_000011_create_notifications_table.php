<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('recipient_user_id')->constrained('users')->restrictOnDelete();
            $table->string('type', 50);
            $table->string('entity_type', 40);
            $table->uuid('entity_id');
            $table->uuid('event_id')->nullable(); // FK to outbox_events added later
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->enum('channel', ['IN_APP', 'TELEGRAM']);
            $table->enum('delivery_status', ['PENDING', 'SENT', 'FAILED', 'SKIPPED'])->default('PENDING');
            $table->timestamp('available_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('failure_code', 80)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['event_id', 'recipient_user_id', 'channel'], 'notifications_event_recipient_channel_unique');
            $table->index(['recipient_user_id', 'created_at']);
            $table->index(['delivery_status', 'available_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
