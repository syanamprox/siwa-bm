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
        $query = User::orderBy('created_at', 'desc')->whereNull('deleted_at');

        // Filter by role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'active') {
                $query->where('status_aktif', true);
            } elseif ($request->status == 'inactive') {
                $query->where('status_aktif', false);
            }
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->with('userWilayah.wilayah')->paginate(10);

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'lurah', 'rw', 'rt'])],
            'status_aktif' => 'required|boolean',
            'wilayah_ids' => 'nullable|array',
            'wilayah_ids.*' => 'nullable|integer|exists:wilayahs,id',
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
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status_aktif' => $request->boolean('status_aktif'),
            ]);

            // Handle file upload
            if ($request->hasFile('foto_profile')) {
                $file = $request->file('foto_profile');
                $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/avatars'), $filename);
                $user->avatar = 'uploads/avatars/' . $filename;
                $user->save();
            }

            // Assign wilayah
            if ($request->has('wilayah_ids') && is_array($request->wilayah_ids)) {
                foreach ($request->wilayah_ids as $wilayahId) {
                    // Use firstOrCreate to avoid duplicate entries
                    \App\Models\UserWilayah::firstOrCreate([
                        'user_id' => $user->id,
                        'wilayah_id' => $wilayahId
                    ]);
                }
            }

            // Log activity - Disabled for debugging
            /*
            if (auth()->check()) {
                \App\Models\AktivitasLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'create',
                    'module' => 'users',
                    'description' => "Membuat user baru: {$user->name}",
                    'old_data' => null,
                    'new_data' => json_encode($user->toArray()),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }
            */

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat',
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6',
            'role' => ['required', Rule::in(['admin', 'lurah', 'rw', 'rt'])],
            'status_aktif' => 'required|boolean',
            'wilayah_ids' => 'nullable|array',
            'wilayah_ids.*' => 'nullable|integer|exists:wilayahs,id',
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

            // Use database transaction for consistency
            \DB::transaction(function () use ($request, $user) {
                // Update user data
                $user->name = $request->name;
                $user->email = $request->email;
                $user->role = $request->role;
                $user->status_aktif = $request->boolean('status_aktif');

                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }

                // Handle file upload
                if ($request->hasFile('foto_profile')) {
                    // Delete old avatar if exists
                    if ($user->avatar && file_exists(public_path($user->avatar))) {
                        unlink(public_path($user->avatar));
                    }

                    $file = $request->file('foto_profile');
                    $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/avatars'), $filename);
                    $user->avatar = 'uploads/avatars/' . $filename;
                }

                $user->save();

                // Update wilayah assignments - handle more efficiently
                $newWilayahIds = $request->has('wilayah_ids') && is_array($request->wilayah_ids)
                    ? $request->wilayah_ids
                    : [];

                // Get existing wilayah assignments
                $existingWilayahIds = $user->userWilayah()->pluck('wilayah_id')->toArray();

                // Remove wilayah assignments that are no longer selected
                $toRemove = array_diff($existingWilayahIds, $newWilayahIds);
                if (!empty($toRemove)) {
                    \App\Models\UserWilayah::where('user_id', $user->id)
                        ->whereIn('wilayah_id', $toRemove)
                        ->delete();
                }

                // Add new wilayah assignments
                $toAdd = array_diff($newWilayahIds, $existingWilayahIds);
                foreach ($toAdd as $wilayahId) {
                    \App\Models\UserWilayah::firstOrCreate([
                        'user_id' => $user->id,
                        'wilayah_id' => $wilayahId
                    ]);
                }
            });

            // Log activity with error handling
            try {
                if (auth()->check()) {
                    \App\Models\AktivitasLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'update',
                        'module' => 'users',
                        'description' => "Mengupdate user: {$user->name}",
                        'old_data' => json_encode($dataLama),
                        'new_data' => json_encode([
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'status_aktif' => $user->status_aktif
                        ]),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent()
                    ]);
                }
            } catch (\Exception $logError) {
                \Log::error('Activity logging error: ' . $logError->getMessage());
                // Continue execution even if logging fails
            }

            // Reload user with relationships for response
            $user->load('userWilayah.wilayah');

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            \Log::error('User update exception: ' . $e->getMessage());
            \Log::error('Exception file: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Exception trace: ' . $e->getTraceAsString());

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

            // Use soft delete - this will set deleted_at timestamp
            // Keep all data including wilayah assignments intact
            $user->delete();

            // Log activity with error handling
            try {
                if (auth()->check()) {
                    \App\Models\AktivitasLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'delete',
                        'module' => 'users',
                        'description' => "Menghapus user: {$user->name}",
                        'old_data' => json_encode($dataUser),
                        'new_data' => json_encode(['deleted_at' => now()]),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent()
                    ]);
                }
            } catch (\Exception $logError) {
                    \Log::error('Delete activity logging error: ' . $logError->getMessage());
                    // Continue execution even if logging fails
            }

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('User delete exception: ' . $e->getMessage());
            \Log::error('Exception file: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Exception trace: ' . $e->getTraceAsString());

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
            $oldStatus = $user->status_aktif;
            $user->status_aktif = !$user->status_aktif;
            $user->save();
            $status = $user->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

            // Log activity with error handling
            try {
                if (auth()->check()) {
                    \App\Models\AktivitasLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'toggle_status',
                        'module' => 'users',
                        'description' => "Mengubah status user: {$user->name} menjadi $status",
                        'old_data' => json_encode(['status_aktif' => $oldStatus]),
                        'new_data' => json_encode(['status_aktif' => $user->status_aktif]),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent()
                    ]);
                }
            } catch (\Exception $logError) {
                \Log::error('Toggle status activity logging error: ' . $logError->getMessage());
                // Continue execution even if logging fails
            }

            return response()->json([
                'success' => true,
                'message' => "User berhasil $status",
                'data' => $user
            ]);

        } catch (\Exception $e) {
            \Log::error('Toggle status exception: ' . $e->getMessage());
            \Log::error('Exception file: ' . $e->getFile() . ':' . $e->getLine());

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
            // Generate secure random password
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $newPassword = substr(str_shuffle($characters), 0, 8);
            $hashedPassword = Hash::make($newPassword);

            // Update user password
            $user->password = $hashedPassword;
            $user->save();

            // Log activity with error handling
            try {
                if (auth()->check()) {
                    \App\Models\AktivitasLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'reset_password',
                        'module' => 'users',
                        'description' => "Reset password user: {$user->name}",
                        'old_data' => null,
                        'new_data' => json_encode(['password_reset' => true, 'user_id' => $user->id]),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent()
                    ]);
                }
            } catch (\Exception $logError) {
                \Log::error('Reset password activity logging error: ' . $logError->getMessage());
                // Continue execution even if logging fails
            }

            $response = [
                'success' => true,
                'message' => 'Password berhasil direset',
                'data' => [
                    'password' => $newPassword,
                    'user_name' => $user->name,
                    'user_email' => $user->email
                ]
            ];

            \Log::info("Reset password response: " . json_encode($response));

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Reset password exception: ' . $e->getMessage());
            \Log::error('Exception file: ' . $e->getFile() . ':' . $e->getLine());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate name from username
     */
    private function generateNameFromUsername($username, $role)
    {
        // Basic name generation based on username
        $name = ucfirst($username);

        // Add role-based suffix
        switch ($role) {
            case 'admin':
                return $name . ' Administrator';
            case 'lurah':
                return $name . ', S.Sos';
            case 'rw':
                return $name . ' (RW)';
            case 'rt':
                return $name . ' (RT)';
            default:
                return $name;
        }
    }
}
