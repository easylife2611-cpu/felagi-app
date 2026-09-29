<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('need_id')->constrained('needs')->restrictOnDelete();
            $table->foreignUuid('provider_id')->constrained('users')->restrictOnDelete();
            $table->decimal('offered_price', 12, 2);
            $table->char('currency', 3)->default('ETB');
            $table->text('proposal_message');
            $table->string('delivery_time_text', 255)->nullable();
            $table->string('availability_text', 255)->nullable();
            $table->text('additional_notes')->nullable();
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED', 'WITHDRAWN'])->default('PENDING');
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->unique(['need_id', 'provider_id']);
            $table->index(['need_id', 'status', 'created_at']);
            $table->index(['provider_id', 'status']);
        });

        // Composite unique (id, need_id) for need_awards FK
        DB::statement('ALTER TABLE offers ADD UNIQUE offers_id_need_id_unique (id, need_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
