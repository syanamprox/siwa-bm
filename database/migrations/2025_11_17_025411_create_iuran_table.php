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
        Schema::create('iuran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warga_id');
            $table->unsignedBigInteger('kk_id')->nullable();
            $table->unsignedBigInteger('jenis_iuran_id');
            $table->string('rt_id', 3);
            $table->string('rw_id', 3);
            $table->decimal('nominal', 10, 2);
            $table->string('periode_bulan', 7); // Format: YYYY-MM
            $table->enum('status', ['belum_bayar', 'tertunda', 'lunas', 'batal'])->default('belum_bayar');
            $table->date('jatuh_tempo');
            $table->decimal('denda_terlambatan', 10, 2)->default(0);
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('warga_id')->references('id')->on('warga')->onDelete('cascade');
            $table->foreign('kk_id')->references('id')->on('keluarga')->onDelete('set null');
            $table->foreign('jenis_iuran_id')->references('id')->on('jenis_iuran')->onDelete('cascade');

            $table->index(['warga_id', 'jenis_iuran_id', 'periode_bulan']);
            $table->index(['status', 'jatuh_tempo']);
            $table->index(['rt_id', 'rw_id', 'periode_bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iuran');
    }
};
