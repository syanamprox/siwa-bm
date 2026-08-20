<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Status keluarga baru: Tetap / Domisili / Non Domisili / Pendatang (berbasis domisili faktual).
 * Semua status tetap ditagih iuran; arsip = soft delete.
 * Kolom status_domisili_keluarga dipensiunkan (dijadikan nullable, tidak dipakai lagi).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Kolom lama ENUM(Aktif,Pindah,Non-Aktif,Dibubarkan) → VARCHAR, validasi pindah ke layer aplikasi
        DB::statement("ALTER TABLE keluargas MODIFY status_keluarga VARCHAR(50) NOT NULL DEFAULT 'Tetap'");

        // Migrasi nilai lama → baru
        DB::table('keluargas')->where('status_keluarga', 'Aktif')->update(['status_keluarga' => 'Tetap']);
        DB::table('keluargas')->where('status_keluarga', 'Non-Aktif')->update(['status_keluarga' => 'Pendatang']);
        DB::table('keluargas')->where('status_keluarga', 'Pindah')->update(['status_keluarga' => 'Non Domisili']);
        DB::table('keluargas')->where('status_keluarga', 'Dibubarkan')->update(['status_keluarga' => 'Tetap']);

        Schema::table('keluargas', function (Blueprint $table) {
            $table->string('status_domisili_keluarga')->nullable()->default(null)->change();
        });
        // Field pensiun — null-kan semua
        DB::table('keluargas')->update(['status_domisili_keluarga' => null]);
    }

    public function down(): void
    {
        DB::table('keluargas')->where('status_keluarga', 'Tetap')->update(['status_keluarga' => 'Aktif']);
        DB::table('keluargas')->where('status_keluarga', 'Pendatang')->update(['status_keluarga' => 'Non-Aktif']);
        DB::table('keluargas')->where('status_keluarga', 'Non Domisili')->update(['status_keluarga' => 'Pindah']);
        DB::table('keluargas')->whereNull('status_domisili_keluarga')->update(['status_domisili_keluarga' => 'Tetap']);

        DB::statement("ALTER TABLE keluargas MODIFY status_keluarga ENUM('Aktif','Pindah','Non-Aktif','Dibubarkan') NOT NULL");
        Schema::table('keluargas', function (Blueprint $table) {
            $table->string('status_domisili_keluarga')->nullable(false)->default('Tetap')->change();
        });
    }
};
