<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Models\JenisIuran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JenisIuranController extends Controller
{
    use LogsActivity;

    /**
     * GET /api/jenis-iuran — global (rt_id null) + milik RT user/terpilih.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = JenisIuran::query();

        // Scope RT: login rw/rt hanya lihat jenis global + milik RT-nya
        if (! in_array($user->role, ['admin', 'lurah'], true)) {
            $rtIds = $user->role === 'rw'
                ? \App\Models\Wilayah::whereIn('parent_id', $user->wilayah()->pluck('wilayah_id'))->pluck('id')
                : $user->wilayah()->pluck('wilayah_id');
            $query->where(fn ($q) => $q->whereNull('rt_id')->orWhereIn('rt_id', $rtIds));
        }
        // Filter eksplisit ?rt_id= (admin lihat jenis satu RT)
        if ($request->filled('rt_id')) {
            $rtId = (int) $request->input('rt_id');
            $query->where(fn ($q) => $q->whereNull('rt_id')->orWhere('rt_id', $rtId));
        }

        if ($request->boolean('only_active')) {
            $query->where('is_aktif', true);
        }
        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                    ->orWhere('kode', 'like', "%{$s}%")
                    ->orWhere('keterangan', 'like', "%{$s}%");
            });
        }
        if ($request->filled('periode')) {
            $query->where('periode', $request->input('periode'));
        }
        if ($request->filled('status')) {
            $query->where('is_aktif', $request->input('status') === '1');
        }

        $items = $query->with('rt:id,nama')
            ->withCount(['keluarga as koneksi_aktif' => fn ($q) => $q->where('status_aktif', true)])
            ->orderBy('nama')
            ->get();

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_aktif'] = $request->boolean('is_aktif', true);

        $jenis = JenisIuran::create($validated);
        $this->logActivity($request, 'create', 'jenis_iuran', "Tambah jenis iuran {$jenis->nama}", null, $jenis->only(['kode', 'nama', 'jumlah', 'periode']));

        return response()->json(['data' => $jenis], 201);
    }

    public function update(Request $request, JenisIuran $jenisIuran): JsonResponse
    {
        $validated = $request->validate($this->rules($jenisIuran->id));
        $validated['is_aktif'] = $request->boolean('is_aktif', $jenisIuran->is_aktif);

        $old = $jenisIuran->only(['kode', 'nama', 'jumlah']);
        $jenisIuran->update($validated);
        $this->logActivity($request, 'update', 'jenis_iuran', "Ubah jenis iuran {$jenisIuran->nama}", $old, $jenisIuran->fresh()->only(['kode', 'nama', 'jumlah', 'periode']));

        return response()->json(['data' => $jenisIuran->fresh()]);
    }

    public function destroy(Request $request, JenisIuran $jenisIuran): JsonResponse
    {
        if ($jenisIuran->keluargaIuran()->where('status_aktif', true)->exists()) {
            return response()->json(['message' => 'Masih ada keluarga terhubung dengan jenis iuran ini. Nonaktifkan koneksi terlebih dahulu.'], 422);
        }

        $old = $jenisIuran->only(['kode', 'nama']);
        $jenisIuran->delete();
        $this->logActivity($request, 'delete', 'jenis_iuran', "Hapus jenis iuran {$old['nama']}", $old, null);

        return response()->json(['message' => 'Jenis iuran dihapus.']);
    }

    /**
     * PUT /api/jenis-iuran/{id}/toggle-status
     */
    public function toggleStatus(Request $request, JenisIuran $jenisIuran): JsonResponse
    {
        $jenisIuran->update(['is_aktif' => ! $jenisIuran->is_aktif]);
        $this->logActivity($request, 'toggle_status', 'jenis_iuran', "Toggle jenis iuran {$jenisIuran->nama} → ".($jenisIuran->is_aktif ? 'aktif' : 'nonaktif'));

        return response()->json(['data' => $jenisIuran->fresh()]);
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'kode' => [
                'required', 'string', 'max:10',
                $ignoreId
                    ? Rule::unique('jenis_iurans', 'kode')->ignore($ignoreId)->whereNull('deleted_at')
                    : Rule::unique('jenis_iurans', 'kode')->whereNull('deleted_at'),
            ],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'periode' => ['required', 'in:bulanan,tahunan,sekali'],
            'keterangan' => ['nullable', 'string'],
            'rt_id' => ['nullable', 'exists:wilayahs,id'],
        ];
    }
}
