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
        Schema::create('keluarga', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 16)->unique();
            $table->unsignedBigInteger('kepala_keluarga_id')->nullable();
            $table->text('alamat_kk');
            $table->string('rt_kk', 3);
            $table->string('rw_kk', 3);
            $table->string('kelurahan_kk', 50);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key akan ditambahkan setelah tabel warga dibuat
            // $table->foreign('kepala_keluarga_id')->references('id')->on('warga')->onDelete('set null');
            $table->index(['no_kk', 'rt_kk', 'rw_kk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluarga');
    }
};
