<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('email');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
            $table->string('phone', 20)->nullable()->after('status');
            $table->string('avatar')->nullable()->after('phone');
            $table->timestamp('last_login')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'phone', 'avatar', 'last_login']);
        });
    }
};
