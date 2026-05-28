<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('cnic', 15)->nullable()->unique();
            $table->enum('department', [
                'administrative',
                'finance',
                'academic_support',
                'support',
            ])->default('administrative');
            $table->string('designation', 100)->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('photo')->nullable();
            $table->boolean('status')->default(true);
            // Nullable user_id — only Finance/Library staff get portal (Phase 4/5)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_seeded')->default(false); // for demo system
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
