@extends('layouts.app')

@section('title', 'Generate Tagihan Iuran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-magic me-2"></i>
            Generate Tagihan Iuran
        </h1>
        <a href="{{ route('iuran.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Generate Tagihan</h6>
        </div>
        <div class="card-body">
            <form id="generateForm">
                @csrf
                <div class="row">
                    <!-- Periode Bulan -->
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="periode_bulan" class="form-label">Periode Bulan <span class="text-danger">*</span></label>
                            <select class="form-control" id="periode_bulan" name="periode_bulan" required>
                                <option value="">Pilih Periode</option>
                                @foreach($periodeOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $value == now()->format('Y-m') ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filter RT (untuk RW/Keatas) -->
                    <div class="col-md-8" id="rtFilter" style="display: none;">
                        <div class="mb-3">
                            <label for="rt_id" class="form-label">Filter RT (Opsional)</label>
                            <select class="form-control" id="rt_id" name="rt_id">
                                <option value="">Semua RT</option>
                            </select>
                            <small class="form-text text-muted">Filter berdasarkan RT tertentu</small>
                        </div>
                    </div>
                </div>

                <!-- Generate Options -->
                <div class="mb-3">
                    <label class="form-label">Opsi Generate</label>
                    <div class="card border-left-info">
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="use_existing_connections" checked>
                                <label class="form-check-label" for="use_existing_connections">
                                    <strong>Generate dari Koneksi Iuran yang Sudah Ada</strong>
                                    <small class="text-muted d-block">Membuat tagihan berdasarkan koneksi iuran yang sudah dibuat di menu Keluarga Iuran</small>
                                </label>
                            </div>
                            <div id="custom_iuran_options" style="display: none;">
                                <hr>
                                <p class="mb-2">Pilih jenis iuran (opsional jika tidak menggunakan koneksi yang ada):</p>
                                <div class="row">
                                    @foreach($jenisIurans as $jenisIuran)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ $jenisIuran->id }}" id="ji_{{ $jenisIuran->id }}" name="jenis_iuran_ids[]">
                                                <label class="form-check-label" for="ji_{{ $jenisIuran->id }}">
                                                    {{ $jenisIuran->nama }}
                                                    <small class="text-muted">Rp {{ number_format($jenisIuran->jumlah, 0, ',', '.') }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="form-text text-muted">Sistem akan generate tagihan untuk semua keluarga yang memiliki koneksi iuran aktif</small>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" id="resetBtn">
                        <i class="fas fa-redo me-1"></i>
                        Reset Form
                    </button>
                    <div>
                        <button type="button" class="btn btn-info me-2" id="previewBtn" disabled>
                            <i class="fas fa-eye me-1"></i>
                            Preview
                        </button>
                        <button type="submit" class="btn btn-success" id="generateBtn" disabled>
                            <i class="fas fa-magic me-1"></i>
                            Generate Tagihan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Section -->
<div class="row mt-4" id="previewSection" style="display: none;">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-eye me-2"></i>
                    Preview Tagihan Akan Dibuat
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3" id="previewSummary"></div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No. KK</th>
                                <th>Kepala Keluarga</th>
                                <th>RT</th>
                                <th>Jenis Iuran</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="previewTable">
                            <!-- Preview data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="bg-white p-4 rounded text-center">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <p class="mb-0">Sedang generate tagihan...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load RT options based on user role
    loadRtOptions();

    // Toggle generate and preview buttons based on selections
    function toggleGenerateButton() {
        const periodeSelected = $('#periode_bulan').val() !== '';
        const useExistingConnections = $('#use_existing_connections').is(':checked');
        const jenisSelected = $('input[name="jenis_iuran_ids[]"]:checked').length > 0;

        const isValid = periodeSelected && (useExistingConnections || jenisSelected);
        $('#generateBtn').prop('disabled', !isValid);
        $('#previewBtn').prop('disabled', !isValid);
    }

    // Reset form function
    function resetForm() {
        $('#generateForm')[0].reset();
        $('#previewSection').hide();
        toggleGenerateButton();
    }

    $('#periode_bulan, input[name="jenis_iuran_ids[]"], #use_existing_connections').on('change', toggleGenerateButton);

    // Toggle custom iuran options
    $('#use_existing_connections').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('#custom_iuran_options').toggle(!isChecked);

        // Clear custom selections when using existing connections
        if (isChecked) {
            $('input[name="jenis_iuran_ids[]"]').prop('checked', false);
        }
    });

    // Reset button
    $('#resetBtn').on('click', function() {
        resetForm();
    });

    // Preview button
    $('#previewBtn').on('click', function() {
        if (!validateForm()) {
            return;
        }
        loadPreview();
    });

    // Generate form submission
    $('#generateForm').on('submit', function(e) {
        e.preventDefault();
        generateIuran();
    });

    function validateForm() {
        const periode = $('#periode_bulan').val();
        const useExistingConnections = $('#use_existing_connections').is(':checked');
        const jenisSelected = $('input[name="jenis_iuran_ids[]"]:checked').length;

        if (!periode) {
            showToast('Silakan pilih periode', 'error');
            return false;
        }

        if (!useExistingConnections && jenisSelected === 0) {
            showToast('Silakan pilih minimal satu jenis iuran atau gunakan koneksi yang ada', 'error');
            return false;
        }

        return true;
    }

    function loadRtOptions() {
        $.get('/admin/api/iuran/generation/rt-options')
            .done(function(response) {
                if (response.success) {
                    const rtSelect = $('#rt_id');
                    rtSelect.empty().append('<option value="">Semua RT</option>');

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(rt) {
                            rtSelect.append(`<option value="${rt.id}">${rt.nama}</option>`);
                        });

                        // Show RT filter if there are options
                        $('#rtFilter').show();
                    }
                }
            })
            .fail(function(xhr) {
                console.error('Failed to load RT options:', xhr.responseJSON);
                // Hide RT filter on error
                $('#rtFilter').hide();
            });
    }

    function loadPreview() {
        const formData = $('#generateForm').serialize();

        $('#previewBtn').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-1"></i> Loading...');

        $.get('/admin/api/iuran/generation/preview', formData)
            .done(function(response) {
                if (response.success) {
                    displayPreview(response.data);
                    $('#previewSection').show();
                } else {
                    showToast(response.message, 'error');
                }
            })
            .fail(function() {
                showToast('Gagal memuat preview', 'error');
            })
            .always(function() {
                $('#previewBtn').prop('disabled', false)
                    .html('<i class="fas fa-eye me-1"></i> Preview');
            });
    }

    function displayPreview(data) {
        const summary = data.summary;
        const preview = data.preview;

        $('#previewSummary').html(`
            <strong>Ringkasan:</strong>
            ${summary.total_families} keluarga akan dibuatkan ${summary.total_iuran} tagihan untuk periode ${summary.periode}
        `);

        const tbody = $('#previewTable');
        tbody.empty();

        preview.forEach(function(item) {
            let iuransHtml = '';
            item.iurans.forEach(function(iuran) {
                iuransHtml += `<small class="d-block">${iuran.jenis_iuran}: Rp ${number_format(iuran.nominal, 0, ',', '.')}</small>`;
            });

            const row = `
                <tr>
                    <td>${item.no_kk}</td>
                    <td>${item.kepala_keluarga}</td>
                    <td>${item.rt}</td>
                    <td>${iuransHtml}</td>
                    <td><strong>Rp ${number_format(item.total, 0, ',', '.')}</strong></td>
                </tr>
            `;
            tbody.append(row);
        });
    }

      function generateIuran() {
        if (!validateForm()) {
            return;
        }

        performGenerate();
    }

    function performGenerate() {
        const formData = $('#generateForm').serialize();

        $('#loadingOverlay').show();
        $('#generateBtn').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-1"></i> Generating...');

        $.post('/admin/api/iuran/generation/generate', formData)
            .done(function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("iuran.index") }}';
                    }, 2000);
                } else {
                    showToast(response.message, 'error');
                }
            })
            .fail(function(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessage = '';
                    for (const field in errors) {
                        errorMessage += `${errors[field][0]}\n`;
                    }
                    showToast('Validation Error:\n' + errorMessage, 'error');
                } else {
                    showToast('Gagal generate iuran', 'error');
                }
            })
            .always(function() {
                $('#loadingOverlay').hide();
                $('#generateBtn').prop('disabled', false)
                    .html('<i class="fas fa-magic me-1"></i> Generate Tagihan');
            });
    }

    function number_format(number, decimals, dec_point, thousands_sep) {
        // Simple number format function
        number = parseFloat(number);
        if (isNaN(number)) return '0';
        return number.toLocaleString('id-ID');
    }
});
</script>
@endpush