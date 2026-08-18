<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Controllers\Api\Concerns\ScopesToWilayah;
use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    use ScopesToWilayah, LogsActivity;

    /**
     * GET /api/warga — list + filter + paginate (scoped per role).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Warga::with(['keluarga:id,no_kk,rt_id', 'keluarga.wilayah:id,nama', 'keluarga.kepalaKeluarga:id,nama_lengkap']);
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

        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            $data['foto_ktp'] = $file->storeAs('documents/ktp', "ktp_{$validated['nik']}_".time().'.'.$file->extension(), 'public');
        } elseif (! empty($validated['foto_ktp_data'])) {
            $data['foto_ktp'] = $this->storeBase64Image($validated['foto_ktp_data'], $validated['nik']);
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

        if ($request->hasFile('foto_ktp')) {
            if ($warga->foto_ktp) {
                \Storage::disk('public')->delete($warga->foto_ktp);
            }
            $file = $request->file('foto_ktp');
            $data['foto_ktp'] = $file->storeAs('documents/ktp', "ktp_{$validated['nik']}_".time().'.'.$file->extension(), 'public');
        } elseif (! empty($validated['foto_ktp_data'])) {
            if ($warga->foto_ktp) {
                \Storage::disk('public')->delete($warga->foto_ktp);
            }
            $data['foto_ktp'] = $this->storeBase64Image($validated['foto_ktp_data'], $validated['nik']);
        }

        $warga->update($data + ['updated_by' => $request->user()->id]);
        $this->logActivity($request, 'update', 'warga', "Ubah warga {$warga->nama_lengkap} (NIK {$warga->nik})", $old, $warga->fresh()->only(['nik', 'nama_lengkap', 'kk_id', 'hubungan_keluarga']));

        return response()->json(['data' => $warga->fresh()->load('keluarga:id,no_kk')]);
    }

    /**
     * Simpan data-URL base64 image → path storage. Return path.
     */
    private function storeBase64Image(string $dataUrl, string $nik): string
    {
        [$meta, $content] = explode(',', $dataUrl, 2);
        preg_match('/image\/(\w+)/', $meta, $m);
        $ext = $m[1] ?? 'jpg';

        return \Storage::disk('public')->put(
            "documents/ktp/ktp_{$nik}_".time().'.'.$ext,
            base64_decode($content)
        ) ? "documents/ktp/ktp_{$nik}_".time().'.'.$ext : '';
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
