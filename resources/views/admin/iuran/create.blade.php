@extends('layouts.app')

@section('title', 'Tambah Tagihan Iuran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Tagihan Iuran
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('iuran.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Keluarga <span class="text-danger">*</span></label>
                                <select class="form-select" id="kk_id" name="kk_id" required>
                                    <option value="">Pilih Keluarga</option>
                                    @foreach($keluargas as $keluarga)
                                        <option value="{{ $keluarga->id }}" data-nomor="{{ $keluarga->no_kk }}">{{ $keluarga->no_kk }} - {{ $keluarga->nama_kepala_keluarga }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Iuran <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis_iuran_id" name="jenis_iuran_id" required>
                                    <option value="">Pilih Keluarga dulu</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Periode <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="periode_bulan" name="periode_bulan" value="{{ date('Y-m') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nominal <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="nominal" name="nominal" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="belum_bayar">Belum Bayar</option>
                                    <option value="sebagian">Sebagian</option>
                                    <option value="lunas">Lunas</option>
                                    <option value="batal">Batal</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jatuh Tempo</label>
                                <input type="date" class="form-control" id="jatuh_tempo" name="jatuh_tempo">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('iuran.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan
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

@push('scripts')
<script>
$(document).ready(function() {
    // Load jenis iuran when keluarga selected
    $('#kk_id').on('change', function() {
        const keluargaId = $(this).val();
        if (keluargaId) {
            $.get(`/api/iuran/keluarga/${keluargaId}/jenis-iuran`)
                .done(function(response) {
                    if (response.success) {
                        const select = $('#jenis_iuran_id');
                        select.html('<option value="">Pilih Jenis Iuran</option>');
                        response.data.forEach(function(jenis) {
                            const nominal = jenis.effective_nominal || jenis.jumlah;
                            select.append(`<option value="${jenis.id}" data-nominal="${nominal}">${jenis.nama} - Rp ${Number(nominal).toLocaleString('id-ID')}</option>`);
                        });
                    }
                });
        } else {
            $('#jenis_iuran_id').html('<option value="">Pilih Keluarga dulu</option>');
            $('#nominal').val('');
        }
    });

    // Auto-fill nominal when jenis iuran selected
    $('#jenis_iuran_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const nominal = selectedOption.data('nominal');
        if (nominal) {
            $('#nominal').val(nominal);
        }
    });

    // Set default jatuh tempo to end of month
    function setDefaultJatuhTempo() {
        const periode = $('#periode_bulan').val();
        if (periode) {
            const [year, month] = periode.split('-');
            const lastDay = new Date(year, month, 0).getDate();
            const defaultDate = `${year}-${month.padStart(2, '0')}-${lastDay.toString().padStart(2, '0')}`;
            $('#jatuh_tempo').val(defaultDate);
        }
    }

    $('#periode_bulan').on('change', setDefaultJatuhTempo);
    setDefaultJatuhTempo(); // Set on page load
});
</script>
@endpush