<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Status iuran disederhanakan: hanya belum_bayar / lunas.
 * Tidak ada bayar sebagian — pembayaran selalu nominal penuh.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Tagihan 'sebagian' (data dummy): hapus pembayaran parsialnya, kembali belum_bayar
        $sebagianIds = DB::table('iurans')->where('status', 'sebagian')->pluck('id');
        if ($sebagianIds->isNotEmpty()) {
            DB::table('pembayaran_iurans')->whereIn('iuran_id', $sebagianIds)->delete();
            DB::table('iurans')->whereIn('id', $sebagianIds)->update(['status' => 'belum_bayar']);
        }
        // 'batal' tidak dipakai lagi → belum_bayar (tagihan batal dihapus saja di UI)
        DB::table('iurans')->where('status', 'batal')->update(['status' => 'belum_bayar']);

        DB::statement("ALTER TABLE iurans MODIFY status ENUM('belum_bayar','lunas') NOT NULL DEFAULT 'belum_bayar'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE iurans MODIFY status ENUM('belum_bayar','sebagian','lunas','batal') NOT NULL DEFAULT 'belum_bayar'");
    }
};
