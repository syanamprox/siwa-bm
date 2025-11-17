<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Informasi Warga (SIWA) Kelurahan">
    <meta name="author" content="SIWA Team">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIWA - Sistem Informasi Warga')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor_sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('sbadmin2.css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this application -->
    <style>
        /* Override sidebar to white theme for better readability */
        .sidebar {
            background: #ffffff !important;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .sidebar .nav-item .nav-link {
            color: #3a3b45 !important;
            font-weight: 500;
            border-radius: 0.35rem;
            margin: 0.25rem 0;
        }

        .sidebar .nav-item .nav-link:hover {
            color: #1e88e5 !important;
            background-color: rgba(26, 136, 229, 0.1) !important;
        }

        .sidebar .nav-item .nav-link.active {
            color: #1e88e5 !important;
            background-color: rgba(26, 136, 229, 0.15) !important;
            border-left: 4px solid #1e88e5 !important;
            font-weight: 600;
        }

        .sidebar .nav-item .collapse .collapse-inner .collapse-item {
            color: #3a3b45 !important;
            border-radius: 0.35rem;
        }

        .sidebar .nav-item .collapse .collapse-inner .collapse-item:hover {
            color: #1e88e5 !important;
            background-color: rgba(26, 136, 229, 0.1) !important;
        }

        .sidebar .nav-item .collapse .collapse-inner .collapse-item.active {
            color: #1e88e5 !important;
            background-color: rgba(26, 136, 229, 0.15) !important;
            font-weight: 600;
        }

        /* Sidebar brand styling */
        .sidebar-brand {
            color: #3a3b45 !important;
            font-weight: 600;
        }

        .sidebar-brand:hover {
            color: #1e88e5 !important;
        }

        /* Sidebar divider */
        .sidebar-divider {
            border-top: 1px solid #e3e6f0 !important;
        }

        /* Sidebar heading */
        .sidebar-heading {
            color: #3a3b45 !important;
            font-weight: 600;
        }

        /* Collapse menu improvements */
        .sidebar .nav-item .collapse {
            background-color: #f8f9fc !important;
            border: 1px solid #e3e6f0 !important;
            border-radius: 0.35rem;
            margin: 0 0.5rem 0.5rem 0.5rem;
            padding: 0.25rem;
        }

        /* Card header in white sidebar */
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e3e6f0;
            color: #3a3b45;
        }

        /* Text improvements for better readability in sidebar */
        .sidebar .text-gray-300, .sidebar .text-gray-400, .sidebar .text-gray-500, .sidebar .text-gray-600, .sidebar .text-gray-700, .sidebar .text-gray-800, .sidebar .text-gray-900 {
            color: #3a3b45 !important;
        }

        /* Sidebar icons color */
        .sidebar .fas.fa-tachometer-alt,
        .sidebar .fas fa-users,
        .sidebar .fas fa-user-friends,
        .sidebar .fas fa-dollar-sign,
        .sidebar .fas fa-chart-bar,
        .fas.fa-user-shield,
        .fas fa-cogs,
        .fas fa-database {
            color: #3a3b45 !important;
        }

        .sidebar .nav-item .nav-link i {
            color: #6c757d !important;
        }

        .sidebar .nav-item .nav-link:hover i {
            color: #1e88e5 !important;
        }

        .sidebar .nav-item .nav-link.active i {
            color: #1e88e5 !important;
        }
        .user-role-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.35rem;
            color: #ffffff !important;
        }
        .role-admin { background-color: #dc3545; }
        .role-lurah { background-color: #28a745; }
        .role-rw { background-color: #007bff; }
        .role-rt { background-color: #6c757d; }
        .data-table-container {
            overflow-x: auto;
        }
        .modal-content {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .btn-popup {
            margin: 0.25rem;
        }
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9998;
        }
        .alert-dismissible .btn-close {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 2;
            padding: 1.25rem 1rem;
        }
        .stats-card {
            transition: all 0.3s ease-in-out;
        }
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>

    <!-- Chart.js for dashboard -->
    @if(request()->is('dashboard') || request()->is('dashboard/*'))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endif

    <!-- jQuery for AJAX operations -->
    <script src="{{ asset('vendor_sbadmin2/vendor/jquery/jquery.min.js') }}"></script>

    @stack('styles')
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @if(auth()->check())
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-city"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SIWA</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading Manajemen Data -->
            <div class="sidebar-heading">
                Manajemen Data
            </div>

            <!-- Data Warga (RT level and above) -->
            @if(auth()->user()->hasRole(['rt','rw','lurah','admin']))
            <li class="nav-item {{ request()->is('rt/warga*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('warga.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Data Warga</span></a>
            </li>
            @endif

            <!-- Data Keluarga (RW level and above) -->
            @if(auth()->user()->hasRole(['rw','lurah','admin']))
            <li class="nav-item {{ request()->is('rw/keluarga*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('keluarga.index') }}">
                    <i class="fas fa-user-friends"></i>
                    <span>Data Keluarga</span></a>
            </li>
            @endif

            <!-- Manajemen Iuran -->
            <li class="nav-item {{ request()->is('iuran*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('iuran.index') }}">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Manajemen Iuran</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading Laporan -->
            <div class="sidebar-heading">
                Laporan
            </div>

            <!-- Laporan (Lurah & Admin) -->
            @if(auth()->user()->hasRole(['lurah','admin']))
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="true" aria-controls="collapseLaporan">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan</span>
                </a>
                <div id="collapseLaporan" class="collapse" aria-labelledby="headingLaporan" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ request()->is('lurah/laporan/wilayah') ? 'active' : '' }}" href="{{ route('laporan.wilayah') }}">Laporan Wilayah</a>
                        <a class="collapse-item {{ request()->is('lurah/laporan/kependudukan') ? 'active' : '' }}" href="{{ route('laporan.kependudukan') }}">Laporan Kependudukan</a>
                        <a class="collapse-item {{ request()->is('lurah/laporan/export') ? 'active' : '' }}" href="{{ route('laporan.export') }}">Export Data</a>
                    </div>
                </div>
            </li>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading Sistem -->
            <div class="sidebar-heading">
                Sistem
            </div>

            <!-- User Management (Admin Only) -->
            @if(auth()->user()->isAdmin())
            <li class="nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Manajemen User</span></a>
            </li>

            <!-- Pengaturan Sistem (Admin Only) -->
            <li class="nav-item {{ request()->is('admin/pengaturan*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pengaturan.index') }}">
                    <i class="fas fa-cogs"></i>
                    <span>Pengaturan Sistem</span></a>
            </li>

            <!-- Backup & Restore (Admin Only) -->
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="confirmBackup()">
                    <i class="fas fa-database"></i>
                    <span>Backup & Restore</span></a>
            </li>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        @endif
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @if(auth()->check())
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Cari data..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Cari data..." aria-label="Search" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->username }}</span>
                                <span class="user-role-badge text-white {{ 'role-' . auth()->user()->role }}">{{ auth()->user()->role_label }}</span>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </li>

                    </ul>

                </nav>
                @endif
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    @if(request()->is('dashboard'))
                    <!-- Dashboard heading is handled in dashboard view -->
                    @else
                        @hasSection('header')
                            @yield('header')
                        @else
                            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                                <h1 class="h3 mb-0 text-gray-800">@yield('title', 'SIWA')</h1>
                            </div>
                        @endif
                    @endif

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; {{ date('Y') }} SIWA - Sistem Informasi Warga Kelurahan</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Loading Overlay -->
    <div class="overlay" id="loadingOverlay"></div>
    <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor_sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor_sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('sbadmin2.js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    @if(request()->is('dashboard') || request()->is('dashboard/*'))
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endif

    <!-- Toast Notification Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <!-- Toast notifications will be inserted here -->
    </div>

    <!-- Common JavaScript Functions -->
    <script>
        // Global variables
        window.showToast = function(message, type = 'success') {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();

            const toastHtml = `
                <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-${type} text-white">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                        <strong class="me-auto">Notifikasi</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;

            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 3000
            });

            toast.show();

            toastElement.addEventListener('hidden.bs.toast', () => {
                toastElement.remove();
            });
        };

        window.showLoading = function() {
            document.getElementById('loadingOverlay').style.display = 'block';
            document.querySelector('.loading-spinner').style.display = 'block';
        };

        window.hideLoading = function() {
            document.getElementById('loadingOverlay').style.display = 'none';
            document.querySelector('.loading-spinner').style.display = 'none';
        };

        window.confirmAction = function(message, callback) {
            if (confirm(message)) {
                callback();
            }
        };

        window.formatRupiah = function(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        };

        // CSRF Token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Auto-hide alerts after 5 seconds
        $(document).ready(function() {
            $('.alert').fadeTo(5000, 500).slideUp(500, function(){
                $(this).remove();
            });
        });

        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });

        // Backup confirmation
        function confirmBackup() {
            if (confirm('Apakah Anda ingin membuat backup database sekarang?')) {
                showLoading();
                $.post('{{ route("backup.create") }}')
                    .done(function(response) {
                        hideLoading();
                        showToast(response.message || 'Backup berhasil dibuat', 'success');
                    })
                    .fail(function(xhr) {
                        hideLoading();
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat membuat backup';
                        showToast(message, 'error');
                    });
            }
        }
    </script>

    @stack('scripts')

</body>

</html>
