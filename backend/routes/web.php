<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIWA (Next.js SPA backend)
|--------------------------------------------------------------------------
| Semua UI diserve Next.js (root project). Laravel hanya API + storage.
| Route /up (health check) otomatis via bootstrap/app.php.
*/

// Storage link helper untuk local dev (php artisan storage:link tetap cara utama)
Route::get('/', fn () => response()->json([
    'app' => 'SIWA API',
    'status' => 'ok',
    'frontend' => config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:9910')),
]));
