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
            'tabel_referensi' => 'warga',
            'id_referensi' => $warga->id,
            'jenis_aktivitas' => 'public_access',
            'deskripsi' => 'Public portal access check for NIK: ' . substr($warga->nik, 0, 6) . '******',
            'data_lama' => null,
            'data_baru' => json_encode(['ip' => $request->ip(), 'search_query' => $search])
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
            'tabel_referensi' => 'keluarga',
            'id_referensi' => $keluarga->id,
            'jenis_aktivitas' => 'public_access',
            'deskripsi' => 'Public portal family check for KK: ' . substr($keluarga->no_kk, 0, 6) . '******',
            'data_lama' => null,
            'data_baru' => json_encode(['ip' => $request->ip()])
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

        // Get iuran data for this warga
        $iuranData = Iuran::with(['jenisIuran', 'pembayaranIuran'])
            ->where('warga_id', $warga->id)
            ->orderBy('periode_bulan', 'desc')
            ->take(12) // Last 12 months
            ->get();

        // Sanitize iuran data for public display
        $data = $this->sanitizeIuranData($iuranData, $warga);

        // Log the public access
        \App\Models\AktivitasLog::create([
            'user_id' => null,
            'tabel_referensi' => 'iuran',
            'id_referensi' => $warga->id,
            'jenis_aktivitas' => 'public_access',
            'deskripsi' => 'Public portal iuran check for NIK: ' . substr($warga->nik, 0, 6) . '******',
            'data_lama' => null,
            'data_baru' => json_encode(['ip' => $request->ip()])
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
            'agama' => $warga->agama,
            'status_perkawinan' => $warga->status_perkawinan,
            'pekerjaan' => $warga->pekerjaan,
            'pendidikan_terakhir' => $warga->pendidikan_terakhir,
            'rt_domisili' => $warga->rt_domisili,
            'rw_domisili' => $warga->rw_domisili,
            'kelurahan_domisili' => $warga->kelurahan_domisili,
            'alamat_domisili' => $warga->alamat_domisili,
            'status_domisili' => $warga->status_domisili,
            'no_telepon' => $warga->no_telepon ? substr($warga->no_telepon, 0, 3) . '***' . substr($warga->no_telepon, -3) : null,
            'email' => $warga->email ? substr($warga->email, 0, 3) . '***@***.com' : null,
            'keluarga' => $warga->keluarga ? [
                'no_kk' => substr($warga->keluarga->no_kk, 0, 6) . '******' . substr($warga->keluarga->no_kk, -4),
                'alamat_kk' => $warga->keluarga->alamat_kk,
                'rt_kk' => $warga->keluarga->rt_kk,
                'rw_kk' => $warga->keluarga->rw_kk,
                'kelurahan_kk' => $warga->keluarga->kelurahan_kk,
            ] : null
        ];
    }

    /**
     * Sanitize keluarga data for public display
     */
    private function sanitizeKeluargaData($keluarga)
    {
        return [
            'no_kk' => substr($keluarga->no_kk, 0, 6) . '******' . substr($keluarga->no_kk, -4),
            'alamat_kk' => $keluarga->alamat_kk,
            'rt_kk' => $keluarga->rt_kk,
            'rw_kk' => $keluarga->rw_kk,
            'kelurahan_kk' => $keluarga->kelurahan_kk,
            'jumlah_anggota' => $keluarga->wargas->count(),
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
                'total_tagihan' => 'Rp ' . number_format($iuranData->where('status', 'belum_bayar')->sum('jumlah'), 0, ',', '.'),
                'total_dibayar' => 'Rp ' . number_format($iuranData->pluck('pembayaranIuran')->flatten()->sum('jumlah_bayar'), 0, ',', '.'),
                'jumlah_tunggakan' => $iuranData->where('status', 'belum_bayar')->count(),
            ],
            'detail_iuran' => $iuranData->map(function($iuran) {
                $pembayaran = $iuran->pembayaranIuran->first();
                return [
                    'jenis_iuran' => $iuran->jenisIuran->nama,
                    'periode' => $iuran->periode_bulan,
                    'nominal' => 'Rp ' . number_format($iuran->nominal, 0, ',', '.'),
                    'status' => $iuran->status,
                    'jatuh_tempo' => $iuran->jatuh_tempo,
                    'tanggal_bayar' => $pembayaran ? $pembayaran->tanggal_bayar : null,
                    'metode_pembayaran' => $pembayaran ? $pembayaran->metode_pembayaran : null,
                ];
            })
        ];
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
