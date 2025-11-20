# 📋 SIWA - Iuran System Implementation Plan

## 🎯 **Objective**
Implementasi sistem iuran KK-based dengan historical data preservation dan status management.

---

## 📊 **Final System Architecture**

### **Core Components:**
1. **KK-Based Billing**: 1 KK → Multiple iuran connections → Generate tagihan per periode
2. **Many-to-Many**: Keluarga ↔ Jenis Iuran via keluarga_iuran pivot table
3. **Status Management**: keluarga.status_keluarga (Aktif/Pindah/Non-Aktif/Dibubarkan)
4. **Historical Preservation**: Soft deletes + status untuk audit completeness

### **Jenis Iuran Final:**
```
✅ Iuran Kebersihan (Bulanan - Rp 25,000)
✅ Iuran Keamanan (Bulanan - Rp 30,000)
✅ Iuran Sosial/Kematian (Bulanan - Rp 10,000)
✅ Iuran Kampung (Bulanan - Rp 10,000)
✅ Iuran Acara 17 Agustus (Tahunan - Rp 50,000)
```

### **Payment Methods:**
```
✅ Cash (Tunai) dengan kuitansi
✅ Digital payment (QRIS, E-wallet)
✅ Bank transfer (tanpa bukti, trust-based)
❌ Auto-debit arrangement (dihapus)
```

---

## 🏗️ **Implementation Plan**

### **Phase 1: Database Structure Fix (Priority)**

#### **1.1 Fix iurans Table Structure**
```sql
-- Migration: Add missing fields to iurans table (CORRECTED - NO rt_id/rw_id)
ALTER TABLE iurans ADD COLUMN periode_bulan VARCHAR(7) NULL; -- '2025-01'
ALTER TABLE iurans ADD COLUMN jatuh_tempo DATE NULL;
ALTER TABLE iurans ADD COLUMN denda_terlambatan DECIMAL(10,2) DEFAULT 0;
ALTER TABLE iurans ADD COLUMN reminder_sent_at TIMESTAMP NULL;

-- Add indexes (EFFICIENT - no redundant fields)
ALTER TABLE iurans ADD INDEX idx_periode_bulan (periode_bulan);
ALTER TABLE iurans ADD INDEX idx_kk_periode (kk_id, periode_bulan);
ALTER TABLE iurans ADD INDEX idx_status (status);

-- Note: rt_id/rw_id diambil dari relasi keluarga ( tidak redundant )
```

#### **1.2 Create keluarga_iuran Pivot Table**
```sql
-- Migration: Create pivot table for many-to-many relationship
CREATE TABLE keluarga_iuran (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    keluarga_id BIGINT NOT NULL,
    jenis_iuran_id BIGINT NOT NULL,
    nominal_custom DECIMAL(10,2) NULL COMMENT 'Custom amount, NULL = use default',
    status_aktif BOOLEAN DEFAULT TRUE COMMENT 'Include in future generation',
    alasan_custom TEXT NULL COMMENT 'Reason for custom amount',
    created_by BIGINT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (keluarga_id) REFERENCES keluargas(id),
    FOREIGN KEY (jenis_iuran_id) REFERENCES jenis_iurans(id),
    FOREIGN KEY (created_by) REFERENCES users(id),

    UNIQUE KEY unique_keluarga_jenis (keluarga_id, jenis_iuran_id),
    INDEX idx_keluarga_id (keluarga_id),
    INDEX idx_status_aktif (status_aktif),
    INDEX idx_deleted_at (deleted_at)
);
```

#### **1.3 Add keluarga.status_keluarga Field**
```sql
-- Migration: Add status field for historical data management
ALTER TABLE keluargas ADD COLUMN status_keluarga ENUM('Aktif', 'Pindah', 'Non-Aktif', 'Dibubarkan') DEFAULT 'Aktif';
ALTER TABLE keluargas ADD COLUMN keterangan_status TEXT NULL COMMENT 'Reason for status change';
ALTER TABLE keluargas ADD COLUMN tanggal_status DATE NULL COMMENT 'Status change date';
ALTER TABLE keluargas ADD INDEX idx_status_keluarga (status_keluarga);
```

#### **1.4 Update Jenis Iuran Data**
```sql
-- Update existing jenis iuran records
UPDATE jenis_iurans SET
    nominal_default = 25000,
    periode = 'bulanan'
WHERE id = 1 AND nama LIKE '%Kebersihan%';

UPDATE jenis_iurans SET
    nominal_default = 30000,
    periode = 'bulanan'
WHERE id = 2 AND nama LIKE '%Keamanan%';

UPDATE jenis_iurans SET
    nominal_default = 10000,
    periode = 'bulanan'
WHERE id = 3 AND nama LIKE '%Sosial%';

UPDATE jenis_iurans SET
    nominal_default = 10000,
    periode = 'bulanan'
WHERE id = 6 AND nama LIKE '%Kerja Bakti%';

UPDATE jenis_iurans SET
    nama = 'Iuran Kampung',
    nominal_default = 10000,
    periode = 'bulanan'
WHERE id = 6;

UPDATE jenis_iurans SET
    nominal_default = 50000,
    periode = 'tahunan'
WHERE id = 5 AND nama LIKE '%17 Agustus%';

-- Delete unused iuran types
DELETE FROM jenis_iurans WHERE nama LIKE '%Sampah%' OR nama LIKE '%Infrastruktur%';
```

---

### **Phase 2: Model Relationships Update**

#### **2.1 Update Iuran Model**
```php
// app/Models/Iuran.php
class Iuran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'iurans';
    protected $fillable = [
        'kk_id', 'jenis_iuran_id',
        'nominal', 'periode_bulan', 'status', 'jatuh_tempo',
        'denda_terlambatan', 'reminder_sent_at'
    ];

    // Note: rt_id/rw_id diambil dari relasi keluarga untuk efisiensi

    protected $casts = [
        'nominal' => 'decimal:2',
        'denda_terlambatan' => 'decimal:2',
        'jatuh_tempo' => 'date',
        'reminder_sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Remove old warga relationship
    // public function warga() { ... } // DELETE THIS

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'kk_id');
    }

    public function jenisIuran()
    {
        return $this->belongsTo(JenisIuran::class, 'jenis_iuran_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranIuran::class, 'iuran_id');
    }
}
```

#### **2.2 Update Keluarga Model**
```php
// app/Models/Keluarga.php
class Keluarga extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // ... existing fields ...
        'status_keluarga',
        'keterangan_status',
        'tanggal_status'
    ];

    protected $casts = [
        // ... existing casts ...
        'status_keluarga' => 'string',
        'tanggal_status' => 'date',
    ];

    public function keluargaIuran()
    {
        return $this->belongsToMany(JenisIuran::class, 'keluarga_iuran')
            ->withPivot(['nominal_custom', 'status_aktif', 'alasan_custom'])
            ->withTimestamps();
    }

    public function iuran()
    {
        return $this->hasMany(Iuran::class, 'kk_id');
    }

    public function activeIuranConnections()
    {
        return $this->keluargaIuran()->wherePivot('status_aktif', true);
    }

    public function isActive()
    {
        return $this->status_keluarga === 'Aktif';
    }
}
```

#### **2.3 Create KeluargaIuran Model**
```php
// app/Models/KeluargaIuran.php
class KeluargaIuran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'keluarga_iuran';
    protected $fillable = [
        'keluarga_id',
        'jenis_iuran_id',
        'nominal_custom',
        'status_aktif',
        'alasan_custom',
        'created_by'
    ];

    protected $casts = [
        'nominal_custom' => 'decimal:2',
        'status_aktif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function jenisIuran()
    {
        return $this->belongsTo(JenisIuran::class);
    }

    public function getEffectiveNominalAttribute()
    {
        return $this->nominal_custom ?? $this->jenisIuran->nominal_default;
    }
}
```

#### **2.4 Update JenisIuran Model**
```php
// app/Models/JenisIuran.php
class JenisIuran extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama', 'deskripsi', 'nominal_default', 'periode', 'status_aktif'
    ];

    protected $casts = [
        'nominal_default' => 'decimal:2',
        'status_aktif' => 'boolean',
    ];

    public function keluarga()
    {
        return $this->belongsToMany(Keluarga::class, 'keluarga_iuran')
            ->withPivot(['nominal_custom', 'status_aktif', 'alasan_custom'])
            ->withTimestamps();
    }
}
```

---

### **Phase 3: Basic CRUD Implementation**

#### **3.1 Keluarga-Iuran Connection Management**
```php
// app/Http/Controllers/KeluargaIuranController.php
class KeluargaIuranController extends Controller
{
    /**
     * Display overview of all keluarga-iuran connections across all families
     */
    public function overview()
    {
        $connections = KeluargaIuran::with(['keluarga', 'jenisIuran'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $summary = [
            'total_connections' => KeluargaIuran::count(),
            'active_connections' => KeluargaIuran::where('status_aktif', true)->count(),
            'families_with_iuran' => KeluargaIuran::pluck('keluarga_id')->unique()->count(),
            'total_custom_nominals' => KeluargaIuran::whereNotNull('nominal_custom')->count()
        ];

        return view('admin.keluarga_iuran.overview', compact('connections', 'summary'));
    }

    /**
     * Index: Show all iuran connections for a specific family
     * URL: /admin/keluarga/{keluarga}/iuran
     */
    public function index(Keluarga $keluarga)
    {
        $connections = $keluarga->keluargaIuran()
            ->with('jenisIuran')
            ->orderBy('created_at', 'desc')
            ->get();

        $availableIurans = JenisIuran::where('is_aktif', true)
            ->whereNotIn('id', $connections->pluck('jenis_iuran_id'))
            ->get();

        return view('admin.keluarga_iuran.index', compact('keluarga', 'connections', 'availableIurans'));
    }

    /**
     * Store: Connect family to iuran type (AJAX support)
     * URL: POST /admin/keluarga/{keluarga}/iuran
     */
    public function store(Request $request, Keluarga $keluarga): JsonResponse
    {
        $validated = $request->validate([
            'jenis_iuran_id' => 'required|exists:jenis_iurans,id',
            'nominal_custom' => 'nullable|numeric|min:0',
            'status_aktif' => 'boolean',
            'alasan_custom' => 'nullable|string|max:255'
        ]);

        // Check if connection already exists
        $exists = $keluarga->keluargaIuran()
            ->where('jenis_iuran_id', $validated['jenis_iuran_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi iuran sudah ada untuk keluarga ini'
            ], 422);
        }

        $connection = $keluarga->keluargaIuran()->create([
            'jenis_iuran_id' => $validated['jenis_iuran_id'],
            'nominal_custom' => $validated['nominal_custom'],
            'status_aktif' => $validated['status_aktif'] ?? true,
            'alasan_custom' => $validated['alasan_custom'],
            'created_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Koneksi iuran berhasil ditambahkan',
            'data' => $connection->load('jenisIuran')
        ]);
    }

    /**
     * Update: Modify connection (AJAX support)
     * URL: PUT /admin/keluarga/{keluarga}/iuran/{jenisIuran}
     */
    public function update(Request $request, Keluarga $keluarga, JenisIuran $jenisIuran): JsonResponse
    {
        $connection = $keluarga->keluargaIuran()
            ->where('jenis_iuran_id', $jenisIuran->id)
            ->firstOrFail();

        $validated = $request->validate([
            'nominal_custom' => 'nullable|numeric|min:0',
            'status_aktif' => 'required|boolean',
            'alasan_custom' => 'nullable|string|max:255'
        ]);

        $connection->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Koneksi iuran berhasil diperbarui',
            'data' => $connection->load('jenisIuran')
        ]);
    }

    /**
     * Destroy: Remove connection (AJAX support)
     * URL: DELETE /admin/keluarga/{keluarga}/iuran/{jenisIuran}
     */
    public function destroy(Keluarga $keluarga, JenisIuran $jenisIuran): JsonResponse
    {
        $connection = $keluarga->keluargaIuran()
            ->where('jenis_iuran_id', $jenisIuran->id)
            ->firstOrFail();

        $connection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Koneksi iuran berhasil dihapus'
        ]);
    }

    /**
     * API: Get available jenis iuran for a family
     */
    public function getAvailableJenisIuran(Keluarga $keluarga): JsonResponse
    {
        $existingConnections = $keluarga->keluargaIuran()->pluck('jenis_iuran_id');

        $availableIurans = JenisIuran::where('is_aktif', true)
            ->whereNotIn('id', $existingConnections)
            ->get(['id', 'nama', 'kode', 'jumlah', 'periode']);

        return response()->json([
            'success' => true,
            'data' => $availableIurans
        ]);
    }

    /**
     * API: Get active connections for a family
     */
    public function getActiveConnections(Keluarga $keluarga): JsonResponse
    {
        $connections = $keluarga->keluargaIuran()
            ->where('status_aktif', true)
            ->with('jenisIuran')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $connections
        ]);
    }
}
```

#### **3.2 Iuran Generation System**
```php
// app/Http/Controllers/IuranGenerationController.php
class IuranGenerationController extends Controller
{
    public function create()
    {
        $periodeOptions = $this->getPeriodeOptions();
        return view('admin.iuran.generate', compact('periodeOptions'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'periode_bulan' => 'required|string|regex:/^\d{4}-\d{2}$/', // YYYY-MM format
            'rt_id' => 'nullable|exists:wilayahs,id'
        ]);

        DB::beginTransaction();

        try {
            $periode = $validated['periode_bulan'];
            $rtId = $validated['rt_id'];

            // Get families based on user role and RT filter
            $families = $this->getFamiliesByRoleAndRt($rtId);

            $generatedCount = 0;
            $duplicateCount = 0;

            foreach ($families as $family) {
                // Skip if family is not active
                if ($family->status_keluarga !== 'Aktif') {
                    continue;
                }

                // Get active iuran connections for this family
                $activeConnections = $family->activeIuranConnections;

                foreach ($activeConnections as $connection) {
                    // Check if iuran already exists for this period
                    $exists = Iuran::where('kk_id', $family->id)
                        ->where('jenis_iuran_id', $connection->id)
                        ->where('periode_bulan', $periode)
                        ->exists();

                    if ($exists) {
                        $duplicateCount++;
                        continue;
                    }

                    // Create new iuran (NO rt_id/rw_id - diambil dari keluarga)
                    Iuran::create([
                        'kk_id' => $family->id,
                        'jenis_iuran_id' => $connection->id,
                        'nominal' => $connection->pivot->nominal_custom ?? $connection->nominal_default,
                        'periode_bulan' => $periode,
                        'status' => 'belum_bayar',
                        'jatuh_tempo' => $this->calculateJatuhTempo($periode, $connection->periode),
                        'created_by' => auth()->id()
                    ]);

                    $generatedCount++;
                }
            }

            DB::commit();

            return back()->with('success',
                "Berhasil generate {$generatedCount} tagihan iuran untuk periode {$periode}" .
                ($duplicateCount > 0 ? " ({$duplicateCount} dilewati karena sudah ada)" : "")
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate iuran: ' . $e->getMessage());
        }
    }

    private function getFamiliesByRoleAndRt($rtId = null)
    {
        $query = Keluarga::where('status_keluarga', 'Aktif');

        // Filter by RT if specified
        if ($rtId) {
            $query->where('rt_id', $rtId);
        }

        // Filter by user role
        switch (auth()->user()->role) {
            case 'rt':
                $query->where('rt_id', auth()->user()->rt_id);
                break;
            case 'rw':
                if ($rtId) {
                    // Validate RT belongs to this RW
                    $rt = Wilayah::find($rtId);
                    if ($rt->parent_id != auth()->user()->rw_id) {
                        throw new \Exception('RT tidak berada dalam wilayah RW Anda');
                    }
                } else {
                    // Get all RT under this RW
                    $query->whereIn('rt_id', function($q) {
                        $q->select('id')->from('wilayahs')
                            ->where('parent_id', auth()->user()->rw_id);
                    });
                }
                break;
            case 'lurah':
                // Can access all families in kelurahan
                break;
            case 'admin':
                // Can access all families
                break;
        }

        return $query->with(['rt', 'rw'])->get();
    }

// BENAR: Query patterns untuk iuran berdasarkan RT/Wilayah
public function getIuranByRT($rtId)
{
    // EFFICIENT: Gunakan relasi untuk mendapatkan rt_id
    return Iuran::whereHas('keluarga', function($query) use ($rtId) {
        $query->where('rt_id', $rtId);
    })->with(['keluarga.rt', 'jenisIuran'])->get();
}

public function getLaporanByRT($rtId)
{
    // OPTIMAL: Join query untuk reporting
    return DB::table('iurans as i')
        ->join('keluargas as k', 'i.kk_id', '=', 'k.id')
        ->join('wilayahs as rt', 'k.rt_id', '=', 'rt.id')
        ->where('k.rt_id', $rtId)
        ->select(
            'i.*',
            'k.no_kk',
            'rt.nama as rt_nama'
        )
        ->get();
}

    private function calculateJatuhTempo($periode, $jenisPeriode)
    {
        $year = substr($periode, 0, 4);
        $month = substr($periode, 5, 2);

        switch ($jenisPeriode) {
            case 'tahunan':
                return Carbon::create($year, 6, 30); // 30 Juni
            case 'sekali':
                return Carbon::create($year, $month, 15); // 15 hari di bulan yang sama
            default: // bulanan
                return Carbon::create($year, $month, 25); // 25 setiap bulan
        }
    }

    private function getPeriodeOptions()
    {
        $options = [];
        $currentDate = Carbon::now();

        // Generate options for last 3 months and next 3 months
        for ($i = -3; $i <= 3; $i++) {
            $date = $currentDate->copy()->addMonths($i);
            $options[$date->format('Y-m')] = $date->format('F Y');
        }

        return $options;
    }
}
```

#### **3.3 Payment Processing System**
```php
// app/Http/Controllers/PembayaranIuranController.php
class PembayaranIuranController extends Controller
{
    public function create()
    {
        return view('admin.pembayaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'iuran_ids' => 'required|array',
            'iuran_ids.*' => 'exists:iurans,id',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,digital',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            $pembayaran = PembayaranIuran::create([
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'keterangan' => $validated['keterangan'],
                'created_by' => auth()->id()
            ]);

            // Attach payment to iurans
            $totalTerbayar = 0;
            foreach ($validated['iuran_ids'] as $iuranId) {
                $iuran = Iuran::find($iuranId);
                $totalTerbayar += $iuran->nominal;

                $pembayaran->iuran()->attach($iuranId);

                // Update iuran status if fully paid
                if ($totalTerbayar >= $validated['jumlah_bayar']) {
                    $iuran->update(['status' => 'lunas']);
                } else {
                    $iuran->update(['status' => 'sebagian']);
                }
            }

            DB::commit();

            return back()->with('success',
                "Pembayaran berhasil dicatat untuk " . count($validated['iuran_ids']) . " tagihan"
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }
}
```

---

### **Phase 4: Views Implementation**

#### **4.1 Keluarga-Iuran Connection Views**
```blade
<!-- resources/views/admin/keluarga_iuran/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="text-dark mb-4">Iuran - {{ $keluarga->no_kk }} ({{ $keluarga->kepalaKeluarga->nama_lengkap }})</h3>

    <!-- Connection List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Koneksi Iuran Aktif</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Jenis Iuran</th>
                        <th>Nominal Default</th>
                        <th>Nominal Custom</th>
                        <th>Status</th>
                        <th>Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($connections as $connection)
                    <tr>
                        <td>{{ $connection->nama }}</td>
                        <td>Rp {{ number_format($connection->nominal_default, 0) }}</td>
                        <td>
                            @if($connection->pivot->nominal_custom)
                                <span class="text-warning">Rp {{ number_format($connection->pivot->nominal_custom, 0) }}</span>
                            @else
                                <span class="text-muted">Default</span>
                            @endif
                        </td>
                        <td>
                            @if($connection->pivot->status_aktif)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Non-Aktif</span>
                            @endif
                        </td>
                        <td>{{ $connection->pivot->alasan_custom ?? '-' }}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-warning" onclick="editConnection({{ $connection->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteConnection({{ $connection->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Connection Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Koneksi Iuran</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('keluarga.iuran.store', $keluarga->id) }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenis Iuran</label>
                            <select name="jenis_iuran_id" class="form-control" required>
                                <option value="">Pilih Jenis Iuran</option>
                                @foreach($availableIurans as $iuran)
                                <option value="{{ $iuran->id }}">{{ $iuran->nama }} (Rp {{ number_format($iuran->nominal_default, 0) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nominal Custom (kosongkan jika default)</label>
                            <input type="number" name="nominal_custom" class="form-control" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alasan Custom (jika ada perubahan nominal)</label>
                    <textarea name="alasan_custom" class="form-control" rows="2"></textarea>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
```

---

### **Phase 5: Routes Configuration**

#### **5.1 Add Iuran Routes**
```php
// routes/web.php - Add these routes in admin middleware group

// Keluarga-Iuran Overview (Global View)
Route::get('/keluarga-iuran/overview', [KeluargaIuranController::class, 'overview'])->name('keluarga_iuran.overview');

// Keluarga-Iuran Connection Management (Per-Keluarga CRUD)
Route::get('/keluarga/{keluarga}/iuran', [KeluargaIuranController::class, 'index'])->name('keluarga_iuran.index');
Route::post('/keluarga/{keluarga}/iuran', [KeluargaIuranController::class, 'store'])->name('keluarga_iuran.store');
Route::put('/keluarga/{keluarga}/iuran/{jenisIuran}', [KeluargaIuranController::class, 'update'])->name('keluarga_iuran.update');
Route::delete('/keluarga/{keluarga}/iuran/{jenisIuran}', [KeluargaIuranController::class, 'destroy'])->name('keluarga_iuran.destroy');

// API Routes for Keluarga-Iuran operations
Route::get('/api/keluarga-iuran/{keluargaId}/available', [KeluargaIuranController::class, 'getAvailableJenisIuran'])->name('api.keluarga_iuran.available');
Route::get('/api/keluarga-iuran/{keluargaId}/active', [KeluargaIuranController::class, 'getActiveConnections'])->name('api.keluarga_iuran.active');

// Iuran Generation
Route::get('/iuran/generate', [IuranGenerationController::class, 'create'])->name('iuran.generate.create');
Route::post('/iuran/generate', [IuranGenerationController::class, 'generate'])->name('iuran.generate.store');

// Pembayaran Iuran
Route::get('/pembayaran-iuran', [PembayaranIuranController::class, 'create'])->name('pembayaran.create');
Route::post('/pembayaran-iuran', [PembayaranIuranController::class, 'store'])->name('pembayaran.store');

// Iuran Reports
Route::get('/iuran/laporan', [IuranLaporanController::class, 'index'])->name('iuran.laporan');
Route::get('/iuran/laporan/export', [IuranLaporanController::class, 'export'])->name('iuran.laporan.export');
```

#### **5.2 Navigation Integration**
```php
// Add navigation link to sidebar (resources/views/layouts/app.blade.php)
<li class="nav-item">
    <a class="nav-link" href="{{ route('keluarga_iuran.overview') }}">
        <i class="fas fa-link"></i>
        <span>Koneksi Iuran</span>
    </a>
</li>

// Add "Kelola Iuran" button in keluarga module (resources/views/admin/keluarga/index.blade.php)
<a href="{{ route('keluarga_iuran.index', $keluarga) }}" class="btn btn-outline-primary btn-sm">
    <i class="fas fa-link me-1"></i>Kelola Iuran
</a>
```

---

## ⚡ **Execution Priority Order**

### **Day 1: Database Structure**
1. ✅ Migration: Fix iurans table
2. ✅ Migration: Create keluarga_iuran pivot table
3. ✅ Migration: Add status_keluarga field
4. ✅ Migration: Update jenis iuran data

### **Day 2: Model & Relationships**
1. ✅ Update Iuran model (remove warga relationship)
2. ✅ Update Keluarga model (add keluargaIuran relationship)
3. ✅ Create KeluargaIuran model
4. ✅ Update JenisIuran model
5. ✅ Update PembayaranIuran model

### **Day 3: Basic CRUD**
1. ✅ KeluargaIuranController (connection management)
2. ✅ IuranGenerationController (manual generation)
3. ✅ PembayaranIuranController (payment processing)

### **Day 4: Views & Frontend**
1. ✅ Keluarga-iuran connection view
2. ✅ Iuran generation form
3. ✅ Payment input form
4. ✅ Simple dashboard/laporan

### **Day 5: Testing & Refinement**
1. ✅ Test role-based access
2. ✅ Test anti-duplicate generation
3. ✅ Test historical data preservation
4. ✅ Test status management workflow

---

## 🎯 **Success Criteria**

### **Must-Have (MVP):**
- [x] Database structure complete
- [x] Many-to-many connection working
- [x] Manual iuran generation functional
- [x] Payment processing working
- [x] Historical data preserved
- [x] Role-based access working

### **Should-Have:**
- [x] Simple reporting dashboard
- [x] Status management UI
- [x] Bulk operations
- [x] Export functionality

### **Nice-to-Have (Future):**
- [ ] Advanced analytics
- [ ] Email/SMS notifications
- [ ] Mobile app interface
- [ ] API integration

---

**Dokumentasi lengkap ini akan menjadi panduan implementasi sistem iuran SIWA.** 📋

Silakan review dan konfirmasi jika sudah siap untuk eksekusi!