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
        Schema::create('warga', function (Blueprint $table) {
            $table->id();

            // Data KTP (Data Tetap)
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap', 100);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->text('alamat_ktp');
            $table->string('rt_ktp', 3);
            $table->string('rw_ktp', 3);
            $table->string('kelurahan_ktp', 50);
            $table->string('kecamatan_ktp', 50);
            $table->string('kabupaten_ktp', 50);
            $table->string('provinsi_ktp', 50);
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('pekerjaan', 50);
            $table->enum('kewarganegaraan', ['WNI', 'WNA']);
            $table->enum('pendidikan_terakhir', ['Tidak/Sekolah', 'SD', 'SMP', 'SMA', 'D1/D2/D3', 'S1', 'S2', 'S3']);
            $table->string('foto_ktp')->nullable();

            // Data Domisili (Data Dinamis)
            $table->unsignedBigInteger('kk_id')->nullable();
            $table->string('hubungan_keluarga', 25)->nullable();
            $table->text('alamat_domisili');
            $table->string('rt_domisili', 3);
            $table->string('rw_domisili', 3);
            $table->string('kelurahan_domisili', 50);
            $table->string('no_telepon', 15)->nullable();
            $table->string('email', 50)->nullable();
            $table->enum('status_domisili', ['Tetap', 'Kontrak', 'Ngontrak']);
            $table->date('tanggal_mulai_domisili');

            // Tracking fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys dan indexes
            // Foreign key akan ditambahkan setelah tabel keluarga dibuat
            // $table->foreign('kk_id')->references('id')->on('keluarga')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes untuk performance
            $table->index(['nik', 'nama_lengkap']);
            $table->index(['rt_domisili', 'rw_domisili']);
            $table->index(['jenis_kelamin', 'agama', 'pekerjaan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warga');
    }
};
