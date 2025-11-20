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
        Schema::create('iurans', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('kk_id')->comment('Reference to keluargas table');
            $table->unsignedBigInteger('jenis_iuran_id')->comment('Reference to jenis_iurans table');
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who created this record');

            // Billing details
            $table->string('periode_bulan', 7)->comment('Billing period in YYYY-MM format');
            $table->decimal('nominal', 15, 2)->default(0)->comment('Billing amount');
            $table->decimal('denda_terlambatan', 10, 2)->default(0)->comment('Late payment penalty');

            // Status and dates
            $table->enum('status', ['belum_bayar', 'sebagian', 'lunas', 'batal'])->default('belum_bayar');
            $table->date('jatuh_tempo')->nullable()->comment('Due date for payment');
            $table->timestamp('reminder_sent_at')->nullable()->comment('When last reminder was sent');

            // Additional information
            $table->text('keterangan')->nullable()->comment('Additional notes');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('kk_id')->references('id')->on('keluargas')->onDelete('cascade');
            $table->foreign('jenis_iuran_id')->references('id')->on('jenis_iurans')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for performance
            $table->index('periode_bulan');
            $table->index(['kk_id', 'periode_bulan']);
            $table->index('status');
            $table->index('jatuh_tempo');

            // Unique constraint to prevent duplicates
            $table->unique(['kk_id', 'jenis_iuran_id', 'periode_bulan'], 'unique_billing_record');
        });

        // Create pembayaran_iurans table for payment tracking
        Schema::create('pembayaran_iurans', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('iuran_id')->comment('Reference to iurans table');
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who recorded this payment');

            // Payment details
            $table->decimal('jumlah_bayar', 15, 2)->comment('Amount paid');
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'qris', 'ewallet'])->default('cash');
            $table->string('nomor_referensi')->nullable()->comment('Transaction reference number');

            // Additional information
            $table->text('keterangan')->nullable()->comment('Payment notes');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('iuran_id')->references('id')->on('iurans')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('iuran_id');
            $table->index('metode_pembayaran');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_iurans');
        Schema::dropIfExists('iurans');
    }
};
