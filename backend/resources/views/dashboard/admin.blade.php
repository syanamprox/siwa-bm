@extends('layouts.app')

@section('title', 'Dashboard Admin - SIWA')

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
                <h6 class="m-0 font-weight-bold text-primary">Demografi Jenis Kelamin</h6>
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
                <div class="chart-area">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pendidikan Terakhir</h6>
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
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="educationChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> SD
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> SMP
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> SMA
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-warning"></i> Kuliah
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Content Column -->
    <div class="col-lg-6 mb-4">

        <!-- Project Card Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pemasukan Iuran Bulan Ini</h6>
            </div>
            <div class="card-body">
                <div class="h4 mb-0 font-weight-bold text-success">Rp {{ number_format($data['pemasukan_bulan_ini'], 0, ',', '.') }}</div>
                <div class="small text-gray-500">Total pemasukan dari iuran warga</div>
            </div>
        </div>

        <!-- Color System -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body">
                        Primary
                        <div class="text-white-50 small">#4e73df</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card bg-success text-white shadow">
                    <div class="card-body">
                        Success
                        <div class="text-white-50 small">#1cc88a</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card bg-info text-white shadow">
                    <div class="card-body">
                        Info
                        <div class="text-white-50 small">#36b9cc</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card bg-warning text-white shadow">
                    <div class="card-body">
                        Warning
                        <div class="text-white-50 small">#f6c23e</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-6 mb-4">

        <!-- Illustrations -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tunggakan Iuran</h6>
            </div>
            <div class="card-body text-center">
                <div class="h4 mb-0 font-weight-bold text-warning">Rp {{ number_format($data['total_iuran_bulanan'], 0, ',', '.') }}</div>
                <div class="small text-gray-500">Total tunggakan iuran yang belum dibayar</div>
            </div>
        </div>

        <!-- Approach -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Development Approach</h6>
            </div>
            <div class="card-body">
                <p>SIWA is built using Laravel 12 with the following technologies:</p>
                <p class="mb-0">SB Admin 2 template, Bootstrap 5, jQuery, Chart.js, and MySQL database.</p>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terkini</h6>
            </div>
            <div class="card-body">
                @if($data['recent_activities']->count() > 0)
                    @foreach($data['recent_activities'] as $activity)
                        <div class="small text-gray-500 mb-1">
                            <strong>{{ $activity->user->username ?? 'System' }}</strong> -
                            {{ $activity->deskripsi }} -
                            <small>{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500">Belum ada aktivitas tercatat.</p>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Gender Demographics Chart
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const genderChart = new Chart(genderCtx, {
        type: 'bar',
        data: {
            labels: ['Laki-laki', 'Perempuan'],
            datasets: [{
                label: 'Jumlah Warga',
                data: [
                    {{ $data['demografi_jenis_kelamin']['L'] ?? 0 }},
                    {{ $data['demografi_jenis_kelamin']['P'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
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

    // Education Level Chart
    const educationCtx = document.getElementById('educationChart').getContext('2d');
    const educationLabels = [{!! $data['demografi_pendidikan']->keys()->map(function($item) { return "'".$item."'"; })->join(',') !!}];
    const educationData = [{!! $data['demografi_pendidikan']->values()->map(function($item) { return $item; })->join(',') !!}];

    const educationChart = new Chart(educationCtx, {
        type: 'doughnut',
        data: {
            labels: educationLabels.length > 0 ? educationLabels : ['SD', 'SMP', 'SMA', 'Kuliah'],
            datasets: [{
                data: educationData.length > 0 ? educationData : [120, 85, 65, 45],
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e'
                ],
                hoverBackgroundColor: [
                    '#2e59d9',
                    '#17a673',
                    '#2c9faf',
                    '#f4b619'
                ],
                hoverBorderColor: "#ffffff",
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
</script>
@endpush