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
            KeluargaSeeder::class,
            WargaSeeder::class,
            PengaturanSistemSeeder::class,
            JenisIuranRt02Seeder::class,
            Rt02Rw03IuranSeeder::class,
            KasUnitWilayahSeeder::class, // materialize unit kas wilayah (tanpa data demo)
            KasRt02Rw03RealSeeder::class, // buku kas asli RT02 RW03 2023-2026
            KasRw03RealSeeder::class, // buku kas asli RW03 Juli 2026
        ]);
    }
}
