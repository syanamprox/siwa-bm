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
        Schema::table('keluarga_iuran', function (Blueprint $table) {
            $table->dropUnique('unique_keluarga_jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluarga_iuran', function (Blueprint $table) {
            $table->unique(['keluarga_id', 'jenis_iuran_id'], 'unique_keluarga_jenis');
        });
    }
};
