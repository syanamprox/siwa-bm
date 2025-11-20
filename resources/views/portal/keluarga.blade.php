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
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="mb-1">Cek Data Keluarga</h4>
                                <small class="opacity-75">Informasi data kependudukan keluarga</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Alert Container -->
                        <div id="alert-container" class="mb-3"></div>

                        <!-- Form -->
                        <form id="keluarga-form" class="mb-4">
                            @csrf
                            <div class="form-group">
                                <label for="no_kk" class="form-label font-weight-bold">
                                    <i class="fas fa-id-card text-primary mr-2"></i>
                                    Nomor Kartu Keluarga (KK)
                                </label>
                                <input type="text" class="form-control form-control-portal" id="no_kk" name="no_kk"
                                       placeholder="Masukkan nomor KK" required>
                                <small class="form-text text-muted">
                                    Masukkan nomor Kartu Keluarga untuk melihat data keluarga
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="captcha-keluarga" class="form-label font-weight-bold">
                                    <i class="fas fa-shield-alt text-primary mr-2"></i>
                                    Kode Verifikasi
                                </label>
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control form-control-portal" id="captcha-keluarga"
                                               name="captcha" placeholder="Masukkan kode di samping" required
                                               style="height: 58px;">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="captcha-display" id="captcha-display-keluarga"
                                             style="height: 58px; line-height: 52px; font-size: 1.4rem;">
                                            {{ session('captcha_code') ?? strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6)) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="form-text text-muted">Masukkan kode verifikasi untuk keamanan</small>
                                    <small class="form-text text-primary" style="cursor: pointer;" id="refresh-captcha-keluarga">
                                        <i class="fas fa-sync-alt mr-1"></i>Refresh Kode
                                    </small>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-portal btn-block">
                                    <i class="fas fa-search mr-2"></i>
                                    Cek Data Keluarga
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
                                <i class="fas fa-shield-alt text-success mr-2"></i>
                                <span>Data Aman</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-users text-primary mr-2"></i>
                                <span>Data Keluarga</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-map-marker-alt text-info mr-2"></i>
                                <span>Lokasi Valid</span>
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
                                    <i class="fas fa-family mr-2"></i>
                                    Data Yang Ditampilkan:
                                </h6>
                                <ul class="small text-gray-600">
                                    <li>Kepala Keluarga dan anggota keluarga</li>
                                    <li>Alamat lengkap (sensor)</li>
                                    <li>Status domisili keluarga</li>
                                    <li>Jumlah anggota keluarga</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-privacy mr-2"></i>
                                    Perlindungan Data:
                                </h6>
                                <ul class="small text-gray-600">
                                    <li>No. KK ditampilkan sebagian</li>
                                    <li>Alamat dipotong untuk privasi</li>
                                    <li>Data pribadi anggota tidak lengkap</li>
                                    <li>Monitor log akses warga</li>
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
        $('#keluarga-form').on('submit', function(e) {
            e.preventDefault();

            const formData = {
                no_kk: $('#no_kk').val(),
                captcha: $('#captcha-keluarga').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Show loading
            showFormLoading($(this));

            // Make AJAX request
            $.ajax({
                url: '/portal/cek-keluarga',
                type: 'POST',
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
                },
                success: function(response) {
                    hideFormLoading($('#keluarga-form'));
                    if (response.success) {
                        displayKeluargaResult(response.data);
                        // Auto refresh captcha after successful submission
                        refreshCaptcha('keluarga');
                    } else {
                        showToast('error', response.message);
                        // Refresh captcha on error too
                        refreshCaptcha('keluarga');
                    }
                },
                error: function(xhr, status, error) {
                    hideFormLoading($('#keluarga-form'));
                    const response = xhr.responseJSON || { message: 'Terjadi kesalahan pada server' };
                    if (response.message && response.message.includes('CAPTCHA')) {
                        showToast('error', response.message);
                        refreshCaptcha('keluarga');
                    } else if (xhr.status === 429) {
                        showToast('warning', response.message || 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.');
                    } else if (xhr.status === 404) {
                        showToast('error', 'Data keluarga tidak ditemukan. Silakan periksa kembali nomor KK Anda.');
                    } else {
                        showToast('error', response.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    }
                }
            });
        });

        // Refresh captcha
        $('#refresh-captcha-keluarga').on('click', function() {
            refreshCaptcha('keluarga');
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

        // Display keluarga result function
        function displayKeluargaResult(data) {
            // Sanitize name function - show first 4 characters + "***" for privacy
            const sanitizeName = (name) => {
                if (!name) return '-';
                return name.length > 4 ? name.substring(0, 4) + '***' : name;
            };

            // Format date to "d F Y" (01 Januari 2024)
            const formatDate = (dateString) => {
                if (!dateString) return dateString;
                const date = new Date(dateString);
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const day = String(date.getDate()).padStart(2, '0');
                const month = months[date.getMonth()];
                const year = date.getFullYear();
                return `${day} ${month} ${year}`;
            };

            const anggotaHtml = data.anggota_keluarga.map(anggota => `
                <tr>
                    <td>${sanitizeName(anggota.nama_lengkap)}</td>
                    <td>${anggota.hubungan_keluarga}</td>
                    <td><span class="badge ${anggota.jenis_kelamin === 'Laki-laki' ? 'badge-primary' : 'badge-info'}">${anggota.jenis_kelamin}</span></td>
                </tr>
            `).join('');

            const resultHtml = `
                <div class="alert alert-success alert-dismissible fade show">
                    <h5><i class="fas fa-check-circle mr-2"></i>Data Keluarga Ditemukan!</h5>
                    <p class="mb-0">Data keluarga berhasil diverifikasi</p>
                </div>

                <div class="card border-left-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-home mr-2"></i>Identitas Keluarga</h6>
                    </div>
                    <div class="card-body py-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>No. KK:</strong> ${data.no_kk}</p>
                                <p class="mb-2"><strong>Kepala Keluarga:</strong> ${data.kepala_keluarga}</p>
                                <p class="mb-0"><strong>Jumlah Anggota:</strong> ${data.jumlah_anggota} orang</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>RT/RW KTP:</strong> ${data.rt_kk}/${data.rw_kk}</p>
                                <p class="mb-2"><strong>Kelurahan KTP:</strong> ${data.kelurahan_kk}</p>
                                <p class="mb-0"><strong>Status Domisili:</strong> ${data.status_domisili_keluarga}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-map-marker-alt mr-2"></i>Alamat Lengkap</h6>
                    </div>
                    <div class="card-body py-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Alamat KTP:</strong></p>
                                <p class="text-gray-700">${data.alamat_kk}</p>
                                <p class="mb-2 mt-3"><strong>RT/RW KTP:</strong> ${data.rt_kk}/${data.rw_kk}</p>
                                <p class="mb-0"><strong>Kelurahan KTP:</strong> ${data.kelurahan_kk}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Alamat Domisili:</strong></p>
                                <p class="text-gray-700">${data.alamat_domisili || 'Sama dengan alamat KTP'}</p>
                                <p class="mb-2 mt-3"><strong>RT/RW Domisili:</strong> ${data.rt || '-'}/${data.rw || '-'}</p>
                                <p class="mb-0"><strong>Kelurahan Domisili:</strong> ${data.kelurahan || '-'}</p>
                            </div>
                        </div>
                        ${data.tanggal_mulai_domisili_keluarga ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <p class="mb-0"><small><strong>Tanggal Mulai Domisili:</strong> ${formatDate(data.tanggal_mulai_domisili_keluarga)}</small></p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-users mr-2"></i>Daftar Anggota Keluarga</h6>
                    </div>
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <th>Hubungan</th>
                                        <th>Jenis Kelamin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${anggotaHtml}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;

            $('#result-container').html(resultHtml).show();
            showToast('success', 'Data keluarga berhasil ditemukan!');

            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#result-container').offset().top - 100
            }, 1000);
        }
    });
</script>
@endsection