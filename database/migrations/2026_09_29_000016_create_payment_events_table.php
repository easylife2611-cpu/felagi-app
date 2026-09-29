<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')->nullable()->constrained('payments')->restrictOnDelete();
            $table->string('provider', 50);
            $table->string('provider_event_id', 255);
            $table->char('payload_digest', 64);
            $table->timestamp('received_at')->useCurrent();
            $table->boolean('signature_valid');
            $table->string('event_type', 80);
            $table->enum('processing_status', ['RECEIVED', 'APPLIED', 'DUPLICATE', 'REVIEW_REQUIRED', 'REJECTED'])->default('RECEIVED');
            $table->json('sanitized_metadata')->nullable();

            $table->unique(['provider', 'provider_event_id']);
            $table->index(['processing_status', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_events');
    }
};
