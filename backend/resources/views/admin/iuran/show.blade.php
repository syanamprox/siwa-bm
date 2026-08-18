@extends('layouts.app')

@section('title', 'Detail Tagihan Iuran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-eye me-2"></i>
                        Detail Tagihan Iuran
                    </h5>
                    <div>
                        <a href="{{ route('iuran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informasi Tagihan -->
                    <div class="row mb-4">
                        <!-- Informasi Keluarga -->
                        <div class="col-md-4">
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <h6 class="text-primary">
                                        <i class="fas fa-users me-2"></i>Informasi Keluarga
                                    </h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="text-muted" style="width: 120px;">No. KK:</td>
                                            <td><strong>{{ $iuran->keluarga->no_kk }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Kepala Keluarga:</td>
                                            <td><strong>{{ $iuran->keluarga->kepalaKeluarga->nama_lengkap }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Jenis Iuran:</td>
                                            <td><strong>{{ $iuran->jenisIuran->nama }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Periode:</td>
                                            <td><strong>{{ \Carbon\Carbon::parse($iuran->periode_bulan . '-01')->format('F Y') }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Status Tagihan -->
                        <div class="col-md-4">
                            <div class="card border-left-warning">
                                <div class="card-body">
                                    <h6 class="text-warning">
                                        <i class="fas fa-info-circle me-2"></i>Status Tagihan
                                    </h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="text-muted" style="width: 120px;">Status:</td>
                                            <td>
                                                @switch($iuran->status)
                                                    @case('belum_bayar')
                                                        <span class="badge bg-warning text-white">Belum Bayar</span>
                                                        @break
                                                    @case('sebagian')
                                                        <span class="badge bg-info text-white">Sebagian</span>
                                                        @break
                                                    @case('lunas')
                                                        <span class="badge bg-success text-white">Lunas</span>
                                                        @break
                                                    @case('batal')
                                                        <span class="badge bg-secondary text-white">Batal</span>
                                                        @break
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Nominal:</td>
                                            <td><strong class="text-primary">Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Denda Terlambat:</td>
                                            <td><strong class="text-danger">Rp {{ number_format($iuran->denda_terlambatan, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Jatuh Tempo:</td>
                                            <td><strong>{{ $iuran->jatuh_tempo ? $iuran->jatuh_tempo->format('d M Y') : '-' }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Total Pembayaran -->
                        <div class="col-md-4">
                            <div class="card border-left-success">
                                <div class="card-body">
                                    <h6 class="text-success">
                                        <i class="fas fa-calculator me-2"></i>Total Pembayaran
                                    </h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="text-muted" style="width: 120px;">Total Tagihan:</td>
                                            <td><strong class="h5 text-success">Rp {{ number_format($iuran->nominal + $iuran->denda_terlambatan, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Sudah Dibayar:</td>
                                            <td><strong class="h5 text-primary">Rp {{ number_format($iuran->total_pembayaran, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Sisa Tagihan:</td>
                                            <td><strong class="h5 text-danger">Rp {{ number_format(max(0, ($iuran->nominal + $iuran->denda_terlambatan) - $iuran->total_pembayaran), 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    @if($iuran->keterangan)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-left-info">
                                <div class="card-body">
                                    <h6 class="text-info">
                                        <i class="fas fa-comment me-2"></i>Keterangan
                                    </h6>
                                    <p class="mb-0">{{ $iuran->keterangan }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Informasi Pembuat -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-left-secondary">
                                <div class="card-body">
                                    <h6 class="text-secondary">
                                        <i class="fas fa-user-plus me-2"></i>Informasi Pembuat
                                    </h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="text-muted" style="width: 130px;">Dibuat oleh:</td>
                                            <td><strong>{{ $iuran->createdBy->name ?? 'System' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Tanggal Dibuat:</td>
                                            <td><strong>{{ $iuran->created_at->format('d M Y H:i') }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-left-dark">
                                <div class="card-body">
                                    <h6 class="text-dark">
                                        <i class="fas fa-clock me-2"></i>Informasi Update
                                    </h6>
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td class="text-muted" style="width: 130px;">Terakhir Update:</td>
                                            <td><strong>{{ $iuran->updated_at->format('d M Y H:i') }}</strong></td>
                                        </tr>
                                    </table>
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
                                        <i class="fas fa-history me-2"></i>Riwayat Pembayaran
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($iuran->pembayaran && $iuran->pembayaran->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Tanggal</th>
                                                        <th>Jumlah</th>
                                                        <th>Metode</th>
                                                        <th>Keterangan</th>
                                                        <th>Dibuat oleh</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($iuran->pembayaran as $index => $pembayaran)
                                                    <tr>
                                                        <td><span class="badge bg-dark text-white">{{ $index + 1 }}</span></td>
                                                        <td>{{ $pembayaran->created_at->format('d M Y H:i') }}</td>
                                                        <td><strong class="text-success">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</strong></td>
                                                        <td>
                                                            @switch($pembayaran->metode_pembayaran)
                                                                @case('cash')
                                                                    <span class="badge bg-primary text-white">Cash</span>
                                                                    @break
                                                                @case('transfer')
                                                                    <span class="badge bg-info text-white">Transfer</span>
                                                                    @break
                                                                @case('qris')
                                                                    <span class="badge bg-success text-white">QRIS</span>
                                                                    @break
                                                                @case('ewallet')
                                                                    <span class="badge bg-warning text-white">E-Wallet</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-secondary text-white">{{ $pembayaran->metode_pembayaran }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>{{ $pembayaran->keterangan ?? '-' }}</td>
                                                        <td><strong>{{ $pembayaran->createdBy->name ?? 'System' }}</strong></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Belum ada pembayaran untuk tagihan ini.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection