<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Controllers\Api\Concerns\ScopesToWilayah;
use App\Models\KasTransaksi;
use App\Models\KasUnit;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Modul KAS — laporan keuangan per unit kas (wilayah + organisasi).
 * Scoping: admin/camat semua unit; lurah = tree kelurahannya; rw = RW + RT children;
 * rt = RT miliknya. Unit di luar scope → 404 (jangan bocor keberadaan unit).
 */
class KasController extends Controller
{
    use ScopesToWilayah, LogsActivity;

    /* ────────────────────────── AUTH ENDPOINTS ────────────────────────── */

    /**
     * GET /api/kas/units — unit kas dalam scope user.
     */
    public function units(Request $request): JsonResponse
    {
        $query = KasUnit::with('wilayah.parent.parent')->orderBy('jenis')->orderBy('nama');

        $wilayahIds = $this->allowedWilayahIds($request->user());
        if ($wilayahIds !== null) {
            // Unit selalu punya wilayah kecuali kecamatan (wilayah_id null, admin/camat saja)
            $query->whereIn('wilayah_id', $wilayahIds);
        }

        return response()->json(['data' => $query->get()->map(fn ($u) => $this->formatUnit($u))]);
    }

    /**
     * POST /api/kas/units — daftar organisasi kas (musholla, karang taruna, dll).
     */
    public function storeUnit(Request $request): JsonResponse
    {
        if (! in_array($request->user()->role, ['admin', 'lurah', 'rw', 'rt'], true)) {
            abort(403, 'Hanya admin dan pengurus wilayah yang dapat mendaftarkan unit organisasi.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'wilayah_id' => ['required', 'integer', 'exists:wilayahs,id'],
        ], ['wilayah_id.exists' => 'Wilayah tidak ditemukan.']);

        $wilayah = Wilayah::find($validated['wilayah_id']);
        abort_unless(in_array($wilayah->tingkat, ['RT', 'RW', 'Kelurahan'], true), 422, 'Organisasi hanya boleh menempel di wilayah RT, RW, atau Kelurahan.');

        // Non-admin: wilayah harus dalam scope (404 — jangan bocor)
        $wilayahIds = $this->allowedWilayahIds($request->user());
        if ($wilayahIds !== null && ! in_array($wilayah->id, $wilayahIds, true)) {
            abort(404, 'Wilayah tidak ditemukan.');
        }

        if (KasUnit::where('jenis', 'organisasi')->where('wilayah_id', $wilayah->id)->where('nama', $validated['nama'])->exists()) {
            return response()->json(['message' => "Sudah ada unit organisasi bernama {$validated['nama']} di wilayah ini."], 422);
        }

        $unit = KasUnit::create([
            'nama' => $validated['nama'],
            'jenis' => 'organisasi',
            'wilayah_id' => $wilayah->id,
            'created_by' => $request->user()->id,
        ]);

        $this->logActivity($request, 'create', 'kas', "Daftar unit kas organisasi {$unit->nama} under {$wilayah->nama}", null, ['unit_id' => $unit->id, 'wilayah_id' => $wilayah->id]);

        return response()->json(['data' => $this->formatUnit($unit->load('wilayah.parent'))], 201);
    }

    /**
     * DELETE /api/kas/units/{unit} — soft delete, hanya admin + unit organisasi.
     */
    public function destroyUnit(Request $request, KasUnit $unit): JsonResponse
    {
        $wilayahIds = $this->allowedWilayahIds($request->user());
        if ($wilayahIds !== null && ($unit->wilayah_id === null || ! in_array($unit->wilayah_id, $wilayahIds, true))) {
            abort(404, 'Unit kas tidak ditemukan.');
        }

        if ($request->user()->role !== 'admin' || $unit->jenis !== 'organisasi') {
            abort(403, 'Hanya admin dapat menghapus unit organisasi.');
        }

        $this->logActivity($request, 'delete', 'kas', "Hapus unit kas {$unit->nama}", $unit->only(['id', 'nama', 'jenis', 'wilayah_id']));

        $unit->delete();

        return response()->json(['data' => null, 'message' => 'Unit kas organisasi dihapus.']);
    }

    /**
     * GET /api/kas/summary?unit_id=&bulan=YYYY-M — ringkasan kas per unit.
     */
    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'unit_id' => ['required', 'integer'],
            'bulan' => ['nullable', 'date_format:Y-m'],
        ]);

        $unit = $this->findUnitInScope($request, (int) $validated['unit_id']);
        $bulan = $validated['bulan'] ?? now()->format('Y-m');

        return response()->json(['data' => $this->buildSummary($unit, $bulan)]);
    }

    /**
     * POST /api/kas/transaksis — catat transaksi manual (sumber selalu 'manual').
     */
    public function storeTrx(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kas_unit_id' => ['required', 'integer'],
            'tipe' => ['required', 'in:masuk,keluar'],
            'jumlah' => ['required', 'numeric', 'min:100'],
            'kategori' => ['required', 'string', 'in:'.implode(',', KasUnit::KATEGORI)],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'tanggal' => ['required', 'date'],
        ]);

        $unit = $this->findUnitInScope($request, (int) $validated['kas_unit_id']);

        $trx = KasTransaksi::create([
            'kas_unit_id' => $unit->id,
            'tipe' => $validated['tipe'],
            'sumber' => 'manual',
            'jumlah' => $validated['jumlah'],
            'kategori' => $validated['kategori'],
            'keterangan' => $validated['keterangan'] ?? null,
            'tanggal' => $validated['tanggal'],
            'created_by' => $request->user()->id,
        ]);

        $this->logActivity($request, 'create', 'kas', "Transaksi kas {$validated['tipe']} Rp ".number_format((float) $validated['jumlah'], 0, ',', '.')." ({$validated['kategori']}) di {$unit->nama}", null, ['trx_id' => $trx->id, 'unit_id' => $unit->id]);

        return response()->json(['data' => [
            'id' => $trx->id,
            'kas_unit_id' => $trx->kas_unit_id,
            'tipe' => $trx->tipe,
            'sumber' => $trx->sumber,
            'jumlah' => (float) $trx->jumlah,
            'kategori' => $trx->kategori,
            'keterangan' => $trx->keterangan,
            'tanggal' => $trx->tanggal->toDateString(),
        ]], 201);
    }

    /**
     * DELETE /api/kas/transaksis/{trx} — hanya transaksi manual dalam scope.
     */
    public function destroyTrx(Request $request, KasTransaksi $trx): JsonResponse
    {
        $unit = $this->findUnitInScope($request, $trx->kas_unit_id);

        if ($trx->sumber !== 'manual') {
            abort(403, 'Transaksi hasil pembayaran iuran tidak dapat dihapus manual.');
        }

        $this->logActivity($request, 'delete', 'kas', "Hapus transaksi kas {$trx->tipe} Rp ".number_format((float) $trx->jumlah, 0, ',', '.')." di {$unit->nama}", $trx->only(['id', 'tipe', 'sumber', 'jumlah', 'kategori', 'tanggal']));

        $trx->delete();

        return response()->json(['data' => null, 'message' => 'Transaksi kas dihapus.']);
    }

    /* ────────────────────────── PORTAL PUBLIC ────────────────────────── */

    /**
     * GET /api/portal/kas/units — daftar ringkas semua unit aktif (tanpa auth).
     */
    public function unitsPublic(Request $request): JsonResponse
    {
        if ($this->tooManyAttempts($request, 'portal-kas-units')) {
            return $this->rateLimited();
        }

        return response()->json(['data' => KasUnit::with('wilayah.parent.parent')
            ->orderBy('jenis')->orderBy('nama')->get()
            ->map(fn ($u) => $this->formatUnit($u))]);
    }

    /**
     * GET /api/portal/kas/summary?unit_id=&bulan= — ringkasan kas publik (tanpa auth).
     */
    public function summaryPublic(Request $request): JsonResponse
    {
        if ($this->tooManyAttempts($request, 'portal-kas-summary')) {
            return $this->rateLimited();
        }

        $validated = $request->validate([
            'unit_id' => ['required', 'integer'],
            'bulan' => ['nullable', 'date_format:Y-m'],
        ]);

        $unit = KasUnit::find((int) $validated['unit_id']);
        abort_unless($unit, 404, 'Unit kas tidak ditemukan.');

        return response()->json(['data' => $this->buildSummary($unit, $validated['bulan'] ?? now()->format('Y-m'))]);
    }

    /* ────────────────────────── HELPERS ────────────────────────── */

    /**
     * Wilayah IDs yang boleh diakses user (null = tanpa batas / admin+camat).
     * lurah: kelurahan + seluruh RW + RT dibawahnya · rw: RW + RT children · rt: RT pivot.
     */
    private function allowedWilayahIds($user): ?array
    {
        if ($this->isUnrestricted($user)) {
            return null;
        }

        $pivotIds = $user->wilayah()->allRelatedIds();

        if ($user->role === 'lurah') {
            $rwIds = Wilayah::whereIn('parent_id', $pivotIds)->pluck('id');

            return $pivotIds->merge($rwIds)->merge(Wilayah::whereIn('parent_id', $rwIds)->pluck('id'))
                ->unique()->values()->all();
        }

        if ($user->role === 'rw') {
            return $pivotIds->merge(Wilayah::whereIn('parent_id', $pivotIds)->pluck('id'))
                ->unique()->values()->all();
        }

        return $pivotIds->all(); // role rt
    }

    /**
     * Unit kas by id dalam scope user — 404 jika di luar scope.
     */
    private function findUnitInScope(Request $request, int $unitId): KasUnit
    {
        $unit = KasUnit::find($unitId);
        abort_unless($unit, 404, 'Unit kas tidak ditemukan.');

        $wilayahIds = $this->allowedWilayahIds($request->user());
        if ($wilayahIds !== null && ($unit->wilayah_id === null || ! in_array($unit->wilayah_id, $wilayahIds, true))) {
            abort(404, 'Unit kas tidak ditemukan.');
        }

        return $unit;
    }

    /**
     * Shape konsisten unit kas utk list & summary.
     */
    private function formatUnit(KasUnit $unit): array
    {
        $wilayah = $unit->relationLoaded('wilayah') ? $unit->wilayah : $unit->wilayah()->first();
        $kelurahanNama = match ($unit->jenis) {
            'rt' => $wilayah?->parent?->parent?->nama,
            'rw' => $wilayah?->parent?->nama,
            'kelurahan' => $wilayah?->nama,
            default => null,
        };

        return [
            'id' => $unit->id,
            'nama' => $unit->nama,
            'jenis' => $unit->jenis,
            'wilayah_nama' => $wilayah?->nama,
            'kelurahan_nama' => $kelurahanNama,
            'rw_nama' => $unit->jenis === 'rt' ? $wilayah?->parent?->nama : null,
            'parent_label' => $unit->jenis === 'organisasi'
                ? 'under '.$wilayah?->nama
                : $wilayah?->parent?->nama,
        ];
    }

    /**
     * Ringkasan kas satu unit untuk satu bulan (dipakai endpoint auth + portal).
     */
    private function buildSummary(KasUnit $unit, string $bulan): array
    {
        $unit->load('wilayah.parent');

        $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $trx = KasTransaksi::where('kas_unit_id', $unit->id);

        $saldoAwal = (float) (clone $trx)->where('tanggal', '<', $start->toDateString())
            ->sum(DB::raw("CASE WHEN tipe = 'masuk' THEN jumlah ELSE -jumlah END"));

        $inMonth = fn ($q) => $q->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);

        $pemasukanIuran = (float) $inMonth(clone $trx)->where('tipe', 'masuk')->where('sumber', 'iuran')->sum('jumlah');
        $pemasukanLain = (float) $inMonth(clone $trx)->where('tipe', 'masuk')->where('sumber', 'manual')->sum('jumlah');
        $pengeluaran = (float) $inMonth(clone $trx)->where('tipe', 'keluar')->sum('jumlah');

        // Tren 3 bulan (2 bulan sebelum + bulan query)
        $trenStart = $start->copy()->subMonths(2)->startOfMonth();
        $perBulan = (clone $trx)->whereBetween('tanggal', [$trenStart->toDateString(), $end->toDateString()])
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan")
            ->selectRaw("SUM(CASE WHEN tipe = 'masuk' THEN jumlah ELSE 0 END) as masuk")
            ->selectRaw("SUM(CASE WHEN tipe = 'keluar' THEN jumlah ELSE 0 END) as keluar")
            ->groupBy('bulan')->get()->keyBy('bulan');

        $tren = [];
        for ($i = 2; $i >= 0; $i--) {
            $m = $start->copy()->subMonths($i)->format('Y-m');
            $tren[] = [
                'bulan' => $m,
                'masuk' => (float) ($perBulan[$m]->masuk ?? 0),
                'keluar' => (float) ($perBulan[$m]->keluar ?? 0),
            ];
        }

        $tx = $inMonth(KasTransaksi::where('kas_unit_id', $unit->id))
            ->orderBy('tanggal')->orderBy('id')->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'tgl' => $t->tanggal->locale('id')->translatedFormat('d M'),
                'ket' => $t->keterangan,
                'kat' => $t->kategori,
                'masuk' => $t->tipe === 'masuk' ? (float) $t->jumlah : 0,
                'keluar' => $t->tipe === 'keluar' ? (float) $t->jumlah : 0,
                'sumber' => $t->sumber,
            ]);

        return [
            'unit' => $this->formatUnit($unit),
            'periode_label' => $start->locale('id')->translatedFormat('F Y'),
            'saldo_awal' => $saldoAwal,
            'pemasukan_iuran' => $pemasukanIuran,
            'pemasukan_lain' => $pemasukanLain,
            'pengeluaran' => $pengeluaran,
            'saldo_akhir' => $saldoAwal + $pemasukanIuran + $pemasukanLain - $pengeluaran,
            'tren' => $tren,
            'tx' => $tx,
        ];
    }

    private function tooManyAttempts(Request $request, string $key): bool
    {
        $k = $key.':'.$request->ip();
        if (RateLimiter::tooManyAttempts($k, 100)) {
            return true;
        }
        RateLimiter::hit($k, 60);

        return false;
    }

    private function rateLimited(): JsonResponse
    {
        return response()->json(['message' => 'Terlalu banyak permintaan. Coba lagi dalam 1 menit.'], 429);
    }
}
