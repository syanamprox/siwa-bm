<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Role camat — akses pantau seluruh kelurahan di kecamatan (read-mostly, tanpa admin sistem).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','camat','lurah','rw','rt') NOT NULL DEFAULT 'rt'");
    }

    public function down(): void
    {
        // users camat dinormalkan ke lurah dulu supaya enum aman
        DB::table('users')->where('role', 'camat')->update(['role' => 'lurah']);
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','lurah','rw','rt') NOT NULL DEFAULT 'rt'");
    }
};
