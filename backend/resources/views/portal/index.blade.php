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

            <!-- Alert Container -->
            <div id="alert-container" class="mb-3"></div>

            <!-- Service Cards -->
            <div class="row">
                <!-- Iuran Service Card -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="{{ route('portal.iuran') }}" class="text-decoration-none">
                        <div class="card card-portal h-100 service-card">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="text-center">
                                    <div class="service-icon bg-white text-primary mx-auto mb-3">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <h5 class="mb-1">Cek Status Iuran</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-gray-700 text-center mb-3">
                                    Pantau status pembayaran iuran warga secara real-time
                                </p>
                                <ul class="list-unstyled text-center text-gray-600 small">
                                    <li><i class="fas fa-check text-success mr-2"></i>Status Pembayaran</li>
                                    <li><i class="fas fa-check text-success mr-2"></i>Histori Iuran</li>
                                    <li><i class="fas fa-check text-success mr-2"></i>Info Tunggakan</li>
                                </ul>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="text-center">
                                    <button class="btn btn-primary btn-sm">
                                        <i class="fas fa-arrow-right mr-1"></i>
                                        Akses Layanan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Warga Service Card -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="{{ route('portal.warga') }}" class="text-decoration-none">
                        <div class="card card-portal h-100 service-card">
                            <div class="card-header bg-gradient-info text-white">
                                <div class="text-center">
                                    <div class="service-icon bg-white text-info mx-auto mb-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h5 class="mb-1">Cek Data Warga</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-gray-700 text-center mb-3">
                                    Verifikasi data kependudukan warga secara aman
                                </p>
                                <ul class="list-unstyled text-center text-gray-600 small">
                                    <li><i class="fas fa-check text-success mr-2"></i>Data Identitas</li>
                                    <li><i class="fas fa-check text-success mr-2"></i>Status Kependudukan</li>
                                    <li><i class="fas fa-check text-success mr-2"></i>Validasi Resmi</li>
                                </ul>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="text-center">
                                    <button class="btn btn-info btn-sm">
                                        <i class="fas fa-arrow-right mr-1"></i>
                                        Akses Layanan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Keluarga Service Card -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="{{ route('portal.keluarga') }}" class="text-decoration-none">
                        <div class="card card-portal h-100 service-card">
                            <div class="card-header bg-gradient-success text-white">
                                <div class="text-center">
                                    <div class="service-icon bg-white text-success mx-auto mb-3">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <h5 class="mb-1">Cek Data Keluarga</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-gray-700 text-center mb-3">
                                    Informasi data kependudukan keluarga lengkap
                                </p>
                                <ul class="list-unstyled text-center text-gray-600 small">
                                    <li><i class="fas fa-check text-success mr-2"></i>Data KK</li>
                                    <li><i class="fas fa-check text-success mr-2"></i>Daftar Anggota</li>
                                    <li><i class="fas fa-check text-success mr-2"></i>Alamat Domisili</li>
                                </ul>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="text-center">
                                    <button class="btn btn-success btn-sm">
                                        <i class="fas fa-arrow-right mr-1"></i>
                                        Akses Layanan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Information Section -->
            <div class="card card-portal">
                <div class="card-body">
                    <h5 class="card-title text-center mb-4">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        Informasi Portal Layanan
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="icon-circle bg-primary text-white mb-3 mx-auto">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h6 class="font-weight-bold">Keamanan Terjamin</h6>
                                <p class="small text-gray-600">
                                    Dilengkapi sistem keamanan berlapis, rate limiting, dan data sanitization untuk melindungi informasi pribadi
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="icon-circle bg-info text-white mb-3 mx-auto">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h6 class="font-weight-bold">24/7 Online</h6>
                                <p class="small text-gray-600">
                                    Akses layanan kapan saja tanpa batas waktu untuk memudahkan warga dalam mendapatkan informasi
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="icon-circle bg-success text-white mb-3 mx-auto">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <h6 class="font-weight-bold">Data Valid</h6>
                                <p class="small text-gray-600">
                                    Informasi yang disajikan merupakan data resmi dari database kependudukan kelurahan
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card card-portal mt-4">
                <div class="card-footer bg-light">
                    <div class="text-center">
                        <h6 class="font-weight-bold text-primary mb-3">Hubungi Kami</h6>
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="feature-item mr-4">
                                <i class="fas fa-phone text-primary mr-2"></i>
                                <span>+62 812-3456-7890</span>
                            </div>
                            <div class="feature-item mr-4">
                                <i class="fas fa-envelope text-primary mr-2"></i>
                                <span>info@wonocolo.surabaya.go.id</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                                <span>Kantor Kecamatan Wonocolo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add hover effects for service cards
    $('.service-card').hover(
        function() {
            $(this).find('.service-icon').addClass('fa-spin');
        },
        function() {
            $(this).find('.service-icon').removeClass('fa-spin');
        }
    );

    // Add click ripple effect
    $('.service-card').on('click', function(e) {
        const card = $(this);
        const ripple = $('<span class="ripple"></span>');

        card.append(ripple);

        setTimeout(function() {
            ripple.remove();
        }, 600);
    });
});
</script>
@endpush

@push('styles')
<style>
.service-card {
    transition: all 0.3s ease;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.service-card .service-icon {
    transition: all 0.3s ease;
}

.service-card:hover .service-icon {
    transform: scale(1.1) rotate(5deg);
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    transform: scale(0);
    animation: ripple-animation 0.6s ease-out;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.card-portal {
    position: relative;
    overflow: hidden;
}
</style>
@endpush