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

        if (
            Schema::hasColumn('forms', 'submit_button_text') ||
            Schema::hasColumn('forms', 'success_title') ||
            Schema::hasColumn('forms', 'success_message')
        ) {
            $rows = DB::table('forms')
                ->select('id', 'data_options', 'submit_button_text', 'success_title', 'success_message')
                ->get();

            foreach ($rows as $row) {
                $options = [];
                if (! empty($row->data_options)) {
                    $decoded = json_decode($row->data_options, true);
                    if (is_array($decoded)) {
                        $options = $decoded;
                    }
                }

                if (Schema::hasColumn('forms', 'submit_button_text') && $row->submit_button_text !== null) {
                    $options['submit_button_text'] = $row->submit_button_text;
                }
                if (Schema::hasColumn('forms', 'success_title') && $row->success_title !== null) {
                    $options['success_title'] = $row->success_title;
                }
                if (Schema::hasColumn('forms', 'success_message') && $row->success_message !== null) {
                    $options['success_message'] = $row->success_message;
                }

                if (! empty($options)) {
                    DB::table('forms')
                        ->where('id', $row->id)
                        ->update(['data_options' => json_encode($options)]);
                }
            }

            Schema::table('forms', function (Blueprint $table) {
                if (Schema::hasColumn('forms', 'submit_button_text')) {
                    $table->dropColumn('submit_button_text');
                }
                if (Schema::hasColumn('forms', 'success_title')) {
                    $table->dropColumn('success_title');
                }
                if (Schema::hasColumn('forms', 'success_message')) {
                    $table->dropColumn('success_message');
                }
            });
        }
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

        Schema::table('forms', function (Blueprint $table) {
            if (Schema::hasColumn('forms', 'data_options')) {
                $table->dropColumn('data_options');
            }
        });
    }
};
