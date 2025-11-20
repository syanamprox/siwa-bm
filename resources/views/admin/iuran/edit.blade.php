@extends('layouts.app')

@section('title', 'Edit Tagihan Iuran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Tagihan Iuran
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('iuran.update', $iuran->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Keluarga <span class="text-danger">*</span></label>
                                <select class="form-select" id="kk_id" name="kk_id" required>
                                    @foreach($keluargas as $keluarga)
                                        <option value="{{ $keluarga->id }}" {{ $keluarga->id == $iuran->kk_id ? 'selected' : '' }}>
                                            {{ $keluarga->no_kk }} - {{ $keluarga->nama_kepala_keluarga }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Iuran <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis_iuran_id" name="jenis_iuran_id" required>
                                    @foreach($jenisIurans as $jenisIuran)
                                        <option value="{{ $jenisIuran->id }}" {{ $jenisIuran->id == $iuran->jenis_iuran_id ? 'selected' : '' }}>
                                            {{ $jenisIuran->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Periode <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="periode_bulan" name="periode_bulan" value="{{ $iuran->periode_bulan }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nominal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="nominal" name="nominal" min="0" step="0.01" value="{{ $iuran->nominal }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="belum_bayar" {{ $iuran->status == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                                    <option value="sebagian" {{ $iuran->status == 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                                    <option value="lunas" {{ $iuran->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                    <option value="batal" {{ $iuran->status == 'batal' ? 'selected' : '' }}>Batal</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Denda Terlambat</label>
                                <input type="number" class="form-control" id="denda_terlambatan" name="denda_terlambatan" min="0" step="0.01" value="{{ $iuran->denda_terlambatan ?? 0 }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jatuh Tempo</label>
                                <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo" value="{{ $iuran->jatuh_tempo ? $iuran->jatuh_tempo->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ $iuran->keterangan }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('iuran.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Update
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection