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
        Schema::create('aktivitas_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('tabel_referensi', 50); // nama tabel yang diubah
            $table->unsignedBigInteger('id_referensi'); // id record yang diubah
            $table->string('jenis_aktivitas', 20); // create, update, delete, login, logout
            $table->string('deskripsi', 255);
            $table->json('data_lama')->nullable(); // data sebelum perubahan
            $table->json('data_baru')->nullable(); // data setelah perubahan
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['user_id', 'tabel_referensi', 'jenis_aktivitas']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_log');
    }
};
