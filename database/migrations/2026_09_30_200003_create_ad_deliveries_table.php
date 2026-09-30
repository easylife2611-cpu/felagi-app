<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id')->constrained('ad_campaigns')->cascadeOnDelete();
            $table->integer('campaign_version');
            $table->string('placement_id', 64);
            $table->string('slot_id', 64)->nullable();
            $table->integer('creative_version')->nullable();
            $table->string('policy_version', 32)->nullable();
            $table->string('session_ref', 64)->nullable();
            $table->dateTime('eligible_at');
            $table->dateTime('expires_at');
            $table->timestamps();
            $table->index('campaign_id');
            $table->index('placement_id');
            $table->index('session_ref');
            $table->index('eligible_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_deliveries');
    }
};
