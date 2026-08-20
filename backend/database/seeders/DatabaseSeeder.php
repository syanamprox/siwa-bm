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
            ContohKeluargaSeeder::class,
            WargaRealRt02Rw03Seeder::class,
            KeluargaSeeder::class,
            WargaSeeder::class,
            PengaturanSistemSeeder::class,
            JenisIuranRt02Seeder::class,
            Rt02Rw03IuranSeeder::class,
        ]);
    }
}
