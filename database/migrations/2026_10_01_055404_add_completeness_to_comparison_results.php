<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comparison_results', function (Blueprint $table) {
            $table->string('completeness', 20)->default('COMPLETE')->after('fit_explanation');
            $table->json('missing_criteria')->nullable()->after('completeness');
            $table->json('uncertain_criteria')->nullable()->after('missing_criteria');
        });
    }

    public function down(): void
    {
        Schema::table('comparison_results', function (Blueprint $table) {
            $table->dropColumn(['completeness', 'missing_criteria', 'uncertain_criteria']);
        });
    }
};
