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
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="mb-1">Cek Data Warga</h4>
                                <small class="opacity-75">Verifikasi data kependudukan warga</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Alert Container -->
                        <div id="alert-container" class="mb-3"></div>

                        <!-- Form -->
                        <form id="warga-form" class="mb-4">
                            @csrf
                            <div class="form-group">
                                <label for="search" class="form-label font-weight-bold">
                                    <i class="fas fa-search text-primary mr-2"></i>
                                    Cari Data Warga
                                </label>
                                <input type="text" class="form-control form-control-portal" id="search" name="search"
                                       placeholder="Masukkan NIK atau nama lengkap" required>
                                <small class="form-text text-muted">
                                    Cari berdasarkan NIK (16 digit) atau nama lengkap warga
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="captcha-warga" class="form-label font-weight-bold">
                                    <i class="fas fa-shield-alt text-primary mr-2"></i>
                                    Kode Verifikasi
                                </label>
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-portal" id="captcha-warga"
                                               name="captcha" placeholder="Masukkan kode di samping" required
                                               style="height: 58px;">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="captcha-display" id="captcha-display-warga"
                                             style="height: 58px; line-height: 52px; font-size: 1.4rem;">
                                            {{ session('captcha_code') ?? strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6)) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="form-text text-muted">Masukkan kode verifikasi untuk keamanan</small>
                                    <small class="form-text text-primary" style="cursor: pointer;" id="refresh-captcha-warga">
                                        <i class="fas fa-sync-alt mr-1"></i>Refresh Kode
                                    </small>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-portal btn-block">
                                    <i class="fas fa-search mr-2"></i>
                                    Cari Data Warga
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
                                <span>Data Privasi</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-user-check text-primary mr-2"></i>
                                <span>Validasi Resmi</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-database text-info mr-2"></i>
                                <span>Data Update</span>
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
                                    <i class="fas fa-search mr-2"></i>
                                    Cara Pencarian:
                                </h6>
                                <ul class="small text-gray-600">
                                    <li>Masukkan NIK 16 digit (hasil lebih akurat)</li>
                                    <li>Masukkan nama lengkap (hasil umum)</li>
                                    <li>Data sensitif akan disembunyikan</li>
                                    <li>Hanya data non-privasi yang ditampilkan</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Keamanan Data:
                                </h6>
                                <ul class="small text-gray-600">
                                    <li>Rate limiting untuk mencegah penyalahgunaan</li>
                                    <li>Data disensor untuk privasi warga</li>
                                    <li>Logging untuk audit trail</li>
                                    <li>IP tracking untuk monitoring</li>
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
        $('#warga-form').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                search: $('#search').val(),
                captcha: $('#captcha-warga').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Show loading
            showFormLoading($(this));

            // Make AJAX request
            $.ajax({
                url: '/portal/cek-warga',
                type: 'POST',
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                },
                success: function(response) {
                    hideFormLoading($('#warga-form'));
                    if (response.success) {
                        displayWargaResult(response.data);
                        // Auto refresh captcha after successful submission
                        refreshCaptcha('warga');
                    } else {
                        showToast('error', response.message);
                        // Refresh captcha on error too
                        refreshCaptcha('warga');
                    }
                },
                error: function(xhr, status, error) {
                    hideFormLoading($('#warga-form'));
                    const response = xhr.responseJSON || { message: 'Terjadi kesalahan pada server' };
                    if (response.message && response.message.includes('CAPTCHA')) {
                        showToast('error', response.message);
                        refreshCaptcha('warga');
                    } else if (xhr.status === 429) {
                        showToast('warning', response.message || 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.');
                    } else if (xhr.status === 404) {
                        showToast('error', 'Data warga tidak ditemukan. Silakan periksa kembali NIK atau nama Anda.');
                    } else {
                        showToast('error', response.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                }
            });
        });

        // Refresh captcha
        $('#refresh-captcha-warga').on('click', function() {
            refreshCaptcha('warga');
        });

        // Refresh captcha function
        function refreshCaptcha(type) {
            $.get('{{ route("portal.captcha") }}')
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

        // Display warga result function
        function displayWargaResult(data) {
            const resultHtml = `
                <div class="alert alert-success alert-dismissible fade show">
                    <h5><i class="fas fa-check-circle mr-2"></i>Data Warga Ditemukan!</h5>
                    <p class="mb-0">Data kependudukan berhasil diverifikasi</p>
                </div>

                <div class="card border-left-success mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Identitas Pribadi</h6>
                    </div>
                    <div class="card-body py-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Nama Lengkap:</strong> ${data.nama_lengkap}</p>
                                <p class="mb-2"><strong>NIK:</strong> ${data.nik}</p>
                                <p class="mb-2"><strong>Tempat Lahir:</strong> ${data.tempat_lahir}</p>
                                <p class="mb-0"><strong>Tanggal Lahir:</strong> ${data.tanggal_lahir || '-'}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Jenis Kelamin:</strong> ${data.jenis_kelamin}</p>
                                <p class="mb-2"><strong>Golongan Darah:</strong> ${data.golongan_darah || '-'}</p>
                                <p class="mb-2"><strong>Agama:</strong> ${data.agama}</p>
                                <p class="mb-0"><strong>Status Perkawinan:</strong> ${data.status_perkawinan}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-briefcase mr-2"></i>Informasi Tambahan</h6>
                    </div>
                    <div class="card-body py-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Pekerjaan:</strong> ${data.pekerjaan}</p>
                                <p class="mb-2"><strong>Pendidikan:</strong> ${data.pendidikan_terakhir}</p>
                                <p class="mb-0"><strong>Kewarganegaraan:</strong> ${data.kewarganegaraan}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>No. Telepon:</strong> ${data.no_telepon || '-'}</p>
                                <p class="mb-2"><strong>Email:</strong> ${data.email || '-'}</p>
                                <p class="mb-0"><strong>Hub. Keluarga:</strong> ${data.hubungan_keluarga}</p>
                            </div>
                        </div>
                    </div>
                </div>

                ${data.keluarga ? `
                <div class="card border-left-primary">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0"><i class="fas fa-home mr-2"></i>Data Keluarga</h6>
                    </div>
                    <div class="card-body py-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>No. KK:</strong> ${data.keluarga.no_kk}</p>
                                <p class="mb-2"><strong>Alamat KTP:</strong> ${data.keluarga.alamat_kk}</p>
                                <p class="mb-0"><strong>RT/RW KTP:</strong> ${data.keluarga.rt_kk}/${data.keluarga.rw_kk}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Kelurahan:</strong> ${data.keluarga.kelurahan}</p>
                                <p class="mb-2"><strong>RT/RW Domisili:</strong> ${data.keluarga.rt || '-'}/${data.keluarga.rw || '-'}</p>
                                <p class="mb-0"><strong>Status Domisili:</strong> ${data.keluarga.status_domisili_keluarga}</p>
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}
            `;

            $('#result-container').html(resultHtml).show();
            showToast('success', 'Data warga berhasil ditemukan!');

            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#result-container').offset().top - 100
            }, 1000);
        }
    });
</script>
@endsection