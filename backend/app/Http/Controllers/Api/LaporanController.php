<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ScopesToWilayah;
use App\Models\Keluarga;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;

/**
 * Laporan — data agregat untuk render tabel/export di frontend.
 * (LaporanController lama tidak pernah ada; ini implementasi baru.)
 */
class LaporanController extends Controller
{
    use ScopesToWilayah;

    /**
     * GET /api/laporan/kependudukan — rekap per RT/RW (scoped).
     */
    public function kependudukan(): JsonResponse
    {
        $keluargaQuery = Keluarga::query();
        $rtIds = null;
        if (! $this->isUnrestricted(request()->user())) {
            $rtIds = $this->rtIdsForUser(request()->user())->all();
            $keluargaQuery->whereIn('rt_id', $rtIds);
        }

        $rows = Wilayah::where('tingkat', 'RT')
            ->when($rtIds, fn ($q) => $q->whereIn('id', $rtIds))
            ->with('parent:id,nama,kode')
            ->withCount([
                'keluarga as total_kk' => fn ($q) => $q->whereNull('deleted_at'),
            ])
            ->orderBy('kode')
            ->get();

        // warga counts per RT via join
        $wargaPerRt = Warga::join('keluargas', 'wargas.kk_id', '=', 'keluargas.id')
            ->when($rtIds, fn ($q) => $q->whereIn('keluargas.rt_id', $rtIds))
            ->whereNull('wargas.deleted_at')
            ->whereNull('keluargas.deleted_at')
            ->selectRaw('keluargas.rt_id, SUM(jenis_kelamin = "L") as laki, SUM(jenis_kelamin = "P") as perempuan, COUNT(*) as total')
            ->groupBy('keluargas.rt_id')
            ->get()
            ->keyBy('rt_id');

        $data = $rows->map(fn ($rt) => [
            'rt' => $rt->nama,
            'rw' => $rt->parent?->nama,
            'total_kk' => $rt->total_kk,
            'laki' => (int) ($wargaPerRt[$rt->id]->laki ?? 0),
            'perempuan' => (int) ($wargaPerRt[$rt->id]->perempuan ?? 0),
            'total_warga' => (int) ($wargaPerRt[$rt->id]->total ?? 0),
        ])
            ->filter(fn ($r) => $r['total_kk'] > 0 || $r['total_warga'] > 0) // RT tanpa data tidak ditampilkan
            ->values();

        return response()->json(['data' => [
            'rows' => $data->values(),
            'totals' => [
                'total_kk' => (int) $data->sum('total_kk'),
                'laki' => (int) $data->sum('laki'),
                'perempuan' => (int) $data->sum('perempuan'),
                'total_warga' => (int) $data->sum('total_warga'),
            ],
        ]]);
    }

    /**
     * GET /api/laporan/wilayah — struktur wilayah + beban KK.
     */
    public function wilayah(): JsonResponse
    {
        $user = request()->user();
        $rtIds = null;
        if (! $this->isUnrestricted($user)) {
            $rtIds = $this->rtIdsForUser($user)->all();
        }

        // Kelurahan dalam scope: turunan dari RT yang terlihat (lurah/rw/rt); camat/admin = semua.
        $kelurahanIds = null;
        if ($rtIds !== null) {
            $kelurahanIds = Wilayah::whereIn('wilayahs.id', $rtIds)
                ->join('wilayahs as rw', 'wilayahs.parent_id', '=', 'rw.id')
                ->join('wilayahs as kel', 'rw.parent_id', '=', 'kel.id')
                ->pluck('kel.id')
                ->unique()
                ->values();
        }

        // Tree Kelurahan → RW → RT (total_kk di level RT, RT difilter scope)
        $tree = Wilayah::where('tingkat', 'Kelurahan')
            ->when($kelurahanIds, fn ($q) => $q->whereIn('id', $kelurahanIds))
            ->with(['children' => fn ($q) => $q->orderBy('kode')->with([
                'children' => fn ($r) => $r->orderBy('kode')
                    ->when($rtIds, fn ($r2) => $r2->whereIn('id', $rtIds))
                    ->withCount(['keluarga as total_kk' => fn ($k) => $k->whereNull('deleted_at')]),
            ])])
            ->orderBy('kode')
            ->get();

        return response()->json(['data' => $tree]);
    }
}
