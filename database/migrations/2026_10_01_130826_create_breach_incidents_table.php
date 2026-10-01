<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Breach Incidents — Proclamation 1321/2024, Art. 30
 * 72-hour notification deadline to ECA + affected users
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breach_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('description');
            $table->string('severity', 20);       // P1..P4
            $table->string('status', 30)->default('detected');
            $table->json('data_categories');      // affected data types
            $table->unsignedInteger('affected_count')->default(0);
            $table->timestamp('detected_at');
            $table->timestamp('contained_at')->nullable();
            $table->timestamp('eca_notified_at')->nullable();
            $table->timestamp('users_notified_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('reported_by')->nullable();
            $table->text('remediation')->nullable();
            $table->timestamps();
            $table->index(['severity', 'status']);
            $table->index('detected_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breach_incidents');
    }
};
