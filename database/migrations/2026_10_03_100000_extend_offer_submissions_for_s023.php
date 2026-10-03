<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_submissions', function (Blueprint $table) {
            $table->string('state', 32)->default('free');
            $table->uuid('draft_id')->nullable();
            $table->unsignedInteger('draft_version')->default(1);
            $table->string('draft_hash', 64)->nullable();
            $table->string('idempotency_key', 100)->nullable();
            $table->string('policy_version', 32)->nullable();
            $table->unsignedBigInteger('amount_minor')->default(0);
            $table->char('currency', 3)->default('ETB');
            $table->uuid('offer_id')->nullable();
            $table->string('refund_reference', 255)->nullable();
        });

        if (Schema::hasColumn('offer_submissions', 'status')) {
            DB::table('offer_submissions')->where('status', 'PENDING_PAYMENT')->update(['state' => 'pending']);
            DB::table('offer_submissions')->where('status', 'UNLOCKED')->update(['state' => 'submitted']);
            DB::table('offer_submissions')->where('status', 'EXPIRED')->update(['state' => 'failed']);
        }

        Schema::table('offer_submissions', function (Blueprint $table) {
            $table->unique(['provider_id', 'idempotency_key'], 'offer_sub_provider_idem_unique');
            $table->unique('offer_id', 'offer_sub_offer_id_unique');
            $table->index('state', 'offer_sub_state_index');
        });

        Schema::table('offer_submissions', function (Blueprint $table) {
            $table->dropIndex('offer_submissions_status_index');
        });
        Schema::table('offer_submissions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('offer_submissions', function (Blueprint $table) {
            $table->string('status', 32)->default('PENDING_PAYMENT');
        });

        DB::table('offer_submissions')->where('state', 'submitted')->update(['status' => 'UNLOCKED']);
        DB::table('offer_submissions')->whereIn('state', ['failed', 'refund-pending'])->update(['status' => 'EXPIRED']);
        DB::table('offer_submissions')->whereIn('state', ['free', 'payment-required', 'pending', 'payment-verified', 'submission-recovery'])->update(['status' => 'PENDING_PAYMENT']);

        Schema::table('offer_submissions', function (Blueprint $table) {
            $table->index('status', 'offer_submissions_status_index');
        });

        Schema::table('offer_submissions', function (Blueprint $table) {
            $table->dropUnique('offer_sub_provider_idem_unique');
            $table->dropUnique('offer_sub_offer_id_unique');
            $table->dropIndex('offer_sub_state_index');
        });
        Schema::table('offer_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'state', 'draft_id', 'draft_version', 'draft_hash',
                'idempotency_key', 'policy_version', 'amount_minor',
                'currency', 'offer_id', 'refund_reference',
            ]);
        });
    }
};