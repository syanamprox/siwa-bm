<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WilayahSeeder::class,
            UserSeeder::class,
            WargaRealRt02Rw03Seeder::class,
            MasterKkRt02Rw03Seeder::class, // 57 KK master kelurahan (stub, tanpa arsip dokumen)
            KeluargaSeeder::class,
            WargaSeeder::class,
            PengaturanSistemSeeder::class,
            JenisIuranRt02Seeder::class, // jenis RT02 (Sosial/RT/Rukem) + koneksi semua KK — PRASYARAT generate
            // Rt02Rw03IuranSeeder DIKOSONGKAN per keputusan user 23 Agt: tagihan &
            // pembayaran TIDAK lagi di-seed — admin generate via UI (Generate Tagihan)
            // saat produksi, sesuai kondisi riil per periode.
            KasUnitWilayahSeeder::class, // materialize unit kas wilayah (tanpa data demo)
            KasOrganisasiRw03Seeder::class, // unit kas organisasi real RW03 & RT02 (musholla, rukem, PKK, karang taruna, sosial)
            KasRt02Rw03RealSeeder::class, // buku kas asli RT02 RW03 2023-2026
            KasRw03RealSeeder::class, // buku kas asli RW03 Juli 2026
            KasKartarRw03RealSeeder::class, // buku kas asli Karang Taruna RW03 Feb 2023-Jul 2026
            KasRukemRw03RealSeeder::class, // buku kas asli Rukem Sehati RW03 Feb 2017-Agt 2026
        ]);
    }
}
