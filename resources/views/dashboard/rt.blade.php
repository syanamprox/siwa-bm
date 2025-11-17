@extends('layouts.app')

@section('title', 'Dashboard RT - SIWA')

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
                            Total Warga RT
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Lunasi Bulan Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data['total_warga'], 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            Tunggakan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($data['total_tagihan_iuran'], 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">

    <!-- Recent Payments -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pembayaran Terkini</h6>
            </div>
            <div class="card-body">
                @if($data['recent_payments']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['recent_payments'] as $payment)
                                    <tr>
                                        <td>{{ $payment->iuran->warga->nama_lengkap }}</td>
                                        <td>{{ formatRupiah($payment->jumlah_bayar) }}</td>
                                        <td>{{ $payment->tanggal_bayar->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">Belum ada pembayaran tercatat.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Pending Iuran -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Iuran Belum Dibayar</h6>
            </div>
            <div class="card-body">
                @if($data['pending_iuran']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jenis Iuran</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['pending_iuran'] as $iuran)
                                    <tr>
                                        <td>{{ $iuran->warga->nama_lengkap }}</td>
                                        <td>{{ $iuran->jenisIuran->nama }}</td>
                                        <td>{{ formatRupiah($iuran->nominal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">Semua iuran telah lunas.</p>
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
                    <div class="col-sm-12 mb-3">
                        <a href="{{ route('warga.index') }}" class="btn btn-primary btn-user btn-block">
                            <i class="fas fa-users fa-sm fa-fw mr-2"></i>
                            Kelola Data Warga
                        </a>
                    </div>
                    <div class="col-sm-12 mb-3">
                        <a href="{{ route('iuran.index') }}" class="btn btn-success btn-user btn-block">
                            <i class="fas fa-dollar-sign fa-sm fa-fw mr-2"></i>
                            Manajemen Iuran
                        </a>
                    </div>
                    <div class="col-sm-12 mb-3">
                        <button class="btn btn-info btn-user btn-block" onclick="showQuickPayment()">
                            <i class="fas fa-money-bill-wave fa-sm fa-fw mr-2"></i>
                            Pembayaran Cepat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <!-- Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistik RT</h6>
            </div>
            <div class="card-body">
                <div class="row no-gutters align-items-center mb-3">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success">Pemasukan Bulanan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($data['pemasukan_bulan_ini'], 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning">Total Tunggakan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['pending_iuran']->count() }} Tagihan</div>
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
                <p class="mb-0">Dashboard RT - Sistem Informasi Warga</p>
                <small class="text-gray-500">Hak akses: RT Level</small>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Quick Payment function
    function showQuickPayment() {
        showToast('Fitur pembayaran cepat akan segera tersedia', 'info');
    }
</script>
@endpush