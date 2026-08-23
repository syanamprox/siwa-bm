<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Controllers\Api\Concerns\ScopesToWilayah;
use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class WargaController extends Controller
{
    use ScopesToWilayah, LogsActivity;

    /**
     * GET /api/warga — list + filter + paginate (scoped per role).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Warga::with(['keluarga:id,no_kk,rt_id,kepala_keluarga_id', 'keluarga.wilayah:id,nama', 'keluarga.kepalaKeluarga:id,nama_lengkap']);
        $query = $this->scopeWarga($query);

        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('nik', 'like', "%{$s}%")
                    ->orWhere('nama_lengkap', 'like', "%{$s}%")
                    ->orWhereHas('keluarga', fn ($k) => $k->where('no_kk', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->input('jenis_kelamin'));
        }
        if ($request->filled('agama')) {
            $query->where('agama', $request->input('agama'));
        }
        if ($request->filled('pendidikan')) {
            $query->where('pendidikan_terakhir', $request->input('pendidikan'));
        }
        if ($request->filled('meninggal')) {
            $query->where('meninggal', $request->boolean('meninggal'));
        }
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->boolean('is_verified'));
        }
        if ($request->filled('kk_id')) {
            $query->where('kk_id', $request->input('kk_id'));
        }
        if ($request->input('status_kk') === 'punya_kk') {
            $query->whereNotNull('kk_id');
        }
        if ($request->input('status_kk') === 'tanpa_kk') {
            $query->whereNull('kk_id');
        }

        $perPage = min((int) $request->input('per_page', 15), 100);
        $page = $query->orderBy($request->input('sort_field', 'created_at'), $request->input('sort_direction', 'desc'))
            ->paginate($perPage);

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

    public function show(Request $request, Warga $warga): JsonResponse
    {
        $this->authorizeWarga($request, $warga);
        $warga->load(['keluarga.wilayah.parent.parent', 'keluarga.kepalaKeluarga:id,nama_lengkap']);

        return response()->json(['data' => $warga]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $data = collect($validated)->except(['foto_ktp', 'foto_ktp_data'])->all();

        if ($request->hasFile('foto_ktp') || ! empty($validated['foto_ktp_data'])) {
            $data['foto_ktp'] = $this->storeKtpPhoto(
                $request->hasFile('foto_ktp') ? $request->file('foto_ktp') : $validated['foto_ktp_data'],
                $validated['nik']
            );
        }

        $warga = Warga::create($data + ['created_by' => $request->user()->id]);
        $this->logActivity($request, 'create', 'warga', "Tambah warga {$warga->nama_lengkap} (NIK {$warga->nik})", null, $warga->only(['nik', 'nama_lengkap', 'kk_id']));

        return response()->json(['data' => $warga->load('keluarga:id,no_kk')], 201);
    }

    public function update(Request $request, Warga $warga): JsonResponse
    {
        $this->authorizeWarga($request, $warga);
        $validated = $request->validate($this->rules($warga->id));
        $data = collect($validated)->except(['foto_ktp', 'foto_ktp_data'])->all();
        $old = $warga->only(['nik', 'nama_lengkap', 'kk_id', 'hubungan_keluarga']);

        if ($request->hasFile('foto_ktp') || ! empty($validated['foto_ktp_data'])) {
            if ($warga->foto_ktp) {
                \Storage::disk('public')->delete($warga->foto_ktp);
            }
            $data['foto_ktp'] = $this->storeKtpPhoto(
                $request->hasFile('foto_ktp') ? $request->file('foto_ktp') : $validated['foto_ktp_data'],
                $validated['nik']
            );
        }

        $warga->update($data + ['updated_by' => $request->user()->id]);
        $this->logActivity($request, 'update', 'warga', "Ubah warga {$warga->nama_lengkap} (NIK {$warga->nik})", $old, $warga->fresh()->only(['nik', 'nama_lengkap', 'kk_id', 'hubungan_keluarga']));

        return response()->json(['data' => $warga->fresh()->load('keluarga:id,no_kk')]);
    }

    /**
     * Simpan foto KTP — kompres (max width 1200px) + konversi WebP via
     * Intervention Image (GD). Selalu ringan, format konsisten .webp.
     *
     * @param  UploadedFile|string  $source  File multipart atau data-URL base64.
     */
    private function storeKtpPhoto(UploadedFile|string $source, string $nik): string
    {
        if (is_string($source) && strlen($source) > 4_000_000) {
            throw ValidationException::withMessages([
                'foto_ktp_data' => 'Ukuran foto terlalu besar (maks 2MB).',
            ]);
        }

        $manager = new ImageManager(new GdDriver);
        $image = is_string($source)
            ? $manager->decodeDataUri($source)
            : $manager->decodeSplFileInfo($source);

        $webp = (string) $image->scaleDown(width: 1200)->encode(new WebpEncoder(quality: 82));

        $path = "documents/ktp/ktp_{$nik}_".time().'.webp';
        \Storage::disk('public')->put($path, $webp);

        return $path;
    }

    public function destroy(Request $request, Warga $warga): JsonResponse
    {
        $this->authorizeWarga($request, $warga);

        if ($warga->hubungan_keluarga === 'Kepala Keluarga') {
            return response()->json(['message' => 'Tidak dapat menghapus Kepala Keluarga. Ganti kepala keluarga terlebih dahulu.'], 422);
        }

        $old = $warga->only(['nik', 'nama_lengkap', 'kk_id']);
        if ($warga->foto_ktp) {
            \Storage::disk('public')->delete($warga->foto_ktp);
        }
        $warga->delete();
        $this->logActivity($request, 'delete', 'warga', "Hapus warga {$old['nama_lengkap']} (NIK {$old['nik']})", $old, null);

        return response()->json(['message' => 'Warga dihapus.']);
    }

    /**
     * POST /api/warga/{warga}/verify — tandai data terverifikasi petugas (admin only).
     * Body opsional {verified: false} untuk MEMBATALKAN verifikasi (status kembali
     * belum, verified_by/at dikosongkan). Idempotent dua arah.
     */
    public function verify(Request $request, Warga $warga): JsonResponse
    {
        abort_unless($request->user()->role === 'admin', 403, 'Hanya admin yang dapat memverifikasi data warga.');
        $this->authorizeWarga($request, $warga);

        $verified = $request->boolean('verified', true);

        if ($verified && ! $warga->is_verified) {
            $warga->update([
                'is_verified' => true,
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);
            $this->logActivity($request, 'verify', 'warga', "Verifikasi data warga {$warga->nama_lengkap} (NIK {$warga->nik})", null, ['is_verified' => true]);
        } elseif (! $verified && $warga->is_verified) {
            $warga->update([
                'is_verified' => false,
                'verified_by' => null,
                'verified_at' => null,
            ]);
            $this->logActivity($request, 'unverify', 'warga', "Batalkan verifikasi data warga {$warga->nama_lengkap} (NIK {$warga->nik})", ['is_verified' => true], ['is_verified' => false]);
        }

        return response()->json(['data' => $warga->fresh()->only(['id', 'nik', 'is_verified', 'verified_by', 'verified_at'])]);
    }

    /**
     * GET /api/warga/statistics — scoped.
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = Warga::query();
        $query = $this->scopeWarga($query);

        $data = [
            'total_warga' => (clone $query)->count(),
            'warga_laki' => (clone $query)->where('jenis_kelamin', 'L')->count(),
            'warga_perempuan' => (clone $query)->where('jenis_kelamin', 'P')->count(),
            'warga_dengan_kk' => (clone $query)->whereNotNull('kk_id')->count(),
            'warga_tanpa_kk' => (clone $query)->whereNull('kk_id')->count(),
            'warga_meninggal' => (clone $query)->where('meninggal', true)->count(),
            'warga_terverifikasi' => (clone $query)->where('is_verified', true)->count(),
            'warga_by_agama' => (clone $query)->selectRaw('agama, COUNT(*) as total')->groupBy('agama')->pluck('total', 'agama'),
            'warga_by_pendidikan' => (clone $query)->selectRaw('pendidikan_terakhir, COUNT(*) as total')->groupBy('pendidikan_terakhir')->pluck('total', 'pendidikan_terakhir'),
        ];

        return response()->json(['data' => $data]);
    }

    private function rules(?int $ignoreId = null): array
    {
        $nikUnique = $ignoreId
            ? 'required|digits:16|unique:wargas,nik,'.$ignoreId
            : 'required|digits:16|unique:wargas,nik';

        return [
            'nik' => $nikUnique,
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'golongan_darah' => ['nullable', 'in:A,B,AB,O,A+,B+,AB+,O+,A-,B-,AB-,O-,Tidak Tahu'],
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu'],
            'status_perkawinan' => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
            'pekerjaan' => ['required', 'string', 'max:100'],
            'pendidikan_terakhir' => ['required', 'in:Tidak Sekolah,SD/sederajat,SMP/sederajat,SMA/sederajat,D1,D2,D3,D4/S1,S2,S3'],
            'kewarganegaraan' => ['required', 'in:WNI,WNA'],
            'hubungan_keluarga' => ['required', 'string', 'max:50'],
            'kk_id' => ['nullable', 'exists:keluargas,id'],
            'meninggal' => ['nullable', 'boolean'],
            'tanggal_meninggal' => ['nullable', 'date', 'after:tanggal_lahir', 'before_or_equal:today'],
            'foto_ktp' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'foto_ktp_data' => ['nullable', 'string', 'starts_with:data:image'],
        ];
    }

    /**
     * 404 jika warga di luar scope user (jangan bocorkan keberadaannya).
     */
    private function authorizeWarga(Request $request, Warga $warga): void
    {
        if ($this->isUnrestricted($request->user())) {
            return;
        }
        $inScope = $warga->keluarga()
            ->whereIn('keluargas.rt_id', $this->rtIdsForUser($request->user()))
            ->exists();
        abort_if(! $inScope, 404, 'Warga tidak ditemukan.');
    }
}
