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
        Schema::table('wargas', function (Blueprint $table) {
            // Update the golongan_darah enum to include all valid blood types
            $table->enum('golongan_darah', [
                'A', 'B', 'AB', 'O',
                'A+', 'B+', 'AB+', 'O+',
                'A-', 'B-', 'AB-', 'O-',
                'Tidak Tahu'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            // Revert to original enum values
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->change();
        });
    }
};
