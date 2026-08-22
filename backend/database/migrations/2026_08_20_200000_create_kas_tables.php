<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modul KAS — laporan keuangan per unit kas.
 * Unit wilayah (rt/rw/kelurahan) auto-dapat dari KasUnit::forWilayah saat pembayaran iuran masuk;
 * unit kecamatan dibuat manual (wilayah_id null — kecamatan tidak ada di tabel wilayahs);
 * unit organisasi (musholla/karang taruna/posyandu) menempel ke wilayah RT/RW/Kelurahan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_units', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->enum('jenis', ['rt', 'rw', 'kelurahan', 'kecamatan', 'organisasi']);
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayahs')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Unit wilayah tidak dobel (jenis + wilayah, nama selalu = nama wilayah);
            // organisasi boleh LEBIH DARI SATU per wilayah asal nama beda;
            // NULL wilayah (kecamatan) bebas dobel di MySQL
            $table->unique(['jenis', 'wilayah_id', 'nama']);
        });

        Schema::create('kas_transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_unit_id')->constrained('kas_units')->cascadeOnDelete();
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->enum('sumber', ['iuran', 'manual'])->default('manual');
            // Idempotent auto-post: satu pembayaran maksimal satu baris kas
            $table->foreignId('pembayaran_iuran_id')->nullable()->constrained('pembayaran_iurans')->cascadeOnDelete()->unique();
            $table->decimal('jumlah', 14, 2);
            $table->string('kategori', 50);
            $table->string('keterangan', 255)->nullable();
            $table->date('tanggal');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kas_unit_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_transaksis');
        Schema::dropIfExists('kas_units');
    }
};
