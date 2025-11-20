@extends('layouts.app')

@section('title', 'Manajemen Iuran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-money-check-alt me-2"></i>
                        Manajemen Iuran
                    </h5>
                    <div>
                        <a href="{{ route('iuran.generate') }}" class="btn btn-success">
                            <i class="fas fa-magic me-1"></i>
                            Generate Iuran
                        </a>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                            <i class="fas fa-plus me-1"></i>
                            Tambah Iuran
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tagihan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stats-total">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lunas</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stats-lunas">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Belum Bayar</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stats-belum">0</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">% Lunas</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="stats-persentase">0%</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <form id="filterForm" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="search" name="search" placeholder="Cari No. KK, Nama, atau Jenis Iuran...">
                            </div>
                            <div class="col-md-3">
                                <input type="month" class="form-control" id="periode" name="periode" placeholder="Periode">
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="belum_bayar">Belum Bayar</option>
                                    <option value="lunas">Lunas</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters">
                                    <i class="fas fa-redo me-1"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Periode</th>
                                    <th>No. KK</th>
                                    <th>Kepala Keluarga</th>
                                    <th>Jenis Iuran</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="dataTable">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination" class="d-flex justify-content-between align-items-center mt-3">
                        <div id="paginationInfo"></div>
                        <nav id="paginationLinks"></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Iuran Manual</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Keluarga <span class="text-danger">*</span></label>
                            <select class="form-control" id="kk_id" name="kk_id" required>
                                <option value="">Pilih Keluarga</option>
                                @foreach($keluargas as $keluarga)
                                    <option value="{{ $keluarga->id }}" data-nomor="{{ $keluarga->no_kk }}">{{ $keluarga->no_kk }} - {{ $keluarga->kepalaKeluarga->nama_lengkap ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Iuran <span class="text-danger">*</span></label>
                            <select class="form-control" id="jenis_iuran_id" name="jenis_iuran_id" required>
                                <option value="">Pilih Jenis Iuran</option>
                                @foreach($jenisIurans as $jenisIuran)
                                    <option value="{{ $jenisIuran->id }}">{{ $jenisIuran->nama }} - Rp {{ number_format($jenisIuran->jumlah, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periode <span class="text-danger">*</span></label>
                            <input type="month" class="form-control" id="periode_bulan" name="periode_bulan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nominal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="nominal" name="nominal" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="belum_bayar" selected>Belum Bayar</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Generate Bulk Modal -->
<div class="modal fade" id="generateBulkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Tagihan Bulk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="generateBulkForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Periode <span class="text-danger">*</span></label>
                            <input type="month" class="form-control" id="bulk_periode" name="periode_bulan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Iuran <span class="text-danger">*</span></label>
                            <div class="border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                                @foreach($jenisIurans as $jenisIuran)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $jenisIuran->id }}" id="ji_{{ $jenisIuran->id }}" name="jenis_iuran_ids[]">
                                        <label class="form-check-label" for="ji_{{ $jenisIuran->id }}">
                                            {{ $jenisIuran->nama }} (Rp {{ number_format($jenisIuran->jumlah, 0, ',', '.') }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Sistem akan generate tagihan untuk semua keluarga aktif yang terhubung dengan jenis iuran yang dipilih. Tagihan yang sudah ada akan dilewati.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-magic me-1"></i>
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Tagihan Iuran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tagihan iuran ini?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Tagihan yang sudah memiliki pembayaran tidak dapat dihapus.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Pembayaran Iuran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="paymentForm">
                @csrf
                <input type="hidden" id="payment_iuran_id" name="iuran_id">
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="paymentInfo"></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah_bayar" name="jumlah_bayar" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select class="form-control" id="metode_pembayaran" name="metode_pembayaran" required>
                                <option value="">Pilih Metode</option>
                                <option value="cash" selected>Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nomor Referensi</label>
                            <input type="text" class="form-control" id="nomor_referensi" name="nomor_referensi" placeholder="Nomor referensi transaksi (opsional)">
                            <small class="text-muted">Isi dengan nomor referensi jika menggunakan transfer atau e-wallet</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-credit-card me-1"></i>
                        Proses Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Dibuat Oleh</th>
                                <th>Keterangan</th>
                                <th>Nomor Referensi</th>
                            </tr>
                        </thead>
                        <tbody id="paymentHistoryTable">
                            <!-- Payment history will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i> Detail Tagihan Iuran
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Informasi Keluarga dan Tagihan -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <h6 class="text-primary">
                                    <i class="fas fa-users me-2"></i> Informasi Keluarga
                                </h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="text-muted" style="width: 120px;">No. KK:</td>
                                        <td><strong id="detail_no_kk">-</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Kepala Keluarga:</td>
                                        <td><strong id="detail_kepala_keluarga">-</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jenis Iuran:</td>
                                        <td><strong id="detail_jenis_iuran">-</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Periode:</td>
                                        <td><strong id="detail_periode">-</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <h6 class="text-warning">
                                    <i class="fas fa-info-circle me-2"></i> Status & Tagihan
                                </h6>
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="text-muted" style="width: 120px;">Status:</td>
                                        <td id="detail_status">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Nominal:</td>
                                        <td><strong class="text-primary" id="detail_nominal">-</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Denda:</td>
                                        <td><strong class="text-danger" id="detail_denda">-</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Jatuh Tempo:</td>
                                        <td><strong id="detail_jatuh_tempo">-</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

               
                <!-- Keterangan -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card border-left-info">
                            <div class="card-body">
                                <h6 class="text-info">
                                    <i class="fas fa-comment me-2"></i> Keterangan
                                </h6>
                                <p class="mb-0" id="detail_keterangan">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-secondary">
                            <div class="card-body">
                                <h6 class="text-secondary">
                                    <i class="fas fa-user-plus me-2"></i> Informasi Pembuat
                                </h6>
                                <p class="mb-1"><small class="text-muted">Dibuat oleh:</small> <strong id="detail_dibuat_oleh">-</strong></p>
                                <p class="mb-0"><small class="text-muted">Tanggal:</small> <strong id="detail_tanggal_dibuat">-</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Pembayaran -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-left-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-history me-2"></i> Riwayat Pembayaran
                                </h6>
                            </div>
                            <div class="card-body">
                                <div id="detail_payment_history">
                                    <!-- Payment history will be loaded via AJAX -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
</style>
@endpush

@push('scripts')
<script>

// Global variable for tracking current page
let currentPage = 1;

// Global functions for onclick handlers (must be outside document.ready)
function showPaymentModal(id) {
    console.log('💳 showPaymentModal called with id:', id);

    // Find the iuran data from the current page or fetch it
    $.get('/admin/api/iuran/' + id)
        .done(function(response) {
            if (response.success) {
                const iuran = response.data;

                $('#payment_iuran_id').val(id);
                $('#paymentInfo').html(`
                    <strong>No. KK:</strong> ${iuran.keluarga?.no_kk || '-'}<br>
                    <strong>Kepala Keluarga:</strong> ${iuran.keluarga?.kepala_keluarga?.nama_lengkap || '-'}<br>
                    <strong>Jenis Iuran:</strong> ${iuran.jenis_iuran?.nama || '-'}<br>
                    <strong>Periode:</strong> ${iuran.periode_bulan || '-'}<br>
                    <strong>Total Tagihan:</strong> Rp ${Number(iuran.nominal || 0).toLocaleString('id-ID')}
                `);

                // Set max payment amount
                const totalTagihan = iuran.nominal - (iuran.total_dibayar || 0);
                $('#jumlah_bayar').attr('max', totalTagihan);
                $('#jumlah_bayar').val(totalTagihan);

                $('#paymentModal').modal('show');
            } else {
                alert('Gagal memuat data pembayaran');
            }
        })
        .fail(function() {
            alert('Error mengambil data iuran');
        });
}

function showIuranDetail(id) {
    console.log('👁️ showIuranDetail called with id:', id);

    // Fetch iuran data and populate detail modal
    $.get('/admin/api/iuran/' + id)
        .done(function(response) {
            if (response.success) {
                const iuran = response.data;

                // Populate detail modal
                $('#detail_no_kk').text(iuran.keluarga?.no_kk || '-');
                $('#detail_kepala_keluarga').text(iuran.keluarga?.kepala_keluarga?.nama_lengkap || '-');
                $('#detail_jenis_iuran').text(iuran.jenis_iuran?.nama || '-');
                $('#detail_periode').text(iuran.periode_bulan ? new Date(iuran.periode_bulan + '-01').toLocaleDateString('id-ID', { year: 'numeric', month: 'long' }) : '-');
                $('#detail_nominal').text('Rp ' + (iuran.nominal ? parseInt(iuran.nominal).toLocaleString('id-ID') : '0'));
                $('#detail_denda').text('Rp ' + (iuran.denda_terlambatan ? parseInt(iuran.denda_terlambatan).toLocaleString('id-ID') : '0'));
                $('#detail_jatuh_tempo').text(iuran.jatuh_tempo ? new Date(iuran.jatuh_tempo).toLocaleDateString('id-ID') : '-');

                // Status badge
                const statusHtml = {
                    'belum_bayar': '<span class="badge bg-warning text-white">Belum Bayar</span>',
                    'sebagian': '<span class="badge bg-info text-white">Sebagian</span>',
                    'lunas': '<span class="badge bg-success text-white">Lunas</span>',
                    'batal': '<span class="badge bg-secondary text-white">Batal</span>'
                };
                $('#detail_status').html(statusHtml[iuran.status] || '-');

                // Format keterangan untuk menampilkan format periode yang lebih baik
                let formattedKeterangan = iuran.keterangan || '-';
                if (formattedKeterangan.includes('Generate otomatis periode')) {
                    // Replace format "2025-11" dengan "November 2025"
                    formattedKeterangan = formattedKeterangan.replace(/(\d{4}-\d{2})/g, function(match) {
                        const [year, month] = match.split('-');
                        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        return months[parseInt(month) - 1] + ' ' + year;
                    });
                }
                $('#detail_keterangan').text(formattedKeterangan);
                $('#detail_dibuat_oleh').text(iuran.created_by?.name || 'System');
                $('#detail_tanggal_dibuat').text(iuran.created_at ? new Date(iuran.created_at).toLocaleString('id-ID') : '-');

                // Payment history
                let paymentHistoryHtml = '';
                if (iuran.pembayaran && iuran.pembayaran.length > 0) {
                    paymentHistoryHtml = '<div class="table-responsive"><table class="table table-sm table-hover">';
                    paymentHistoryHtml += '<thead><tr><th>No.</th><th>Tanggal</th><th>Jumlah</th><th>Metode</th><th>Keterangan</th></tr></thead><tbody>';

                    iuran.pembayaran.forEach((payment, index) => {
                        const metodeHtml = {
                            'cash': '<span class="badge bg-primary text-white">Cash</span>',
                            'transfer': '<span class="badge bg-info text-white">Transfer</span>',
                            'qris': '<span class="badge bg-success text-white">QRIS</span>',
                            'ewallet': '<span class="badge bg-warning text-white">E-Wallet</span>'
                        };

                        paymentHistoryHtml += '<tr>';
                        paymentHistoryHtml += '<td><span class="badge bg-dark text-white">' + (index + 1) + '</span></td>';
                        paymentHistoryHtml += '<td>' + new Date(payment.created_at).toLocaleString('id-ID') + '</td>';
                        paymentHistoryHtml += '<td><strong class="text-success">Rp ' + parseInt(payment.jumlah_bayar).toLocaleString('id-ID') + '</strong></td>';
                        paymentHistoryHtml += '<td>' + (metodeHtml[payment.metode_pembayaran] || payment.metode_pembayaran) + '</td>';
                        paymentHistoryHtml += '<td>' + (payment.keterangan || '-') + '</td>';
                        paymentHistoryHtml += '</tr>';
                    });

                    paymentHistoryHtml += '</tbody></table></div>';
                } else {
                    paymentHistoryHtml = '<div class="alert alert-warning"><i class="fas fa-info-circle me-2"></i>Belum ada pembayaran untuk tagihan ini.</div>';
                }
                $('#detail_payment_history').html(paymentHistoryHtml);

                $('#detailModal').modal('show');
            } else {
                showToast('Gagal memuat detail iuran', 'error');
            }
        })
        .fail(function() {
            showToast('Gagal mengambil data iuran', 'error');
        });
}

function confirmDelete(id) {
    console.log('🗑️ confirmDelete called with id:', id);

    // Set the delete form action
    $('#deleteForm').attr('action', '/admin/api/iuran/' + id);

    // Show delete confirmation modal
    $('#deleteModal').modal('show');
}

$(document).ready(function() {
    let currentPage = 1;
    let currentUrl = '/admin/iuran';

    // Load statistics
    function loadStatistics() {
        console.log('📊 loadStatistics called');

        // Get same filter parameters as loadIuran
        var formData = $('#filterForm').serializeArray();
        var params = {};

        $.each(formData, function(i, field) {
            params[field.name] = field.value;
        });

        console.log('📊 Statistics filter params:', params);

        // Build query string for statistics
        var queryString = $.param(params);
        var statisticsUrl = '/admin/api/iuran/statistics';
        if (queryString) {
            statisticsUrl += '?' + queryString;
        }

        console.log('📊 Statistics URL:', statisticsUrl);

        $.get(statisticsUrl)
            .done(function(response) {
                console.log('✅ Statistics success:', response);
                if (response.success) {
                    const data = response.data;
                    $('#stats-total').text(data.total);
                    $('#stats-lunas').text(data.lunas);
                    $('#stats-belum').text(data.belum_bayar);
                    $('#stats-persentase').text(data.persentase_lunas + '%');

                    // Debug: Log statistics update
                    console.log('📊 Updated statistics:', {
                        total: data.total,
                        lunas: data.lunas,
                        belum_bayar: data.belum_bayar,
                        persentase: data.persentase_lunas + '%'
                    });
                }
            })
            .fail(function(xhr) {
                console.error('❌ Statistics error:', xhr);
            });
    }

    // Load iuran data
    function loadIuran(page = 1) {
        console.log('🔄 loadIuran called with page:', page);
        showLoading();

        var formData = $('#filterForm').serializeArray();
        var params = {};

        $.each(formData, function(i, field) {
            params[field.name] = field.value;
        });

        params.page = page;
        console.log('📤 Request params:', params);

        $.ajax({
            url: '/admin/api/iuran',
            type: 'GET',
            data: params,
            success: function(response) {
                console.log('✅ AJAX Success:', response);
                hideLoading();
                if (response.success) {
                    console.log('📊 Rendering table with data:', response.data);
                    console.log('📄 Pagination info:', response.pagination);
                    renderIuranTable(response.data);
                    renderPagination(response.pagination);
                } else {
                    console.error('❌ Response error:', response.message);
                    if (typeof showToast !== 'undefined') {
                        showToast(response.message, 'error');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            },
            error: function(xhr) {
                console.error('❌ AJAX Error:', xhr);
                console.log('Status:', xhr.status);
                console.log('Response Text:', xhr.responseText);
                console.log('Response JSON:', xhr.responseJSON);

                hideLoading();
                var message = xhr.responseJSON?.message || 'Gagal memuat data iuran';
                console.log('📝 Error message:', message);

                if (typeof showToast !== 'undefined') {
                    showToast(message, 'error');
                } else {
                    alert('Error: ' + message);
                }
            }
        });
    }

    // Render iuran table
    function renderIuranTable(iuranList) {
        console.log('🎨 renderIuranTable called with:', iuranList);
        var html = '';
        var no = 1;

        if (iuranList.length === 0) {
            console.log('📭 No data available');
            html = '<tr><td colspan="10" class="text-center text-muted">Tidak ada data iuran</td></tr>';
        } else {
            console.log(`📊 Rendering ${iuranList.length} items`);
            iuranList.forEach(function(iuran) {
                var statusBadge = '';
                switch(iuran.status) {
                    case 'belum_bayar':
                        statusBadge = '<span class="badge badge-danger">Belum Bayar</span>';
                        break;
                    case 'sebagian':
                        statusBadge = '<span class="badge badge-warning">Sebagian</span>';
                        break;
                    case 'lunas':
                        statusBadge = '<span class="badge badge-success">Lunas</span>';
                        break;
                    case 'batal':
                        statusBadge = '<span class="badge badge-secondary">Batal</span>';
                        break;
                    default:
                        statusBadge = '<span class="badge badge-secondary">' + iuran.status + '</span>';
                }

                // Format periode (F Y)
                var periodeFormatted = '-';
                if (iuran.periode_bulan) {
                    var [year, month] = iuran.periode_bulan.split('-');
                    var dateObj = new Date(year, month - 1);
                    periodeFormatted = dateObj.toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long'
                    });
                }

                // Format jatuh tempo (d F Y)
                var jatuhTempo = '-';
                if (iuran.jatuh_tempo) {
                    var date = new Date(iuran.jatuh_tempo);
                    jatuhTempo = date.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        year: 'numeric',
                        month: 'long'
                    });
                }

                html += `
                    <tr>
                        <td>${no++}</td>
                        <td>${periodeFormatted}</td>
                        <td><code>${iuran.keluarga?.no_kk || '-'}</code></td>
                        <td>${iuran.keluarga?.kepala_keluarga?.nama_lengkap || '-'}</td>
                        <td>${iuran.jenis_iuran?.nama || '-'}</td>
                        <td>Rp ${Number(iuran.nominal || 0).toLocaleString('id-ID')}</td>
                        <td>${statusBadge}</td>
                        <td>${jatuhTempo}</td>
                        <td>
                            ${iuran.status === 'lunas' ?
                                `<button type="button" class="btn btn-sm btn-success" disabled title="Sudah Lunas">
                                    <i class="fas fa-check-circle"></i>
                                </button>` :
                                `<button type="button" class="btn btn-sm btn-info" onclick="showPaymentModal(${iuran.id})" title="Bayar">
                                    <i class="fas fa-money-bill-wave"></i>
                                </button>`
                            }
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-info" onclick="showIuranDetail(${iuran.id})" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${iuran.id})" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        console.log('💾 Updating #dataTable with HTML');
        $('#dataTable').html(html);
        console.log('✅ Table render completed');
    }

    // Render pagination
    function renderPagination(pagination) {
        var html = '';

        // Previous button
        if (pagination.current_page > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadIuran(${pagination.current_page - 1})">Previous</a></li>`;
        }

        // Page numbers
        for (var i = 1; i <= pagination.last_page; i++) {
            var active = i === pagination.current_page ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadIuran(${i})">${i}</a></li>`;
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadIuran(${pagination.current_page + 1})">Next</a></li>`;
        }

        $('#paginationLinks').html(html);
        $('#paginationInfo').html(`Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total || 0} entries`);
    }


    // Bind event handlers
    function bindEventHandlers() {
        // Delete button
        $('.delete-btn').off('click').on('click', function() {
            const id = $(this).data('id');
            $('#deleteForm').attr('action', `/admin/iuran/${id}`);
            $('#deleteModal').modal('show');
        });

        // Payment button
        $('.payment-btn').off('click').on('click', function() {
            const id = $(this).data('id');
            const data = $(this).data('record');

            $('#payment_iuran_id').val(id);
            $('#paymentInfo').html(`
                <strong>No. KK:</strong> ${data.keluarga?.no_kk || '-'}<br>
                <strong>Kepala Keluarga:</strong> ${data.keluarga?.kepala_keluarga || '-'}<br>
                <strong>Jenis Iuran:</strong> ${data.jenis_iuran?.nama || '-'}<br>
                <strong>Periode:</strong> ${data.periode_bulan || '-'}<br>
                <strong>Total Tagihan:</strong> Rp ${Number(data.total_tagihan || 0).toLocaleString('id-ID')}
            `);

            // Set max payment amount
            $('#jumlah_bayar').attr('max', data.total_tagihan);
            $('#jumlah_bayar').val(data.total_tagihan);

            $('#paymentModal').modal('show');
        });

        // Payment history button
        $('.payment-history-btn').off('click').on('click', function() {
            const id = $(this).data('id');
            loadPaymentHistory(id);
            $('#paymentHistoryModal').modal('show');
        });
    }

    // Auto-trigger search with debounce
    let searchTimeout;
    $('#search, #periode, #status').on('input change', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadStatistics();
            loadIuran(1);
        }, 500);
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#filterForm')[0].reset();
        loadStatistics();
        loadIuran(1);
    });

    // Removed jenis iuran loading - now using server-side rendered options

    // Create form submission
    $('#createForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post('/admin/iuran', formData)
            .done(function(response) {
                if (response.success) {
                    $('#createModal').modal('hide');
                    $('#createForm')[0].reset();
                    loadStatistics();
                    loadIuran(currentPage);
                    showToast(response.message, 'success');
                } else {
                    showToast(response.message, 'error');
                }
            })
            .fail(function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = '';
                    for (const field in errors) {
                        errorMessage += `${errors[field][0]}\n`;
                    }
                    showToast(errorMessage, 'error');
                } else {
                    showToast('Terjadi kesalahan', 'error');
                }
            });
    });

    
    // Generate bulk form submission
    $('#generateBulkForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post('/admin/api/iuran/generate-bulk', formData)
            .done(function(response) {
                if (response.success) {
                    $('#generateBulkModal').modal('hide');
                    $('#generateBulkForm')[0].reset();
                    loadStatistics();
                    loadIuran(currentPage);
                    showToast(response.message, 'success');
                } else {
                    showToast(response.message, 'error');
                }
            })
            .fail(function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = '';
                    for (const field in errors) {
                        errorMessage += `${errors[field][0]}\n`;
                    }
                    showToast(errorMessage, 'error');
                } else {
                    showToast('Terjadi kesalahan', 'error');
                }
            });
    });

    // Delete form submission
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        const url = $(this).attr('action');

        $.ajax({
            url: url,
            method: 'DELETE',
            data: $(this).serialize()
        })
            .done(function(response) {
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    loadStatistics();
                    loadIuran(currentPage);
                    showToast(response.message, 'success');
                } else {
                    showToast(response.message, 'error');
                }
            })
            .fail(function(xhr) {
                showToast(xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
            });
    });

    // Load payment history
    function loadPaymentHistory(iuranId) {
        $.get(`/admin/api/iuran/${iuranId}/payment-history`)
            .done(function(response) {
                if (response.success) {
                    const tbody = $('#paymentHistoryTable');
                    tbody.empty();

                    if (response.data.length === 0) {
                        tbody.append('<tr><td colspan="6" class="text-center">Belum ada pembayaran</td></tr>');
                        return;
                    }

                    response.data.forEach(function(payment) {
                        const row = `
                            <tr>
                                <td>${formatDateTime(payment.created_at)}</td>
                                <td><strong class="text-success">Rp ${Number(payment.jumlah_bayar).toLocaleString('id-ID')}</strong></td>
                                <td>${getPaymentMethodLabel(payment.metode_pembayaran)}</td>
                                <td>${payment.created_by_name || '-'}</td>
                                <td>${payment.keterangan || '-'}</td>
                                <td><code>${payment.nomor_referensi || '-'}</code></td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                }
            })
            .fail(function() {
                showToast('Gagal memuat riwayat pembayaran', 'error');
            });
    }

    // Payment method change handler
    $('#metode_pembayaran').on('change', function() {
        const method = $(this).val();

        // Suggest filling nomor_referensi for non-cash payments
        if (method !== 'cash') {
            $('#nomor_referensi').attr('placeholder', 'Nomor referensi wajib diisi untuk ' + method);
        } else {
            $('#nomor_referensi').attr('placeholder', 'Nomor referensi transaksi (opsional)');
        }
    });

    // Payment form submission
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();

        // AJAX form submission
        $.ajax({
            url: '/admin/api/iuran/payment',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#paymentModal').modal('hide');
                    $('#paymentForm')[0].reset();
                    loadStatistics();
                    loadIuran(currentPage);
                    showToast(response.message, 'success');
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = '';
                    for (const field in errors) {
                        errorMessage += `${errors[field][0]}\n`;
                    }
                    showToast(errorMessage, 'error');
                } else {
                    showToast(xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            }
        });
    });

    // Utility functions
    function getPaymentMethodLabel(method) {
        const labels = {
            'cash': 'Tunai',
            'transfer': 'Transfer Bank',
            'qris': 'QRIS',
            'ewallet': 'E-Wallet'
        };
        return labels[method] || method;
    }

    
    function formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('id-ID');
    }

    // Event handlers for filters
    console.log('🎯 Setting up filter event handlers');
    $('#periode, #status').on('change', function() {
        console.log('🔄 Filter changed, reloading data');
        loadStatistics();
        loadIuran();
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        console.log('🔄 Reset filters clicked');
        $('#filterForm')[0].reset();
        loadStatistics();
        loadIuran();
    });

    // Initial load
    console.log('🚀 Starting initial data load');
    loadStatistics();
    loadIuran();
});
</script>
@endpush