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
        Schema::table('keluargas', function (Blueprint $table) {
            $table->foreign('kepala_keluarga_id')->references('id')->on('wargas')->onDelete('set null');
        });

        Schema::table('wargas', function (Blueprint $table) {
            $table->foreign('kk_id')->references('id')->on('keluargas')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('iurans', function (Blueprint $table) {
            $table->foreign('keluarga_id')->references('id')->on('keluargas')->onDelete('cascade');
            $table->foreign('jenis_iuran_id')->references('id')->on('jenis_iurans')->onDelete('cascade');
        });

        Schema::table('pembayaran_iurans', function (Blueprint $table) {
            $table->foreign('iuran_id')->references('id')->on('iurans')->onDelete('cascade');
            $table->foreign('warga_id')->references('id')->on('wargas')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluargas', function (Blueprint $table) {
            $table->dropForeign(['kepala_keluarga_id']);
        });

        Schema::table('wargas', function (Blueprint $table) {
            $table->dropForeign(['kk_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::table('iurans', function (Blueprint $table) {
            $table->dropForeign(['keluarga_id']);
            $table->dropForeign(['jenis_iuran_id']);
        });

        Schema::table('pembayaran_iurans', function (Blueprint $table) {
            $table->dropForeign(['iuran_id']);
            $table->dropForeign(['warga_id']);
            $table->dropForeign(['created_by']);
        });
    }
};