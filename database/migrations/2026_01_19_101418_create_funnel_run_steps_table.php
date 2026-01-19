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
        Schema::create('funnel_run_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('funnel_step_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, success, failed
            $table->timestamp('executed_at')->nullable();
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['funnel_run_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funnel_run_steps');
    }
};
