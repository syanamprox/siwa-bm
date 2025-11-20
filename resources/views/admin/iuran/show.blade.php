@extends('layouts.app')

@section('title', 'Detail Tagihan Iuran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Detail Tagihan Iuran
                    </h5>
                    <div>
                        <a href="{{ route('iuran.edit', $iuran->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Edit
                        </a>
                        <a href="{{ route('iuran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informasi Tagihan -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Informasi Tagihan</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td style="width: 150px;"><strong>No. KK:</strong></td>
                                    <td>{{ $iuran->keluarga->no_kk }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kepala Keluarga:</strong></td>
                                    <td>{{ $iuran->keluarga->kepalaKeluarga->nama_lengkap }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Iuran:</strong></td>
                                    <td>{{ $iuran->jenisIuran->nama }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Periode:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($iuran->periode_bulan . '-01')->format('F Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Status & Pembayaran</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td style="width: 150px;"><strong>Status:</strong></td>
                                    <td>
                                        @switch($iuran->status)
                                            @case('belum_bayar')
                                                <span class="badge bg-warning">Belum Bayar</span>
                                                @break
                                            @case('sebagian')
                                                <span class="badge bg-info">Sebagian</span>
                                                @break
                                            @case('lunas')
                                                <span class="badge bg-success">Lunas</span>
                                                @break
                                            @case('batal')
                                                <span class="badge bg-secondary">Batal</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Nominal:</strong></td>
                                    <td>Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Denda Terlambat:</strong></td>
                                    <td>Rp {{ number_format($iuran->denda_terlambatan, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jatuh Tempo:</strong></td>
                                    <td>{{ $iuran->jatuh_tempo ? $iuran->jatuh_tempo->format('d M Y') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Total Tagihan -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Total Tagihan:</strong><br>
                                <span class="h4">Rp {{ number_format($iuran->nominal + $iuran->denda_terlambatan, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Dibayar:</strong><br>
                                <span class="h4">Rp {{ number_format($iuran->total_pembayaran, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Sisa:</strong><br>
                                <span class="h4">Rp {{ number_format(max(0, ($iuran->nominal + $iuran->denda_terlambatan) - $iuran->total_pembayaran), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    @if($iuran->keterangan)
                    <div class="mb-4">
                        <h6 class="text-muted">Keterangan</h6>
                        <p>{{ $iuran->keterangan }}</p>
                    </div>
                    @endif

                    <!-- Informasi Pembuat -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Informasi Pembuat</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td style="width: 150px;"><strong>Dibuat oleh:</strong></td>
                                    <td>{{ $iuran->createdBy->name ?? 'System' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Dibuat:</strong></td>
                                    <td>{{ $iuran->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Informasi Update</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td style="width: 150px;"><strong>Terakhir Update:</strong></td>
                                    <td>{{ $iuran->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Riwayat Pembayaran -->
                    @if($iuran->pembayaran && $iuran->pembayaran->count() > 0)
                    <div class="mt-4">
                        <h6 class="text-muted">Riwayat Pembayaran</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
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
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $pembayaran->created_at->format('d M Y H:i') }}</td>
                                        <td>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td>
                                            @switch($pembayaran->metode_pembayaran)
                                                @case('cash')
                                                    <span class="badge bg-primary">Cash</span>
                                                    @break
                                                @case('transfer')
                                                    <span class="badge bg-info">Transfer</span>
                                                    @break
                                                @case('qris')
                                                    <span class="badge bg-success">QRIS</span>
                                                    @break
                                                @case('ewallet')
                                                    <span class="badge bg-warning">E-Wallet</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $pembayaran->metode_pembayaran }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $pembayaran->keterangan ?? '-' }}</td>
                                        <td>{{ $pembayaran->createdBy->name ?? 'System' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <div class="mt-4">
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Belum ada pembayaran untuk tagihan ini.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection