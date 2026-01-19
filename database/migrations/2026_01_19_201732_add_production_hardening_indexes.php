<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add idempotency_key to contracts table
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('idempotency_key')->nullable()->unique()->after('status');
        });

        // Add idempotency_key to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('idempotency_key')->nullable()->unique()->after('status');
        });

        // Add unique index to payments.transaction_id
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
            $table->unique('transaction_id');
        });

        // Add unique index on funnel_run_steps (funnel_run_id, funnel_step_id)
        Schema::table('funnel_run_steps', function (Blueprint $table) {
            $table->unique(['funnel_run_id', 'funnel_step_id'], 'funnel_run_steps_unique');
        });

        // Add unique index on funnel_runs for running status (subscriber + funnel)
        Schema::table('funnel_runs', function (Blueprint $table) {
            $table->index(['subscriber_id', 'funnel_id', 'status'], 'funnel_runs_active_check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn('idempotency_key');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropColumn('idempotency_key');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['transaction_id']);
            $table->index('transaction_id');
        });

        Schema::table('funnel_run_steps', function (Blueprint $table) {
            $table->dropUnique('funnel_run_steps_unique');
        });

        Schema::table('funnel_runs', function (Blueprint $table) {
            $table->dropIndex('funnel_runs_active_check');
        });
    }
};
