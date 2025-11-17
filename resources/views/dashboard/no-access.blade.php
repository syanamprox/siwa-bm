@extends('layouts.app')

@section('title', 'Tidak Ada Akses - SIWA')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow border-left-warning">
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
                </div>
                <h3 class="text-gray-800 mb-3">Tidak Ada Akses Wilayah</h3>
                <p class="text-gray-500 mb-4">
                    Akun Anda belum memiliki akses ke wilayah manapun. Silakan hubungi administrator untuk memberikan akses wilayah yang sesuai dengan peran Anda.
                </p>
                <div class="small text-gray-400">
                    <p class="mb-1">Username: {{ Auth::user()->username }}</p>
                    <p class="mb-1">Role: {{ Auth::user()->role_label }}</p>
                    <p>Email: admin@siwa.local</p>
                </div>
                <hr>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection