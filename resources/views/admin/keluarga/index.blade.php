@extends('layouts.app')

@section('title', 'Data Keluarga - SIWA')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-id-card mr-2"></i>Data Keluarga
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-user" onclick="showCreateModal()">
                <i class="fas fa-plus mr-2"></i>Tambah Keluarga
            </button>
            <button type="button" class="btn btn-info btn-user" onclick="showImportModal()">
                <i class="fas fa-file-import mr-2"></i>Import
            </button>
            <button type="button" class="btn btn-success btn-user" onclick="exportData()">
                <i class="fas fa-file-export mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total KK
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalKeluarga">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Anggota
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAnggota">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Rata-rata Anggota
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rataRataAnggota">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                KK Tanpa Kepala
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kkTanpaKepala">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Pencarian dan Filter
            </h6>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <label for="search">Cari KK</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" placeholder="No. KK atau Nama Kepala Keluarga">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="rt">RT</label>
                        <input type="text" class="form-control" id="rt" name="rt" placeholder="RT">
                    </div>
                    <div class="col-md-2">
                        <label for="rw">RW</label>
                        <input type="text" class="form-control" id="rw" name="rw" placeholder="RW">
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="resetFilters()">
                            <i class="fas fa-redo mr-1"></i>Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Keluarga Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>Daftar Keluarga
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="#" onclick="refreshData()">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </a>
                    <a class="dropdown-item" href="#" onclick="showStatistics()">
                        <i class="fas fa-chart-bar mr-2"></i>Statistik
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="keluargaTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Alamat</th>
                            <th>RT/RW</th>
                            <th>Jumlah Anggota</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">
                                <i class="fas fa-spinner fa-spin"></i>
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="form-group mb-0">
                    <label for="perPage">Show:</label>
                    <select class="form-control form-control-sm d-inline-block ml-2" id="perPage" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <nav>
                    <ul class="pagination mb-0" id="pagination">
                        <!-- Pagination will be loaded via AJAX -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

</div>

<!-- Create/Edit Keluarga Modal -->
<div class="modal fade" id="keluargaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="keluargaModalTitle">
                    <i class="fas fa-id-card mr-2"></i>Tambah Data Keluarga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="keluargaForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="keluarga_id" name="keluarga_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_kk">No. KK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="no_kk" name="no_kk" maxlength="16" required>
                                <small class="form-text text-muted">16 digit angka</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kepala_keluarga_id">Kepala Keluarga</label>
                                <select class="form-control" id="kepala_keluarga_id" name="kepala_keluarga_id">
                                    <option value="">Pilih Kepala Keluarga</option>
                                </select>
                                <small class="form-text text-muted">Pilih dari daftar warga yang belum memiliki KK</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat_kk">Alamat KK <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alamat_kk" name="alamat_kk" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="rt_kk">RT <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="rt_kk" name="rt_kk" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="rw_kk">RW <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="rw_kk" name="rw_kk" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kelurahan_kk">Kelurahan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kelurahan_kk" name="kelurahan_kk" required>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6><i class="fas fa-users mr-2"></i>Anggota Keluarga</h6>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Pilih warga yang akan menjadi anggota keluarga ini. Warga yang dipilih akan secara otomatis terhubung dengan KK ini.
                    </div>

                    <div id="anggotaContainer">
                        <div class="row anggota-row mb-2">
                            <div class="col-md-6">
                                <select class="form-control select-warga" name="anggota_ids[]" required>
                                    <option value="">Pilih Warga</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" name="hubungan_anggota[]" required>
                                    <option value="">Pilih Hubungan</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-danger remove-anggota" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-sm btn-success" onclick="addAnggotaRow()">
                            <i class="fas fa-plus mr-2"></i>Tambah Anggota
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveKeluargaBtn">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Keluarga Modal -->
<div class="modal fade" id="viewKeluargaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-id-card mr-2"></i>Detail Data Keluarga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewKeluargaContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="editKeluargaBtn">Edit</button>
                <button type="button" class="btn btn-info" id="addMemberBtn">Tambah Anggota</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash mr-2 text-danger"></i>Hapus Data Keluarga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm">
                @csrf
                <input type="hidden" id="delete_keluarga_id">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data keluarga ini?</p>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Semua anggota keluarga akan terlepas dari KK ini dan data yang dihapus tidak dapat dikembalikan.
                    </div>
                    <div class="alert alert-info">
                        <strong>No. KK:</strong> <span id="delete_no_kk"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus mr-2"></i>Tambah Anggota Keluarga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMemberForm">
                @csrf
                <input type="hidden" id="add_member_keluarga_id" name="keluarga_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="warga_id">Pilih Warga <span class="text-danger">*</span></label>
                        <select class="form-control" id="warga_id" name="warga_id" required>
                            <option value="">Pilih Warga</option>
                        </select>
                        <small class="form-text text-muted">Warga yang belum memiliki KK</small>
                    </div>
                    <div class="form-group">
                        <label for="hubungan_keluarga">Hubungan dalam Keluarga <span class="text-danger">*</span></label>
                        <select class="form-control" id="hubungan_keluarga" name="hubungan_keluarga" required>
                            <option value="">Pilih Hubungan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="addMemberBtn">
                        <i class="fas fa-plus mr-2"></i>Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadKeluarga();
    loadStatistics();
    loadFormData();

    // Form submission
    $('#keluargaForm').on('submit', function(e) {
        e.preventDefault();
        saveKeluarga();
    });

    // Delete form submission
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        deleteKeluarga();
    });

    // Add member form submission
    $('#addMemberForm').on('submit', function(e) {
        e.preventDefault();
        addMember();
    });

    // Per page change
    $('#perPage').on('change', function() {
        loadKeluarga();
    });

    // Initialize with one anggota row
    updateWargaOptions();
});

function loadKeluarga(page = 1) {
    showLoading();

    var formData = $('#filterForm').serializeArray();
    var params = {};

    $.each(formData, function(i, field) {
        params[field.name] = field.value;
    });

    params.page = page;
    params.per_page = $('#perPage').val();

    $.ajax({
        url: '/api/keluarga',
        type: 'GET',
        data: params,
        success: function(response) {
            hideLoading();
            if (response.success) {
                renderKeluargaTable(response.data, response.pagination);
                renderPagination(response.pagination);
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal memuat data keluarga';
            showToast(message, 'error');
        }
    });
}

function renderKeluargaTable(keluargaList, pagination) {
    var html = '';
    var no = (pagination.current_page - 1) * pagination.per_page;

    if (keluargaList.length === 0) {
        html = '<tr><td colspan="7" class="text-center text-muted">Tidak ada data keluarga</td></tr>';
    } else {
        keluargaList.forEach(function(keluarga) {
            no++;
            var namaKepala = keluarga.nama_kepala_keluarga || 'Belum ditentukan';
            var jumlahAnggota = keluarga.jumlah_anggota || keluarga.anggota_keluarga?.length || 0;

            html += `
                <tr>
                    <td>${no}</td>
                    <td><code>${keluarga.no_kk_format || keluarga.no_kk}</code></td>
                    <td><strong>${namaKepala}</strong></td>
                    <td><small>${keluarga.alamat_kk}</small></td>
                    <td>${keluarga.rt_kk}/${keluarga.rw_kk}</td>
                    <td><span class="badge badge-info">${jumlahAnggota} orang</span></td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-info" onclick="viewKeluarga(${keluarga.id})" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" onclick="editKeluarga(${keluarga.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-success" onclick="showAddMemberModal(${keluarga.id})" title="Tambah Anggota">
                                <i class="fas fa-user-plus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${keluarga.id}, '${keluarga.no_kk}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    $('#keluargaTable tbody').html(html);
}

function renderPagination(pagination) {
    var html = '';

    // Previous button
    if (pagination.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadKeluarga(${pagination.current_page - 1})">Previous</a></li>`;
    }

    // Page numbers
    for (var i = 1; i <= pagination.last_page; i++) {
        var active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadKeluarga(${i})">${i}</a></li>`;
    }

    // Next button
    if (pagination.current_page < pagination.last_page) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadKeluarga(${pagination.current_page + 1})">Next</a></li>`;
    }

    $('#pagination').html(html);
}

function loadStatistics() {
    $.ajax({
        url: '/api/keluarga/statistics',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#totalKeluarga').text(response.data.total_keluarga);
                $('#totalAnggota').text(response.data.total_anggota);
                $('#rataRataAnggota').text(response.data.rata_rata_anggota);
                $('#kkTanpaKepala').text(response.data.kk_tanpa_kepala);
            }
        },
        error: function(xhr) {
            console.error('Failed to load statistics');
        }
    });
}

function loadFormData() {
    $.ajax({
        url: '/api/keluarga/create',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                populateSelectOptions(response.data);
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Gagal memuat form data';
            showToast(message, 'error');
        }
    });
}

function populateSelectOptions(data) {
    // Populate hubungan keluarga
    var hubunganHtml = '<option value="">Pilih Hubungan</option>';
    data.daftar_hubungan.forEach(function(hubungan) {
        hubunganHtml += `<option value="${hubungan}">${hubungan}</option>`;
    });

    $('.select-warga').each(function() {
        $(this).html('<option value="">Pilih Warga</option>');
        data.warga_list.forEach(function(warga) {
            $(this).append(`<option value="${warga.id}">${warga.nama_lengkap} - ${warga.nik}</option>`);
        });
    });

    $('[name="hubungan_anggota[]"]').html(hubunganHtml);
    $('#kepala_keluarga_id').html('<option value="">Pilih Kepala Keluarga</option>');
    data.warga_list.forEach(function(warga) {
        $('#kepala_keluarga_id').append(`<option value="${warga.id}">${warga.nama_lengkap} - ${warga.nik}</option>`);
    });
}

function updateWargaOptions() {
    $.ajax({
        url: '/api/keluarga/create',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                populateSelectOptions(response.data);
            }
        },
        error: function(xhr) {
            console.error('Failed to update warga options');
        }
    });
}

function showCreateModal() {
    $('#keluargaModalTitle').html('<i class="fas fa-id-card mr-2"></i>Tambah Data Keluarga');
    $('#saveKeluargaBtn').html('<i class="fas fa-save mr-2"></i>Simpan');
    $('#keluargaForm')[0].reset();
    $('#keluarga_id').val('');

    // Reset anggota rows
    $('#anggotaContainer').html(`
        <div class="row anggota-row mb-2">
            <div class="col-md-6">
                <select class="form-control select-warga" name="anggota_ids[]" required>
                    <option value="">Pilih Warga</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-control" name="hubungan_anggota[]" required>
                    <option value="">Pilih Hubungan</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger remove-anggota" style="display: none;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `);

    updateWargaOptions();
    $('#keluargaModal').modal('show');
}

function addAnggotaRow() {
    var newRow = `
        <div class="row anggota-row mb-2">
            <div class="col-md-6">
                <select class="form-control select-warga" name="anggota_ids[]" required>
                    <option value="">Pilih Warga</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-control" name="hubungan_anggota[]" required>
                    <option value="">Pilih Hubungan</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger remove-anggota">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    $('#anggotaContainer').append(newRow);
    updateWargaOptions();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    var rows = $('.anggota-row').length;
    $('.remove-anggota').each(function() {
        $(this).toggle(rows > 1);
    });
}

$(document).on('click', '.remove-anggota', function() {
    $(this).closest('.anggota-row').remove();
    updateRemoveButtons();
});

function editKeluarga(id) {
    showLoading();

    $.ajax({
        url: '/api/keluarga/' + id + '/edit',
        type: 'GET',
        success: function(response) {
            hideLoading();
            if (response.success) {
                var keluarga = response.data.keluarga;

                $('#keluargaModalTitle').html('<i class="fas fa-id-card mr-2"></i>Edit Data Keluarga');
                $('#saveKeluargaBtn').html('<i class="fas fa-save mr-2"></i>Update');

                // Fill form data
                $('#keluarga_id').val(keluarga.id);
                $('#no_kk').val(keluarga.no_kk);
                $('#alamat_kk').val(keluarga.alamat_kk);
                $('#rt_kk').val(keluarga.rt_kk);
                $('#rw_kk').val(keluarga.rw_kk);
                $('#kelurahan_kk').val(keluarga.kelurahan_kk);

                // Populate and select kepala keluarga
                $('#kepala_keluarga_id').html('<option value="">Pilih Kepala Keluarga</option>');
                response.data.warga_list.forEach(function(warga) {
                    var selected = warga.id === keluarga.kepala_keluarga_id ? 'selected' : '';
                    $('#kepala_keluarga_id').append(`<option value="${warga.id}" ${selected}>${warga.nama_lengkap} - ${warga.nik}</option>`);
                });

                // Load current anggota
                $('#anggotaContainer').html('');
                if (keluarga.anggota_keluarga && keluarga.anggota_keluarga.length > 0) {
                    keluarga.anggota_keluarga.forEach(function(anggota) {
                        if (anggota.id !== keluarga.kepala_keluarga_id) {
                            var anggotaRow = `
                                <div class="row anggota-row mb-2">
                                    <div class="col-md-6">
                                        <select class="form-control select-warga" name="anggota_ids[]" required>
                                            <option value="">Pilih Warga</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select class="form-control" name="hubungan_anggota[]" required>
                                            <option value="">Pilih Hubungan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-danger remove-anggota">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                            $('#anggotaContainer').append(anggotaRow);
                        }
                    });
                }

                updateWargaOptions();
                updateRemoveButtons();

                // Select current anggota
                if (keluarga.anggota_keluarga) {
                    keluarga.anggota_keluarga.forEach(function(anggota, index) {
                        if (anggota.id !== keluarga.kepala_keluarga_id) {
                            $('.select-warga').eq(index + 1).val(anggota.id);
                            $('[name="hubungan_anggota[]"]').eq(index).val(anggota.hubungan_keluarga);
                        }
                    });
                }

                $('#keluargaModal').modal('show');
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal memuat data keluarga';
            showToast(message, 'error');
        }
    });
}

function saveKeluarga() {
    var form = $('#keluargaForm')[0];
    var formData = new FormData(form);
    var keluargaId = $('#keluarga_id').val();
    var url = keluargaId ? '/api/keluarga/' + keluargaId : '/api/keluarga';
    var method = keluargaId ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            showLoading();
            $('#saveKeluargaBtn').prop('disabled', true);
        },
        success: function(response) {
            hideLoading();
            $('#saveKeluargaBtn').prop('disabled', false);

            if (response.success) {
                $('#keluargaModal').modal('hide');
                showToast(response.message, 'success');
                loadKeluarga();
                loadStatistics();
            } else {
                if (response.errors) {
                    displayValidationErrors(response.errors);
                } else {
                    showToast(response.message, 'error');
                }
            }
        },
        error: function(xhr) {
            hideLoading();
            $('#saveKeluargaBtn').prop('disabled', false);

            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                displayValidationErrors(xhr.responseJSON.errors);
            } else {
                var message = xhr.responseJSON?.message || 'Gagal menyimpan data keluarga';
                showToast(message, 'error');
            }
        }
    });
}

function viewKeluarga(id) {
    showLoading();

    $.ajax({
        url: '/api/keluarga/' + id,
        type: 'GET',
        success: function(response) {
            hideLoading();
            if (response.success) {
                var keluarga = response.data;

                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-id-card mr-2"></i>Data KK</h6>
                            <table class="table table-sm">
                                <tr><td>No. KK</td><td><strong>${keluarga.no_kk_format || keluarga.no_kk}</strong></td></tr>
                                <tr><td>Kepala Keluarga</td><td><strong>${keluarga.nama_kepala_keluarga || 'Belum ditentukan'}</strong></td></tr>
                                <tr><td>Alamat</td><td>${keluarga.alamat_kk}</td></tr>
                                <tr><td>RT/RW</td><td>${keluarga.rt_kk}/${keluarga.rw_kk}</td></tr>
                                <tr><td>Kelurahan</td><td>${keluarga.kelurahan_kk}</td></tr>
                                <tr><td>Jumlah Anggota</td><td>${keluarga.jumlah_anggota || keluarga.anggota_keluarga?.length || 0} orang</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-users mr-2"></i>Daftar Anggota</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Hubungan</th>
                                            <th>NIK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;

                if (keluarga.anggota_keluarga && keluarga.anggota_keluarga.length > 0) {
                    keluarga.anggota_keluarga.forEach(function(anggota) {
                        content += `
                            <tr>
                                <td>${anggota.nama_lengkap}</td>
                                <td>${anggota.hubungan_keluarga || '-'}</td>
                                <td><code>${anggota.nik}</code></td>
                            </tr>
                        `;
                    });
                } else {
                    content += '<tr><td colspan="3" class="text-center text-muted">Belum ada anggota</td></tr>';
                }

                content += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <small class="text-muted">
                                Dibuat: ${keluarga.created_at} | Diupdate: ${keluarga.updated_at}
                            </small>
                        </div>
                    </div>
                `;

                $('#viewKeluargaContent').html(content);
                $('#editKeluargaBtn').off('click').on('click', function() {
                    $('#viewKeluargaModal').modal('hide');
                    editKeluarga(id);
                });
                $('#addMemberBtn').off('click').on('click', function() {
                    $('#viewKeluargaModal').modal('hide');
                    showAddMemberModal(id);
                });
                $('#viewKeluargaModal').modal('show');
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal memuat data keluarga';
            showToast(message, 'error');
        }
    });
}

function confirmDelete(id, noKk) {
    $('#delete_keluarga_id').val(id);
    $('#delete_no_kk').text(noKk);
    $('#deleteModal').modal('show');
}

function deleteKeluarga() {
    var id = $('#delete_keluarga_id').val();

    $.ajax({
        url: '/api/keluarga/' + id,
        type: 'DELETE',
        beforeSend: function() {
            showLoading();
        },
        success: function(response) {
            hideLoading();
            $('#deleteModal').modal('hide');

            if (response.success) {
                showToast(response.message, 'success');
                loadKeluarga();
                loadStatistics();
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal menghapus data keluarga';
            showToast(message, 'error');
        }
    });
}

function showAddMemberModal(keluargaId) {
    $('#add_member_keluarga_id').val(keluargaId);
    $('#addMemberForm')[0].reset();

    // Load available warga
    $.ajax({
        url: '/api/keluarga/create',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#warga_id').html('<option value="">Pilih Warga</option>');
                response.data.warga_list.forEach(function(warga) {
                    $('#warga_id').append(`<option value="${warga.id}">${warga.nama_lengkap} - ${warga.nik}</option>`);
                });

                // Populate hubungan
                var hubunganHtml = '<option value="">Pilih Hubungan</option>';
                response.data.daftar_hubungan.forEach(function(hubungan) {
                    hubunganHtml += `<option value="${hubungan}">${hubungan}</option>`;
                });
                $('#hubungan_keluarga').html(hubunganHtml);

                $('#addMemberModal').modal('show');
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Gagal memuat data warga';
            showToast(message, 'error');
        }
    });
}

function addMember() {
    var form = $('#addMemberForm')[0];
    var formData = new FormData(form);
    var keluargaId = $('#add_member_keluarga_id').val();

    $.ajax({
        url: '/api/keluarga/' + keluargaId + '/add-member',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            showLoading();
            $('#addMemberBtn').prop('disabled', true);
        },
        success: function(response) {
            hideLoading();
            $('#addMemberBtn').prop('disabled', false);

            if (response.success) {
                $('#addMemberModal').modal('hide');
                showToast(response.message, 'success');
                loadKeluarga();
                loadStatistics();
            } else {
                if (response.errors) {
                    displayValidationErrors(response.errors);
                } else {
                    showToast(response.message, 'error');
                }
            }
        },
        error: function(xhr) {
            hideLoading();
            $('#addMemberBtn').prop('disabled', false);

            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                displayValidationErrors(xhr.responseJSON.errors);
            } else {
                var message = xhr.responseJSON?.message || 'Gagal menambah anggota';
                showToast(message, 'error');
            }
        }
    });
}

function applyFilters() {
    loadKeluarga(1);
}

function resetFilters() {
    $('#filterForm')[0].reset();
    loadKeluarga(1);
}

function refreshData() {
    loadKeluarga();
    loadStatistics();
}

function showImportModal() {
    showToast('Fitur import akan segera tersedia', 'info');
}

function exportData() {
    showToast('Fitur export akan segera tersedia', 'info');
}

function showStatistics() {
    showToast('Fitur statistik akan segera tersedia', 'info');
}

function displayValidationErrors(errors) {
    var errorMessages = [];
    for (var field in errors) {
        errorMessages.push(errors[field][0]);
    }
    showToast(errorMessages.join('<br>'), 'error');
}
</script>
@endpush