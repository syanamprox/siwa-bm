<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\ScopesToWilayah;
use App\Models\AktivitasLog;
use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\PembayaranIuran;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ScopesToWilayah;

    /**
     * GET /api/dashboard — stats role-aware.
     * admin/lurah: seluruh kelurahan. rw: RT-RT di bawah RW-nya. rt: RT-nya.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $keluargaIds = Keluarga::query();
        $keluargaIds = $this->scopeKeluarga($keluargaIds)->pluck('id');

        $wargaQuery = Warga::query();
        $wargaQuery = $this->scopeWarga($wargaQuery);

        $data = [
            'role' => $user->role,
            'total_warga' => (clone $wargaQuery)->count(),
            'warga_laki' => (clone $wargaQuery)->where('jenis_kelamin', 'L')->count(),
            'warga_perempuan' => (clone $wargaQuery)->where('jenis_kelamin', 'P')->count(),
            'total_keluarga' => $keluargaIds->count(),
            'total_tagihan_iuran' => Iuran::query()
                ->whereIn('kk_id', $keluargaIds)
                ->where('status', '!=', 'batal')
                ->where('status', '!=', 'lunas')
                ->sum('nominal'),
            'pemasukan_bulan_ini' => PembayaranIuran::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('jumlah_bayar'),
        ];

        // Wilayah counts (admin/camat: semua; lurah: kelurahan-nya; rw/rt: miliknya)
        if ($this->isUnrestricted($user)) {
            $data['total_rt'] = Wilayah::where('tingkat', 'RT')->count();
            $data['total_rw'] = Wilayah::where('tingkat', 'RW')->count();
            $data['warga_per_rw'] = $this->wargaPerRw();
        } else {
            $rtIds = $this->rtIdsForUser($user);
            if (in_array($user->role, ['lurah', 'rw'], true)) {
                $data['total_rt'] = $rtIds->count();
                $data['total_rw'] = Wilayah::whereIn('id', $rtIds)->distinct()->count('parent_id');
                $data['warga_per_rw'] = $this->wargaPerRw($rtIds);
            } else {
                $data['total_rt'] = 1;
                $data['warga_per_rt'] = $this->wargaPerRt($rtIds);
            }
        }

        // Chart tren pembayaran 6 bulan terakhir
        $data['pembayaran_tren'] = PembayaranIuran::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as bulan, SUM(jumlah_bayar) as total')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->map(fn ($r) => ['bulan' => $r->bulan, 'total' => (float) $r->total]);

        // Aktivitas terbaru (visible semua role)
        $data['recent_activities'] = AktivitasLog::with('user:id,name,username')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'Sistem',
                'action' => $log->action,
                'module' => $log->module,
                'description' => $log->description,
                'created_at' => $log->created_at?->toIso8601String(),
            ]);

        // RT-specific: tagihan jatuh tempo terdekat
        if ($user->role === 'rt' || $user->role === 'rw') {
            $data['pending_iuran'] = Iuran::with(['keluarga:id,no_kk,kepala_keluarga_id', 'keluarga.kepalaKeluarga:id,nama_lengkap', 'jenisIuran:id,nama'])
                ->whereIn('kk_id', $keluargaIds)
                ->where('status', 'belum_bayar')
                ->orderBy('jatuh_tempo')
                ->limit(10)
                ->get()
                ->map(fn ($i) => [
                    'id' => $i->id,
                    'keluarga' => $i->keluarga?->nama_kepala_keluarga,
                    'jenis' => $i->jenisIuran?->nama,
                    'periode' => $i->periode_bulan,
                    'nominal' => (float) $i->nominal,
                    'jatuh_tempo' => $i->jatuh_tempo?->format('Y-m-d'),
                ]);
        }

        return response()->json(['data' => $data]);
    }

    private function wargaPerRw($rtIds = null)
    {
        $query = Warga::join('keluargas', 'wargas.kk_id', '=', 'keluargas.id')
            ->join('wilayahs as rt', 'keluargas.rt_id', '=', 'rt.id')
            ->join('wilayahs as rw', 'rt.parent_id', '=', 'rw.id')
            ->whereNull('wargas.deleted_at')
            ->whereNull('keluargas.deleted_at');

        if ($rtIds !== null) {
            $query->whereIn('rt.id', $rtIds);
        }

        return $query->groupBy('rw.nama')
            ->selectRaw('rw.nama as nama, COUNT(*) as total')
            ->pluck('total', 'nama');
    }

    private function wargaPerRt($rtIds)
    {
        return Warga::join('keluargas', 'wargas.kk_id', '=', 'keluargas.id')
            ->whereIn('keluargas.rt_id', $rtIds)
            ->whereNull('wargas.deleted_at')
            ->whereNull('keluargas.deleted_at')
            ->groupBy('keluargas.rt_id')
            ->selectRaw('keluargas.rt_id as rt_id, COUNT(*) as total')
            ->pluck('total', 'rt_id');
    }
}
