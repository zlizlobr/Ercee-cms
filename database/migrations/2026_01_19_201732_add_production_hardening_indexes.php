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
        if (Schema::hasTable('contracts') && ! Schema::hasColumn('contracts', 'idempotency_key')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->string('idempotency_key')->nullable()->unique()->after('status');
            });
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'idempotency_key')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('idempotency_key')->nullable()->unique()->after('status');
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropIndex(['transaction_id']);
                $table->unique('transaction_id');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('contracts') && Schema::hasColumn('contracts', 'idempotency_key')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropUnique(['idempotency_key']);
                $table->dropColumn('idempotency_key');
            });
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'idempotency_key')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropUnique(['idempotency_key']);
                $table->dropColumn('idempotency_key');
            });
        }

        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropUnique(['transaction_id']);
                $table->index('transaction_id');
            });
        }

    }
};
