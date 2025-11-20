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
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateBulkModal">
                            <i class="fas fa-magic me-1"></i>
                            Generate Bulk
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
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
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="search" name="search" placeholder="Cari No. KK, Nama, atau Jenis Iuran...">
                            </div>
                            <div class="col-md-2">
                                <input type="month" class="form-control" id="periode" name="periode" placeholder="Periode">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="belum_bayar">Belum Bayar</option>
                                    <option value="sebagian">Sebagian</option>
                                    <option value="lunas">Lunas</option>
                                    <option value="batal">Batal</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="keluarga_id" name="keluarga_id">
                                    <option value="">Semua Keluarga</option>
                                    @foreach($keluargas as $keluarga)
                                        <option value="{{ $keluarga->id }}">{{ $keluarga->no_kk }} - {{ $keluarga->nama_kepala_keluarga }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-secondary" id="resetFilters">
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
                <h5 class="modal-title">Tambah Tagihan Iuran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Keluarga <span class="text-danger">*</span></label>
                            <select class="form-select" id="kk_id" name="kk_id" required>
                                <option value="">Pilih Keluarga</option>
                                @foreach($keluargas as $keluarga)
                                    <option value="{{ $keluarga->id }}" data-nomor="{{ $keluarga->no_kk }}">{{ $keluarga->no_kk }} - {{ $keluarga->nama_kepala_keluarga }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Iuran <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_iuran_id" name="jenis_iuran_id" required>
                                <option value="">Pilih Keluarga dulu</option>
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
                            <select class="form-select" id="status" name="status" required>
                                <option value="belum_bayar">Belum Bayar</option>
                                <option value="sebagian">Sebagian</option>
                                <option value="lunas">Lunas</option>
                                <option value="batal">Batal</option>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tagihan Iuran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Keluarga <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_kk_id" name="kk_id" required>
                                @foreach($keluargas as $keluarga)
                                    <option value="{{ $keluarga->id }}">{{ $keluarga->no_kk }} - {{ $keluarga->nama_kepala_keluarga }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Iuran <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_jenis_iuran_id" name="jenis_iuran_id" required>
                                @foreach($jenisIurans as $jenisIuran)
                                    <option value="{{ $jenisIuran->id }}">{{ $jenisIuran->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periode <span class="text-danger">*</span></label>
                            <input type="month" class="form-control" id="edit_periode_bulan" name="periode_bulan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nominal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_nominal" name="nominal" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="belum_bayar">Belum Bayar</option>
                                <option value="sebagian">Sebagian</option>
                                <option value="lunas">Lunas</option>
                                <option value="batal">Batal</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Denda Terlambat</label>
                            <input type="number" class="form-control" id="edit_denda_terlambatan" name="denda_terlambatan" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jatuh Tempo</label>
                            <input type="date" class="form-control" id="edit_jatuh_tempo" name="jatuh_tempo">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Update
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tagihan iuran ini?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Tagihan yang sudah memiliki pembayaran tidak dapat dihapus.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                <option value="">Pilih Metode</option>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                                <option value="gopay">GoPay</option>
                                <option value="ovo">OVO</option>
                                <option value="dana">DANA</option>
                                <option value="shopeepay">ShopeePay</option>
                                <option value="linkaja">LinkAja</option>
                                <option value="ewallet">E-Wallet Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="bankDetailsGroup" style="display: none;">
                            <label class="form-label">Nama Bank</label>
                            <select class="form-select" id="nama_bank" name="nama_bank">
                                <option value="">Pilih Bank</option>
                                <option value="bca">BCA</option>
                                <option value="bni">BNI</option>
                                <option value="bri">BRI</option>
                                <option value="mandiri">Mandiri</option>
                                <option value="bsi">BSI</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="nomorRekeningGroup" style="display: none;">
                            <label class="form-label">Nomor Rekening/Referensi</label>
                            <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening">
                        </div>
                        <div class="col-md-6" id="namaPengirimGroup" style="display: none;">
                            <label class="form-label">Nama Pengirim</label>
                            <input type="text" class="form-control" id="nama_pengirim" name="nama_pengirim">
                        </div>
                        <div class="col-md-6" id="waktuPembayaranGroup" style="display: none;">
                            <label class="form-label">Waktu Pembayaran</label>
                            <input type="datetime-local" class="form-control" id="waktu_pembayaran" name="waktu_pembayaran">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan_pembayaran" name="keterangan" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>
                        <div class="col-12" id="buktiPembayaranGroup" style="display: none;">
                            <label class="form-label">Bukti Pembayaran</label>
                            <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*,.pdf">
                            <small class="text-muted">Format: JPG, PNG, atau PDF. Maksimal 2MB</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Dibuat Oleh</th>
                                <th>Keterangan</th>
                                <th>Bukti</th>
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
$(document).ready(function() {
    let currentPage = 1;
    let currentUrl = '/admin/iuran';

    // Load statistics
    function loadStatistics() {
        const periode = $('#periode').val() || new Date().toISOString().slice(0, 7);

        $.get('/admin/api/iuran/statistics?periode=' + periode)
            .done(function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#stats-total').text(data.total);
                    $('#stats-lunas').text(data.lunas);
                    $('#stats-belum').text(data.belum_bayar);
                    $('#stats-persentase').text(data.persentase_lunas + '%');
                }
            });
    }

    // Load data table
    function loadData(page = 1) {
        const formData = $('#filterForm').serialize();
        const url = `${currentUrl}?page=${page}&${formData}`;

        $.get(url)
            .done(function(html) {
                // Extract table content and pagination from response
                const tempDiv = $('<div>').html(html);
                $('#dataTable').html(tempDiv.find('#dataTable').html());
                $('#paginationInfo').html(tempDiv.find('#paginationInfo').html());
                $('#paginationLinks').html(tempDiv.find('#paginationLinks').html());

                // Update current page
                currentPage = page;

                // Re-bind event handlers for new content
                bindEventHandlers();
            })
            .fail(function() {
                Swal.fire('Error', 'Gagal memuat data', 'error');
            });
    }

    // Bind event handlers
    function bindEventHandlers() {
        // Edit button
        $('.edit-btn').off('click').on('click', function() {
            const id = $(this).data('id');
            $.get(`/admin/iuran/${id}/edit`)
                .done(function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#edit_id').val(data.id);
                        $('#edit_kk_id').val(data.kk_id);
                        $('#edit_jenis_iuran_id').val(data.jenis_iuran_id);
                        $('#edit_periode_bulan').val(data.periode_bulan);
                        $('#edit_nominal').val(data.nominal);
                        $('#edit_status').val(data.status);
                        $('#edit_denda_terlambatan').val(data.denda_terlambatan);
                        $('#edit_jatuh_tempo').val(data.jatuh_tempo);
                        $('#edit_keterangan').val(data.keterangan);
                        $('#editModal').modal('show');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                });
        });

        // Delete button
        $('.delete-btn').off('click').on('click', function() {
            const id = $(this).data('id');
            $('#deleteForm').attr('action', `/admin/iuran/${id}`);
            $('#deleteModal').modal('show');
        });

        // Detail button
        $('.detail-btn').off('click').on('click', function() {
            const id = $(this).data('id');
            window.location.href = `/admin/iuran/${id}`;
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
    $('#search, #periode, #status, #keluarga_id').on('input change', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadStatistics();
            loadData(1);
        }, 500);
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#filterForm')[0].reset();
        loadStatistics();
        loadData(1);
    });

    // Load jenis iuran when keluarga selected
    $('#kk_id').on('change', function() {
        const keluargaId = $(this).val();
        if (keluargaId) {
            $.get(`/admin/api/iuran/keluarga/${keluargaId}/jenis-iuran`)
                .done(function(response) {
                    if (response.success) {
                        const select = $('#jenis_iuran_id');
                        select.html('<option value="">Pilih Jenis Iuran</option>');
                        response.data.forEach(function(jenis) {
                            select.append(`<option value="${jenis.id}">${jenis.nama} - Rp ${Number(jenis.effective_nominal || jenis.jumlah).toLocaleString('id-ID')}</option>`);
                        });
                    }
                });
        } else {
            $('#jenis_iuran_id').html('<option value="">Pilih Keluarga dulu</option>');
        }
    });

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
                    loadData(currentPage);
                    Swal.fire('Success', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            })
            .fail(function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = '';
                    for (const field in errors) {
                        errorMessage += `${errors[field][0]}\n`;
                    }
                    Swal.fire('Validation Error', errorMessage, 'error');
                } else {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                }
            });
    });

    // Edit form submission
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        const formData = $(this).serialize();

        $.ajax({
            url: `/admin/iuran/${id}`,
            method: 'PUT',
            data: formData
        })
            .done(function(response) {
                if (response.success) {
                    $('#editModal').modal('hide');
                    loadStatistics();
                    loadData(currentPage);
                    Swal.fire('Success', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            })
            .fail(function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = '';
                    for (const field in errors) {
                        errorMessage += `${errors[field][0]}\n`;
                    }
                    Swal.fire('Validation Error', errorMessage, 'error');
                } else {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
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
                    loadData(currentPage);
                    Swal.fire('Success', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            })
            .fail(function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = '';
                    for (const field in errors) {
                        errorMessage += `${errors[field][0]}\n`;
                    }
                    Swal.fire('Validation Error', errorMessage, 'error');
                } else {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
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
                    loadData(currentPage);
                    Swal.fire('Success', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            })
            .fail(function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
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
                        tbody.append('<tr><td colspan="7" class="text-center">Belum ada pembayaran</td></tr>');
                        return;
                    }

                    response.data.forEach(function(payment) {
                        const statusBadge = getStatusBadge(payment.status);
                        const buktiHtml = payment.bukti_pembayaran
                            ? `<a href="/storage/pembayaran/${payment.bukti_pembayaran}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>`
                            : '-';

                        const row = `
                            <tr>
                                <td>${formatDateTime(payment.created_at)}</td>
                                <td>Rp ${Number(payment.jumlah).toLocaleString('id-ID')}</td>
                                <td>${getPaymentMethodLabel(payment.metode_pembayaran)}</td>
                                <td>${statusBadge}</td>
                                <td>${payment.created_by_name || '-'}</td>
                                <td>${payment.keterangan || '-'}</td>
                                <td>${buktiHtml}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                }
            })
            .fail(function() {
                Swal.fire('Error', 'Gagal memuat riwayat pembayaran', 'error');
            });
    }

    // Payment method change handler
    $('#metode_pembayaran').on('change', function() {
        const method = $(this).val();

        // Hide all optional fields
        $('#bankDetailsGroup, #nomorRekeningGroup, #namaPengirimGroup, #waktuPembayaranGroup, #buktiPembayaranGroup').hide();

        // Show fields based on payment method
        if (method === 'transfer') {
            $('#bankDetailsGroup, #nomorRekeningGroup, #namaPengirimGroup, #waktuPembayaranGroup, #buktiPembayaranGroup').show();
            $('#waktu_pembayaran').val(new Date().toISOString().slice(0, 16));
        } else if (['qris', 'gopay', 'ovo', 'dana', 'shopeepay', 'linkaja', 'ewallet'].includes(method)) {
            $('#nomorRekeningGroup, #buktiPembayaranGroup').show();
        }
    });

    // Payment form submission
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // AJAX form submission with file support
        $.ajax({
            url: '/admin/api/iuran/payment',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#paymentModal').modal('hide');
                    $('#paymentForm')[0].reset();
                    $('#bankDetailsGroup, #nomorRekeningGroup, #namaPengirimGroup, #waktuPembayaranGroup, #buktiPembayaranGroup').hide();
                    loadStatistics();
                    loadData(currentPage);
                    Swal.fire('Success', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = '';
                    for (const field in errors) {
                        errorMessage += `${errors[field][0]}\n`;
                    }
                    Swal.fire('Validation Error', errorMessage, 'error');
                } else {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                }
            }
        });
    });

    // Utility functions
    function getPaymentMethodLabel(method) {
        const labels = {
            'tunai': 'Tunai',
            'transfer': 'Transfer Bank',
            'qris': 'QRIS',
            'gopay': 'GoPay',
            'ovo': 'OVO',
            'dana': 'DANA',
            'shopeepay': 'ShopeePay',
            'linkaja': 'LinkAja',
            'ewallet': 'E-Wallet'
        };
        return labels[method] || method;
    }

    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge bg-warning">Pending</span>',
            'verified': '<span class="badge bg-success">Terverifikasi</span>',
            'rejected': '<span class="badge bg-danger">Ditolak</span>'
        };
        return badges[status] || status;
    }

    function formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('id-ID');
    }

    // Initial load
    loadStatistics();
    loadData(1);
});
</script>
@endpush