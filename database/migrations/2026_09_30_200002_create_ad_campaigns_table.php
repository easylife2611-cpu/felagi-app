<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('advertiser_id')->constrained('advertisers')->restrictOnDelete();
            $table->string('campaign_name', 120);
            $table->string('status', 20)->default('DRAFT');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('timezone', 64)->default('Africa/Addis_Ababa');
            $table->foreignUuid('creative_id')->nullable()->constrained('ad_creatives')->nullOnDelete();
            $table->json('placement_ids');
            $table->json('targeting_policy')->nullable();
            $table->json('frequency_policy')->nullable();
            $table->integer('priority')->default(0);
            $table->string('commercial_reference', 255)->nullable();
            $table->string('destination_type', 20)->default('INTERNAL');
            $table->text('destination_value');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('published_at')->nullable();
            $table->dateTime('paused_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->index('status');
            $table->index(['start_at', 'end_at']);
            $table->index('advertiser_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_campaigns');
    }
};
