@extends('portal.layouts.app')

@section('content')
<!-- Portal Header -->
<div class="portal-header">
    <h1>
        <i class="fas fa-search-location"></i>
        Portal Informasi Warga
    </h1>
    <p class="lead">
        Layanan informasi publik untuk mengecek data kependudukan dan status pembayaran iuran secara aman
    </p>
</div>

<!-- Alert Container -->
<div id="alert-container"></div>

<!-- Cari Data Warga -->
<div class="portal-card">
    <div class="card-header">
        <i class="fas fa-user"></i>
        Cari Data Warga
    </div>
    <div class="card-body">
        <form id="cekWargaForm">
            <div class="form-group">
                <label for="search_warga" class="form-label">
                    <strong>Masukkan NIK (16 digit) atau Nama Lengkap</strong>
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" id="search_warga" name="search"
                           placeholder="Contoh: 3578027006710001 atau Misfa" required>
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="captcha-container">
                <div class="captcha-display" id="captchaDisplay">Loading...</div>
                <button type="button" class="captcha-refresh" id="refreshCaptcha">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <div class="form-group mt-2">
                    <label for="captcha_warga" class="form-label">Masukkan kode di atas</label>
                    <input type="text" class="form-control" id="captcha_warga" name="captcha"
                           placeholder="Masukkan kode captcha" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">
                <i class="fas fa-search"></i>
                Cari Data Warga
            </button>
        </form>

        <div class="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="mt-2">Sedang mencari data...</div>
        </div>
    </div>
</div>

<!-- Cek Status Keluarga -->
<div class="portal-card">
    <div class="card-header">
        <i class="fas fa-users"></i>
        Cek Status Keluarga
    </div>
    <div class="card-body">
        <form id="cekKeluargaForm">
            <div class="form-group">
                <label for="no_kk" class="form-label">
                    <strong>Masukkan Nomor Kartu Keluarga</strong>
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" id="no_kk" name="no_kk"
                           placeholder="Contoh: 3578020609220004" required>
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="fas fa-id-card"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="captcha_keluarga" class="form-label">Masukkan kode captcha</label>
                <input type="text" class="form-control" id="captcha_keluarga" name="captcha"
                       placeholder="Masukkan kode captcha" required>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">
                <i class="fas fa-search"></i>
                Cek Keluarga
            </button>
        </form>

        <div class="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="mt-2">Sedang mencari data...</div>
        </div>
    </div>
</div>

<!-- Cek Status Iuran -->
<div class="portal-card">
    <div class="card-header">
        <i class="fas fa-credit-card"></i>
        Cek Status Pembayaran Iuran
    </div>
    <div class="card-body">
        <form id="cekIuranForm">
            <div class="form-group">
                <label for="nik_iuran" class="form-label">
                    <strong>Masukkan NIK (16 digit)</strong>
                </label>
                <div class="input-group">
                    <input type="text" class="form-control" id="nik_iuran" name="nik"
                           placeholder="Contoh: 3578027006710001" required>
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="captcha_iuran" class="form-label">Masukkan kode captcha</label>
                <input type="text" class="form-control" id="captcha_iuran" name="captcha"
                       placeholder="Masukkan kode captcha" required>
            </div>

            <button type="submit" class="btn btn-success btn-lg btn-block">
                <i class="fas fa-search"></i>
                Cek Status Iuran
            </button>
        </form>

        <div class="loading">
            <div class="spinner-border text-success" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="mt-2">Sedang memeriksa status...</div>
        </div>
    </div>
</div>

<!-- Result Container (diperlihat setelah pencarian) -->
<div class="result-container" id="resultContainer" style="display: none;">
    <div class="result-header">
        <h4><i class="fas fa-check-circle"></i> Hasil Pencarian</h4>
    </div>
    <div class="card-body">
        <div id="resultContent">
            <!-- Results will be loaded here -->
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Load captcha on page load
    loadCaptcha();

    // Refresh captcha on button click
    $('#refreshCaptcha').on('click', function() {
        loadCaptcha();
    });

    // Cek Warga Form
    $('#cekWargaForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            search: $('#search_warga').val(),
            captcha: $('#captcha_warga').val()
        };

        showLoading();

        $.ajax({
            url: '/portal/cek-warga',
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();

                if (response.success) {
                    displayWargaResult(response.data, 'warga');
                    showAlert('success', 'Data warga ditemukan!');
                    $('#cekWargaForm')[0].reset();
                    loadCaptcha(); // Refresh captcha after successful submission
                } else {
                    showAlert('danger', response.message);
                    checkRateLimit(response);
                    loadCaptcha(); // Refresh captcha on error
                }
            },
            error: function(xhr) {
                hideLoading();
                const response = xhr.responseJSON || { message: 'Terjadi kesalahan server' };
                showAlert('danger', response.message);
                checkRateLimit(xhr);
                loadCaptcha(); // Refresh captcha on error
            }
        });
    });

    // Cek Keluarga Form
    $('#cekKeluargaForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            no_kk: $('#no_kk').val(),
            captcha: $('#captcha_keluarga').val()
        };

        showLoading();

        $.ajax({
            url: '/portal/cek-keluarga',
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();

                if (response.success) {
                    displayKeluargaResult(response.data, 'keluarga');
                    showAlert('success', 'Data keluarga ditemukan!');
                    $('#cekKeluargaForm')[0].reset();
                    loadCaptcha(); // Refresh captcha after successful submission
                } else {
                    showAlert('danger', response.message);
                    checkRateLimit(response);
                    loadCaptcha(); // Refresh captcha on error
                }
            },
            error: function(xhr) {
                hideLoading();
                const response = xhr.responseJSON || { message: 'Terjadi kesalahan server' };
                showAlert('danger', response.message);
                checkRateLimit(xhr);
                loadCaptcha(); // Refresh captcha on error
            }
        });
    });

    // Cek Iuran Form
    $('#cekIuranForm').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            nik: $('#nik_iuran').val(),
            captcha: $('#captcha_iuran').val()
        };

        showLoading();

        $.ajax({
            url: '/portal/cek-iuran',
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();

                if (response.success) {
                    displayIuranResult(response.data, 'iuran');
                    showAlert('success', 'Data iuran ditemukan!');
                    $('#cekIuranForm')[0].reset();
                    loadCaptcha(); // Refresh captcha after successful submission
                } else {
                    showAlert('danger', response.message);
                    checkRateLimit(response);
                    loadCaptcha(); // Refresh captcha on error
                }
            },
            error: function(xhr) {
                hideLoading();
                const response = xhr.responseJSON || { message: 'Terjadi kesalahan server' };
                showAlert('danger', response.message);
                checkRateLimit(xhr);
                loadCaptcha(); // Refresh captcha on error
            }
        });
    });

    // Function to load captcha
    function loadCaptcha() {
        $.ajax({
            url: '/portal/captcha',
            method: 'GET',
            success: function(response) {
                $('#captchaDisplay').text(response.captcha);
                $('.captcha-container input[name="captcha"]').val(''); // Clear captcha inputs
                $('.captcha-container input[name="captcha"]').prop('required', true);
            },
            error: function() {
                $('#captchaDisplay').text('ERROR');
                showAlert('warning', 'Gagal memuat captcha. Silakan refresh halaman.');
            }
        });
    }

    // Function to display warga results
    function displayWargaResult(data, type) {
        const resultContent = `
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary"><i class="fas fa-user"></i> Data Pribadi</h5>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value">${data.nama_lengkap}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">NIK</div>
                            <div class="info-value">${data.nik}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tempat Lahir</div>
                            <div class="info-value">${data.tempat_lahir || '-'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Lahir</div>
                            <div class="info-value">${data.tanggal_lahir ? formatDate(data.tanggal_lahir) : '-'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jenis Kelamin</div>
                            <div class="info-value">${data.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Golongan Darah</div>
                            <div class="info-value">${data.golongan_darah || '-'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Agama</div>
                            <div class="info-value">${data.agama}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status Perkawinan</div>
                            <div class="info-value">${data.status_perkawinan}</div>
                        </div>
                        <div class="info-item">
                            <div="info-label">Pekerjaan</div>
                            <div class="info-value">${data.pekerjaan}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Pendidikan</div>
                            <div class="info-value">${data.pendidikan_terakhir}</div>
                        </div>
                    </div>
                </div>
                ${data.keluarga ? `
                <div class="col-md-6">
                    <h5 class="text-success"><i class="fas fa-home"></i> Data Keluarga</h5>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">No. KK</div>
                            <div class="info-value">${data.keluarga.no_kk}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Alamat KTP</div>
                            <div class="info-value">${data.keluarga.alamat_kk}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">RT/RW KTP</div>
                            <div class="info-value">RT ${data.keluarga.rt_kk} / RW ${data.keluarga.rw_kk}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kelurahan KTP</div>
                            <div class="info-value">${data.keluarga.kelurahan_kk}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status Domisili</div>
                            <div class="info-value">${data.keluarga.status_domisili_keluarga || '-'}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Alamat Domisili</div>
                            <div class="info-value">${data.keluarga.alamat_domisili || '-'}</div>
                        </div>
                        <div class="info-item">
                            <div="info-label">RT/RW Domisili</div>
                            <div class="info-value">${data.keluarga.rt ? `RT ${data.keluarga.rt}` : '-'} / ${data.keluarga.rw ? `RW ${data.keluarga.rw}` : '-'}</div>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
        `;

        $('#resultContent').html(resultContent);
        $('#resultContainer').show();

        // Scroll to results
        $('html, body').animate({
            scrollTop: $('#resultContainer').offset().top - 100
        }, 800);
    }

    // Function to display keluarga results
    function displayKeluargaResult(data, type) {
        const resultContent = `
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-success"><i class="fas fa-users"></i> Data Keluarga</h5>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">No. KK</div>
                            <div class="info-value">${data.no_kk}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Alamat KTP</div>
                            <div class="info-value">${data.alamat_kk}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">RT/RW KTP</div>
                            <div class="info-value">RT ${data.rt_kk} / RW ${data.rw_kk}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kelurahan KTP</div>
                            <div class="info-value">${data.kelurahan_kk}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status Domisili</div>
                            <div class="info-value">${data.status_domisili_keluarga}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jumlah Anggota</div>
                            <div class="info-value">${data.jumlah_anggota} orang</div>
                        </div>
                        ${data.kepala_keluarga ? `
                        <div class="info-item">
                            <div class="info-label">Kepala Keluarga</div>
                            <div class="info-value">${data.kepala_keluarga}</div>
                        </div>
                        ` : ''}
                        <div class="info-item">
                            <div class="info-label">Tanggal Mulai Domisili</div>
                            <div class="info-value">${data.tanggal_mulai_domisili_keluarga ? formatDate(data.tanggal_mulai_domisili_keluarga) : '-'}</div>
                        </div>
                    </div>
                </div>
            </div>

            ${data.anggota_keluarga && data.anggota_keluarga.length > 0 ? `
            <div class="row mt-4">
                <div class="col-md-12">
                    <h6 class="text-info"><i class="fas fa-users"></i> Daftar Anggota Keluarga</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Hubungan</th>
                                    <th>Jenis Kelamin</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.anggota_keluarga.map(function(member) {
                                    return `
                                        <tr>
                                            <td>${member.nama_lengkap}</td>
                                            <td>${member.hubungan_keluarga}</td>
                                            <td>${member.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                                        </tr>
                                    `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            ` : ''}
        `;

        $('#resultContent').html(resultContent);
        $('#resultContainer').show();

        // Scroll to results
        $('html, body').animate({
            scrollTop: $('#resultContainer').offset().top - 100
        }, 800);
    }

    // Function to display iuran results
    function displayIuranResult(data, type) {
        const ringkasan = data.ringkasan_iuran;

        const resultContent = `
            <div class="row">
                <div class="col-md-12">
                    <h5 class="text-success"><i class="fas fa-user"></i> ${data.nama_warga}</h5>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">NIK</div>
                            <div class="info-value">${data.nik}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Total Tagihan</div>
                            <div class="info-value text-danger">${ringkasan.total_tagihan}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Total Lunas</div>
                            <div class="info-value text-success">${ringkasan.total_lunas}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jumlah Tagihan</div>
                            <div class="info-value">${ringkasan.jumlah_tagihan}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Menunggak Pembayaran</div>
                            <div class="info-value text-warning">${ringkasan.jumlah_tunggakan}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Sudah Lunas</div>
                            <div class="info-value text-success">${ringkasan.jumlah_lunas}</div>
                        </div>
                    </div>
                </div>
            </div>

            ${data.detail_iuran && data.detail_iuran.length > 0 ? `
            <div class="row mt-4">
                <div class="col-md-12">
                    <h6 class="text-info"><i class="fas fa-list"></i> Detail Iuran (6 Terbaru)</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Jenis Iuran</th>
                                    <th>Periode</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                    <th>Jatuh Tempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.detail_iuran.map(function(iuran) {
                                    const statusClass = iuran.status === 'Lunas' ? 'text-success' :
                                                      iuran.status === 'Belum Bayar' ? 'text-danger' : 'text-warning';
                                    const statusIcon = iuran.status === 'Lunas' ? 'check-circle' :
                                                     iuran.status === 'Belum Bayar' ? 'times-circle' : 'exclamation-triangle';

                                    return `
                                        <tr>
                                            <td>${iuran.jenis_iuran}</td>
                                            <td>${iuran.periode}</td>
                                            <td>${iuran.nominal}</td>
                                            <td class="${statusClass}">
                                                <i class="fas fa-${statusIcon}"></i> ${iuran.status}
                                            </td>
                                            <td>${iuran.jatuh_tempo || '-'}</td>
                                        </tr>
                                    `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            ` : ''}
        `;

        $('#resultContent').html(resultContent);
        $('#resultContainer').show();

        // Scroll to results
        $('html, body').animate({
            scrollTop: $('#resultContainer').offset().top - 100
        }, 800);
    }

    // Helper function to format date
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
});
</script>
@endsection