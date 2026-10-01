<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting_drafts', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('impact_preview');
            $table->string('scheduled_status', 20)->default('NONE')->after('scheduled_at');
            $table->timestamp('scheduled_processed_at')->nullable()->after('scheduled_status');
            $table->index(['scheduled_status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::table('setting_drafts', function (Blueprint $table) {
            $table->dropIndex(['scheduled_status', 'scheduled_at']);
            $table->dropColumn(['scheduled_at', 'scheduled_status', 'scheduled_processed_at']);
        });
    }
};
