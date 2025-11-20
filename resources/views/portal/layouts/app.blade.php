<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Portal Publik - Sistem Informasi Warga</title>

    <!-- Custom fonts for this template-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Roboto+Slab:400,700&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom styles for portal -->
    <style>
        /* Portal-specific styles */
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            background-color: #4e73df;
            min-height: 100vh;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .main-container {
            width: 100%;
            padding: 2rem;
        }

        @media (min-width: 768px) {
            .main-container {
                max-width: 1000px;
                margin: 0 auto;
            }
        }

        /* Portal Header */
        .portal-header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 3rem;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .portal-header h1 {
            color: #4e73df;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .portal-header .lead {
            color: #858796;
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        /* Portal Cards */
        .portal-card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .portal-card .card-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border-bottom: none;
            color: white;
            font-weight: 600;
            padding: 1.5rem;
        }

        .portal-card .card-header i {
            margin-right: 0.5rem;
        }

        .portal-card .card-body {
            padding: 2rem;
        }

        /* Form styling */
        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Buttons */
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }

        .btn-secondary {
            background-color: #858796;
            border-color: #858796;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background-color: #717384;
            border-color: #6b6d7d;
        }

        .btn-success {
            background-color: #1cc88a;
            border-color: #1cc88a;
            font-weight: 600;
        }

        .btn-success:hover {
            background-color: #17a673;
            border-color: #149157;
        }

        /* Captcha styling */
        .captcha-container {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .captcha-display {
            background: white;
            border: 2px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-align: center;
            margin-bottom: 0.5rem;
            font-family: 'Courier New', monospace;
            color: #5a5c69;
        }

        .captcha-refresh {
            background: #858796;
            color: white;
            border: none;
            border-radius: 0.35rem;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .captcha-refresh:hover {
            background: #717384;
        }

        /* Results */
        .result-container {
            background: white;
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-top: 2rem;
        }

        .result-header {
            background: #1cc88a;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 15px 15px 0 0;
        }

        .result-header i {
            margin-right: 0.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .info-item {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 1rem;
        }

        .info-label {
            font-size: 0.75rem;
            color: #858796;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 0.25rem;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-weight: 600;
            color: #5a5c69;
            font-size: 0.95rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.35rem;
        }

        .alert-success {
            background-color: #d1f2eb;
            border-left: 4px solid #1cc88a;
            color: #0f5132;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-left: 4px solid #e74a3b;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-left: 4px solid #f6c23e;
            color: #856404;
        }

        /* Loading */
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .loading.show {
            display: block;
        }

        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        /* Footer */
        .footer-info {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
            color: #858796;
            font-size: 0.875rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }

            .portal-header {
                padding: 2rem 1rem;
            }

            .portal-header h1 {
                font-size: 2rem;
            }

            .portal-card .card-body {
                padding: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <div class="main-container">
                    @yield('content')

                    <div class="footer-info">
                        <p class="mb-1">
                            <i class="fas fa-shield-alt"></i>
                            Portal Informasi Warga - Kelurahan Bendul Merisi
                        </p>
                        <p class="mb-0">
                            <small>Protected by rate limiting and data sanitization</small>
                        </p>
                    </div>
                </div>
            </div>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Core plugin JavaScript-->
    <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/js/sb-admin-2.min.js"></script>

    <script>
        // Common JavaScript functions
        function showLoading() {
            $('.loading').addClass('show');
        }

        function hideLoading() {
            $('.loading').removeClass('show');
        }

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>'}
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            $('#alert-container').html(alertHtml);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        // Rate limiting feedback
        let rateLimitMessage = false;
        function checkRateLimit(response) {
            if (response.status === 429 && !rateLimitMessage) {
                showAlert('warning', 'Too many requests. Please wait a moment before trying again.');
                rateLimitMessage = true;
                setTimeout(() => { rateLimitMessage = false; }, 60000); // Reset after 1 minute
            }
        }
    </script>

    @yield('scripts')
</body>

</html>
</head>
<body>
    <div class="portal-container">
        @yield('content')

        <div class="footer-info">
            <p class="mb-1">
                <i class="fas fa-shield-alt"></i>
                Portal Informasi Warga - Kelurahan Bendul Merisi
            </p>
            <p class="mb-0">
                <small>Protected by rate limiting and data sanitization</small>
            </p>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Common JavaScript functions
        function showLoading() {
            $('.loading').addClass('show');
        }

        function hideLoading() {
            $('.loading').removeClass('show');
        }

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <strong>${type === 'success' ? '✅' : '❌'}</strong> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            $('#alert-container').html(alertHtml);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        // Rate limiting feedback
        let rateLimitMessage = false;
        function checkRateLimit(response) {
            if (response.status === 429 && !rateLimitMessage) {
                showAlert('warning', 'Too many requests. Please wait a moment before trying again.');
                rateLimitMessage = true;
                setTimeout(() => { rateLimitMessage = false; }, 60000); // Reset after 1 minute
            }
        }
    </script>

    @yield('scripts')
</body>
</html>