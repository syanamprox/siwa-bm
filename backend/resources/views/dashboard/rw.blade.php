@extends('layouts.app')

@section('title', 'Dashboard RW - SIWA')

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
                            Total Warga RW
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah RT</div>
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
                            Tagihan Iuran
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($data['total_tagihan_iuran'], 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">Distribusi Warga per RT</h6>
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
            <div class="card-body">
                @if($data['warga_per_rt']->count() > 0)
                    <canvas id="wargaPerRtChart"></canvas>
                @else
                    <p class="text-gray-500">Belum ada data warga.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Quick Actions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <a href="{{ route('warga.index') }}" class="btn btn-primary btn-user btn-block">
                            <i class="fas fa-users fa-sm fa-fw mr-2"></i>
                            Data Warga
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <a href="{{ route('keluarga.index') }}" class="btn btn-success btn-user btn-block">
                            <i class="fas fa-user-friends fa-sm fa-fw mr-2"></i>
                            Data Keluarga
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <a href="{{ route('iuran.index') }}" class="btn btn-info btn-user btn-block">
                            <i class="fas fa-dollar-sign fa-sm fa-fw mr-2"></i>
                            Manajemen Iuran
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <a href="#" class="btn btn-warning btn-user btn-block">
                            <i class="fas fa-chart-bar fa-sm fa-fw mr-2"></i>
                            Laporan RW
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <!-- Recent Payments -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pembayaran Terkini</h6>
            </div>
            <div class="card-body">
                @if($data['recent_payments']->count() > 0)
                    @foreach($data['recent_payments'] as $payment)
                        <div class="small text-gray-500 mb-1">
                            <strong>{{ $payment->iuran->warga->nama_lengkap }}</strong> -
                            {{ formatRupiah($payment->jumlah_bayar) }} -
                            <small>{{ $payment->tanggal_bayar->format('d M Y') }}</small>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500">Belum ada pembayaran tercatat.</p>
                @endif
            </div>
        </div>

        <!-- System Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">Dashboard RW - Sistem Informasi Warga</p>
                <small class="text-gray-500">Hak akses: RW Level</small>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Warga per RT Chart
    @if($data['warga_per_rt']->count() > 0)
    const wargaPerRtCtx = document.getElementById('wargaPerRtChart').getContext('2d');
    const wargaPerRtChart = new Chart(wargaPerRtCtx, {
        type: 'doughnut',
        data: {
            labels: [{!! $data['warga_per_rt']->pluck('rt_domisili')->map(function($item) { return "'".$item."'"; })->join(',') !!}],
            datasets: [{
                data: [{!! $data['warga_per_rt']->pluck('total')->join(',') !!}],
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#e74a3b',
                    '#5a5c69'
                ],
                hoverBackgroundColor: [
                    '#2e59d9',
                    '#17a673',
                    '#2c9faf',
                    '#f4b619',
                    '#c0392b',
                    '#3a3b47'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif
</script>
@endpush