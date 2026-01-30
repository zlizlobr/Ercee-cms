<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->dropIndex('navigations_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->unique('classes');
        });
    }
};
