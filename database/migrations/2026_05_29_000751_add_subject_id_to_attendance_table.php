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
        Schema::table('attendance', function (Blueprint $table) {
            // subject_id column already exists from initial migration
            // Only add FK constraint + index if not present
            if (!collect(\DB::select("SHOW INDEX FROM attendance WHERE Key_name = 'attendance_subject_id_foreign'"))->count()) {
                $table->foreign('subject_id')
                      ->references('id')->on('subjects')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeignIfExists(['subject_id']);
        });
    }
};
