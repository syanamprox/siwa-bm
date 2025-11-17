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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->enum('role', ['admin', 'lurah', 'rw', 'rt'])->default('rt')->after('email');
            $table->boolean('status_aktif')->default(true)->after('role');
            $table->string('foto_profile')->nullable()->after('status_aktif');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'status_aktif', 'foto_profile']);
            $table->dropSoftDeletes();
        });
    }
};
