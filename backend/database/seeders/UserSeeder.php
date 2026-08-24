<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wilayah;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Pivot user_wilayahs disinkronkan penuh per user seeder (replace mapping salah/lama).
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Administrator', 'username' => 'admin', 'password' => 'Ber217antok', 'role' => 'admin', 'wilayah' => null],
            ['name' => 'MUSLICH HARIADI, S.SOS., M.M', 'username' => 'camat', 'password' => 'camat123', 'role' => 'camat', 'wilayah' => null],
            ['name' => 'RULLY PRASETYA NEGARA, S.STP.,M.Si', 'username' => 'lurah', 'password' => 'lurah123', 'role' => 'lurah', 'wilayah' => 'Kelurahan Bendul Merisi'],
            ['name' => 'BAMBANG SETYAWAN', 'username' => 'rw03', 'password' => 'rw123', 'role' => 'rw', 'wilayah' => 'RW 03 Bendul Merisi'],
            ['name' => 'TRI BAGUS WAHYUDI', 'username' => 'rt01', 'password' => 'rt123', 'role' => 'rt', 'wilayah' => 'RT 01 RW 03 Bendul Merisi'],
            ['name' => 'AKHMAD SURYADI', 'username' => 'rt02', 'password' => 'rt123', 'role' => 'rt', 'wilayah' => 'RT 02 RW 03 Bendul Merisi'],
            ['name' => 'M. YASIN', 'username' => 'rt03', 'password' => 'rt123', 'role' => 'rt', 'wilayah' => 'RT 03 RW 03 Bendul Merisi'],
            ['name' => 'SULICHAH', 'username' => 'rt04', 'password' => 'rt123', 'role' => 'rt', 'wilayah' => 'RT 04 RW 03 Bendul Merisi'],
        ];

        foreach ($users as $u) {
            $user = User::updateOrCreate(
                ['username' => $u['username']],
                [
                    'name' => $u['name'],
                    'password' => bcrypt($u['password']),
                    'role' => $u['role'],
                    'status_aktif' => 1,
                ]
            );

            $wilayahIds = $u['wilayah']
                ? Wilayah::where('nama', $u['wilayah'])->pluck('id')
                : collect();

            if ($wilayahIds->isEmpty() && $u['wilayah']) {
                $this->command->warn("⚠️  Wilayah '{$u['wilayah']}' utk user {$u['username']} tidak ditemukan — pivot dilewati.");

                continue;
            }

            $user->wilayah()->sync($wilayahIds);
        }
    }
}
