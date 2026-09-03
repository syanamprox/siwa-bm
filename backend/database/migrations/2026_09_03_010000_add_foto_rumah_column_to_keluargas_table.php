<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Foto rumah + penghuni (verifikasi identitas keluarga oleh petugas).
     * Berbeda dengan foto_kk (statis, ter-seed): foto rumah di-upload petugas
     * lewat aplikasi (POST /keluarga/{id}/foto-rumah) → public/rumah/{no_kk}.ext,
     * dikunci middleware signature yang sama. Nullable — fresh seed aman tanpa file.
     */
    public function up(): void
    {
        Schema::table('keluargas', function (Blueprint $table) {
            $table->string('foto_rumah', 255)->nullable()->after('foto_kk');
        });
    }

    public function down(): void
    {
        Schema::table('keluargas', function (Blueprint $table) {
            $table->dropColumn('foto_rumah');
        });
    }
};
