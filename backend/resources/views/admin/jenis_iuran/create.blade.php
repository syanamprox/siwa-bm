@extends('layouts.app')

@section('title', 'Tambah Jenis Iuran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus me-2"></i>
            Tambah Jenis Iuran
        </h1>
        <a href="{{ route('jenis_iuran.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Jenis Iuran</h6>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('jenis_iuran.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Jenis Iuran <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nama') is-invalid @enderror"
                                   id="nama"
                                   name="nama"
                                   value="{{ old('nama') }}"
                                   placeholder="Contoh: Iuran Kebersihan"
                                   required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('kode') is-invalid @enderror"
                                   id="kode"
                                   name="kode"
                                   value="{{ old('kode') }}"
                                   placeholder="Contoh: IK"
                                   maxlength="10"
                                   required>
                            @error('kode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('jumlah') is-invalid @enderror"
                                   id="jumlah"
                                   name="jumlah"
                                   value="{{ old('jumlah') }}"
                                   placeholder="25000"
                                   min="0"
                                   step="1000"
                                   required>
                            @error('jumlah')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="periode" class="form-label">Periode <span class="text-danger">*</span></label>
                            <select class="form-select @error('periode') is-invalid @enderror"
                                    id="periode"
                                    name="periode"
                                    required>
                                <option value="">Pilih Periode</option>
                                <option value="bulanan" {{ old('periode') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="tahunan" {{ old('periode') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                                <option value="sekali" {{ old('periode') == 'sekali' ? 'selected' : '' }}>Sekali</option>
                            </select>
                            @error('periode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sasaran" class="form-label">Sasaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('sasaran') is-invalid @enderror"
                                    id="sasaran"
                                    name="sasaran"
                                    required>
                                <option value="">Pilih Sasaran</option>
                                <option value="kk" {{ old('sasaran') == 'kk' ? 'selected' : '' }}>Per KK</option>
                                <option value="warga" {{ old('sasaran') == 'warga' ? 'selected' : '' }}>Per Warga</option>
                                <option value="semua" {{ old('sasaran') == 'semua' ? 'selected' : '' }}>Semua</option>
                            </select>
                            @error('sasaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                              id="keterangan"
                              name="keterangan"
                              rows="3"
                              placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="is_aktif"
                               name="is_aktif"
                               value="1"
                               {{ old('is_aktif') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_aktif">
                            Aktif
                        </label>
                    </div>
                    <small class="text-muted">Jenis iuran yang aktif dapat digunakan untuk generate tagihan</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('jenis_iuran.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate kode from nama
    $('#nama').on('input', function() {
        const nama = $(this).val();
        if (nama && !$('#kode').val()) {
            // Generate simple kode from first letters
            const words = nama.toUpperCase().split(' ');
            let kode = '';
            words.forEach(word => {
                if (word.length > 0) {
                    kode += word[0];
                }
            });
            $('#kode').val(kode.substring(0, 10));
        }
    });

    // Format nominal input
    $('#jumlah').on('blur', function() {
        const value = $(this).val();
        if (value) {
            // Just show formatting, keep raw value for form submission
            const formatted = parseInt(value).toLocaleString('id-ID');
            console.log('Nominal: Rp ' + formatted);
        }
    });
});
</script>
@endpush