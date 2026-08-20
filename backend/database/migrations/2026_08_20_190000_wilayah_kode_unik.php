<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kode wilayah kini globally-unique: prefix kode kelurahan di RW & RT.
 * Contoh: RT 02 RW 03 Bendul Merisi = 0302 → BM0302 · RW 03 = 03 → BM03.
 * Ditambah unique index agar duplikasi tidak mungkin tersimpan lagi.
 */
return new class extends Migration
{
    public function up(): void
    {
        // RW: {kodeKelurahan}{kodeRW}
        DB::statement("UPDATE wilayahs rw
            JOIN wilayahs kel ON kel.id = rw.parent_id AND kel.tingkat = 'Kelurahan'
            SET rw.kode = CONCAT(kel.kode, rw.kode)
            WHERE rw.tingkat = 'RW'");

        // RT: {kodeKelurahan}{kodeRWTerpakaiSekarangSudahBerprefix}{nomorRT}
        // RT lama = {rwLama}{rtNomor} (4 digit, mis. 0302) → ganti prefix rw baru
        DB::statement("UPDATE wilayahs rt
            JOIN wilayahs rw ON rw.id = rt.parent_id AND rw.tingkat = 'RW'
            JOIN wilayahs kel ON kel.id = rw.parent_id AND kel.tingkat = 'Kelurahan'
            SET rt.kode = CONCAT(kel.kode, rt.kode)
            WHERE rt.tingkat = 'RT'");

        // Bersihkan sisa kode RT yang belum terprefix (pengaman — pola lama 4 digit angka)
        DB::statement("UPDATE wilayahs rt
            JOIN wilayahs rw ON rw.id = rt.parent_id
            SET rt.kode = CONCAT(rw.kode, SUBSTRING(rt.kode, 3))
            WHERE rt.tingkat = 'RT' AND rt.kode REGEXP '^[0-9]{4}$'");

        Schema::table('wilayahs', function ($table) {
            $table->unique('kode');
        });
    }

    public function down(): void
    {
        Schema::table('wilayahs', function ($table) {
            $table->dropUnique(['kode']);
        });
        // Kembalikan kode relative (buang 2 karakter prefix kelurahan) utk RW & RT
        DB::statement("UPDATE wilayahs SET kode = SUBSTRING(kode, 3) WHERE tingkat IN ('RW','RT')");
    }
};
