<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Portal publik — tanpa auth, rate-limited, data tersanitasi server-side.
 */
class PortalController extends Controller
{
    /**
     * POST /api/portal/cek-warga — cari by NIK (exact) atau nama (LIKE, first match).
     */
    public function cekWarga(Request $request): JsonResponse
    {
        if ($this->tooManyAttempts($request, 'cek-warga')) {
            return $this->rateLimited();
        }

        $validated = $request->validate([
            'search' => ['required', 'string', 'min:3', 'max:255'],
        ], ['search.min' => 'Minimal 3 karakter untuk pencarian.']);

        $search = $validated['search'];
        $warga = null;

        if (strlen($search) === 16 && ctype_digit($search)) {
            $warga = Warga::with('keluarga.wilayah.parent.parent')->where('nik', $search)->first();
        }
        $warga ??= Warga::with('keluarga.wilayah.parent.parent')->where('nama_lengkap', 'like', "%{$search}%")->first();

        if (! $warga) {
            return response()->json(['message' => 'Data warga tidak ditemukan. Periksa kembali NIK atau nama.'], 404);
        }

        $this->logPortalAccess($request, 'warga', $warga->nik);

        return response()->json(['data' => $this->sanitizeWarga($warga)]);
    }

    /**
     * POST /api/portal/cek-keluarga — cari by no KK.
     */
    public function cekKeluarga(Request $request): JsonResponse
    {
        if ($this->tooManyAttempts($request, 'cek-keluarga')) {
            return $this->rateLimited();
        }

        $validated = $request->validate([
            'no_kk' => ['required', 'digits:16'],
        ], ['no_kk.digits' => 'Nomor KK harus 16 digit.']);

        $keluarga = Keluarga::with(['anggotaKeluarga', 'kepalaKeluarga:id,nama_lengkap', 'wilayah.parent.parent'])
            ->where('no_kk', $validated['no_kk'])
            ->first();

        if (! $keluarga) {
            return response()->json(['message' => 'Data keluarga tidak ditemukan. Periksa kembali nomor KK.'], 404);
        }

        $this->logPortalAccess($request, 'keluarga', $keluarga->no_kk);

        return response()->json(['data' => [
            'no_kk' => $this->mask($keluarga->no_kk),
            'alamat' => \Str::limit($keluarga->alamat_domisili ?? $keluarga->alamat_kk, 30, '...'),
            'rt' => $keluarga->wilayah?->nama,
            'rw' => $keluarga->wilayah?->parent?->nama,
            'kelurahan' => $keluarga->wilayah?->parent?->parent?->nama,
            'kepala_keluarga' => $keluarga->kepalaKeluarga?->nama_lengkap,
            'jumlah_anggota' => $keluarga->anggotaKeluarga->count(),
            'is_verified' => (bool) $keluarga->is_verified, // KK terverifikasi petugas kelurahan
            'anggota' => $keluarga->anggotaKeluarga->map(fn ($w) => [
                'nama' => $this->maskNama($w->nama_lengkap),
                'hubungan' => $w->hubungan_keluarga,
                'jenis_kelamin' => $w->jenis_kelamin,
                'is_verified' => (bool) $w->is_verified,
            ]),
        ]]);
    }

    /**
     * POST /api/portal/cek-iuran — status iuran by NIK (12 bulan terakhir).
     */
    public function cekIuran(Request $request): JsonResponse
    {
        if ($this->tooManyAttempts($request, 'cek-iuran')) {
            return $this->rateLimited();
        }

        $validated = $request->validate([
            'nik' => ['required', 'digits:16'],
        ], ['nik.digits' => 'NIK harus 16 digit.']);

        $warga = Warga::where('nik', $validated['nik'])->first();
        if (! $warga) {
            return response()->json(['message' => 'Data warga tidak ditemukan. Periksa kembali NIK.'], 404);
        }
        if (! $warga->kk_id) {
            return response()->json(['message' => 'Warga belum terdaftar dalam kartu keluarga. Hubungi RT setempat.'], 404);
        }

        $iurans = Iuran::with(['jenisIuran:id,nama', 'pembayaran'])
            ->where('kk_id', $warga->kk_id)
            ->where('status', '!=', 'batal')
            ->where('periode_bulan', '>=', now()->subMonths(11)->startOfMonth()->format('Y-m')) // 12 bulan terakhir (window), bukan 12 baris
            ->orderByDesc('periode_bulan')
            ->get();

        $this->logPortalAccess($request, 'iuran', $warga->nik);

        $tagihan = $iurans->where('status', 'belum_bayar');

        return response()->json(['data' => [
            'nama' => $warga->nama_lengkap,
            'nik' => $this->mask($warga->nik),
            'ringkasan' => [
                'jumlah_tagihan' => $iurans->count(),
                'jumlah_tunggakan' => $tagihan->count(),
                'total_tunggakan' => (float) $tagihan->sum('nominal'),
                'jumlah_lunas' => $iurans->where('status', 'lunas')->count(),
            ],
            'detail' => $iurans->values()->map(fn ($i) => [
                'jenis' => $i->jenisIuran?->nama ?? '-',
                'periode' => $this->formatPeriode($i->periode_bulan),
                'nominal' => (float) $i->nominal,
                'status' => $i->status,
                'jatuh_tempo' => $i->jatuh_tempo?->format('d/m/Y'),
                'dibayar' => (float) $i->pembayaran->sum('jumlah_bayar'),
                'dibayar_pada' => $i->status === 'lunas'
                    ? $i->pembayaran->max('created_at')?->timezone(config('app.timezone'))->translatedFormat('d/m/Y')
                    : null,
            ]),
        ]]);
    }

    /* ── helpers ── */

    private function sanitizeWarga(Warga $warga): array
    {
        return [
            'nama_lengkap' => $warga->nama_lengkap,
            'nik' => $this->mask($warga->nik),
            'tempat_lahir' => $warga->tempat_lahir,
            'tanggal_lahir' => $warga->tanggal_lahir
                ? Carbon::parse($warga->tanggal_lahir)->locale('id')->translatedFormat('d F').' ****'
                : null,
            'jenis_kelamin' => $warga->jenis_kelamin,
            'agama' => $warga->agama,
            'status_perkawinan' => $warga->status_perkawinan,
            'pekerjaan' => $warga->pekerjaan,
            'hubungan_keluarga' => $warga->hubungan_keluarga,
            'is_verified' => (bool) $warga->is_verified, // data terverifikasi petugas kelurahan
            'no_telepon' => $warga->no_telepon ? substr($warga->no_telepon, 0, 3).'***'.substr($warga->no_telepon, -3) : null,
            'keluarga' => $warga->keluarga ? [
                'no_kk' => $this->mask($warga->keluarga->no_kk),
                'alamat' => \Str::limit($warga->keluarga->alamat_domisili ?? $warga->keluarga->alamat_kk, 20, '...'),
                'rt' => $warga->keluarga->wilayah?->nama,
                'rw' => $warga->keluarga->wilayah?->parent?->nama,
                'kelurahan' => $warga->keluarga->wilayah?->parent?->parent?->nama,
                'status_domisili' => $warga->keluarga->status_keluarga,
            ] : null,
        ];
    }

    private function mask(string $num): string
    {
        return substr($num, 0, 6).'******'.substr($num, -4);
    }

    /**
     * Nama disamarkan: kata PERTAMA & TERAKHIR utuh, hanya kata tengah
     * yang di-blur inisial ("Tutik T*** Wahyuningsih", "Fakhri A*** R*** Al-rasyid"→
     * 2 kata: utuh semua · 1 kata: utuh). Minimal 2 kata tetap terbaca.
     */
    private function maskNama(?string $nama): ?string
    {
        if (! $nama) {
            return null;
        }

        $parts = preg_split('/\s+/', trim($nama));
        $count = count($parts);
        if ($count <= 2) {
            return implode(' ', $parts);
        }

        $parts = array_map(function ($p, $i) use ($count) {
            return ($i === 0 || $i === $count - 1) ? $p : mb_substr($p, 0, 1).'***';
        }, $parts, array_keys($parts));

        return implode(' ', $parts);
    }

    private function formatPeriode(string $periode): string
    {
        return Carbon::createFromFormat('Y-m', $periode)->locale('id')->translatedFormat('F Y');
    }

    private function tooManyAttempts(Request $request, string $key): bool
    {
        $k = $key.':'.$request->ip();
        if (RateLimiter::tooManyAttempts($k, 5)) {
            return true;
        }
        RateLimiter::hit($k, 60);

        return false;
    }

    private function rateLimited(): JsonResponse
    {
        return response()->json(['message' => 'Terlalu banyak permintaan. Coba lagi dalam 1 menit.'], 429);
    }

    private function logPortalAccess(Request $request, string $module, string $identifier): void
    {
        try {
            \App\Models\AktivitasLog::create([
                'user_id' => null,
                'action' => 'public_access',
                'module' => 'portal_'.$module,
                'description' => "Portal check {$module}: ".$this->mask($identifier),
                'old_data' => null,
                'new_data' => json_encode(['ip' => $request->ip()]),
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? 'Unknown', 0, 255),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Portal log gagal: '.$e->getMessage());
        }
    }
}
