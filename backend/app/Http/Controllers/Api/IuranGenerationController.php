<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Controllers\Api\Concerns\ScopesToWilayah;
use App\Models\Iuran;
use App\Models\JenisIuran;
use App\Models\Keluarga;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IuranGenerationController extends Controller
{
    use ScopesToWilayah, LogsActivity;

    /**
     * GET /api/iuran/generation/rt-options — RT dalam scope user (rw/rt dibatasi).
     */
    public function rtOptions(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Wilayah::where('tingkat', 'RT')->with('parent:id,nama,kode')->orderBy('kode');

        if (! $this->isUnrestricted($user)) {
            $query->whereIn('id', $this->rtIdsForUser($user));
        }

        return response()->json(['data' => $query->get(['id', 'kode', 'nama', 'parent_id'])]);
    }

    /**
     * GET /api/iuran/generation/preview — dry-run: KK mana yang kena tagihan.
     */
    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'periode_bulan' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'jenis_iuran_ids' => ['nullable', 'array'],
            'jenis_iuran_ids.*' => ['exists:jenis_iurans,id'],
            'rt_id' => ['nullable', 'exists:wilayahs,id'],
        ]);

        return response()->json(['data' => $this->buildPreview($request, $validated)]);
    }

    /**
     * POST /api/iuran/generation/generate — commit (idempotent per kk+jenis+periode).
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'periode_bulan' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'jenis_iuran_ids' => ['nullable', 'array'],
            'jenis_iuran_ids.*' => ['exists:jenis_iurans,id'],
            'rt_id' => ['nullable', 'exists:wilayahs,id'],
        ]);

        $preview = $this->buildPreview($request, $validated);

        $result = DB::transaction(function () use ($request, $validated, $preview) {
            $generated = 0;
            $duplicates = 0;

            foreach ($preview['preview'] as $family) {
                foreach ($family['iurans'] as $item) {
                    // Guard semantic per jenis (concurrent-safe di dalam transaksi)
                    if ($this->findExistingTagihan($family['kk_id'], $item['jenis_iuran_id'], $item['periode_jenis'], $validated['periode_bulan'])) {
                        $duplicates++;
                        continue;
                    }

                    Iuran::create([
                        'kk_id' => $family['kk_id'],
                        'jenis_iuran_id' => $item['jenis_iuran_id'],
                        'nominal' => $item['nominal'],
                        'periode_bulan' => $validated['periode_bulan'],
                        'status' => 'belum_bayar',
                        'jatuh_tempo' => $this->calculateJatuhTempo($item['periode_jenis'], $validated['periode_bulan']),
                        'denda_terlambatan' => 0,
                        'keterangan' => 'Generate otomatis periode '.$this->formatPeriode($validated['periode_bulan']),
                        'created_by' => $request->user()->id,
                    ]);
                    $generated++;
                }
            }

            return compact('generated', 'duplicates');
        });

        $this->logActivity($request, 'generate_iuran', 'iuran', "Generate tagihan periode {$validated['periode_bulan']}: {$result['generated']} dibuat, {$result['duplicates']} duplikat di-skip");

        return response()->json(['data' => $result + ['periode' => $validated['periode_bulan']]]);
    }

    /**
     * Build preview list — keluarga aktif dalam scope, dengan koneksi iuran aktif.
     */
    private function buildPreview(Request $request, array $validated): array
    {
        $user = $request->user();

        // Semua status keluarga (Tetap/Domisili/Non Domisili/Pendatang) tetap ditagih — arsip = soft delete (auto-terkecuali).
        $query = Keluarga::with(['kepalaKeluarga:id,nama_lengkap', 'wilayah:id,nama'])
            ->with(['keluargaIuran' => fn ($q) => $q->where('status_aktif', true)->with('jenisIuran')]);
        $query = $this->scopeKeluarga($query);
        if (! empty($validated['rt_id'])) {
            $query->where('rt_id', $validated['rt_id']);
        }

        $jenisFilter = isset($validated['jenis_iuran_ids'])
            ? array_map('intval', (array) $validated['jenis_iuran_ids']) // query string selalu string — cast utk in_array strict
            : null;
        $preview = [];
        $totalNominal = 0;

        foreach ($query->get() as $keluarga) {
            $items = [];
            $skip = [];
            foreach ($keluarga->keluargaIuran as $conn) {
                $jenis = $conn->jenisIuran;
                if (! $jenis || ! $jenis->is_aktif) {
                    continue;
                }
                if ($jenisFilter && ! in_array($jenis->id, $jenisFilter, true)) {
                    continue;
                }
                $nominal = $conn->nominal_custom ?? $jenis->jumlah;

                // Idempotency semantic per jenis:
                // bulanan → per bulan · tahunan → per tahun · sekali → selamanya.
                $existing = $this->findExistingTagihan($keluarga->id, $jenis->id, $jenis->periode, $validated['periode_bulan']);
                if ($existing) {
                    $skip[] = [
                        'jenis_iuran_id' => $jenis->id,
                        'jenis_iuran' => $jenis->nama,
                        'nominal' => (float) $nominal,
                        'alasan' => match ($jenis->periode) {
                            'tahunan' => 'sudah ditagih tahun ini ('.$existing->periode_bulan.')',
                            'sekali' => 'sudah pernah ditagih ('.$existing->periode_bulan.') — iuran sekali bayar',
                            default => 'sudah ada di periode ini',
                        },
                    ];
                    continue;
                }

                $items[] = [
                    'jenis_iuran_id' => $jenis->id,
                    'jenis_iuran' => $jenis->nama,
                    'nominal' => (float) $nominal,
                    'periode_jenis' => $jenis->periode,
                ];
                $totalNominal += (float) $nominal;
            }
            if ($items === [] && $skip === []) {
                continue; // tidak ada koneksi aktif → skip
            }

            $preview[] = [
                'kk_id' => $keluarga->id,
                'no_kk' => $keluarga->no_kk,
                'kepala_keluarga' => $keluarga->kepalaKeluarga?->nama_lengkap ?? '-',
                'rt' => $keluarga->wilayah?->nama,
                'iurans' => $items,
                'skip' => $skip,
                'total' => array_sum(array_column($items, 'nominal')),
                'sudah_ada' => count($skip),
            ];
        }

        return [
            'preview' => $preview,
            'summary' => [
                'total_families' => count($preview),
                'total_iuran' => array_sum(array_map(fn ($f) => count($f['iurans']), $preview)),
                'total_skip' => array_sum(array_map(fn ($f) => count($f['skip']), $preview)),
                'total_nominal' => $totalNominal,
                'periode' => $validated['periode_bulan'],
            ],
        ];
    }

    /**
     * Tagihan existing per semantic jenis — dasar idempotency generate.
     * bulanan: bulan sama · tahunan: tahun sama (bulan apapun) · sekali: kapanpun.
     */
    private function findExistingTagihan(int $kkId, int $jenisId, string $periodeJenis, string $periodeBulan): ?Iuran
    {
        $query = Iuran::where('kk_id', $kkId)->where('jenis_iuran_id', $jenisId);

        return match ($periodeJenis) {
            'tahunan' => (clone $query)->where('periode_bulan', 'like', substr($periodeBulan, 0, 4).'%')->first(),
            'sekali' => (clone $query)->first(),
            default => (clone $query)->where('periode_bulan', $periodeBulan)->first(),
        };
    }

    private function calculateJatuhTempo(string $periodeJenis, string $periodeBulan): string
    {
        $date = Carbon::createFromFormat('Y-m', $periodeBulan)->startOfMonth();

        return match ($periodeJenis) {
            'tahunan' => $date->setUnitNoOverflow('day', 30, 'month')->format('Y-m-d'),
            'sekali' => $date->setUnitNoOverflow('day', 15, 'month')->format('Y-m-d'),
            default => $date->setUnitNoOverflow('day', 25, 'month')->format('Y-m-d'),
        };
    }

    private function formatPeriode(string $periode): string
    {
        return Carbon::createFromFormat('Y-m', $periode)->locale('id')->translatedFormat('F Y');
    }
}
