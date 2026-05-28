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
        Schema::table('classes', function (Blueprint $table) {
            // Attendance mode: class_incharge = one teacher marks whole class
            //                  subject_wise   = each subject teacher marks their subject
            $table->enum('attendance_mode', ['class_incharge', 'subject_wise'])
                  ->default('class_incharge')
                  ->after('section');

            // Incharge teacher (used when mode = class_incharge)
            $table->unsignedBigInteger('incharge_teacher_id')
                  ->nullable()
                  ->after('attendance_mode');

            $table->foreign('incharge_teacher_id')
                  ->references('id')->on('teachers')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['incharge_teacher_id']);
            $table->dropColumn(['attendance_mode', 'incharge_teacher_id']);
        });
    }
};
