@extends('layouts.app')

@section('title', 'Overview Koneksi Iuran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-link me-2"></i>
            Overview Koneksi Iuran
        </h1>
        <a href="{{ route('keluarga.index') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Hubungkan Iuran ke Keluarga
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Koneksi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['total_connections'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-link fa-2x text-gray-300"></i>
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
                                Koneksi Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['active_connections'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Keluarga Terhubung
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['families_with_iuran'] }}
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Nominal Custom
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $summary['total_custom_nominals'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filter Data
            </h6>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Nama atau No. KK</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan nama atau No. KK..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="filter_jenis_iuran" class="form-label">Jenis Iuran</label>
                    <select class="form-control" id="filter_jenis_iuran" name="filter_jenis_iuran">
                        <option value="">Semua Jenis Iuran</option>
                        @foreach($allJenisIurans ?? [] as $jenisIuran)
                        <option value="{{ $jenisIuran->id }}" {{ request('filter_jenis_iuran') == $jenisIuran->id ? 'selected' : '' }}>{{ $jenisIuran->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter_status" class="form-label">Status</label>
                    <select class="form-control" id="filter_status" name="filter_status">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('filter_status') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('filter_status') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label><br>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                        <i class="fas fa-undo me-1"></i>Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Connections Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Koneksi</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="connectionsTable">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>No. KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Jenis Iuran</th>
                            <th>Nominal Default</th>
                            <th>Nominal Custom</th>
                            <th>Nominal Efektif</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($connections as $index => $connection)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($connection->keluarga)
                                    <a href="{{ route('keluarga_iuran.index', $connection->keluarga) }}" class="text-decoration-none">
                                        {{ $connection->keluarga->no_kk }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $connection->keluarga->kepalaKeluarga->nama_lengkap ?? '-' }}</td>
                            <td>
                                @if($connection->jenisIuran)
                                    <strong>{{ $connection->jenisIuran->nama }}</strong>
                                    <br><small class="text-muted">{{ $connection->jenisIuran->kode }}</small>
                                    <br><span class="badge bg-info text-white">{{ $connection->jenisIuran->periode_label }}</span>
                                @else
                                    <span class="text-danger">Jenis Iuran tidak ditemukan</span>
                                @endif
                            </td>
                            <td>
                                @if($connection->jenisIuran)
                                    <span class="text-muted">Rp {{ number_format($connection->jenisIuran->jumlah, 0) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($connection->nominal_custom)
                                    <span class="text-warning">Rp {{ number_format($connection->nominal_custom, 0) }}</span>
                                @else
                                    <span class="text-muted">Default</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold">
                                    Rp {{ number_format($connection->nominal_custom ?? ($connection->jenisIuran->jumlah ?? 0), 0) }}
                                </span>
                            </td>
                            <td>
                                @if($connection->status_aktif)
                                    <span class="badge bg-success text-white">Aktif</span>
                                @else
                                    <span class="badge bg-secondary text-white">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $connection->created_at->format('d M Y') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-unlink fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada koneksi iuran</p>
                                <a href="{{ route('keluarga.index') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>
                                    Mulai Hubungkan Iuran
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Utility functions for loading and toast notifications
function showLoading() {
    // Create or show loading overlay if it doesn't exist
    if (!$('#loadingOverlay').length) {
        $('body').append('<div id="loadingOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999;"><div class="spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div></div>');
    }
    $('#loadingOverlay').show();
}

function hideLoading() {
    $('#loadingOverlay').hide();
}

function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>');
    }

    var toastId = 'toast_' + Date.now();
    var bgClass = 'bg-info';
    if (type === 'success') bgClass = 'bg-success';
    else if (type === 'error' || type === 'danger') bgClass = 'bg-danger';
    else if (type === 'warning') bgClass = 'bg-warning';

    var toastHtml = `
        <div id="${toastId}" class="toast show" role="alert" style="min-width: 300px;">
            <div class="toast-header ${bgClass} text-white">
                <strong class="me-auto">Notifikasi</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;

    $('#toastContainer').append(toastHtml);

    // Auto remove after 5 seconds
    setTimeout(function() {
        $('#' + toastId).fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}

// AJAX filter functions (backend API pattern like warga & keluarga)
var typingTimer;
var doneTypingInterval = 500; // 500ms delay after typing stops
var currentPage = 1;
var currentFilters = {};

$(document).ready(function() {
    // Load initial data
    loadKeluargaIuranConnections();

    // Auto-trigger with delay for search field
    $('#search').on('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            currentPage = 1;
            loadKeluargaIuranConnections();
        }, doneTypingInterval);
    });

    // Instant trigger for dropdowns
    $('#filter_jenis_iuran, #filter_status').on('change', function() {
        currentPage = 1;
        loadKeluargaIuranConnections();
    });

    // Handle Enter key in search field
    $('#search').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            clearTimeout(typingTimer);
            e.preventDefault();
            currentPage = 1;
            loadKeluargaIuranConnections();
        }
    });
});

function loadKeluargaIuranConnections() {
    showLoading();

    // Collect filter values
    currentFilters = {
        search: $('#search').val(),
        filter_jenis_iuran: $('#filter_jenis_iuran').val(),
        filter_status: $('#filter_status').val(),
        page: currentPage,
        per_page: 25
    };

    $.ajax({
        url: '/admin/api/keluarga-iuran/overview',
        type: 'GET',
        data: currentFilters,
        dataType: 'json',
        success: function(response) {
            hideLoading();

            if (response.success) {
                renderKeluargaIuranTable(response.data);
                renderSummaryCards(response.summary);
                renderPagination(response.pagination);
            } else {
                showToast(response.message || 'Gagal memuat data', 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Terjadi kesalahan saat memuat data';
            showToast(message, 'error');
        }
    });
}

function renderKeluargaIuranTable(connections) {
    var tbody = $('#connectionsTable tbody');
    tbody.empty();

    if (connections.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-unlink fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada koneksi iuran</p>
                    <a href="{{ route('keluarga.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Mulai Hubungkan Iuran
                    </a>
                </td>
            </tr>
        `);
        return;
    }

    connections.forEach(function(connection, index) {
        var nominalDefault = connection.jenis_iuran ?
            'Rp ' + formatNumber(connection.jenis_iuran.jumlah) : '-';
        var nominalCustom = connection.nominal_custom ?
            '<span class="text-warning">Rp ' + formatNumber(connection.nominal_custom) + '</span>' :
            '<span class="text-muted">Default</span>';
        var nominalEfektif = 'Rp ' + formatNumber(
            connection.nominal_custom || (connection.jenis_iuran?.jumlah || 0)
        );
        var statusBadge = connection.status_aktif ?
            '<span class="badge bg-success text-white">Aktif</span>' :
            '<span class="badge bg-secondary text-white">Non-Aktif</span>';

        // Get periode label
        var periodeLabel = '-';
        if (connection.jenis_iuran?.periode) {
            switch(connection.jenis_iuran.periode) {
                case 'bulanan': periodeLabel = 'Setiap Bulan'; break;
                case 'tahunan': periodeLabel = 'Setiap Tahun'; break;
                case 'sekali': periodeLabel = 'Sekali Bayar'; break;
                default: periodeLabel = connection.jenis_iuran.periode;
            }
        }

        var row = `
            <tr>
                <td>${((currentPage - 1) * 25) + index + 1}</td>
                <td>
                    ${connection.keluarga ?
                        `<a href="/admin/keluarga/${connection.keluarga.id}/iuran" class="text-decoration-none">
                            ${connection.keluarga.no_kk}
                        </a>` :
                        '<span class="text-muted">-</span>'
                    }
                </td>
                <td>${connection.keluarga?.kepala_keluarga?.nama_lengkap || '-'}</td>
                <td>
                    ${connection.jenis_iuran ?
                        `<strong>${connection.jenis_iuran.nama}</strong>
                        <br><small class="text-muted">${connection.jenis_iuran.kode}</small>
                        <br><span class="badge bg-info text-white">${periodeLabel}</span>` :
                        '<span class="text-danger">Jenis Iuran tidak ditemukan</span>'
                    }
                </td>
                <td>
                    ${connection.jenis_iuran ?
                        `<span class="text-muted">${nominalDefault}</span>` :
                        '<span class="text-muted">-</span>'
                    }
                </td>
                <td>${nominalCustom}</td>
                <td>
                    <span class="fw-bold">${nominalEfektif}</span>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <small class="text-muted">${formatDate(connection.created_at)}</small>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function renderSummaryCards(summary) {
    $('.row .col-xl-3').each(function(index) {
        var $card = $(this);
        switch(index) {
            case 0: // Total Koneksi
                $card.find('.h5').text(summary.total_connections);
                break;
            case 1: // Koneksi Aktif
                $card.find('.h5').text(summary.active_connections);
                break;
            case 2: // Keluarga Terhubung
                $card.find('.h5').text(summary.families_with_iuran);
                break;
            case 3: // Nominal Custom
                $card.find('.h5').text(summary.total_custom_nominals);
                break;
        }
    });
}

function renderPagination(pagination) {
    // For now, we'll use simple pagination. In a real implementation,
    // you might want to add pagination controls
    // This is simplified since the original didn't have pagination controls
}

function resetFilters() {
    $('#filterForm')[0].reset();
    currentPage = 1;
    loadKeluargaIuranConnections();
    showToast('Filter berhasil direset', 'success');
}

// Helper functions
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function formatDate(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
}
</script>
@endpush