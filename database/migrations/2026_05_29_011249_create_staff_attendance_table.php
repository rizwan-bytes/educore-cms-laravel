<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['Present', 'Absent', 'Late', 'Half_Day', 'Leave'])
                  ->default('Present');
            $table->string('remarks', 255)->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // One record per staff per day
            $table->unique(['staff_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendance');
    }
};
