<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_destinations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('telegram_chat_id')->unique();
            $table->string('public_username', 120)->nullable();
            $table->enum('type', ['OWNED_CHANNEL', 'PARTNER_CHANNEL', 'PARTNER_SUPERGROUP']);
            $table->string('name');
            $table->json('allowed_category_ids')->nullable();
            $table->text('permission_evidence');
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('bot_permission_checked_at')->nullable();
            $table->enum('status', ['DRAFT', 'ACTIVE', 'PAUSED', 'REVOKED'])->default('DRAFT');
            $table->unsignedInteger('daily_cap')->default(10);
            $table->json('quiet_hours')->nullable();
            $table->foreignUuid('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_destinations');
    }
};
