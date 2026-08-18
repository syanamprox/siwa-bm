<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Models\User;
use App\Models\UserWilayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use LogsActivity;

    public function index(Request $request): JsonResponse
    {
        $query = User::with('userWilayah.wilayah:id,nama,kode,tingkat')->orderByDesc('created_at');

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->input('status') === '1');
        }
        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('username', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $perPage = min((int) $request->input('per_page', 15), 100);
        $page = $query->paginate($perPage);

        return response()->json([
            'data' => $page->items(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status_aktif' => $validated['status_aktif'],
        ]);
        $this->syncWilayah($user, $validated['wilayah_ids'] ?? []);
        $this->logActivity($request, 'create', 'user', "Tambah user {$user->username} ({$user->role})", null, ['id' => $user->id, 'username' => $user->username]);

        return response()->json(['data' => $user->fresh()->load('userWilayah.wilayah:id,nama,kode,tingkat')], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate($this->rules($user->id));
        if ($user->id === $request->user()->id && ($validated['status_aktif'] ?? true) === false) {
            return response()->json(['message' => 'Tidak bisa menonaktifkan akun sendiri.'], 422);
        }

        $user->update(array_filter([
            'name' => $validated['name'] ?? null,
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'] ?? $user->email,
            'role' => $validated['role'] ?? null,
            'status_aktif' => $validated['status_aktif'] ?? null,
        ], fn ($v) => $v !== null) + (isset($validated['password']) && $validated['password']
            ? ['password' => Hash::make($validated['password'])]
            : []));

        if (array_key_exists('wilayah_ids', $validated)) {
            $this->syncWilayah($user, $validated['wilayah_ids'] ?? []);
        }

        $this->logActivity($request, 'update', 'user', "Ubah user {$user->username}", null, ['id' => $user->id]);

        return response()->json(['data' => $user->fresh()->load('userWilayah.wilayah:id,nama,kode,tingkat')]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri.'], 422);
        }

        $user->delete();
        $this->logActivity($request, 'delete', 'user', "Hapus user {$user->username}");

        return response()->json(['message' => 'User dihapus.']);
    }

    /**
     * POST /api/users/{user}/toggle-status
     */
    public function toggleStatus(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Tidak bisa menonaktifkan akun sendiri.'], 422);
        }

        $user->update(['status_aktif' => ! $user->status_aktif]);
        $this->logActivity($request, 'toggle_status', 'user', "Toggle user {$user->username} → ".($user->status_aktif ? 'aktif' : 'nonaktif'));

        return response()->json(['data' => $user->fresh()]);
    }

    /**
     * POST /api/users/{user}/reset-password — return password baru (sekali saja).
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $password = substr(str_shuffle(str_repeat('abcdefghjkmnpqrstuvwxyz23456789', 8)), 0, 8);
        $user->update(['password' => Hash::make($password)]);
        $this->logActivity($request, 'reset_password', 'user', "Reset password user {$user->username}");

        return response()->json(['data' => ['password' => $password]]);
    }

    private function syncWilayah(User $user, array $wilayahIds): void
    {
        $user->userWilayah()->delete();
        foreach (array_unique($wilayahIds) as $id) {
            UserWilayah::create(['user_id' => $user->id, 'wilayah_id' => $id]);
        }
    }

    private function rules(?int $ignoreId = null): array
    {
        $usernameUnique = $ignoreId
            ? ['required', 'string', 'max:50', 'regex:/^[a-z0-9_\.]+$/i', Rule::unique('users', 'username')->ignore($ignoreId)]
            : ['required', 'string', 'max:50', 'regex:/^[a-z0-9_\.]+$/i', Rule::unique('users', 'username')];
        $emailUnique = $ignoreId
            ? ['nullable', 'email', Rule::unique('users', 'email')->ignore($ignoreId)]
            : ['nullable', 'email', Rule::unique('users', 'email')];

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => $usernameUnique,
            'email' => $emailUnique,
            'password' => $ignoreId
                ? ['nullable', 'string', 'min:6']
                : ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'lurah', 'rw', 'rt'])],
            'status_aktif' => ['boolean'],
            'wilayah_ids' => ['nullable', 'array'],
            'wilayah_ids.*' => ['exists:wilayahs,id'],
        ];
    }
}
