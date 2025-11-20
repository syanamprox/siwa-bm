<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\JenisIuran;
use App\Models\KeluargaIuran;
use App\Models\PembayaranIuran;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IuranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Iuran::with([
                'keluarga.kepalaKeluarga',
                'jenisIuran',
                'createdBy',
                'pembayaran'
            ])
            ->orderBy('periode_bulan', 'desc')
            ->orderBy('jenis_iuran_id');

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('keluarga', function ($keluarga) use ($search) {
                    $keluarga->where('no_kk', 'like', "%{$search}%")
                            ->orWhereHas('kepalaKeluarga', function ($kepala) use ($search) {
                                $kepala->where('nama_lengkap', 'like', "%{$search}%");
                            });
                })
                ->orWhereHas('jenisIuran', function ($jenis) use ($search) {
                    $jenis->where('nama', 'like', "%{$search}%");
                })
                ->orWhere('periode_bulan', 'like', "%{$search}%");
            });
        }

        // Filter by periode
        if ($request->has('periode') && $request->periode != '') {
            $query->where('periode_bulan', $request->periode);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by keluarga
        if ($request->has('keluarga_id') && $request->keluarga_id != '') {
            $query->where('kk_id', $request->keluarga_id);
        }

        // Filter by jenis iuran
        if ($request->has('jenis_iuran_id') && $request->jenis_iuran_id != '') {
            $query->where('jenis_iuran_id', $request->jenis_iuran_id);
        }

        $iurans = $query->paginate(15);

        // Get data for filters
        $keluargas = Keluarga::orderBy('no_kk')->get();
        $jenisIurans = JenisIuran::orderBy('nama')->get();
        $periodes = Iuran::select('periode_bulan')
            ->distinct()
            ->orderBy('periode_bulan', 'desc')
            ->pluck('periode_bulan');

        return view('admin.iuran.index', compact(
            'iurans',
            'keluargas',
            'jenisIurans',
            'periodes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $keluargas = Keluarga::with(['kepalaKeluarga', 'jenisIuran'])
            ->where('status_keluarga', 'Aktif')
            ->orderBy('no_kk')
            ->get();

        $jenisIurans = JenisIuran::where('is_aktif', true)
            ->orderBy('nama')
            ->get();

        return view('admin.iuran.create', compact('keluargas', 'jenisIurans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kk_id' => 'required|exists:keluargas,id',
            'jenis_iuran_id' => 'required|exists:jenis_iurans,id',
            'periode_bulan' => 'required|date_format:Y-m',
            'nominal' => 'required|numeric|min:0',
            'status' => 'required|in:belum_bayar,sebagian,lunas,batal',
            'jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for duplicates
            $exists = Iuran::where('kk_id', $request->kk_id)
                ->where('jenis_iuran_id', $request->jenis_iuran_id)
                ->where('periode_bulan', $request->periode_bulan)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan untuk keluarga, jenis iuran, dan periode ini sudah ada'
                ], 400);
            }

            $iuran = Iuran::create([
                'kk_id' => $request->kk_id,
                'jenis_iuran_id' => $request->jenis_iuran_id,
                'periode_bulan' => $request->periode_bulan,
                'nominal' => $request->nominal,
                'status' => $request->status,
                'jatuh_tempo' => $request->jatuh_tempo,
                'keterangan' => $request->keterangan,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tagihan iuran berhasil dibuat',
                'data' => $iuran->load(['keluarga', 'jenisIuran'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tagihan iuran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Iuran $iuran)
    {
        $iuran->load([
            'keluarga.kepalaKeluarga',
            'jenisIuran',
            'createdBy',
            'pembayaran'
        ]);

        return view('admin.iuran.show', compact('iuran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Iuran $iuran)
    {
        $iuran->load(['keluarga', 'jenisIuran']);

        $keluargas = Keluarga::with(['kepalaKeluarga'])
            ->orderBy('no_kk')
            ->get();

        $jenisIurans = JenisIuran::where('is_aktif', true)
            ->orderBy('nama')
            ->get();

        return view('admin.iuran.edit', compact('iuran', 'keluargas', 'jenisIurans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Iuran $iuran): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'kk_id' => 'required|exists:keluargas,id',
            'jenis_iuran_id' => 'required|exists:jenis_iurans,id',
            'periode_bulan' => 'required|date_format:Y-m',
            'nominal' => 'required|numeric|min:0',
            'status' => 'required|in:belum_bayar,sebagian,lunas,batal',
            'denda_terlambatan' => 'nullable|numeric|min:0',
            'jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check for duplicates (excluding current record)
            $exists = Iuran::where('kk_id', $request->kk_id)
                ->where('jenis_iuran_id', $request->jenis_iuran_id)
                ->where('periode_bulan', $request->periode_bulan)
                ->where('id', '!=', $iuran->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan untuk keluarga, jenis iuran, dan periode ini sudah ada'
                ], 400);
            }

            $iuran->update([
                'kk_id' => $request->kk_id,
                'jenis_iuran_id' => $request->jenis_iuran_id,
                'periode_bulan' => $request->periode_bulan,
                'nominal' => $request->nominal,
                'status' => $request->status,
                'denda_terlambatan' => $request->denda_terlambatan ?? 0,
                'jatuh_tempo' => $request->jatuh_tempo,
                'keterangan' => $request->keterangan,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tagihan iuran berhasil diupdate',
                'data' => $iuran->load(['keluarga', 'jenisIuran'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate tagihan iuran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Iuran $iuran): JsonResponse
    {
        try {
            // Check if there are any payments
            if ($iuran->pembayaran()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus tagihan yang sudah memiliki pembayaran'
                ], 400);
            }

            $iuran->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tagihan iuran berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tagihan iuran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate bulk iuran for periode
     */
    public function generateBulk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'periode_bulan' => 'required|date_format:Y-m',
            'jenis_iuran_ids' => 'required|array',
            'jenis_iuran_ids.*' => 'exists:jenis_iurans,id',
            'keluarga_ids' => 'nullable|array',
            'keluarga_ids.*' => 'exists:keluargas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $periode = $request->periode_bulan;
            $jenisIuranIds = $request->jenis_iuran_ids;
            $keluargaIds = $request->keluarga_ids ?? Keluarga::where('status_keluarga', 'Aktif')->pluck('id');

            $createdCount = 0;
            $skippedCount = 0;

            foreach ($keluargaIds as $kkId) {
                foreach ($jenisIuranIds as $jiId) {
                    // Check if already exists
                    $exists = Iuran::where('kk_id', $kkId)
                        ->where('jenis_iuran_id', $jiId)
                        ->where('periode_bulan', $periode)
                        ->exists();

                    if (!$exists) {
                        // Get nominal from keluarga_iuran or jenis_iuran
                        $keluargaIuran = KeluargaIuran::where('keluarga_id', $kkId)
                            ->where('jenis_iuran_id', $jiId)
                            ->where('status_aktif', true)
                            ->first();

                        $jenisIuran = JenisIuran::find($jiId);

                        if ($keluargaIuran || $jenisIuran) {
                            Iuran::create([
                                'kk_id' => $kkId,
                                'jenis_iuran_id' => $jiId,
                                'periode_bulan' => $periode,
                                'nominal' => $keluargaIuran?->nominal_custom ?? $jenisIuran->jumlah,
                                'status' => 'belum_bayar',
                                'jatuh_tempo' => Carbon::parse($periode . '-01')->endOfMonth(),
                                'keterangan' => "Generate otomatis periode {$periode}",
                                'created_by' => auth()->id(),
                            ]);
                            $createdCount++;
                        }
                    } else {
                        $skippedCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Generate tagihan selesai. {$createdCount} dibuat, {$skippedCount} sudah ada"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate tagihan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get keluarga jenis iuran connections for dropdown
     */
    public function getKeluargaJenisIuran($keluargaId): JsonResponse
    {
        try {
            $keluarga = Keluarga::findOrFail($keluargaId);
            $jenisIurans = $keluarga->jenisIuran()->wherePivot('status_aktif', true)->get();

            return response()->json([
                'success' => true,
                'data' => $jenisIurans
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $periode = $request->periode ?? date('Y-m');

            $total = Iuran::where('periode_bulan', $periode)->count();
            $belumBayar = Iuran::where('periode_bulan', $periode)->where('status', 'belum_bayar')->count();
            $sebagian = Iuran::where('periode_bulan', $periode)->where('status', 'sebagian')->count();
            $lunas = Iuran::where('periode_bulan', $periode)->where('status', 'lunas')->count();

            $totalNominal = Iuran::where('periode_bulan', $periode)->sum('nominal');
            $totalDenda = Iuran::where('periode_bulan', $periode)->sum('denda_terlambatan');

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'belum_bayar' => $belumBayar,
                    'sebagian' => $sebagian,
                    'lunas' => $lunas,
                    'total_nominal' => $totalNominal,
                    'total_denda' => $totalDenda,
                    'persentase_lunas' => $total > 0 ? round(($lunas / $total) * 100, 2) : 0,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment for iuran
     */
    public function processPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'iuran_id' => 'required|exists:iurans,id',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,gopay,ovo,dana,shopeepay,linkaja,ewallet',
            'nama_bank' => 'required_if:metode_pembayaran,transfer',
            'nomor_rekening' => 'required_if:metode_pembayaran,transfer',
            'nama_pengirim' => 'required_if:metode_pembayaran,transfer',
            'waktu_pembayaran' => 'required_if:metode_pembayaran,transfer',
            'keterangan' => 'nullable|string|max:255',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $iuran = Iuran::findOrFail($request->iuran_id);

            // Check if iuran allows payment
            if (in_array($iuran->status, ['batal', 'lunas'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan ini tidak dapat diproses pembayarannya'
                ], 400);
            }

            // Process file upload
            $buktiPembayaranPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = 'bukti_' . time() . '_' . $iuran->id . '.' . $file->getClientOriginalExtension();
                $buktiPembayaranPath = $file->storeAs('pembayaran', $filename, 'public');
            }

            // Create payment record
            $pembayaran = PembayaranIuran::create([
                'iuran_id' => $iuran->id,
                'jumlah' => $request->jumlah_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'nama_bank' => $request->nama_bank,
                'nomor_rekening' => $request->nomor_rekening,
                'nama_pengirim' => $request->nama_pengirim,
                'waktu_pembayaran' => $request->waktu_pembayaran ? Carbon::parse($request->waktu_pembayaran) : Carbon::now(),
                'status' => $request->metode_pembayaran === 'tunai' ? 'verified' : 'pending',
                'keterangan' => $request->keterangan,
                'bukti_pembayaran' => $buktiPembayaranPath,
                'created_by' => auth()->id(),
            ]);

            // Update iuran status based on total payments
            $totalDibayar = $iuran->pembayaran()->sum('jumlah');
            $totalTagihan = $iuran->total_tagihan ?? $iuran->nominal + $iuran->denda_terlambatan;

            if ($totalDibayar >= $totalTagihan) {
                $iuran->update(['status' => 'lunas']);
            } elseif ($totalDibayar > 0) {
                $iuran->update(['status' => 'sebagian']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses',
                'data' => [
                    'payment_id' => $pembayaran->id,
                    'jumlah' => $pembayaran->jumlah,
                    'status_pembayaran' => $pembayaran->status,
                    'status_iuran' => $iuran->fresh()->status,
                    'total_dibayar' => $totalDibayar,
                    'sisa_tagihan' => max(0, $totalTagihan - $totalDibayar)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history for an iuran
     */
    public function getPaymentHistory($iuranId): JsonResponse
    {
        try {
            $iuran = Iuran::findOrFail($iuranId);

            $payments = $iuran->pembayaran()
                ->with(['createdBy' => function($query) {
                    $query->select('id', 'name');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'jumlah' => $payment->jumlah,
                        'metode_pembayaran' => $payment->metode_pembayaran,
                        'nama_bank' => $payment->nama_bank,
                        'nomor_rekening' => $payment->nomor_rekening,
                        'nama_pengirim' => $payment->nama_pengirim,
                        'waktu_pembayaran' => $payment->waktu_pembayaran,
                        'status' => $payment->status,
                        'keterangan' => $payment->keterangan,
                        'bukti_pembayaran' => $payment->bukti_pembayaran,
                        'created_at' => $payment->created_at,
                        'created_by_name' => $payment->createdBy?->name
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment (for non-cash payments)
     */
    public function verifyPayment(Request $request, PembayaranIuran $pembayaran): JsonResponse
    {
        try {
            if ($pembayaran->status === 'verified') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran sudah diverifikasi'
                ], 400);
            }

            $pembayaran->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => Carbon::now()
            ]);

            // Update iuran status
            $iuran = $pembayaran->iuran;
            $totalDibayar = $iuran->pembayaran()->sum('jumlah');
            $totalTagihan = $iuran->total_tagihan ?? $iuran->nominal + $iuran->denda_terlambatan;

            if ($totalDibayar >= $totalTagihan) {
                $iuran->update(['status' => 'lunas']);
            } elseif ($totalDibayar > 0) {
                $iuran->update(['status' => 'sebagian']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diverifikasi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal verifikasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject payment (for non-cash payments)
     */
    public function rejectPayment(Request $request, PembayaranIuran $pembayaran): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'alasan_penolakan' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($pembayaran->status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran sudah ditolak'
                ], 400);
            }

            $pembayaran->update([
                'status' => 'rejected',
                'alasan_penolakan' => $request->alasan_penolakan,
                'rejected_by' => auth()->id(),
                'rejected_at' => Carbon::now()
            ]);

            // Update iuran status
            $iuran = $pembayaran->iuran;
            $totalDibayar = $iuran->pembayaran()->sum('jumlah');
            $totalTagihan = $iuran->total_tagihan ?? $iuran->nominal + $iuran->denda_terlambatan;

            if ($totalDibayar >= $totalTagihan) {
                $iuran->update(['status' => 'lunas']);
            } elseif ($totalDibayar > 0) {
                $iuran->update(['status' => 'sebagian']);
            } else {
                $iuran->update(['status' => 'belum_bayar']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil ditolak'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}
