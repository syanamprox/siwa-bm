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
        if (!Schema::hasTable('jenis_iurans')) {
            Schema::create('jenis_iurans', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('kode', 10)->unique();
                $table->decimal('jumlah', 12, 2);
                $table->enum('periode', ['bulanan', 'tahunan', 'sekali']);
                $table->text('keterangan')->nullable();
                $table->boolean('is_aktif')->default(true);
                $table->enum('sasaran', ['semua', 'kk', 'warga'])->default('kk');
                $table->string('kelurahan_target')->nullable(); // Opsional: filter berdasarkan kelurahan
                $table->timestamps();

                // Indexes
                $table->index('kode');
                $table->index('is_aktif');
                $table->index('periode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_iurans');
    }
};
