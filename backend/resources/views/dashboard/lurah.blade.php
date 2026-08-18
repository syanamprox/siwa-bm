@extends('layouts.app')

@section('title', 'Dashboard Lurah - SIWA')

@section('content')
<!-- Content Row -->
<div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2 stats-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Warga
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data['total_warga'], 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2 stats-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Keluarga
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data['total_keluarga'], 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2 stats-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total RT</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data['total_rt'], 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2 stats-card">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total RW
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data['total_rw'], 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-map-marked fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pemasukan Iuran Bulanan</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="#">Lihat Detail</a>
                        <a class="dropdown-item" href="#">Export Data</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="h4 mb-0 font-weight-bold text-success">Rp {{ number_format($data['pemasukan_bulan_ini'], 0, ',', '.') }}</div>
                <div class="small text-gray-500">Total pemasukan dari iuran warga bulan ini</div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Tunggakan Iuran</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <div class="dropdown-header">Actions:</div>
                        <a class="dropdown-item" href="#">Lihat Detail</a>
                        <a class="dropdown-item" href="#">Export Data</a>
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body text-center">
                <div class="h4 mb-0 font-weight-bold text-warning">Rp {{ number_format($data['total_tagihan_iuran'], 0, ',', '.') }}</div>
                <div class="small text-gray-500">Total tunggakan iuran yang belum dibayar</div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Warga per RW -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Distribusi Warga per RW</h6>
            </div>
            <div class="card-body">
                @if($data['warga_per_rw']->count() > 0)
                    <canvas id="wargaPerRwChart"></canvas>
                @else
                    <p class="text-gray-500">Belum ada data warga.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <a href="#" class="btn btn-primary btn-user btn-block">
                            <i class="fas fa-user-plus fa-sm fa-fw mr-2"></i>
                            Tambah Warga
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <a href="#" class="btn btn-success btn-user btn-block">
                            <i class="fas fa-user-friends fa-sm fa-fw mr-2"></i>
                            Tambah Keluarga
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <a href="#" class="btn btn-info btn-user btn-block">
                            <i class="fas fa-dollar-sign fa-sm fa-fw mr-2"></i>
                            Generate Iuran
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <a href="#" class="btn btn-warning btn-user btn-block">
                            <i class="fas fa-chart-bar fa-sm fa-fw mr-2"></i>
                            Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Sistem</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">SIWA v1.0 - Sistem Informasi Warga Kelurahan</p>
                <small class="text-gray-500">Dibangun dengan Laravel 12 & SB Admin 2</small>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Warga per RW Chart
    @if($data['warga_per_rw']->count() > 0)
    const wargaPerRwCtx = document.getElementById('wargaPerRwChart').getContext('2d');
    const wargaPerRwChart = new Chart(wargaPerRwCtx, {
        type: 'bar',
        data: {
            labels: [{!! $data['warga_per_rw']->pluck('rw_nama')->map(function($item) { return "'".$item."'"; })->join(',') !!}],
            datasets: [{
                label: 'Jumlah Warga',
                data: [{!! $data['warga_per_rw']->pluck('total')->join(',') !!}],
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    @endif
</script>
@endpush