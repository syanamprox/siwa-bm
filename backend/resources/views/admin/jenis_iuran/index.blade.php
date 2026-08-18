@extends('layouts.app')

@section('title', 'Jenis Iuran - SIWA')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cogs mr-2"></i>Jenis Iuran
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-user" onclick="showCreateModal()">
                <i class="fas fa-plus mr-2"></i>Tambah Jenis Iuran
            </button>
            <button type="button" class="btn btn-success btn-user" onclick="exportData()">
                <i class="fas fa-file-export mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Jenis Iuran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalJenisIuran">
                                0
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAktif">
                                0
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Non-Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalNonAktif">
                                0
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filter Data
            </h6>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" placeholder="🔍 Nama, kode, atau keterangan...">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control" id="filterPeriode">
                            <option value="">📅 Semua Periode</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                            <option value="sekali">Sekali</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <select class="form-control" id="filterStatus">
                            <option value="">✅ Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Non-Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="button" class="btn btn-secondary btn-block" onclick="resetFilters()">
                            <i class="fas fa-redo mr-1"></i>Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Daftar Jenis Iuran
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Nominal</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="jenisIuranTableBody">
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Memuat data...</p>
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

<!-- Create/Edit Jenis Iuran Modal -->
<div class="modal fade" id="jenisIuranModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jenisIuranModalTitle">
                    <i class="fas fa-cogs mr-2"></i>Tambah Jenis Iuran
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="jenisIuranForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="jenis_iuran_id" name="jenis_iuran_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama">Nama Jenis Iuran <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode">Kode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode" name="kode" maxlength="10" required>
                                <small class="form-text text-muted">Maksimal 10 karakter</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jumlah">Nominal <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" min="0" step="1000" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="periode">Periode <span class="text-danger">*</span></label>
                                <select class="form-control" id="periode" name="periode" required>
                                    <option value="">Pilih Periode</option>
                                    <option value="bulanan">Bulanan</option>
                                    <option value="tahunan">Tahunan</option>
                                    <option value="sekali">Sekali</option>
                                </select>
                            </div>
                        </div>
                                            </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Deskripsi atau keterangan tambahan..."></textarea>
                            </div>
                            <div class="form-group mt-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_aktif" name="is_aktif" checked>
                                    <label class="custom-control-label" for="is_aktif">Status Aktif</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm">
                @csrf
                <input type="hidden" id="delete_id" name="jenis_iuran_id">
                @method('DELETE')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jenis iuran "<span id="delete_nama"></span>"?</p>
                    <p class="text-warning"><small><strong>Perhatian:</strong> Jenis iuran yang sudah memiliki koneksi dengan keluarga tidak dapat dihapus.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
console.log('JavaScript file loaded, current line:', location.href);

$(document).ready(function() {
    loadJenisIuran();
    loadStatistics();

    // Search auto-trigger with debouncing
    let searchTimeout;
    $('#search').on('input keyup', function() {
        var searchValue = $(this).val();

        // Clear existing timeout
        clearTimeout(searchTimeout);

        // Add searching indicator
        if (searchValue.length > 0) {
            $(this).addClass('is-valid');
        } else {
            $(this).removeClass('is-valid');
        }

        // Set new timeout
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            loadJenisIuran();
        }, 500);
    });

    // Filter change triggers
    $('#filterPeriode, #filterStatus, #perPage').change(function() {
        currentPage = 1;
        loadJenisIuran();
    });

    // Form submission
    $('#jenisIuranForm').on('submit', function(e) {
        e.preventDefault();

        console.log('Form submission started');

        var formData = new FormData(this);
        var jenisIuranId = $('#jenis_iuran_id').val();
        var url = jenisIuranId ? `/admin/api/jenis-iuran/${jenisIuranId}` : '/admin/api/jenis-iuran';
        var method = jenisIuranId ? 'PUT' : 'POST';

        console.log('jenisIuranId:', jenisIuranId);
        console.log('URL:', url);
        console.log('Method:', method);

        // Debug: Log all form data
        console.log('Form data before sending:');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        if (jenisIuranId) {
            formData.append('_method', 'PUT');
        }

        // Convert checkbox 'on' value to boolean
        if (formData.get('is_aktif') === 'on') {
            formData.set('is_aktif', '1');
        } else {
            formData.set('is_aktif', '0');
        }

        console.log('Form data after conversion:');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#jenisIuranModal').modal('hide');
                resetForm();
                loadJenisIuran();
                loadStatistics();
                showAlert('success', jenisIuranId ? 'Jenis iuran berhasil diperbarui!' : 'Jenis iuran berhasil ditambahkan!');
            },
            error: function(xhr) {
                console.log('AJAX Error Response:');
                console.log('Status:', xhr.status);
                console.log('Response Text:', xhr.responseText);
                console.log('Response JSON:', xhr.responseJSON);

                var errors = xhr.responseJSON?.errors || {};
                var errorMsg = 'Terjadi kesalahan. ';

                for (var field in errors) {
                    errorMsg += errors[field][0] + ' ';
                    break;
                }

                if (Object.keys(errors).length === 0) {
                    errorMsg = xhr.responseJSON?.message || `Server error (${xhr.status})`;
                }

                showAlert('danger', errorMsg);
            }
        });
    });

    // Delete form submission
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: `/admin/api/jenis-iuran/${$('#delete_id').val()}`,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                loadJenisIuran();
                loadStatistics();
                showAlert('success', 'Jenis iuran berhasil dihapus!');
            },
            error: function(xhr) {
                showAlert('danger', xhr.responseJSON?.message || 'Gagal menghapus jenis iuran.');
            }
        });
    });
});

let currentPage = 1;

function loadJenisIuran() {
    var search = $('#search').val();
    var periode = $('#filterPeriode').val();
    var status = $('#filterStatus').val();
    var perPage = $('#perPage').val();

    // Show loading
    $('#jenisIuranTableBody').html(`
        <tr>
            <td colspan="7" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Memuat data...</p>
            </td>
        </tr>
    `);

    // Build data object only with non-empty values
    var data = {
        search: search,
        periode: periode,
        page: currentPage,
        per_page: perPage
    };

    // Only add status if it's not empty
    if (status && status !== '') {
        data.status = status;
    }

    $.ajax({
        url: '/admin/api/jenis-iuran',
        type: 'GET',
        data: data,
        success: function(response) {
            displayJenisIuran(response.data);
            displayPagination(response);
        },
        error: function(xhr) {
            $('#jenisIuranTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <p>Gagal memuat data: ${xhr.responseJSON?.message || 'Server error'}</p>
                    </td>
                </tr>
            `);
        }
    });
}

function displayJenisIuran(data) {
    var html = '';

    if (data.length === 0) {
        html = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada data jenis iuran</p>
                </td>
            </tr>
        `;
    } else {
        for (var i = 0; i < data.length; i++) {
            var item = data[i];
            html += `
                <tr>
                    <td>${((currentPage - 1) * $('#perPage').val()) + i + 1}</td>
                    <td><strong>${item.nama}</strong></td>
                    <td><span class="badge bg-info text-white">${item.kode}</span></td>
                    <td>Rp ${number_format(item.jumlah, 0, ',', '.')}</td>
                    <td><span class="badge bg-primary text-white">${item.periode_label}</span></td>
                    <td>
                        <span class="badge ${item.is_aktif ? 'bg-success' : 'bg-warning'} text-white">
                            ${item.is_aktif ? 'Aktif' : 'Non-Aktif'}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-warning" onclick="editJenisIuran(${item.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-info" onclick="toggleStatus(${item.id})" title="Toggle Status">
                                <i class="fas fa-power-off"></i>
                            </button>
                            <button type="button" class="btn btn-danger" onclick="deleteJenisIuran(${item.id}, '${item.nama}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    $('#jenisIuranTableBody').html(html);
}

function displayPagination(response) {
    var pagination = '';

    if (response.last_page > 1) {
        // Previous button
        pagination += `<li class="page-item ${response.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${response.current_page - 1})">Previous</a>
        </li>`;

        // Page numbers
        for (var i = 1; i <= response.last_page; i++) {
            if (i === 1 || i === response.last_page || (i >= response.current_page - 1 && i <= response.current_page + 1)) {
                pagination += `<li class="page-item ${i === response.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                </li>`;
            } else if (i === response.current_page - 2 || i === response.current_page + 2) {
                pagination += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
            }
        }

        // Next button
        pagination += `<li class="page-item ${response.current_page === response.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="changePage(${response.current_page + 1})">Next</a>
        </li>`;
    }

    $('#pagination').html(pagination);
}

function changePage(page) {
    currentPage = page;
    loadJenisIuran();
}

function loadStatistics() {
    $.ajax({
        url: '/admin/api/jenis-iuran',
        type: 'GET',
        data: { statistics: true },
        success: function(response) {
            $('#totalJenisIuran').text(response.total);
            $('#totalAktif').text(response.aktif);
            $('#totalNonAktif').text(response.non_aktif);
            $('#totalNominal').text('Rp ' + number_format(response.total_nominal, 0, ',', '.'));
        },
        error: function() {
            $('#totalJenisIuran').text('0');
            $('#totalAktif').text('0');
            $('#totalNonAktif').text('0');
            $('#totalNominal').text('Rp 0');
        }
    });
}

function showCreateModal() {
    resetForm();
    $('#jenisIuranModalTitle').html('<i class="fas fa-cogs mr-2"></i>Tambah Jenis Iuran');
    $('#jenisIuranModal').modal('show');
}

function editJenisIuran(id) {
    $.ajax({
        url: `/admin/api/jenis-iuran/${id}/edit`,
        type: 'GET',
        success: function(response) {
            $('#jenis_iuran_id').val(response.id);
            $('#nama').val(response.nama);
            $('#kode').val(response.kode);
            $('#jumlah').val(response.jumlah);
            $('#periode').val(response.periode);
            $('#keterangan').val(response.keterangan);
            $('#is_aktif').prop('checked', response.is_aktif);

            $('#jenisIuranModalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Jenis Iuran');
            $('#jenisIuranModal').modal('show');
        },
        error: function() {
            showAlert('danger', 'Gagal mengambil data jenis iuran.');
        }
    });
}

function deleteJenisIuran(id, nama) {
    $('#delete_id').val(id);
    $('#delete_nama').text(nama);
    $('#deleteModal').modal('show');
}

function toggleStatus(id) {
    $.ajax({
        url: `/admin/api/jenis-iuran/${id}/toggle-status`,
        type: 'PUT',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            loadJenisIuran();
            loadStatistics();
            showAlert('success', 'Status berhasil diperbarui!');
        },
        error: function(xhr) {
            showAlert('danger', xhr.responseJSON?.message || 'Gagal mengubah status.');
        }
    });
}

function resetFilters() {
    $('#search').val('');
    $('#filterPeriode').val('');
    $('#filterStatus').val('');
    $('#perPage').val('10');
    currentPage = 1;
    loadJenisIuran();
}

function resetForm() {
    $('#jenisIuranForm')[0].reset();
    $('#jenis_iuran_id').val('');
    $('#is_aktif').prop('checked', true);
}

// Function to show alerts using consistent showToast (like Keluarga & Warga)
function showAlert(type, message) {
    // Use the layout's showToast function for consistency with Keluarga & Warga
    if (typeof showToast === 'function') {
        showToast(message, type, 3000);
    } else {
        console.log(`${type}: ${message}`);
    }
}

function exportData() {
    var search = $('#search').val();
    var periode = $('#filterPeriode').val();
    var status = $('#filterStatus').val();

    var params = new URLSearchParams({
        export: true
    });

    if (search && search !== '') params.append('search', search);
    if (periode && periode !== '') params.append('periode', periode);
    if (status && status !== '') params.append('status', status);

    window.open(`/admin/jenis-iuran?${params.toString()}`, '_blank');
}

// Load data when page is ready
$(document).ready(function() {
    loadJenisIuran();
    loadStatistics();
});

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number;
    var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
    var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;

    var s = '';
    var toFixedFix = function(n, prec) {
        var k = Math.pow(10, prec);
        return '' + Math.round(n * k) / k;
    };

    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (sep) {
        var re = /(-?\d+)(\d{3})/;
        while (re.test(s[0])) {
            s[0] = s[0].replace(re, '$1' + sep + '$2');
        }
    }

    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }

    return s.join(dec);
}
</script>
@endpush