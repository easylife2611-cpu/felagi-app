<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_publication_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('publication_id')->constrained('telegram_publications')->restrictOnDelete();
            $table->string('event_type', 60);
            $table->string('request_id', 100);
            $table->integer('telegram_response_code')->nullable();
            $table->json('safe_metadata')->nullable();
            $table->timestamp('occurred_at')->useCurrent();

            $table->index(['publication_id', 'occurred_at']);
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_publication_events');
    }
};
