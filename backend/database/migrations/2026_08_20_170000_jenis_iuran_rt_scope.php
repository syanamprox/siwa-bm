<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jenis iuran per-RT: rt_id NULL = global (semua RT), rt_id terisi = milik RT itu.
 * Mencegah pencampuran jenis/nominal iuran antar RT (rapat RT masing-masing).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_iurans', function (Blueprint $table) {
            $table->foreignId('rt_id')->nullable()->after('sasaran')
                ->constrained('wilayahs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jenis_iurans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rt_id');
        });
    }
};
