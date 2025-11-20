<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Warga;
use App\Models\Keluarga;
use App\Models\Iuran;
use App\Models\PembayaranIuran;

class PublicPortalController extends Controller
{
    /**
     * Display the public portal homepage
     */
    public function index()
    {
        return view('portal.index');
    }

    /**
     * Check citizen data by NIK or name
     */
    public function cekWarga(Request $request)
    {
        // Rate limiting: 5 requests per minute per IP
        $key = 'cek-warga:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'code' => 429
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $validator = Validator::make($request->all(), [
            'search' => 'required|string|min:3|max:255',
            'captcha' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simple captcha validation (you may want to use reCAPTCHA)
        if (!$this->validateCaptcha($request->captcha)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid captcha',
                'code' => 422
            ], 422);
        }

        $search = $request->search;
        $warga = null;

        // Try to find by NIK first (exact match)
        if (strlen($search) == 16 && is_numeric($search)) {
            $warga = Warga::with(['keluarga'])
                ->where('nik', $search)
                ->first();
        }

        // If not found by NIK, try by name
        if (!$warga) {
            $warga = Warga::with(['keluarga'])
                ->where('nama_lengkap', 'LIKE', '%' . $search . '%')
                ->first();
        }

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Data warga tidak ditemukan',
                'code' => 404
            ], 404);
        }

        // Sanitize sensitive data for public display
        $data = $this->sanitizeWargaData($warga);

        // Log the public access
        \App\Models\AktivitasLog::create([
            'user_id' => null,
            'action' => 'public_access',
            'module' => 'warga',
            'description' => 'Public portal access check for NIK: ' . substr($warga->nik, 0, 6) . '******',
            'old_data' => null,
            'new_data' => json_encode(['ip' => $request->ip(), 'search_query' => $search]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? 'Unknown'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data warga ditemukan',
            'data' => $data
        ]);
    }

    /**
     * Check family data by KK number
     */
    public function cekKeluarga(Request $request)
    {
        // Rate limiting
        $key = 'cek-keluarga:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'code' => 429
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $validator = Validator::make($request->all(), [
            'no_kk' => 'required|string|min:10|max:20',
            'captcha' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$this->validateCaptcha($request->captcha)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid captcha',
                'code' => 422
            ], 422);
        }

        $keluarga = Keluarga::with(['wargas' => function($query) {
            $query->select('id', 'nama_lengkap', 'hubungan_keluarga', 'jenis_kelamin', 'kk_id');
        }, 'kepalaKeluarga' => function($query) {
            $query->select('id', 'nama_lengkap');
        }])->where('no_kk', $request->no_kk)->first();

        if (!$keluarga) {
            return response()->json([
                'success' => false,
                'message' => 'Data keluarga tidak ditemukan',
                'code' => 404
            ], 404);
        }

        // Sanitize family data for public display
        $data = $this->sanitizeKeluargaData($keluarga);

        // Log the public access
        \App\Models\AktivitasLog::create([
            'user_id' => null,
            'action' => 'public_access',
            'module' => 'keluarga',
            'description' => 'Public portal family check for KK: ' . substr($keluarga->no_kk, 0, 6) . '******',
            'old_data' => null,
            'new_data' => json_encode(['ip' => $request->ip()]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? 'Unknown'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data keluarga ditemukan',
            'data' => $data
        ]);
    }

    /**
     * Check iuran/payment status by NIK
     */
    public function cekIuran(Request $request)
    {
        // Rate limiting
        $key = 'cek-iuran:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'code' => 429
            ], 429);
        }
        RateLimiter::hit($key, 60);

        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|min:16|max:16',
            'captcha' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!$this->validateCaptcha($request->captcha)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid captcha',
                'code' => 422
            ], 422);
        }

        $warga = Warga::where('nik', $request->nik)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'message' => 'Data warga tidak ditemukan',
                'code' => 404
            ], 404);
        }

        // Get iuran data for this warga's keluarga
        $iuranData = Iuran::with(['jenisIuran', 'pembayaranIuran'])
            ->where('kk_id', $warga->kk_id)
            ->orderBy('periode_bulan', 'desc')
            ->take(12) // Last 12 months
            ->get();

        // Sanitize iuran data for public display
        $data = $this->sanitizeIuranData($iuranData, $warga);

        // Log the public access
        \App\Models\AktivitasLog::create([
            'user_id' => null,
            'action' => 'public_access',
            'module' => 'iuran',
            'description' => 'Public portal iuran check for NIK: ' . substr($warga->nik, 0, 6) . '******',
            'old_data' => null,
            'new_data' => json_encode(['ip' => $request->ip()]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? 'Unknown'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data iuran ditemukan',
            'data' => $data
        ]);
    }

    /**
     * Sanitize warga data for public display (hide sensitive information)
     */
    private function sanitizeWargaData($warga)
    {
        return [
            'nama_lengkap' => $warga->nama_lengkap,
            'nik' => substr($warga->nik, 0, 6) . '******' . substr($warga->nik, -4),
            'tempat_lahir' => $warga->tempat_lahir,
            'tanggal_lahir' => $warga->tanggal_lahir,
            'jenis_kelamin' => $warga->jenis_kelamin,
            'golongan_darah' => $warga->golongan_darah,
            'agama' => $warga->agama,
            'status_perkawinan' => $warga->status_perkawinan,
            'pekerjaan' => $warga->pekerjaan,
            'kewarganegaraan' => $warga->kewarganegaraan,
            'pendidikan_terakhir' => $warga->pendidikan_terakhir,
            'no_telepon' => $warga->no_telepon ? substr($warga->no_telepon, 0, 3) . '***' . substr($warga->no_telepon, -3) : null,
            'email' => $warga->email ? substr($warga->email, 0, 3) . '***@***.com' : null,
            'hubungan_keluarga' => $warga->hubungan_keluarga,
            'keluarga' => $this->getKeluargaInfo($warga)
        ];
    }

    /**
     * Helper to get keluarga info safely
     */
    private function getKeluargaInfo($warga)
    {
        if (!$warga->keluarga) return null;

        return [
            'no_kk' => substr($warga->keluarga->no_kk, 0, 6) . '******' . substr($warga->keluarga->no_kk, -4),
            'alamat_kk' => substr($warga->keluarga->alamat_kk, 0, 20) . '...',
            'rt_kk' => $warga->keluarga->rt_kk,
            'rw_kk' => $warga->keluarga->rw_kk,
            'kelurahan_kk' => $warga->keluarga->kelurahan_kk,
            'alamat_domisili' => $warga->keluarga->alamat_domisili ? substr($warga->keluarga->alamat_domisili, 0, 20) . '...' : null,
            'rt' => $warga->keluarga->wilayah ? $warga->keluarga->wilayah->nama : null,
            'rw' => $warga->keluarga->wilayah ? $warga->keluarga->wilayah->parent->nama : null,
            'kelurahan' => $warga->keluarga->wilayah ? $warga->keluarga->wilayah->parent->parent->nama : null,
            'status_domisili_keluarga' => $warga->keluarga->status_domisili_keluarga,
        ];
    }

    /**
     * Sanitize keluarga data for public display
     */
    private function sanitizeKeluargaData($keluarga)
    {
        return [
            'no_kk' => substr($keluarga->no_kk, 0, 6) . '******' . substr($keluarga->no_kk, -4),
            'alamat_kk' => substr($keluarga->alamat_kk, 0, 30) . '...',
            'rt_kk' => $keluarga->rt_kk,
            'rw_kk' => $keluarga->rw_kk,
            'kelurahan_kk' => $keluarga->kelurahan_kk,
            'alamat_domisili' => $keluarga->alamat_domisili ? substr($keluarga->alamat_domisili, 0, 30) . '...' : null,
            'rt' => $keluarga->wilayah ? $keluarga->wilayah->nama : null,
            'rw' => $keluarga->wilayah ? $keluarga->wilayah->parent->nama : null,
            'kelurahan' => $keluarga->wilayah ? $keluarga->wilayah->parent->parent->nama : null,
            'status_domisili_keluarga' => $keluarga->status_domisili_keluarga,
            'tanggal_mulai_domisili_keluarga' => $keluarga->tanggal_mulai_domisili_keluarga,
            'jumlah_anggota' => $keluarga->wargas->count(),
            'kepala_keluarga' => $keluarga->kepalaKeluarga ? $keluarga->kepalaKeluarga->nama_lengkap : null,
            'anggota_keluarga' => $keluarga->wargas->map(function($warga) {
                return [
                    'nama_lengkap' => $warga->nama_lengkap,
                    'hubungan_keluarga' => $warga->hubungan_keluarga,
                    'jenis_kelamin' => $warga->jenis_kelamin,
                ];
            })
        ];
    }

    /**
     * Sanitize iuran data for public display
     */
    private function sanitizeIuranData($iuranData, $warga)
    {
        return [
            'nama_warga' => $warga->nama_lengkap,
            'nik' => substr($warga->nik, 0, 6) . '******' . substr($warga->nik, -4),
            'ringkasan_iuran' => [
                'total_tagihan' => 'Rp ' . number_format($iuranData->where('status', 'belum_bayar')->sum('nominal'), 0, ',', '.'),
                'total_lunas' => 'Rp ' . number_format($iuranData->where('status', 'lunas')->sum('nominal'), 0, ',', '.'),
                'jumlah_tagihan' => $iuranData->count(),
                'jumlah_tunggakan' => $iuranData->where('status', 'belum_bayar')->count(),
                'jumlah_lunas' => $iuranData->where('status', 'lunas')->count(),
            ],
            'detail_iuran' => $iuranData->take(6)->map(function($iuran) { // Limit to 6 recent iuran
                return [
                    'jenis_iuran' => $iuran->jenisIuran ? $iuran->jenisIuran->nama : 'Unknown',
                    'periode' => $this->formatPeriode($iuran->periode_bulan),
                    'nominal' => 'Rp ' . number_format($iuran->nominal, 0, ',', '.'),
                    'status' => $this->formatStatus($iuran->status),
                    'jatuh_tempo' => $iuran->jatuh_tempo ? date('d/m/Y', strtotime($iuran->jatuh_tempo)) : null,
                    'keterangan' => $iuran->keterangan ? substr($iuran->keterangan, 0, 50) . '...' : null,
                ];
            })
        ];
    }

    /**
     * Format periode for better readability
     */
    private function formatPeriode($periode)
    {
        if (strlen($periode) === 7) {
            $year = substr($periode, 0, 4);
            $month = substr($periode, 5, 2);
            $monthNames = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                          '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                          '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
            return ($monthNames[$month] ?? $month) . ' ' . $year;
        }
        return $periode;
    }

    /**
     * Format status for better readability
     */
    private function formatStatus($status)
    {
        $statusMap = [
            'belum_bayar' => 'Belum Bayar',
            'sebagian' => 'Sebagian',
            'lunas' => 'Lunas',
            'batal' => 'Batal'
        ];
        return $statusMap[$status] ?? $status;
    }

    /**
     * Simple captcha validation (replace with reCAPTCHA in production)
     */
    private function validateCaptcha($captcha)
    {
        // This is a simple example - use reCAPTCHA or similar in production
        $sessionCaptcha = session()->get('captcha_code');

        if (!$sessionCaptcha) {
            // Generate new captcha if doesn't exist
            $sessionCaptcha = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
            session()->put('captcha_code', $sessionCaptcha);
        }

        return strtoupper($captcha) === $sessionCaptcha;
    }

    /**
     * Generate new captcha
     */
    public function generateCaptcha()
    {
        $code = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
        session()->put('captcha_code', $code);

        return response()->json([
            'captcha' => $code
        ]);
    }
}
