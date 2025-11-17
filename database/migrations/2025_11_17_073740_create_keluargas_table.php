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
            $table->string('no_kk', 16)->unique();
            $table->foreignId('kepala_keluarga_id')->nullable();

            // Data Alamat Keluarga
            $table->text('alamat_kk');
            $table->string('rt_kk', 10);
            $table->string('rw_kk', 10);
            $table->string('kelurahan_kk', 100);
            $table->string('kecamatan_kk', 100);
            $table->string('kabupaten_kk', 100);
            $table->string('provinsi_kk', 100);

            // Status Domisili Keluarga
            $table->enum('status_domisili_keluarga', ['Tetap', 'Non Domisili', 'Luar', 'Sementara'])->default('Tetap');
            $table->date('tanggal_mulai_domisili_keluarga')->nullable();
            $table->text('keterangan_status')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('no_kk');
            $table->index('kepala_keluarga_id');
            $table->index(['rt_kk', 'rw_kk']);
            $table->index('kelurahan_kk');
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
