<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function indexView()
    {
        return view('admin.users.index');
    }

    /**
     * API: Get users data for AJAX requests
     */
    public function index(Request $request)
    {
        $query = User::with('userWilayah.wilayah')->orderBy('created_at', 'desc');

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_aktif', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(10);

        // Get all available wilayah for assignment
        $wilayah = Wilayah::orderBy('tingkat')->orderBy('nama')->get();

        $wilayahByTingkat = $wilayah->groupBy('tingkat')->mapWithKeys(function ($group) {
            return $group->values();
        });

        return response()->json([
            'success' => true,
            'data' => $users,
            'wilayah' => $wilayahByTingkat
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'roles' => ['admin', 'lurah', 'rw', 'rt'],
                'wilayah' => Wilayah::orderBy('tingkat')->orderBy('nama')->get()->groupBy('tingkat')
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username,NULL,NULL,deleted_at,NULL',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'lurah', 'rw', 'rt'])],
            'status_aktif' => 'required|boolean',
            'wilayah_ids' => 'nullable|array',
            'wilayah_ids.*' => 'exists:wilayah,id',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status_aktif' => $request->boolean('status_aktif'),
            ]);

            // Handle file upload
            if ($request->hasFile('foto_profile')) {
                $file = $request->file('foto_profile');
                $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profile'), $filename);
                $user->foto_profile = 'uploads/profile/' . $filename;
                $user->save();
            }

            // Assign wilayah
            if ($request->has('wilayah_ids') && is_array($request->wilayah_ids)) {
                foreach ($request->wilayah_ids as $wilayahId) {
                    $user->userWilayah()->create(['wilayah_id' => $wilayahId]);
                }
            }

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'users',
                'id_referensi' => $user->id,
                'jenis_aktivitas' => 'create',
                'deskripsi' => "Menambahkan user baru: {$user->username} dengan role {$user->role}",
                'data_baru' => json_encode($user->toArray())
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat',
                'data' => $user->load('userWilayah.wilayah')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('userWilayah.wilayah')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('userWilayah.wilayah')->findOrFail($id);
        $wilayahByTingkat = Wilayah::orderBy('tingkat')->orderBy('nama')->get()->groupBy('tingkat');

        // Get assigned wilayah IDs
        $assignedWilayahIds = $user->userWilayah->pluck('wilayah_id')->toArray();

        return response()->json([
            'success' => true,
            'data' => $user,
            'roles' => ['admin', 'lurah', 'rw', 'rt'],
            'wilayah' => $wilayahByTingkat,
            'assigned_wilayah' => $assignedWilayahIds
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username,'.$id.',id,deleted_at,NULL',
            'password' => 'nullable|string|min:6',
            'role' => ['required', Rule::in(['admin', 'lurah', 'rw', 'rt'])],
            'status_aktif' => 'required|boolean',
            'wilayah_ids' => 'nullable|array',
            'wilayah_ids.*' => 'exists:wilayah,id',
            'foto_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Store old data for logging
            $dataLama = $user->toArray();

            // Update user data
            $user->username = $request->username;
            $user->role = $request->role;
            $user->status_aktif = $request->boolean('status_aktif');

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Handle file upload
            if ($request->hasFile('foto_profile')) {
                // Delete old photo if exists
                if ($user->foto_profile && file_exists(public_path($user->foto_profile))) {
                    unlink(public_path($user->foto_profile));
                }

                $file = $request->file('foto_profile');
                $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profile'), $filename);
                $user->foto_profile = 'uploads/profile/' . $filename;
            }

            $user->save();

            // Update wilayah assignments
            $user->userWilayah()->delete();
            if ($request->has('wilayah_ids') && is_array($request->wilayah_ids)) {
                foreach ($request->wilayah_ids as $wilayahId) {
                    $user->userWilayah()->create(['wilayah_id' => $wilayahId]);
                }
            }

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'users',
                'id_referensi' => $user->id,
                'jenis_aktivitas' => 'update',
                'deskripsi' => "Mengupdate user: {$user->username}",
                'data_lama' => json_encode($dataLama),
                'data_baru' => json_encode($user->load('userWilayah.wilayah')->toArray())
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => $user->load('userWilayah.wilayah')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        try {
            // Store data for logging
            $dataUser = $user->toArray();

            // Delete associated data
            if ($user->foto_profile && file_exists(public_path($user->foto_profile))) {
                unlink(public_path($user->foto_profile));
            }

            $user->userWilayah()->delete();
            $user->delete();

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'users',
                'id_referensi' => $user->id,
                'jenis_aktivitas' => 'delete',
                'deskripsi' => "Menghapus user: {$user->username}",
                'data_lama' => json_encode($dataUser)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        try {
            $user->status_aktif = !$user->status_aktif;
            $user->save();

            $status = $user->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'users',
                'id_referensi' => $user->id,
                'jenis_aktivitas' => 'toggle_status',
                'deskripsi' => "Mengubah status user: {$user->username} menjadi $status",
                'data_lama' => json_encode(['status_aktif' => !$user->status_aktif]),
                'data_baru' => json_encode(['status_aktif' => $user->status_aktif])
            ]);

            return response()->json([
                'success' => true,
                'message' => "User berhasil $status",
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        try {
            $newPassword = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, 8);
            $hashedPassword = Hash::make($newPassword);

            $user->password = $hashedPassword;
            $user->save();

            // Log activity
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'tabel_referensi' => 'users',
                'id_referensi' => $user->id,
                'jenis_aktivitas' => 'reset_password',
                'deskripsi' => "Reset password user: {$user->username}",
                'data_lama' => null,
                'data_baru' => json_encode(['new_password_reset' => true])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset',
                'data' => ['password' => $newPassword]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
