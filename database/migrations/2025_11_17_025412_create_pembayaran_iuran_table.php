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
        Schema::create('pembayaran_iuran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iuran_id');
            $table->decimal('jumlah_bayar', 10, 2);
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'qris', 'ewallet']);
            $table->string('bukti_pembayaran')->nullable(); // path ke file bukti
            $table->date('tanggal_bayar');
            $table->decimal('denda_dibayar', 10, 2)->default(0);
            $table->unsignedBigInteger('petugas_id'); // user yang memproses pembayaran
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('iuran_id')->references('id')->on('iuran')->onDelete('cascade');
            $table->foreign('petugas_id')->references('id')->on('users')->onDelete('restrict');

            $table->index(['iuran_id', 'tanggal_bayar']);
            $table->index(['metode_pembayaran', 'tanggal_bayar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_iuran');
    }
};
