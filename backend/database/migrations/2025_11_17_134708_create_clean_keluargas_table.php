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
        Schema::create('keluargas', function (Blueprint $table) {
            $table->id();

            // Data Kartu Keluarga
            $table->string('no_kk', 16)->unique();
            $table->foreignId('kepala_keluarga_id')->nullable()->constrained('wargas')->onDelete('set null');

            // Data Alamat KTP (Input Manual Lengkap)
            $table->text('alamat_kk'); // Alamat lengkap sesuai KK (jalan sampai negara)
            $table->string('rt_kk', 10)->nullable(); // RT sesuai KK (manual input)
            $table->string('rw_kk', 10)->nullable(); // RW sesuai KK (manual input)
            $table->string('kelurahan_kk', 100)->nullable(); // Kelurahan sesuai KK (manual input)
            $table->string('kecamatan_kk', 100)->nullable(); // Kecamatan sesuai KK (manual input)
            $table->string('kabupaten_kk', 100)->nullable(); // Kabupaten sesuai KK (manual input)
            $table->string('provinsi_kk', 100)->nullable(); // Provinsi sesuai KK (manual input)

            // Data Alamat Domisili (Koneksi Sistem Wilayah)
            $table->string('alamat_domisili')->nullable(); // Alamat domisili (jalan saja)

            // Linking ke Wilayah untuk Domisili (Foreign Key Strategy)
            $table->foreignId('rt_id')->nullable()->constrained('wilayahs')->onDelete('set null');
            // Note: rt, rw, kelurahan, kecamatan, kabupaten, provinsi domisili di-load dynamically via rt_id relationship

            // Status Domisili
            $table->enum('status_domisili_keluarga', ['Tetap', 'Non Domisili', 'Luar', 'Sementara'])->default('Tetap');
            $table->date('tanggal_mulai_domisili_keluarga')->nullable();
            $table->text('keterangan_status')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('no_kk');
            $table->index('kepala_keluarga_id');
            $table->index('rt_id');

            // Indexes untuk Alamat KTP (Manual)
            $table->index(['rt_kk', 'rw_kk']);
            $table->index('kelurahan_kk');

            // Note: Indexes untuk alamat domisili tidak diperlukan karena di-load dynamically via rt_id

            $table->index('status_domisili_keluarga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluargas');
    }
};