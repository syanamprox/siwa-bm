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
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O']);

            // Data Personal (tanpa alamat duplikasi)
            $table->string('agama');
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('pekerjaan');
            $table->string('kewarganegaraan')->default('WNI');
            $table->string('pendidikan_terakhir');
            $table->string('foto_ktp')->nullable();

            // Relasi Keluarga (delayed constraint)
            $table->foreignId('kk_id')->nullable();
            // $table->foreignId('kk_id')->nullable()->constrained('keluargas')->onDelete('set null');
            $table->enum('hubungan_keluarga', ['Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu', 'Lainnya']);

            // Kontak
            $table->string('no_telepon', 15)->nullable();
            $table->string('email')->nullable();

            // Audit & Timestamps
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('nik');
            $table->index('nama_lengkap');
            $table->index('kk_id');
            $table->index('jenis_kelamin');
            $table->index('status_perkawinan');
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