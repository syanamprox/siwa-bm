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

    <!-- Connections Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Koneksi</h6>
            <div>
                <button class="btn btn-sm btn-outline-primary" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i>
                    Refresh
                </button>
            </div>
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
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($connections as $index => $connection)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('keluarga_iuran.index', $connection->keluarga) }}" class="text-decoration-none">
                                    {{ $connection->keluarga->no_kk }}
                                </a>
                            </td>
                            <td>{{ $connection->keluarga->kepalaKeluarga->nama_lengkap ?? '-' }}</td>
                            <td>
                                <strong>{{ $connection->jenisIuran->nama }}</strong>
                                <br><small class="text-muted">{{ $connection->jenisIuran->kode }}</small>
                                <br><span class="badge bg-info">{{ $connection->jenisIuran->periode_label }}</span>
                            </td>
                            <td>
                                <span class="text-muted">Rp {{ number_format($connection->jenisIuran->jumlah, 0) }}</span>
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
                                    Rp {{ number_format($connection->nominal_custom ?? $connection->jenisIuran->jumlah, 0) }}
                                </span>
                            </td>
                            <td>
                                @if($connection->status_aktif)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $connection->created_at->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('keluarga_iuran.index', $connection->keluarga) }}"
                                       class="btn btn-outline-primary btn-sm"
                                       title="Kelola Koneksi">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
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
$(document).ready(function() {
    $('#connectionsTable').DataTable({
        "responsive": true,
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json",
            "emptyTable": "Tidak ada data koneksi iuran",
            "zeroRecords": "Tidak ada koneksi iuran yang cocok dengan filter"
        },
        "order": [[1, "asc"]] // Sort by No. KK
    });
});
</script>
@endpush