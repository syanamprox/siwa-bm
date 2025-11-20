<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Portal Informasi Warga (SIWA) Kelurahan Bendul Merisi">
    <meta name="author" content="SIWA Team">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Portal Informasi Warga - {{ config('app.name', 'SIWA') }}</title>

    <!-- Custom fonts for this template-->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS 4.6 (compatible with SB Admin 2) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('sbadmin2.css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom Portal Styles -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
        }

        .portal-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            padding: 2rem 1rem;
        }

        .card-portal {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .card-portal:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .brand-logo {
            font-size: 4rem;
            color: #4e73df;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .portal-heading {
            color: #3a3b45;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .form-control-portal {
            border-radius: 10rem;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            border: 1px solid #d1d3e2;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control-portal:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            background: rgba(255, 255, 255, 1);
        }

        .btn-portal {
            border-radius: 10rem;
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-portal:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .captcha-display {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 0.5rem;
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 3px;
            text-align: center;
            color: #5a5c69;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .service-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .alert-dismissible .close {
            position: absolute;
            top: 0;
            right: 0;
            padding: 0.75rem 1.25rem;
            color: inherit;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .brand-logo {
                font-size: 3rem;
            }

            .card-portal {
                margin-bottom: 1.5rem;
            }

            .service-icon {
                width: 3rem;
                height: 3rem;
                font-size: 1.2rem;
            }

            .col-md-8 {
                flex: 0 0 70%;
                max-width: 70%;
            }

            .col-md-4 {
                flex: 0 0 30%;
                max-width: 30%;
            }
        }

        @media (max-width: 576px) {
            .brand-logo {
                font-size: 2.5rem;
            }

            .col-md-8, .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .col-md-4 {
                margin-top: 0.5rem;
            }

            .service-icon {
                width: 2.5rem;
                height: 2.5rem;
                font-size: 1rem;
            }

            /* Ensure captcha height is consistent on mobile */
            .captcha-display {
                min-height: 58px !important;
            }
        }

        /* Custom Card Styles */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2);
        }

        /* Gradient Headers */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border-radius: 0.75rem;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }

        /* Border Left Accents */
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        /* Form Controls */
        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            font-size: 0.85rem;
            transition: all 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Buttons */
        .btn {
            border-radius: 0.35rem;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: none;
            letter-spacing: 0.5px;
            transition: all 0.15s ease-in-out;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2e59d9 0%, #1a3d8e 100%);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            border: none;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #17a673 0%, #0e5b45 100%);
            transform: translateY(-1px);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
            border: none;
            color: #fff;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #f4b619 0%, #b5890a 100%);
            transform: translateY(-1px);
        }

        /* Captcha Display */
        .captcha-display {
            background: #fff;
            border: 2px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 0.75rem;
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 3px;
            text-align: center;
            color: #5a5c69;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            writing-mode: horizontal-tb;
            text-orientation: mixed;
            transform: none;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(2px);
        }

        /* Icon Circle */
        .icon-circle {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .icon-circle i {
            margin: 0;
        }

        /* Feature Items */
        .feature-item {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            color: #5a5c69;
        }

        /* Result Cards Enhancement */
        .result-card {
            border-left: 4px solid #1cc88a;
            transition: all 0.3s ease;
        }

        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Loading Spinner Enhancement */
        .spinner-border {
            border-width: 0.25em;
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .alert-success {
            background: linear-gradient(135deg, #d1f2eb 0%, #c3e6cb 100%);
            color: #0f5132;
            border-left: 4px solid #1cc88a;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
            color: #721c24;
            border-left: 4px solid #e74a3b;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            border-left: 4px solid #f6c23e;
        }

        /* Text Colors */
        .text-primary {
            color: #4e73df !important;
        }

        .text-success {
            color: #1cc88a !important;
        }

        .text-info {
            color: #36b9cc !important;
        }

        .text-warning {
            color: #f6c23e !important;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .text-gray-600 {
            color: #858796 !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .card-body {
                padding: 1.25rem;
            }

            .btn {
                font-size: 0.8rem;
            }

            .form-control {
                font-size: 0.8rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Loading overlay for forms */
        .form-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.6;
        }

        .form-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Toast Styles */
        .toast {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .toast-success {
            border-left: 4px solid #1cc88a;
        }

        .toast-success .toast-header {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%) !important;
        }

        .toast-warning {
            border-left: 4px solid #f6c23e;
        }

        .toast-warning .toast-header {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%) !important;
        }

        .toast-error {
            border-left: 4px solid #e74a3b;
        }

        .toast-error .toast-header {
            background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%) !important;
        }

        .toast-info {
            border-left: 4px solid #36b9cc;
        }

        .toast-info .toast-header {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%) !important;
        }

        .toast.fade-in {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="portal-container">
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap core JavaScript 4.6 (compatible with SB Admin 2) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('sbadmin2.js/sb-admin-2.min.js') }}"></script>

    <script>
        // Get CSRF token from meta tag
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Common JavaScript functions
        function showLoading() {
            if ($('#loading-overlay').length === 0) {
                $('body').append(`
                    <div id="loading-overlay" class="loading-overlay">
                        <div class="text-center">
                            <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div class="mt-3 text-white">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Memproses permintaan...
                            </div>
                        </div>
                    </div>
                `);
            }
            $('#loading-overlay').fadeIn(300);
        }

        function hideLoading() {
            $('#loading-overlay').fadeOut(300, function() {
                $(this).remove();
            });
        }

        function showFormLoading($form) {
            $form.addClass('form-loading');
            $form.find('button[type="submit"]').prop('disabled', true);
        }

        function hideFormLoading($form) {
            $form.removeClass('form-loading');
            $form.find('button[type="submit"]').prop('disabled', false);
        }

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show mb-3 fade-in-up" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            ${type === 'success' ? '<i class="fas fa-check-circle fa-lg"></i>' :
                              type === 'warning' ? '<i class="fas fa-exclamation-triangle fa-lg"></i>' :
                              '<i class="fas fa-times-circle fa-lg"></i>'}
                        </div>
                        <div class="flex-grow-1">
                            <strong>${type === 'success' ? 'Berhasil!' :
                                      type === 'warning' ? 'Perhatian!' : 'Error!'}</strong>
                            <div class="small">${message}</div>
                        </div>
                        <button type="button" class="close ml-3" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            `;
            $('#alert-container').html(alertHtml);

            // Auto-hide after 6 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 6000);
        }

        function showToast(type, message, title = null) {
            const toastTitle = title || (type === 'success' ? 'Berhasil!' :
                                       type === 'warning' ? 'Perhatian!' :
                                       type === 'error' ? 'Error!' : 'Informasi');

            const toastIcon = type === 'success' ? 'fa-check-circle' :
                             type === 'warning' ? 'fa-exclamation-triangle' :
                             type === 'error' ? 'fa-times-circle' : 'fa-info-circle';

            const toastId = 'toast-' + Date.now();

            const toastHtml = `
                <div id="${toastId}" class="toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true"
                     style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <div class="toast-header bg-${type} text-white">
                        <i class="fas ${toastIcon} mr-2"></i>
                        <strong class="mr-auto">${toastTitle}</strong>
                        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;

            // Remove any existing toasts to avoid stacking
            $('.toast').remove();

            // Add toast to body
            $('body').append(toastHtml);

            // Show the toast
            $('#' + toastId).toast({
                autohide: true,
                delay: 5000
            }).toast('show');

            // Remove from DOM after hidden
            $('#' + toastId).on('hidden.bs.toast', function () {
                $(this).remove();
            });
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Rate limiting feedback
        let rateLimitMessage = false;
        function checkRateLimit(response) {
            if (response.status === 429 && !rateLimitMessage) {
                showAlert('warning', 'Terlalu banyak permintaan. Silakan tunggu beberapa saat sebelum mencoba lagi.');
                rateLimitMessage = true;
                setTimeout(() => { rateLimitMessage = false; }, 60000);
            }
        }

        // Initialize tooltips and basic debugging
        $(function () {
            console.log('=== LAYOUT INITIALIZED ===');
            console.log('jQuery version:', $.fn.jquery);
            console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');

            $('[data-toggle="tooltip"]').tooltip();
        });

        // Smooth scroll
        $(document).ready(function() {
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 1000);
                }
            });
        });
    </script>

    @yield('scripts')
</body>

</html>