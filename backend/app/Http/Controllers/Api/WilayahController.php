<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use App\Models\Wilayah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WilayahController extends Controller
{
    use LogsActivity;

    /**
     * GET /api/wilayah/tree — hirarki Kelurahan → RW → RT.
     */
    public function tree(): JsonResponse
    {
        $tree = Wilayah::with(['children.children'])
            ->where('tingkat', 'Kelurahan')
            ->orderBy('kode')
            ->get();

        return response()->json(['data' => $tree]);
    }

    /**
     * GET /api/wilayah/children/{parentId} — opsi cascading dropdown.
     */
    public function children(int $parentId): JsonResponse
    {
        $children = Wilayah::where('parent_id', $parentId)->orderBy('kode')->get(['id', 'kode', 'nama', 'tingkat']);

        return response()->json(['data' => $children]);
    }

    /**
     * GET /api/wilayah — flat list (untuk opsi RT di filter).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Wilayah::query();

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->input('tingkat'));
        }
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        $items = $query->with('parent:id,nama,kode,tingkat')
            ->orderBy('tingkat')
            ->orderBy('kode')
            ->get();

        return response()->json(['data' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['kode'] = strtoupper($validated['kode']);

        if ($error = $this->validateHierarchy($validated['tingkat'], $validated['parent_id'] ?? null)) {
            return response()->json(['message' => $error], 422);
        }

        $wilayah = Wilayah::create($validated);
        $this->logActivity($request, 'create', 'wilayah', "Tambah wilayah {$wilayah->nama}", null, $wilayah->only(['kode', 'nama', 'tingkat']));

        return response()->json(['data' => $wilayah], 201);
    }

    public function update(Request $request, Wilayah $wilayah): JsonResponse
    {
        $validated = $request->validate($this->rules($wilayah->id));
        $validated['kode'] = strtoupper($validated['kode']);
        $parentId = $validated['parent_id'] ?? null;

        if ($parentId == $wilayah->id) {
            return response()->json(['message' => 'Wilayah tidak bisa menjadi parent dirinya sendiri.'], 422);
        }
        if ($parentId && $this->isDescendant($wilayah, (int) $parentId)) {
            return response()->json(['message' => 'Tidak boleh membuat referensi melingkar.'], 422);
        }
        if ($error = $this->validateHierarchy($validated['tingkat'], $parentId, $wilayah)) {
            return response()->json(['message' => $error], 422);
        }

        $old = $wilayah->only(['kode', 'nama']);
        $wilayah->update($validated);
        $this->logActivity($request, 'update', 'wilayah', "Ubah wilayah {$wilayah->nama}", $old, $wilayah->fresh()->only(['kode', 'nama', 'tingkat']));

        return response()->json(['data' => $wilayah->fresh()]);
    }

    public function destroy(Request $request, Wilayah $wilayah): JsonResponse
    {
        if ($wilayah->children()->exists()) {
            return response()->json(['message' => 'Masih ada wilayah turunan. Hapus turunan terlebih dahulu.'], 422);
        }
        if ($wilayah->users()->exists()) {
            return response()->json(['message' => 'Wilayah masih terikat ke user (penugasan RT/RW).'], 422);
        }
        if ($wilayah->tingkat === 'RT' && \App\Models\Keluarga::where('rt_id', $wilayah->id)->exists()) {
            return response()->json(['message' => 'Masih ada keluarga berdomisili di RT ini.'], 422);
        }

        $old = $wilayah->only(['kode', 'nama', 'tingkat']);
        $wilayah->delete();
        $this->logActivity($request, 'delete', 'wilayah', "Hapus wilayah {$old['nama']}", $old, null);

        return response()->json(['message' => 'Wilayah dihapus.']);
    }

    private function rules(?int $ignoreId = null): array
    {
        $kodeUnique = $ignoreId
            ? ['required', 'string', 'max:10', Rule::unique('wilayahs', 'kode')->ignore($ignoreId)->whereNull('deleted_at')]
            : ['required', 'string', 'max:10', Rule::unique('wilayahs', 'kode')->whereNull('deleted_at')];

        return [
            'kode' => $kodeUnique,
            'nama' => ['required', 'string', 'max:100'],
            'tingkat' => ['required', Rule::in(['Kelurahan', 'RW', 'RT'])],
            'parent_id' => ['nullable', 'exists:wilayahs,id'],
        ];
    }

    private function validateHierarchy(string $tingkat, ?int $parentId, ?Wilayah $existing = null): ?string
    {
        if ($tingkat === 'Kelurahan' && $parentId) {
            return 'Kelurahan tidak boleh memiliki parent.';
        }
        if ($tingkat !== 'Kelurahan' && ! $parentId) {
            return ucfirst(strtolower($tingkat)).' wajib memiliki parent.';
        }
        if ($parentId) {
            $parent = Wilayah::find($parentId);
            $expected = $tingkat === 'RT' ? 'RW' : 'Kelurahan';
            if ($parent?->tingkat !== $expected) {
                return "Parent untuk {$tingkat} harus {$expected}.";
            }
        }

        return null;
    }

    /**
     * Cek apakah $candidateParentId adalah turunan dari $wilayah (anti circular).
     */
    private function isDescendant(Wilayah $wilayah, int $candidateParentId): bool
    {
        $children = $wilayah->children()->pluck('id');
        foreach ($children as $id) {
            if ($id === $candidateParentId) {
                return true;
            }
            if ($this->isDescendant(Wilayah::find($id), $candidateParentId)) {
                return true;
            }
        }

        return false;
    }
}
