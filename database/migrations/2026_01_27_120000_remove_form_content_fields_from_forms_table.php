<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (! Schema::hasColumn('forms', 'data_options')) {
                $table->json('data_options')->nullable();
            }
        });

        $legacyColumns = [
            'submit_button_text',
            'success_title',
            'success_message',
        ];

        $existingLegacyColumns = array_values(array_filter(
            $legacyColumns,
            fn (string $column): bool => Schema::hasColumn('forms', $column)
        ));

        if ($existingLegacyColumns === []) {
            return;
        }

        $columns = array_merge(['id', 'data_options'], $existingLegacyColumns);
        $rows = DB::table('forms')->select($columns)->get();

        foreach ($rows as $row) {
            $options = [];
            if (! empty($row->data_options)) {
                $decoded = json_decode($row->data_options, true);
                if (is_array($decoded)) {
                    $options = $decoded;
                }
            }

            foreach ($existingLegacyColumns as $column) {
                $value = $row->{$column} ?? null;
                if ($value !== null && $value !== '') {
                    $options[$column] = $value;
                }
            }

            DB::table('forms')
                ->where('id', $row->id)
                ->update(['data_options' => empty($options) ? null : json_encode($options)]);
        }

        Schema::table('forms', function (Blueprint $table) use ($existingLegacyColumns) {
            foreach ($existingLegacyColumns as $column) {
                $table->dropColumn($column);
            }
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            if (! Schema::hasColumn('forms', 'submit_button_text')) {
                $table->string('submit_button_text')->nullable();
            }
            if (! Schema::hasColumn('forms', 'success_title')) {
                $table->string('success_title')->nullable();
            }
            if (! Schema::hasColumn('forms', 'success_message')) {
                $table->text('success_message')->nullable();
            }
        });

        if (! Schema::hasColumn('forms', 'data_options')) {
            return;
        }

        $rows = DB::table('forms')
            ->select('id', 'data_options')
            ->get();

        foreach ($rows as $row) {
            if (empty($row->data_options)) {
                continue;
            }

            $options = json_decode($row->data_options, true);
            if (! is_array($options)) {
                continue;
            }

            DB::table('forms')
                ->where('id', $row->id)
                ->update([
                    'submit_button_text' => $options['submit_button_text'] ?? null,
                    'success_title' => $options['success_title'] ?? null,
                    'success_message' => $options['success_message'] ?? null,
                ]);
        }
    }
};
