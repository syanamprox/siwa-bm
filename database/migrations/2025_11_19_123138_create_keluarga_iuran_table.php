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
        Schema::create('keluarga_iuran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keluarga_id')->constrained('keluargas')->onDelete('cascade');
            $table->foreignId('jenis_iuran_id')->constrained('jenis_iurans')->onDelete('cascade');
            $table->decimal('nominal_custom', 10, 2)->nullable()->comment('Custom amount, NULL = use default');
            $table->boolean('status_aktif')->default(true)->comment('Include in future generation');
            $table->text('alasan_custom')->nullable()->comment('Reason for custom amount');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint
            $table->unique(['keluarga_id', 'jenis_iuran_id'], 'unique_keluarga_jenis');

            // Indexes
            $table->index('keluarga_id');
            $table->index('status_aktif');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluarga_iuran');
    }
};
