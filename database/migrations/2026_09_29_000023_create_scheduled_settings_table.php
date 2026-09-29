<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('setting_key', 120);
            $table->json('planned_value_json');
            $table->timestamp('activate_at');
            $table->timestamp('expire_at')->nullable();
            $table->foreignUuid('approved_by')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['SCHEDULED', 'ACTIVE', 'EXPIRED', 'CANCELLED'])->default('SCHEDULED');
            $table->unsignedInteger('base_version');
            $table->uuid('audit_reference')->nullable();
            $table->timestamps();

            $table->index(['status', 'activate_at']);
            $table->index('setting_key');

            $table->foreign('setting_key')
                  ->references('key')
                  ->on('settings')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_settings');
    }
};
