<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Controllers\Api\Concerns\ScopesToWilayah;
use App\Models\JenisIuran;
use App\Models\Keluarga;
use App\Models\KeluargaIuran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KeluargaIuranController extends Controller
{
    use ScopesToWilayah, LogsActivity;

    /**
     * GET /api/keluarga-iuran — overview semua koneksi (scoped).
     */
    public function index(Request $request): JsonResponse
    {
        $query = KeluargaIuran::with(['keluarga:id,no_kk,kepala_keluarga_id,rt_id', 'keluarga.kepalaKeluarga:id,nama_lengkap', 'jenisIuran:id,nama,kode,jumlah,periode']);

        // scope via keluarga
        $user = $request->user();
        if (! $this->isUnrestricted($user)) {
            $query->whereHas('keluarga', fn ($k) => $k->whereIn('rt_id', $this->rtIdsForUser($user)));
        }

        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->whereHas('keluarga', fn ($k) => $k->where('no_kk', 'like', "%{$s}%")
                    ->orWhereHas('kepalaKeluarga', fn ($w) => $w->where('nama_lengkap', 'like', "%{$s}%")));
            });
        }
        if ($request->filled('jenis_iuran_id')) {
            $query->where('jenis_iuran_id', $request->input('jenis_iuran_id'));
        }
        if ($request->filled('status_aktif')) {
            $query->where('status_aktif', $request->input('status_aktif') === '1');
        }

        $items = $query->orderByDesc('created_at')->get();

        return response()->json([
            'data' => $items,
            'meta' => [
                'total' => $items->count(),
                'aktif' => $items->where('status_aktif', true)->count(),
                'custom_nominal' => $items->whereNotNull('nominal_custom')->count(),
            ],
        ]);
    }

    /**
     * GET /api/keluarga/{id}/iuran-available — jenis yang belum terhubung.
     */
    public function available(Keluarga $keluarga): JsonResponse
    {
        $connected = $keluarga->keluargaIuran()->pluck('jenis_iuran_id');

        $available = JenisIuran::where('is_aktif', true)
            ->whereNotIn('id', $connected)
            ->orderBy('nama')
            ->get(['id', 'nama', 'kode', 'jumlah', 'periode']);

        return response()->json(['data' => $available]);
    }

    /**
     * POST /api/keluarga/{id}/iuran — hubungkan keluarga ke jenis iuran.
     */
    public function store(Request $request, Keluarga $keluarga): JsonResponse
    {
        $validated = $request->validate([
            'jenis_iuran_id' => ['required', 'exists:jenis_iurans,id'],
            'nominal_custom' => ['nullable', 'numeric', 'min:0'],
            'alasan_custom' => ['nullable', 'string', 'max:255'],
            'status_aktif' => ['boolean'],
        ]);

        $exists = $keluarga->keluargaIuran()->where('jenis_iuran_id', $validated['jenis_iuran_id'])->exists();
        if ($exists) {
            return response()->json(['message' => 'Jenis iuran ini sudah terhubung dengan keluarga tersebut.'], 422);
        }

        $conn = $keluarga->keluargaIuran()->create([
            'jenis_iuran_id' => $validated['jenis_iuran_id'],
            'nominal_custom' => $validated['nominal_custom'] ?? null,
            'alasan_custom' => $validated['alasan_custom'] ?? null,
            'status_aktif' => $validated['status_aktif'] ?? true,
            'created_by' => $request->user()->id,
        ]);
        $this->logActivity($request, 'create', 'keluarga_iuran', "Hubungkan KK {$keluarga->no_kk} ke iuran {$conn->jenisIuran?->nama}");

        return response()->json(['data' => $conn->load('jenisIuran:id,nama,kode,jumlah,periode')], 201);
    }

    /**
     * PUT /api/keluarga-iuran/{conn} — ubah nominal custom / status aktif.
     */
    public function update(Request $request, KeluargaIuran $conn): JsonResponse
    {
        $validated = $request->validate([
            'nominal_custom' => ['nullable', 'numeric', 'min:0'],
            'alasan_custom' => ['nullable', 'string', 'max:255'],
            'status_aktif' => ['boolean'],
        ]);

        $conn->update($validated);
        $this->logActivity($request, 'update', 'keluarga_iuran', "Ubah koneksi iuran KK {$conn->keluarga?->no_kk} → {$conn->jenisIuran?->nama}");

        return response()->json(['data' => $conn->fresh()->load('jenisIuran:id,nama,kode,jumlah,periode')]);
    }

    /**
     * DELETE /api/keluarga-iuran/{conn}
     */
    public function destroy(Request $request, KeluargaIuran $conn): JsonResponse
    {
        $info = "KK {$conn->keluarga?->no_kk} → {$conn->jenisIuran?->nama}";
        $conn->delete();
        $this->logActivity($request, 'delete', 'keluarga_iuran', "Putuskan koneksi iuran {$info}");

        return response()->json(['message' => 'Koneksi iuran dihapus.']);
    }
}
