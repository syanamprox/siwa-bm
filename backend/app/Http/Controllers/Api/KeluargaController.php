<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Http\Controllers\Api\Concerns\ScopesToWilayah;
use App\Models\Keluarga;
use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeluargaController extends Controller
{
    use ScopesToWilayah, LogsActivity;

    public function index(Request $request): JsonResponse
    {
        $query = Keluarga::with(['kepalaKeluarga:id,nama_lengkap,nik', 'wilayah:id,nama,kode,parent_id', 'wilayah.parent:id,nama,kode'])
            ->withCount('anggotaKeluarga');
        $query = $this->scopeKeluarga($query);

        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('no_kk', 'like', "%{$s}%")
                    ->orWhereHas('kepalaKeluarga', fn ($k) => $k->where('nama_lengkap', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('status')) {
            $query->where('status_keluarga', $request->input('status'));
        }
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->input('rt_id'));
        }
        if ($request->input('punya_iuran') === '1') {
            $query->whereHas('keluargaIuran', fn ($k) => $k->where('status_aktif', true));
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

    public function show(Request $request, Keluarga $keluarga): JsonResponse
    {
        $this->authorizeKeluarga($request, $keluarga);
        $keluarga->load([
            'kepalaKeluarga',
            'anggotaKeluarga' => fn ($q) => $q->orderByRaw("FIELD(hubungan_keluarga, 'Kepala Keluarga', 'Suami', 'Istri', 'Anak')")->orderBy('tanggal_lahir'),
            'wilayah.parent.parent',
            'keluargaIuran.jenisIuran:id,nama,kode,jumlah,periode',
        ]);

        return response()->json(['data' => $keluarga]);
    }

    /**
     * Create keluarga + warga anggota sekaligus (mode lama: input_mode multi).
     * Mode baru: tanpa warga_data, anggota ditambah via addMember.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());

        $keluarga = DB::transaction(function () use ($request, $validated) {
            $keluarga = Keluarga::create(
                collect($validated)->except(['warga_data', 'input_mode', 'foto_kk'])->all()
                    + ['created_by' => $request->user()->id]
            );

            foreach ($validated['warga_data'] ?? [] as $wd) {
                $warga = Warga::create($this->wargaFields($wd) + [
                    'kk_id' => $keluarga->id,
                    'created_by' => $request->user()->id,
                ]);
                if ($wd['hubungan_keluarga'] === 'Kepala Keluarga') {
                    $keluarga->update(['kepala_keluarga_id' => $warga->id]);
                }
            }

            return $keluarga;
        });

        $this->logActivity($request, 'create', 'keluarga', "Tambah keluarga KK {$keluarga->no_kk}", null, ['no_kk' => $keluarga->no_kk]);

        return response()->json(['data' => $keluarga->load(['kepalaKeluarga:id,nama_lengkap', 'wilayah:id,nama'])], 201);
    }

    public function update(Request $request, Keluarga $keluarga): JsonResponse
    {
        $this->authorizeKeluarga($request, $keluarga);
        $validated = $request->validate($this->rules($keluarga->id));

        DB::transaction(function () use ($request, $keluarga, $validated) {
            $keluarga->update(
                collect($validated)->except(['warga_data', 'input_mode', 'foto_kk'])->all()
                    + ['updated_by' => $request->user()->id]
            );

            // Ganti kepala keluarga
            $newKepalaId = $validated['kepala_keluarga_id'] ?? null;
            if ($newKepalaId && $newKepalaId !== $keluarga->getOriginal('kepala_keluarga_id')) {
                $old = $keluarga->kepalaKeluarga;
                $old?->update(['hubungan_keluarga' => 'Anggota Keluarga']);
                $new = Warga::findOrFail($newKepalaId);
                $new->update(['hubungan_keluarga' => 'Kepala Keluarga', 'kk_id' => $keluarga->id]);
                $keluarga->update(['kepala_keluarga_id' => $newKepalaId]);
            }
        });

        $this->logActivity($request, 'update', 'keluarga', "Ubah keluarga KK {$keluarga->no_kk}", null, ['no_kk' => $keluarga->no_kk]);

        return response()->json(['data' => $keluarga->fresh()->load(['kepalaKeluarga:id,nama_lengkap', 'wilayah:id,nama', 'anggotaKeluarga'])]);
    }

    public function destroy(Request $request, Keluarga $keluarga): JsonResponse
    {
        $this->authorizeKeluarga($request, $keluarga);

        DB::transaction(function () use ($keluarga) {
            $keluarga->anggotaKeluarga->each->delete();
            $keluarga->delete();
        });
        $this->logActivity($request, 'delete', 'keluarga', "Hapus keluarga KK {$keluarga->no_kk}", ['no_kk' => $keluarga->no_kk], null);

        return response()->json(['message' => 'Keluarga dihapus.']);
    }

    /**
     * PATCH /api/keluarga/{id}/status — Aktif/Pindah/Non-Aktif/Dibubarkan.
     */
    public function updateStatus(Request $request, Keluarga $keluarga): JsonResponse
    {
        $this->authorizeKeluarga($request, $keluarga);
        $validated = $request->validate([
            'status_keluarga' => ['required', 'in:Aktif,Pindah,Non-Aktif,Dibubarkan'],
            'keterangan_status' => ['nullable', 'string', 'max:255'],
        ]);

        $keluarga->updateStatus($validated['status_keluarga'], $validated['keterangan_status'] ?? null);
        $this->logActivity($request, 'update_status', 'keluarga', "Status KK {$keluarga->no_kk} → {$validated['status_keluarga']}", null, $validated);

        return response()->json(['data' => $keluarga->fresh()->only(['id', 'no_kk', 'status_keluarga', 'keterangan_status', 'tanggal_status'])]);
    }

    /**
     * POST /api/keluarga/{id}/members — attach warga existing.
     */
    public function addMember(Request $request, Keluarga $keluarga): JsonResponse
    {
        $this->authorizeKeluarga($request, $keluarga);
        $validated = $request->validate([
            'warga_id' => ['required', 'exists:wargas,id'],
            'hubungan_keluarga' => ['required', 'string', 'max:50'],
        ]);

        $warga = Warga::findOrFail($validated['warga_id']);
        if ($warga->kk_id) {
            return response()->json(['message' => 'Warga sudah terdaftar di keluarga lain.'], 422);
        }

        $keluarga->tambahAnggota($warga, $validated['hubungan_keluarga']);
        $this->logActivity($request, 'add_member', 'keluarga', "Tambah anggota {$warga->nama_lengkap} ke KK {$keluarga->no_kk}", null, ['warga_id' => $warga->id]);

        return response()->json(['data' => $keluarga->fresh()->load('anggotaKeluarga')]);
    }

    /**
     * DELETE /api/keluarga/{id}/members/{warga} — detach (kepala diblok).
     */
    public function removeMember(Request $request, Keluarga $keluarga, Warga $warga): JsonResponse
    {
        $this->authorizeKeluarga($request, $keluarga);

        if (! $keluarga->isAnggota($warga)) {
            return response()->json(['message' => 'Warga bukan anggota keluarga ini.'], 422);
        }
        if ($warga->id === $keluarga->kepala_keluarga_id) {
            return response()->json(['message' => 'Ganti kepala keluarga terlebih dahulu sebelum mengeluarkan kepala keluarga.'], 422);
        }

        $keluarga->hapusAnggota($warga);
        $this->logActivity($request, 'remove_member', 'keluarga', "Keluarkan {$warga->nama_lengkap} dari KK {$keluarga->no_kk}", ['warga_id' => $warga->id], null);

        return response()->json(['data' => $keluarga->fresh()->load('anggotaKeluarga')]);
    }

    public function statistics(Request $request): JsonResponse
    {
        $query = Keluarga::query();
        $query = $this->scopeKeluarga($query);

        $data = [
            'total_keluarga' => (clone $query)->count(),
            'total_anggota' => (clone $query)->withCount('anggotaKeluarga')->get()->sum('anggota_keluarga_count'),
            'kk_tanpa_kepala' => (clone $query)->whereNull('kepala_keluarga_id')->count(),
            'kk_by_status' => (clone $query)->selectRaw('status_keluarga, COUNT(*) as total')->groupBy('status_keluarga')->pluck('total', 'status_keluarga'),
        ];
        $data['rata_rata_anggota'] = $data['total_keluarga'] > 0
            ? round($data['total_anggota'] / $data['total_keluarga'], 2)
            : 0;

        return response()->json(['data' => $data]);
    }

    private function rules(?int $ignoreId = null): array
    {
        $kkUnique = $ignoreId
            ? 'required|digits:16|unique:keluargas,no_kk,'.$ignoreId
            : 'required|digits:16|unique:keluargas,no_kk';

        return [
            'no_kk' => $kkUnique,
            'foto_kk' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'alamat_kk' => ['required', 'string', 'max:500'],
            'rt_kk' => ['nullable', 'string', 'max:10'],
            'rw_kk' => ['nullable', 'string', 'max:10'],
            'kelurahan_kk' => ['nullable', 'string', 'max:100'],
            'kecamatan_kk' => ['nullable', 'string', 'max:100'],
            'kabupaten_kk' => ['nullable', 'string', 'max:100'],
            'provinsi_kk' => ['nullable', 'string', 'max:100'],
            'alamat_domisili' => ['nullable', 'string', 'max:500'],
            'rt_id' => ['required', 'exists:wilayahs,id'],
            'status_domisili_keluarga' => ['required', 'in:Tetap,Non Domisili,Luar,Sementara'],
            'tanggal_mulai_domisili_keluarga' => ['nullable', 'date'],
            'keterangan_status' => ['nullable', 'string', 'max:255'],
            'kepala_keluarga_id' => ['nullable', 'exists:wargas,id'],
            'status_keluarga' => ['nullable', 'in:Aktif,Pindah,Non-Aktif,Dibubarkan'],
            'warga_data' => ['nullable', 'array', 'min:1'],
            'warga_data.*.nik' => ['required_with:warga_data', 'digits:16', 'unique:wargas,nik'],
            'warga_data.*.nama_lengkap' => ['required_with:warga_data', 'string', 'max:255'],
            'warga_data.*.jenis_kelamin' => ['required_with:warga_data', 'in:L,P'],
            'warga_data.*.tempat_lahir' => ['nullable', 'string', 'max:100'],
            'warga_data.*.tanggal_lahir' => ['nullable', 'date'],
            'warga_data.*.agama' => ['nullable', 'string', 'max:50'],
            'warga_data.*.pendidikan_terakhir' => ['nullable', 'string', 'max:100'],
            'warga_data.*.pekerjaan' => ['nullable', 'string', 'max:100'],
            'warga_data.*.status_perkawinan' => ['nullable', 'string', 'max:50'],
            'warga_data.*.kewarganegaraan' => ['nullable', 'string', 'max:50'],
            'warga_data.*.golongan_darah' => ['nullable', 'string', 'max:5'],
            'warga_data.*.hubungan_keluarga' => ['required_with:warga_data', 'string', 'max:50'],
            'warga_data.*.no_telepon' => ['nullable', 'string', 'max:20'],
            'warga_data.*.nama_ayah' => ['nullable', 'string', 'max:255'],
            'warga_data.*.nama_ibu' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function wargaFields(array $wd): array
    {
        return collect($wd)->only([
            'nik', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
            'agama', 'pendidikan_terakhir', 'pekerjaan', 'status_perkawinan',
            'kewarganegaraan', 'golongan_darah', 'hubungan_keluarga', 'no_telepon',
            'nama_ayah', 'nama_ibu',
        ])->filter(fn ($v) => $v !== null && $v !== '')->all();
    }

    private function authorizeKeluarga(Request $request, Keluarga $keluarga): void
    {
        if ($this->isUnrestricted($request->user())) {
            return;
        }
        $rtIds = $this->rtIdsForUser($request->user());
        abort_if(! $rtIds->contains($keluarga->rt_id), 404, 'Keluarga tidak ditemukan.');
    }
}
