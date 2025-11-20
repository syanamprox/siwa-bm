@extends('portal.layouts.app')

@section('content')
    <div class="container portal-container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h1 class="display-4 text-white font-weight-bold mb-3">
                        KECAMATAN WONOCOLO SURABAYA
                    </h1>
                    <p class="lead text-white">
                        Portal Informasi Warga - Aman & Terpercaya 24/7 Online
                    </p>
                </div>

                <!-- Main Card -->
                <div class="card card-portal">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="service-icon bg-white text-primary">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="mb-1">Cek Status Iuran</h4>
                                <small class="opacity-75">Pantau status pembayaran iuran warga</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Alert Container -->
                        <div id="alert-container" class="mb-3"></div>

                        <!-- Form -->
                        <form id="iuran-form" class="mb-4">
                            @csrf
                            <div class="form-group">
                                <label for="nik" class="form-label font-weight-bold">
                                    <i class="fas fa-id-card text-primary mr-2"></i>
                                    Nomor Induk Kependudukan (NIK)
                                </label>
                                <input type="text" class="form-control form-control-portal" id="nik" name="nik"
                                       placeholder="Masukkan 16 digit NIK" maxlength="16" required>
                                <small class="form-text text-muted">
                                    Masukkan NIK 16 digit untuk melihat status iuran
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="captcha-iuran" class="form-label font-weight-bold">
                                    <i class="fas fa-shield-alt text-primary mr-2"></i>
                                    Kode Verifikasi
                                </label>
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-portal" id="captcha-iuran"
                                               name="captcha" placeholder="Masukkan kode di samping" required
                                               style="height: 58px;">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="captcha-display" id="captcha-display-iuran"
                                             style="height: 58px; line-height: 52px; font-size: 1.4rem;">
                                            {{ session('captcha_code') ?? strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6)) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="form-text text-muted">Masukkan kode verifikasi untuk keamanan</small>
                                    <small class="form-text text-primary" style="cursor: pointer;" id="refresh-captcha-iuran">
                                        <i class="fas fa-sync-alt mr-1"></i>Refresh Kode
                                    </small>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-portal btn-block">
                                    <i class="fas fa-search mr-2"></i>
                                    Cek Status Iuran
                                </button>
                            </div>
                        </form>

                        <!-- Results -->
                        <div id="result-container" style="display: none;">
                            <!-- Results will be displayed here -->
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="feature-item">
                                <i class="fas fa-lock text-success mr-2"></i>
                                <span>Data Terenkripsi</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-shield-alt text-primary mr-2"></i>
                                <span>Validasi Aman</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle text-info mr-2"></i>
                                <span>Real-Time Update</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card card-portal">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle text-primary mr-2"></i>
                            Informasi Layanan
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-list-alt mr-2"></i>
                                    Jenis Iuran Tersedia:
                                </h6>
                                <ul class="small text-gray-600">
                                    <li>Iuran Kebersihan (Rp 25.000/bulan)</li>
                                    <li>Iuran Keamanan (Rp 30.000/bulan)</li>
                                    <li>Iuran Sosial/Kematian (Rp 10.000/bulan)</li>
                                    <li>Iuran Kampung (Rp 10.000/bulan)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-clock mr-2"></i>
                                    Jadwal Pembayaran:
                                </h6>
                                <ul class="small text-gray-600">
                                    <li>Bat pembayaran: Tanggal 1-30</li>
                                    <li>Metode: Tunai, Transfer, QRIS</li>
                                    <li>Tempat: Kantor Sekretariat RT/RW</li>
                                    <li>Kontak: +62 812-3456-7890</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="text-center mt-4">
                    <a href="{{ route('portal') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Portal Utama
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Form submission
        $('#iuran-form').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                nik: $('#nik').val(),
                captcha: $('#captcha-iuran').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Show loading
            showFormLoading($(this));

            // Make AJAX request
            $.ajax({
                url: '/portal/cek-iuran',
                type: 'POST',
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                },
                success: function(response) {
                    hideFormLoading($('#iuran-form'));
                    if (response.success) {
                        displayIuranResult(response.data);
                        // Auto refresh captcha after successful submission
                        refreshCaptcha('iuran');
                    } else {
                        showToast('error', response.message);
                        // Refresh captcha on error too
                        refreshCaptcha('iuran');
                    }
                },
                error: function(xhr, status, error) {
                    hideFormLoading($('#iuran-form'));
                    const response = xhr.responseJSON || { message: 'Terjadi kesalahan pada server' };
                    if (response.message && response.message.includes('CAPTCHA')) {
                        showToast('error', response.message);
                        refreshCaptcha('iuran');
                    } else if (xhr.status === 429) {
                        showToast('warning', response.message || 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.');
                    } else if (xhr.status === 404) {
                        showToast('error', 'Data warga tidak ditemukan. Silakan periksa kembali NIK Anda.');
                    } else {
                        showToast('error', response.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                }
            });
        });

        // Refresh captcha
        $('#refresh-captcha-iuran').on('click', function() {
            refreshCaptcha('iuran');
        });

        // Refresh captcha function
        function refreshCaptcha(type) {
            $.get('/portal/captcha')
                .done(function(data) {
                    $('#captcha-display-' + type).text(data.captcha);
                    $('#captcha-' + type).val('');
                })
                .fail(function() {
                    // Generate local captcha if server fails
                    const randomCaptcha = Math.random().toString(36).substring(2, 8).toUpperCase();
                    $('#captcha-display-' + type).text(randomCaptcha);
                    $('#captcha-' + type).val('');
                });
        }

        // Display iuran result function
        function displayIuranResult(data) {
            if (!data || !data.nama_warga) {
                showToast('error', 'Data yang diterima tidak valid');
                return;
            }

            const resultHtml = `
                <div class="alert alert-success alert-dismissible fade show">
                    <h5><i class="fas fa-check-circle mr-2"></i>Data Iuran Ditemukan!</h5>
                    <p class="mb-1">Data warga atas nama <strong>${data.nama_warga}</strong></p>
                    <p class="mb-0">NIK: ${data.nik}</p>
                </div>

                <div class="card border-left-success">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="fas fa-chart-pie mr-2"></i>
                            Ringkasan Iuran
                        </h6>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h4 class="text-primary">${data.ringkasan_iuran.jumlah_tagihan}</h4>
                                <small class="text-gray-600">Total Tagihan</small>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-success">${data.ringkasan_iuran.jumlah_lunas}</h4>
                                <small class="text-gray-600">Sudah Lunas</small>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-warning">${data.ringkasan_iuran.jumlah_tunggakan}</h4>
                                <small class="text-gray-600">Belum Bayar</small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <div class="border-top pt-3">
                                    <h5 class="text-danger mb-2">${data.ringkasan_iuran.total_tagihan}</h5>
                                    <small class="text-gray-600">Total Tunggakan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title text-primary">
                            <i class="fas fa-list mr-2"></i>
                            Detail Iuran Terkini
                        </h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Jenis Iuran</th>
                                        <th>Periode</th>
                                        <th>Nominal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.detail_iuran.map(item => {
                                        let statusBadge = '';
                                        let statusText = item.status;

                                        if (item.status.includes('Lunas') && item.tanggal_bayar) {
                                            statusText = `Lunas - ${item.tanggal_bayar}`;
                                            statusBadge = `<span class="badge badge-success">${statusText}</span>`;
                                        } else if (item.status.includes('Lunas')) {
                                            statusBadge = `<span class="badge badge-success">${item.status}</span>`;
                                        } else {
                                            statusBadge = `<span class="badge badge-warning">${item.status}</span>`;
                                        }

                                        return `
                                            <tr>
                                                <td>${item.jenis_iuran}</td>
                                                <td>${item.periode}</td>
                                                <td>${item.nominal}</td>
                                                <td>${statusBadge}</td>
                                            </tr>
                                        `;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;

            $('#result-container').html(resultHtml).show();
            showToast('success', 'Data iuran berhasil ditemukan!');

            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#result-container').offset().top - 100
            }, 1000);
        }
    });
</script>
@endsection