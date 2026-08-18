<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login dengan username (petugas: admin/lurah/rw/rt).
     * Sanctum stateful — session cookie untuk SPA Next.js.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            // Generic message — jangan bocorkan mana yang salah
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if ($user->status_aktif == 0) {
            throw ValidationException::withMessages([
                'username' => ['Akun dinonaktifkan. Hubungi admin.'],
            ]);
        }

        // Set session auth (web guard) — Sanctum stateful pakai ini
        auth()->login($user);
        $request->session()->regenerate();

        return response()->json(['data' => $this->userData($user)]);
    }

    /**
     * User yang sedang login (+ scope wilayahnya).
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json(['data' => $this->userData($user)]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out.']);
    }

    /**
     * Shape user + scoping wilayah (dipakai frontend untuk gating UI).
     */
    private function userData(User $user): array
    {
        $wilayah = $user->userWilayah()->with('wilayah.parent.parent')->first();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'avatar' => $user->avatar,
            'wilayah' => $wilayah ? [
                'wilayah_id' => $wilayah->wilayah_id,
                'nama' => $wilayah->wilayah?->nama,
                'tingkat' => $wilayah->wilayah?->tingkat,
                'rt_id' => $wilayah->wilayah?->tingkat === 'RT' ? $wilayah->wilayah_id : null,
                'rw_id' => $wilayah->wilayah?->parent?->id,
                'rw_nama' => $wilayah->wilayah?->parent?->nama,
                'kelurahan_id' => $wilayah->wilayah?->parent?->parent?->id,
                'kelurahan_nama' => $wilayah->wilayah?->parent?->parent?->nama,
            ] : null,
        ];
    }
}
