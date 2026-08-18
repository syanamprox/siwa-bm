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
            // Add status_keluarga field if not exists
            if (!Schema::hasColumn('keluargas', 'status_keluarga')) {
                $table->enum('status_keluarga', ['Aktif', 'Pindah', 'Non-Aktif', 'Dibubarkan'])->default('Aktif')->after('alamat_kk');
            }

            // Add tanggal_status field if not exists
            if (!Schema::hasColumn('keluargas', 'tanggal_status')) {
                $table->date('tanggal_status')->nullable()->after('keterangan_status');
            }

            // Add index for performance
            $table->index('status_keluarga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keluargas', function (Blueprint $table) {
            if (Schema::hasColumn('keluargas', 'tanggal_status')) {
                $table->dropColumn('tanggal_status');
            }

            if (Schema::hasColumn('keluargas', 'status_keluarga')) {
                $table->dropColumn('status_keluarga');
            }

            $table->dropIndex(['status_keluarga']);
        });
    }
};
