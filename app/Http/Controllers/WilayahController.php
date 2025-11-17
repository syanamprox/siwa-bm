<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WilayahController extends Controller
{
    /**
     * Display wilayah management page.
     */
    public function indexView()
    {
        return view('admin.wilayah.index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $wilayah = Wilayah::with(['parent', 'children'])
            ->orderBy('tingkat')
            ->orderBy('kode')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wilayah,
            'message' => 'Data wilayah berhasil dimuat'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        $parents = Wilayah::where(function($query) {
            $query->where('tingkat', 'kelurahan')
                  ->orWhere('tingkat', 'rw');
        })->orderBy('tingkat')->orderBy('kode')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'parents' => $parents,
                'tingkat_options' => [
                    'kelurahan' => 'Kelurahan',
                    'rw' => 'RW',
                    'rt' => 'RT'
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:10|unique:wilayah,kode,NULL,NULL,deleted_at,NULL',
            'nama' => 'required|string|max:100',
            'tingkat' => ['required', Rule::in(['kelurahan', 'rw', 'rt'])],
            'parent_id' => 'nullable|exists:wilayah,id',
        ], [
            'kode.required' => 'Kode wilayah wajib diisi',
            'kode.unique' => 'Kode wilayah sudah digunakan',
            'nama.required' => 'Nama wilayah wajib diisi',
            'tingkat.required' => 'Tingkat wilayah wajib dipilih',
            'parent_id.exists' => 'Parent wilayah tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate parent relationship
        if ($request->filled('parent_id')) {
            $parent = Wilayah::find($request->parent_id);
            $tingkat = $request->tingkat;

            if ($tingkat === 'kelurahan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelurahan tidak boleh memiliki parent',
                ], 422);
            }

            if ($tingkat === 'rw' && $parent->tingkat !== 'kelurahan') {
                return response()->json([
                    'success' => false,
                    'message' => 'RW hanya memiliki parent Kelurahan',
                ], 422);
            }

            if ($tingkat === 'rt' && $parent->tingkat !== 'rw') {
                return response()->json([
                    'success' => false,
                    'message' => 'RT hanya memiliki parent RW',
                ], 422);
            }
        } else {
            if ($request->tingkat !== 'kelurahan') {
                return response()->json([
                    'success' => false,
                    'message' => 'RW dan RT harus memiliki parent wilayah',
                ], 422);
            }
        }

        try {
            $wilayah = Wilayah::create([
                'kode' => strtoupper($request->kode),
                'nama' => $request->nama,
                'tingkat' => $request->tingkat,
                'parent_id' => $request->parent_id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $wilayah->load(['parent']),
                'message' => 'Wilayah berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan wilayah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $wilayah = Wilayah::with(['parent', 'children', 'users'])->find($id);

        if (!$wilayah) {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $wilayah,
            'message' => 'Detail wilayah berhasil dimuat'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $wilayah = Wilayah::find($id);

        if (!$wilayah) {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah tidak ditemukan'
            ], 404);
        }

        $parents = Wilayah::where(function($query) use ($wilayah) {
            $query->where('tingkat', 'kelurahan')
                  ->orWhere('tingkat', 'rw')
                  ->where('id', '!=', $wilayah->id);
        })->orderBy('tingkat')->orderBy('kode')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'wilayah' => $wilayah,
                'parents' => $parents,
                'tingkat_options' => [
                    'kelurahan' => 'Kelurahan',
                    'rw' => 'RW',
                    'rt' => 'RT'
                ]
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $wilayah = Wilayah::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:10|unique:wilayah,kode,'.$id.',id,deleted_at,NULL',
            'nama' => 'required|string|max:100',
            'tingkat' => ['required', Rule::in(['kelurahan', 'rw', 'rt'])],
            'parent_id' => 'nullable|exists:wilayah,id',
        ], [
            'kode.required' => 'Kode wilayah wajib diisi',
            'kode.unique' => 'Kode wilayah sudah digunakan',
            'nama.required' => 'Nama wilayah wajib diisi',
            'tingkat.required' => 'Tingkat wilayah wajib dipilih',
            'parent_id.exists' => 'Parent wilayah tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Prevent self-parenting
        if ($request->filled('parent_id') && $request->parent_id == $id) {
            return response()->json([
                'success' => false,
                'message' => 'Wilayah tidak bisa menjadi parent dari dirinya sendiri',
            ], 422);
        }

        // Prevent circular reference
        if ($request->filled('parent_id')) {
            $parent = Wilayah::find($request->parent_id);
            $currentChildren = $this->getAllChildren($id);
            if ($currentChildren->contains($request->parent_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa memindahkan wilayah ke child-nya sendiri',
                ], 422);
            }
        }

        // Validate parent relationship
        if ($request->filled('parent_id')) {
            $parent = Wilayah::find($request->parent_id);
            $tingkat = $request->tingkat;

            if ($tingkat === 'kelurahan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelurahan tidak boleh memiliki parent',
                ], 422);
            }

            if ($tingkat === 'rw' && $parent->tingkat !== 'kelurahan') {
                return response()->json([
                    'success' => false,
                    'message' => 'RW hanya memiliki parent Kelurahan',
                ], 422);
            }

            if ($tingkat === 'rt' && $parent->tingkat !== 'rw') {
                return response()->json([
                    'success' => false,
                    'message' => 'RT hanya memiliki parent RW',
                ], 422);
            }
        } else {
            if ($request->tingkat !== 'kelurahan') {
                return response()->json([
                    'success' => false,
                    'message' => 'RW dan RT harus memiliki parent wilayah',
                ], 422);
            }
        }

        try {
            $wilayah->update([
                'kode' => strtoupper($request->kode),
                'nama' => $request->nama,
                'tingkat' => $request->tingkat,
                'parent_id' => $request->parent_id,
            ]);

            return response()->json([
                'success' => true,
                'data' => $wilayah->load(['parent']),
                'message' => 'Wilayah berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui wilayah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $wilayah = Wilayah::findOrFail($id);

        // Check if has children
        if ($wilayah->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus wilayah yang masih memiliki child wilayah'
            ], 422);
        }

        // Check if has related users
        if ($wilayah->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus wilayah yang masih terhubung dengan user'
            ], 422);
        }

        // Check if has related warga (simplified check)
        $wargaCount = 0;
        try {
            if ($wilayah->tingkat === 'rt') {
                $wargaCount = \App\Models\Warga::where('rt_domisili', $wilayah->kode)->count();
            } elseif ($wilayah->tingkat === 'rw') {
                $wargaCount = \App\Models\Warga::where('rw_domisili', $wilayah->kode)->count();
            } elseif ($wilayah->tingkat === 'kelurahan') {
                $wargaCount = \App\Models\Warga::where('kelurahan_domisili', 'like', '%' . $wilayah->nama . '%')->count();
            }

            if ($wargaCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak bisa menghapus wilayah yang masih memiliki {$wargaCount} warga"
                ], 422);
            }
        } catch (\Exception $e) {
            // If warga check fails, continue with deletion
            // (in case warga table doesn't exist yet)
        }

        try {
            $wilayah->delete();

            return response()->json([
                'success' => true,
                'message' => 'Wilayah berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus wilayah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get hierarchical tree data
     */
    public function tree(): JsonResponse
    {
        $kelurahan = Wilayah::with(['children.children'])
            ->where('tingkat', 'kelurahan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kelurahan
        ]);
    }

    /**
     * Get children by parent
     */
    public function getChildren($parentId): JsonResponse
    {
        $children = Wilayah::where('parent_id', $parentId)
            ->orderBy('kode')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $children
        ]);
    }

    /**
     * Get all children recursively (for circular reference check)
     */
    private function getAllChildren($parentId)
    {
        $children = collect();
        $directChildren = Wilayah::where('parent_id', $parentId)->get();

        foreach ($directChildren as $child) {
            $children->push($child->id);
            $children = $children->merge($this->getAllChildren($child->id));
        }

        return $children;
    }
}