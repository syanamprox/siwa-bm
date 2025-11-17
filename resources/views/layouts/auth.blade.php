<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Informasi Warga (SIWA) Kelurahan">
    <meta name="author" content="SIWA Team">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIWA') }} - Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor_sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('sbadmin2.css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card-login {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .brand-logo {
            font-size: 3rem;
            color: #4e73df;
            margin-bottom: 1rem;
        }

        .login-heading {
            color: #3a3b45;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .form-control-user {
            border-radius: 10rem;
            padding: 1rem;
            font-size: 0.85rem;
            border: 1px solid #d1d3e2;
        }

        .form-control-user:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-user {
            border-radius: 10rem;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
        }

        .alert-dismissible .close {
            position: absolute;
            top: 0;
            right: 0;
            padding: 0.75rem 1.25rem;
            color: inherit;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card o-hidden border-0 shadow-lg my-5 card-login" style="max-width: 400px; width: 100%;">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="p-5">
                    <div class="text-center">
                        <div class="brand-logo">
                            <i class="fas fa-city"></i>
                        </div>
                        <h1 class="h4 text-gray-900 mb-4">Selamat Datang di SIWA!</h1>
                        <p class="mb-4">Sistem Informasi Warga Kelurahan</p>
                    </div>

                    <!-- Flash Messages -->
                    @if(session('status'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form class="user" method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Username Field -->
                        <div class="form-group">
                            <input type="text"
                                   class="form-control form-control-user @error('username') is-invalid @enderror"
                                   id="username"
                                   name="username"
                                   placeholder="Username"
                                   value="{{ old('username') }}"
                                   required
                                   autofocus
                                   autocomplete="username">
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <input type="password"
                                   class="form-control form-control-user @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Password"
                                   required
                                   autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox small">
                                <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="remember">Ingat Saya</label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-user btn-block">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login
                        </button>
                    </form>

                    <hr>

                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Sistem ini dilindungi dengan keamanan berlapis
                        </small>
                    </div>

                    <!-- Default Users Info (untuk development) -->
                    @if(config('app.env') == 'local')
                    <div class="text-center mt-4">
                        <small class="text-info">
                            <strong>Akun Default:</strong><br>
                            Admin: admin/admin123<br>
                            Lurah: lurah/lurah123<br>
                            RW: rw01/rw123<br>
                            RT: rt01/rt123
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor_sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor_sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor_sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('sbadmin2.js/sb-admin-2.min.js') }}"></script>

    <script>
        // Auto-hide alerts after 5 seconds
        $(document).ready(function() {
            $('.alert').fadeTo(5000, 500).slideUp(500, function(){
                $(this).remove();
            });
        });

        // Add loading state to login button
        $('form').on('submit', function() {
            $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin mr-2"></i>Masuk...').prop('disabled', true);
        });
    </script>

    @stack('scripts')
</body>

</html>