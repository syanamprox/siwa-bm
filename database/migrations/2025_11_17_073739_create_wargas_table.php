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
        Schema::create('wargas', function (Blueprint $table) {
            $table->id();

            // Data KTP
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap', 255);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->text('alamat_ktp');
            $table->string('rt_ktp', 10);
            $table->string('rw_ktp', 10);
            $table->string('kelurahan_ktp', 100);
            $table->string('kecamatan_ktp', 100);
            $table->string('kabupaten_ktp', 100);
            $table->string('provinsi_ktp', 100);
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']);
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('pekerjaan', 100);
            $table->enum('kewarganegaraan', ['WNI', 'WNA']);
            $table->enum('pendidikan_terakhir', ['Tidak/Sekolah', 'SD', 'SMP', 'SMA', 'D1/D2/D3', 'S1', 'S2', 'S3']);
            $table->string('foto_ktp')->nullable();

            // Data Keluarga
            $table->foreignId('kk_id')->nullable();
            $table->string('hubungan_keluarga', 50)->nullable();

            // Kontak Personal
            $table->string('no_telepon', 20)->nullable();
            $table->string('email', 255)->nullable();

            // Tracking
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('nik');
            $table->index('nama_lengkap');
            $table->index('kk_id');
            $table->index('jenis_kelamin');
            $table->index('agama');
            $table->index('status_perkawinan');
            $table->index('pekerjaan');
            $table->index('pendidikan_terakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wargas');
    }
};