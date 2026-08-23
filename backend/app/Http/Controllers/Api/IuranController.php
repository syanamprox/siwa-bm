<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Controllers\Api\Concerns\ScopesToWilayah;
use App\Models\Iuran;
use App\Models\PembayaranIuran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IuranController extends Controller
{
    use ScopesToWilayah, LogsActivity;

    /**
     * GET /api/iuran — tagihan list (scoped).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Iuran::with([
            'keluarga:id,no_kk,kepala_keluarga_id,rt_id',
            'keluarga.kepalaKeluarga:id,nama_lengkap',
            'keluarga.wilayah:id,nama',
            'jenisIuran:id,nama,kode,jumlah,periode',
        ])->withSum('pembayaran as total_dibayar', 'jumlah_bayar');
        $query = $this->scopeIuran($query);

        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('periode_bulan', 'like', "%{$s}%")
                    ->orWhereHas('keluarga', fn ($k) => $k->where('no_kk', 'like', "%{$s}%")
                        ->orWhereHas('kepalaKeluarga', fn ($w) => $w->where('nama_lengkap', 'like', "%{$s}%")))
                    ->orWhereHas('jenisIuran', fn ($j) => $j->where('nama', 'like', "%{$s}%"));
            });
        }
        foreach (['periode' => 'periode_bulan', 'status' => 'status'] as $param => $col) {
            if ($request->filled($param)) {
                $query->where($col, $request->input($param));
            }
        }
        if ($request->filled('keluarga_id')) {
            $query->where('kk_id', $request->input('keluarga_id'));
        }
        if ($request->filled('jenis_iuran_id')) {
            $query->where('jenis_iuran_id', $request->input('jenis_iuran_id'));
        }

        $perPage = min((int) $request->input('per_page', 15), 100);
        $page = $query->orderByRaw('periode_bulan desc, id desc')->paginate($perPage);

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

    /**
     * GET /api/iuran/statistics — scoped.
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = Iuran::query();
        $query = $this->scopeIuran($query);
        $this->applyFilters($request, $query);

        $data = [
            'total' => (clone $query)->count(),
            'belum_bayar' => (clone $query)->where('status', 'belum_bayar')->count(),
            'lunas' => (clone $query)->where('status', 'lunas')->count(),
            'total_nominal' => (float) (clone $query)->sum('nominal'),
            'total_denda' => (float) (clone $query)->sum('denda_terlambatan'),
        ];
        $data['persentase_lunas'] = $data['total'] > 0
            ? round($data['lunas'] / $data['total'] * 100, 2)
            : 0;

        return response()->json(['data' => $data]);
    }

    /**
     * POST /api/iuran/bayar-batch — multi pembayaran (rapelan) beberapa tagihan sekaligus.
     * Satu metode + keterangan untuk semua item; tiap item punya jumlah masing-masing.
     */
    public function bayarBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.iuran_id' => ['required', 'integer', 'exists:iurans,id'],
            'metode_pembayaran' => ['required', 'in:cash,transfer,qris,ewallet'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $results = ['dibayar' => 0, 'total' => 0.0, 'gagal' => []];

        DB::transaction(function () use ($request, $validated, &$results) {
            foreach ($validated['payments'] as $p) {
                $iuran = Iuran::lockForUpdate()->find($p['iuran_id']);

                if (! $iuran) {
                    $results['gagal'][] = ['iuran_id' => $p['iuran_id'], 'alasan' => 'Tagihan tidak ditemukan'];
                    continue;
                }

                try {
                    $this->authorizeIuran($request, $iuran);
                } catch (\Symfony\Component\HttpKernel\Exception\HttpException) {
                    $results['gagal'][] = ['iuran_id' => $iuran->id, 'alasan' => 'Di luar wilayah Anda'];
                    continue;
                }

                if ($iuran->status === 'lunas') {
                    $results['gagal'][] = ['iuran_id' => $iuran->id, 'alasan' => 'Tagihan sudah lunas'];
                    continue;
                }

                // Tanpa bayar sebagian — selalu nominal penuh + denda
                $jumlah = (float) $iuran->nominal + (float) $iuran->denda_terlambatan;

                $pembayaran = PembayaranIuran::create([
                    'iuran_id' => $iuran->id,
                    'jumlah_bayar' => $jumlah,
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'keterangan' => $validated['keterangan'] ?? null,
                    'nomor_referensi' => PembayaranIuran::generateNomorReferensi(),
                    'created_by' => $request->user()->id,
                ]);

                $iuran->update(['status' => 'lunas']);

                // Auto-post kas RT — hanya bila diaktifkan admin (pengaturan keuangan).
                // Default NONAKTIF: bendahara mencatat kas di buku fisik, posting otomatis
                // akan membuat double-entry. Kegagalan kas tak boleh membatalkan pembayaran.
                if (\App\Models\PengaturanSistem::getValue('auto_post_kas_iuran') === '1') {
                    try {
                        \App\Models\KasTransaksi::postFromPembayaran($pembayaran, $iuran);
                    } catch (\Throwable $e) {
                        \Log::warning('Auto-post kas gagal (pembayaran #'.$pembayaran->id.'): '.$e->getMessage());
                    }
                }

                $results['dibayar']++;
                $results['total'] += $jumlah;
            }
        });

        $this->logActivity($request, 'payment_batch', 'iuran', 'Bayar '.$results['dibayar'].' tagihan sekaligus (rapelan) total '.number_format($results['total'], 0, ',', '.'), null, ['metode' => $validated['metode_pembayaran']]);

        return response()->json(['data' => $results]);
    }

    /**
     * POST /api/iuran/{iuran}/bayar — catat pembayaran.
     */
    public function bayar(Request $request, Iuran $iuran): JsonResponse
    {
        $this->authorizeIuran($request, $iuran);

        $validated = $request->validate([
            'metode_pembayaran' => ['required', 'in:cash,transfer,qris,ewallet'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        if ($iuran->status === 'lunas') {
            return response()->json(['message' => 'Tagihan ini sudah lunas.'], 422);
        }

        // Tanpa bayar sebagian — selalu nominal penuh + denda
        $jumlah = (float) $iuran->nominal + (float) $iuran->denda_terlambatan;

        $pembayaran = DB::transaction(function () use ($request, $iuran, $validated, $jumlah) {
            $pembayaran = PembayaranIuran::create([
                'iuran_id' => $iuran->id,
                'jumlah_bayar' => $jumlah,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'keterangan' => $validated['keterangan'] ?? null,
                'nomor_referensi' => PembayaranIuran::generateNomorReferensi(),
                'created_by' => $request->user()->id,
            ]);

            $iuran->update(['status' => 'lunas']);

            return $pembayaran;
        });

        // Auto-post kas RT — hanya bila diaktifkan admin (pengaturan keuangan).
        // Default NONAKTIF: bendahara mencatat kas di buku fisik (lihat bayarBatch).
        if (\App\Models\PengaturanSistem::getValue('auto_post_kas_iuran') === '1') {
            try {
                \App\Models\KasTransaksi::postFromPembayaran($pembayaran, $iuran);
            } catch (\Throwable $e) {
                \Log::warning('Auto-post kas gagal (pembayaran #'.$pembayaran->id.'): '.$e->getMessage());
            }
        }

        $this->logActivity($request, 'pembayaran', 'iuran', "Pembayaran tagihan #{$iuran->id} (KK {$iuran->keluarga?->no_kk}) Rp ".number_format($jumlah, 0, ',', '.'), null, [
            'iuran_id' => $iuran->id, 'jumlah' => $jumlah, 'metode' => $validated['metode_pembayaran'],
        ]);

        $iuran->refresh();
        return response()->json(['data' => [
            'pembayaran' => $pembayaran->only(['id', 'jumlah_bayar', 'metode_pembayaran', 'nomor_referensi', 'keterangan', 'created_at']),
            'status_iuran' => $iuran->status,
            'total_dibayar' => (float) $iuran->pembayaran()->sum('jumlah_bayar'),
            'sisa_tagihan' => max(0, $iuran->nominal + $iuran->denda_terlambatan - $iuran->pembayaran()->sum('jumlah_bayar')),
        ]], 201);
    }

    /**
     * GET /api/iuran/{iuran}/payments — riwayat pembayaran tagihan.
     */
    public function payments(Request $request, Iuran $iuran): JsonResponse
    {
        $this->authorizeIuran($request, $iuran);

        return response()->json(['data' => $iuran->pembayaran()
            ->with('createdBy:id,name,username')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'jumlah_bayar' => (float) $p->jumlah_bayar,
                'metode_pembayaran' => $p->metode_pembayaran,
                'nomor_referensi' => $p->nomor_referensi,
                'keterangan' => $p->keterangan,
                'petugas' => $p->createdBy?->name,
                'created_at' => $p->created_at?->toIso8601String(),
            ]),
        ]);
    }

    private function applyFilters(Request $request, $query): void
    {
        if ($request->filled('periode')) {
            $query->where('periode_bulan', $request->input('periode'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('jenis_iuran_id')) {
            $query->where('jenis_iuran_id', $request->input('jenis_iuran_id'));
        }
    }

    private function authorizeIuran(Request $request, Iuran $iuran): void
    {
        if ($this->isUnrestricted($request->user())) {
            return;
        }
        $rtIds = $this->rtIdsForUser($request->user());
        $inScope = $iuran->keluarga()->whereIn('keluargas.rt_id', $rtIds)->exists();
        abort_if(! $inScope, 404, 'Tagihan tidak ditemukan.');
    }
}
