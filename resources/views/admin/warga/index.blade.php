@extends('layouts.app')

@section('title', 'Data Warga - SIWA')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users mr-2"></i>Data Warga
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-user" onclick="showCreateModal()">
                <i class="fas fa-plus mr-2"></i>Tambah Warga
            </button>
            <button type="button" class="btn btn-info btn-user" onclick="showImportModal()">
                <i class="fas fa-file-import mr-2"></i>Import
            </button>
            <button type="button" class="btn btn-success btn-user" onclick="exportData()">
                <i class="fas fa-file-export mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Warga
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalWarga">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Punya KK
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="wargaDenganKK">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Laki-laki
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="wargaLaki">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mars fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Perempuan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="wargaPerempuan">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-venus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Pencarian dan Filter
            </h6>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label for="search">Cari Warga</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" placeholder="NIK atau Nama">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="rt">RT</label>
                        <input type="text" class="form-control" id="rt" name="rt" placeholder="RT">
                    </div>
                    <div class="col-md-2">
                        <label for="rw">RW</label>
                        <input type="text" class="form-control" id="rw" name="rw" placeholder="RW">
                    </div>
                    <div class="col-md-2">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="">Semua</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status_kk">Status KK</label>
                        <select class="form-control" id="status_kk" name="status_kk">
                            <option value="">Semua</option>
                            <option value="punya_kk">Punya KK</option>
                            <option value="tanpa_kk">Tanpa KK</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="resetFilters()">
                            <i class="fas fa-redo mr-1"></i>Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Warga Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>Daftar Warga
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="#" onclick="refreshData()">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </a>
                    <a class="dropdown-item" href="#" onclick="showStatistics()">
                        <i class="fas fa-chart-bar mr-2"></i>Statistik
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="wargaTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th>Jenis Kelamin</th>
                            <th>Umur</th>
                            <th>Alamat</th>
                            <th>No. Telepon</th>
                            <th>KK</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center">
                                <i class="fas fa-spinner fa-spin"></i>
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="form-group mb-0">
                    <label for="perPage">Show:</label>
                    <select class="form-control form-control-sm d-inline-block ml-2" id="perPage" style="width: auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <nav>
                    <ul class="pagination mb-0" id="pagination">
                        <!-- Pagination will be loaded via AJAX -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

</div>

<!-- Create/Edit Warga Modal -->
<div class="modal fade" id="wargaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="wargaModalTitle">
                    <i class="fas fa-user-plus mr-2"></i>Tambah Data Warga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="wargaForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Tabs for better organization -->
                    <ul class="nav nav-tabs" id="wargaTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ktp-tab" data-toggle="tab" href="#ktp" role="tab">
                                <i class="fas fa-id-card mr-2"></i>Data KTP
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="domisili-tab" data-toggle="tab" href="#domisili" role="tab">
                                <i class="fas fa-home mr-2"></i>Data Domisili
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="keluarga-tab" data-toggle="tab" href="#keluarga" role="tab">
                                <i class="fas fa-users mr-2"></i>Data Keluarga
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="wargaTabsContent">
                        <!-- Data KTP Tab -->
                        <div class="tab-pane fade show active" id="ktp" role="tabpanel">
                            <input type="hidden" id="warga_id" name="warga_id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nik" name="nik" maxlength="16" required>
                                        <small class="form-text text-muted">16 digit angka</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="golongan_darah">Golongan Darah</label>
                                        <select class="form-control" id="golongan_darah" name="golongan_darah">
                                            <option value="">Pilih</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="agama">Agama <span class="text-danger">*</span></label>
                                        <select class="form-control" id="agama" name="agama" required>
                                            <option value="">Pilih</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="alamat_ktp">Alamat KTP <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat_ktp" name="alamat_ktp" rows="2" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="rt_ktp">RT <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="rt_ktp" name="rt_ktp" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="rw_ktp">RW <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="rw_ktp" name="rw_ktp" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kelurahan_ktp">Kelurahan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kelurahan_ktp" name="kelurahan_ktp" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kecamatan_ktp">Kecamatan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kecamatan_ktp" name="kecamatan_ktp" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="kabupaten_ktp">Kabupaten <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kabupaten_ktp" name="kabupaten_ktp" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="provinsi_ktp">Provinsi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="provinsi_ktp" name="provinsi_ktp" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status_perkawinan">Status Perkawinan <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status_perkawinan" name="status_perkawinan" required>
                                            <option value="">Pilih</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pekerjaan">Pekerjaan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="pendidikan_terakhir">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                        <select class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" required>
                                            <option value="">Pilih</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kewarganegaraan">Kewarganegaraan <span class="text-danger">*</span></label>
                                        <select class="form-control" id="kewarganegaraan" name="kewarganegaraan" required>
                                            <option value="">Pilih</option>
                                            <option value="WNI">WNI</option>
                                            <option value="WNA">WNA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="foto_ktp">Foto KTP</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="foto_ktp" name="foto_ktp" accept="image/*">
                                            <label class="custom-file-label" for="foto_ktp">Pilih file foto KTP</label>
                                        </div>
                                        <small class="form-text text-muted">Format: JPG, PNG. Max: 2MB</small>
                                        <div id="fotoPreview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Domisili Tab -->
                        <div class="tab-pane fade" id="domisili" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="sama_dengan_ktp" name="sama_dengan_ktp" checked>
                                            <label class="form-check-label" for="sama_dengan_ktp">
                                                Alamat domisili sama dengan alamat KTP
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="alamatDomisiliFields" style="display: none;">
                                <div class="form-group">
                                    <label for="alamat_domisili">Alamat Domisili</label>
                                    <textarea class="form-control" id="alamat_domisili" name="alamat_domisili" rows="2"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="rt_domisili">RT</label>
                                            <input type="text" class="form-control" id="rt_domisili" name="rt_domisili">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="rw_domisili">RW</label>
                                            <input type="text" class="form-control" id="rw_domisili" name="rw_domisili">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kelurahan_domisili">Kelurahan</label>
                                            <input type="text" class="form-control" id="kelurahan_domisili" name="kelurahan_domisili">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="no_telepon">No. Telepon</label>
                                        <input type="text" class="form-control" id="no_telepon" name="no_telepon">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status_domisili">Status Domisili</label>
                                        <select class="form-control" id="status_domisili" name="status_domisili">
                                            <option value="">Pilih</option>
                                            <option value="Tetap">Tetap (Alamat & Domisili Sama)</option>
                                            <option value="Non Domisili">Non Domisili (Alamat Sini, Domisili Luar)</option>
                                            <option value="Luar">Luar (Alamat Luar, Domisili Sini)</option>
                                            <option value="Sementara">Sementara (Kontrak/Ngontrak)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_mulai_domisili">Tanggal Mulai Domisili</label>
                                        <input type="date" class="form-control" id="tanggal_mulai_domisili" name="tanggal_mulai_domisili">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Keluarga Tab -->
                        <div class="tab-pane fade" id="keluarga" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="tanpa_keluarga" name="tanpa_keluarga" checked>
                                            <label class="form-check-label" for="tanpa_keluarga">
                                                Tidak memiliki KK/bersendirian
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="keluargaFields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kk_id">Nomor KK</label>
                                            <select class="form-control" id="kk_id" name="kk_id">
                                                <option value="">Pilih KK</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="hubungan_keluarga">Hubungan dalam Keluarga</label>
                                            <select class="form-control" id="hubungan_keluarga" name="hubungan_keluarga">
                                                <option value="">Pilih</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveWargaBtn">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Warga Modal -->
<div class="modal fade" id="viewWargaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user mr-2"></i>Detail Data Warga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewWargaContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="editWargaBtn">Edit</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash mr-2 text-danger"></i>Hapus Data Warga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm">
                @csrf
                <input type="hidden" id="delete_warga_id">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data warga ini?</p>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan.
                    </div>
                    <div class="alert alert-info">
                        <strong>Nama:</strong> <span id="delete_nama"></span><br>
                        <strong>NIK:</strong> <span id="delete_nik"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadWarga();
    loadStatistics();
    loadFormData();

    // Handle checkbox changes
    $('#sama_dengan_ktp').change(function() {
        $('#alamatDomisiliFields').toggle(!this.checked);
    });

    $('#tanpa_keluarga').change(function() {
        $('#keluargaFields').toggle(!this.checked);
    });

    // Form submission
    $('#wargaForm').on('submit', function(e) {
        e.preventDefault();
        saveWarga();
    });

    // Delete form submission
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        deleteWarga();
    });

    // Per page change
    $('#perPage').on('change', function() {
        loadWarga();
    });

    // File input change
    $('#foto_ktp').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#fotoPreview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 200px;">');
            };
            reader.readAsDataURL(file);
        }
    });
});

function loadWarga(page = 1) {
    showLoading();

    var formData = $('#filterForm').serializeArray();
    var params = {};

    $.each(formData, function(i, field) {
        params[field.name] = field.value;
    });

    params.page = page;
    params.per_page = $('#perPage').val();

    $.ajax({
        url: '/api/warga',
        type: 'GET',
        data: params,
        success: function(response) {
            hideLoading();
            if (response.success) {
                renderWargaTable(response.data, response.pagination);
                renderPagination(response.pagination);
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal memuat data warga';
            showToast(message, 'error');
        }
    });
}

function renderWargaTable(wargaList, pagination) {
    var html = '';
    var no = (pagination.current_page - 1) * pagination.per_page;

    if (wargaList.length === 0) {
        html = '<tr><td colspan="9" class="text-center text-muted">Tidak ada data warga</td></tr>';
    } else {
        wargaList.forEach(function(warga) {
            no++;
            var umur = warga.tanggal_lahir ? calculateAge(warga.tanggal_lahir) : '-';
            var kkInfo = warga.keluarga ? warga.keluarga.no_kk : 'Tanpa KK';

            html += `
                <tr>
                    <td>${no}</td>
                    <td><code>${warga.nik_format || warga.nik}</code></td>
                    <td><strong>${warga.nama_lengkap}</strong></td>
                    <td><span class="badge ${warga.jenis_kelamin === 'L' ? 'badge-info' : 'badge-warning'}">${warga.jenis_kelamin_label || warga.jenis_kelamin}</span></td>
                    <td>${umur}</td>
                    <td><small>${warga.alamat_domisili_lengkap || warga.alamat_domisili}</small></td>
                    <td>${warga.no_telepon || '-'}</td>
                    <td><small>${kkInfo}</small></td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-info" onclick="viewWarga(${warga.id})" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" onclick="editWarga(${warga.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${warga.id}, '${warga.nama_lengkap}', '${warga.nik}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    $('#wargaTable tbody').html(html);
}

function renderPagination(pagination) {
    var html = '';

    // Previous button
    if (pagination.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadWarga(${pagination.current_page - 1})">Previous</a></li>`;
    }

    // Page numbers
    for (var i = 1; i <= pagination.last_page; i++) {
        var active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadWarga(${i})">${i}</a></li>`;
    }

    // Next button
    if (pagination.current_page < pagination.last_page) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadWarga(${pagination.current_page + 1})">Next</a></li>`;
    }

    $('#pagination').html(html);
}

function loadStatistics() {
    $.ajax({
        url: '/api/warga/statistics',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#totalWarga').text(response.data.total_warga);
                $('#wargaDenganKK').text(response.data.warga_dengan_kk);
                $('#wargaLaki').text(response.data.warga_laki);
                $('#wargaPerempuan').text(response.data.warga_perempuan);
            }
        },
        error: function(xhr) {
            console.error('Failed to load statistics');
        }
    });
}

function loadFormData() {
    $.ajax({
        url: '/api/warga/create',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                populateSelectOptions(response.data);
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Gagal memuat form data';
            showToast(message, 'error');
        }
    });
}

function populateSelectOptions(data) {
    // Populate agama
    var agamaHtml = '<option value="">Pilih</option>';
    data.daftar_agama.forEach(function(agama) {
        agamaHtml += `<option value="${agama}">${agama}</option>`;
    });
    $('#agama').html(agamaHtml);

    // Populate status perkawinan
    var statusHtml = '<option value="">Pilih</option>';
    data.daftar_status_perkawinan.forEach(function(status) {
        statusHtml += `<option value="${status}">${status}</option>`;
    });
    $('#status_perkawinan').html(statusHtml);

    // Populate pendidikan
    var pendidikanHtml = '<option value="">Pilih</option>';
    data.daftar_pendidikan.forEach(function(pendidikan) {
        pendidikanHtml += `<option value="${pendidikan}">${pendidikan}</option>`;
    });
    $('#pendidikan_terakhir').html(pendidikanHtml);

    // Populate hubungan keluarga
    var hubunganHtml = '<option value="">Pilih</option>';
    data.daftar_hubungan.forEach(function(hubungan) {
        hubunganHtml += `<option value="${hubungan}">${hubungan}</option>`;
    });
    $('#hubungan_keluarga').html(hubunganHtml);

    // Populate keluarga
    var kkHtml = '<option value="">Pilih KK</option>';
    data.keluarga_list.forEach(function(kk) {
        kkHtml += `<option value="${kk.id}">${kk.no_kk_format || kk.no_kk} - ${kk.nama_kepala_keluarga}</option>`;
    });
    $('#kk_id').html(kkHtml);
}

function showCreateModal() {
    $('#wargaModalTitle').html('<i class="fas fa-user-plus mr-2"></i>Tambah Data Warga');
    $('#saveWargaBtn').html('<i class="fas fa-save mr-2"></i>Simpan');
    $('#wargaForm')[0].reset();
    $('#warga_id').val('');
    $('#fotoPreview').html('');
    $('#sama_dengan_ktp').prop('checked', true).change();
    $('#tanpa_keluarga').prop('checked', true).change();
    $('#wargaModal').modal('show');
}

function editWarga(id) {
    showLoading();

    $.ajax({
        url: '/api/warga/' + id + '/edit',
        type: 'GET',
        success: function(response) {
            hideLoading();
            if (response.success) {
                var warga = response.data.warga;

                $('#wargaModalTitle').html('<i class="fas fa-user-edit mr-2"></i>Edit Data Warga');
                $('#saveWargaBtn').html('<i class="fas fa-save mr-2"></i>Update');

                // Fill form data
                $('#warga_id').val(warga.id);
                $('#nik').val(warga.nik);
                $('#nama_lengkap').val(warga.nama_lengkap);
                $('#tempat_lahir').val(warga.tempat_lahir);
                $('#tanggal_lahir').val(warga.tanggal_lahir);
                $('#jenis_kelamin').val(warga.jenis_kelamin);
                $('#golongan_darah').val(warga.golongan_darah);
                $('#alamat_ktp').val(warga.alamat_ktp);
                $('#rt_ktp').val(warga.rt_ktp);
                $('#rw_ktp').val(warga.rw_ktp);
                $('#kelurahan_ktp').val(warga.kelurahan_ktp);
                $('#kecamatan_ktp').val(warga.kecamatan_ktp);
                $('#kabupaten_ktp').val(warga.kabupaten_ktp);
                $('#provinsi_ktp').val(warga.provinsi_ktp);
                $('#agama').val(warga.agama);
                $('#status_perkawinan').val(warga.status_perkawinan);
                $('#pekerjaan').val(warga.pekerjaan);
                $('#kewarganegaraan').val(warga.kewarganegaraan);
                $('#pendidikan_terakhir').val(warga.pendidikan_terakhir);

                // Domisili data
                var isDomisiliSama = warga.isDomisiliSamaKtp;
                $('#sama_dengan_ktp').prop('checked', isDomisiliSama).change();
                if (!isDomisiliSama) {
                    $('#alamat_domisili').val(warga.alamat_domisili);
                    $('#rt_domisili').val(warga.rt_domisili);
                    $('#rw_domisili').val(warga.rw_domisili);
                    $('#kelurahan_domisili').val(warga.kelurahan_domisili);
                }

                $('#no_telepon').val(warga.no_telepon);
                $('#email').val(warga.email);
                $('#status_domisili').val(warga.status_domisili);
                $('#tanggal_mulai_domisili').val(warga.tanggal_mulai_domisili);

                // Keluarga data
                var hasKK = warga.kk_id !== null;
                $('#tanpa_keluarga').prop('checked', !hasKK).change();
                if (hasKK) {
                    $('#kk_id').val(warga.kk_id);
                    $('#hubungan_keluarga').val(warga.hubungan_keluarga);
                }

                // Show foto if exists
                if (warga.foto_ktp_url) {
                    $('#fotoPreview').html('<img src="' + warga.foto_ktp_url + '" class="img-thumbnail" style="max-height: 200px;">');
                }

                $('#wargaModal').modal('show');
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal memuat data warga';
            showToast(message, 'error');
        }
    });
}

function saveWarga() {
    var form = $('#wargaForm')[0];
    var formData = new FormData(form);
    var wargaId = $('#warga_id').val();
    var url = wargaId ? '/api/warga/' + wargaId : '/api/warga';
    var method = wargaId ? 'PUT' : 'POST';

    // Handle domisili sama dengan KTP
    if ($('#sama_dengan_ktp').is(':checked')) {
        formData.set('alamat_domisili', '');
        formData.set('rt_domisili', '');
        formData.set('rw_domisili', '');
        formData.set('kelurahan_domisili', '');
    }

    // Handle tanpa keluarga
    if ($('#tanpa_keluarga').is(':checked')) {
        formData.set('kk_id', '');
        formData.set('hubungan_keluarga', '');
    }

    $.ajax({
        url: url,
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            showLoading();
            $('#saveWargaBtn').prop('disabled', true);
        },
        success: function(response) {
            hideLoading();
            $('#saveWargaBtn').prop('disabled', false);

            if (response.success) {
                $('#wargaModal').modal('hide');
                showToast(response.message, 'success');
                loadWarga();
                loadStatistics();
            } else {
                if (response.errors) {
                    displayValidationErrors(response.errors);
                } else {
                    showToast(response.message, 'error');
                }
            }
        },
        error: function(xhr) {
            hideLoading();
            $('#saveWargaBtn').prop('disabled', false);

            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                displayValidationErrors(xhr.responseJSON.errors);
            } else {
                var message = xhr.responseJSON?.message || 'Gagal menyimpan data warga';
                showToast(message, 'error');
            }
        }
    });
}

function viewWarga(id) {
    showLoading();

    $.ajax({
        url: '/api/warga/' + id,
        type: 'GET',
        success: function(response) {
            hideLoading();
            if (response.success) {
                var warga = response.data;

                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-id-card mr-2"></i>Data KTP</h6>
                            <table class="table table-sm">
                                <tr><td>NIK</td><td><strong>${warga.nik_format || warga.nik}</strong></td></tr>
                                <tr><td>Nama Lengkap</td><td><strong>${warga.nama_lengkap}</strong></td></tr>
                                <tr><td>Tempat, Tanggal Lahir</td><td>${warga.tempat_lahir}, ${warga.tanggal_lahir}</td></tr>
                                <tr><td>Jenis Kelamin</td><td>${warga.jenis_kelamin_label || warga.jenis_kelamin}</td></tr>
                                <tr><td>Golongan Darah</td><td>${warga.golongan_darah || '-'}</td></tr>
                                <tr><td>Agama</td><td>${warga.agama}</td></tr>
                                <tr><td>Status Perkawinan</td><td>${warga.status_perkawinan_label || warga.status_perkawinan}</td></tr>
                                <tr><td>Pekerjaan</td><td>${warga.pekerjaan}</td></tr>
                                <tr><td>Pendidikan Terakhir</td><td>${warga.pendidikan_terakhir}</td></tr>
                                <tr><td>Kewarganegaraan</td><td>${warga.kewarganegaraan_label || warga.kewarganegaraan}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-home mr-2"></i>Data Domisili</h6>
                            <table class="table table-sm">
                                <tr><td>Alamat</td><td>${warga.alamat_domisili}</td></tr>
                                <tr><td>RT/RW</td><td>${warga.rt_domisili}/${warga.rw_domisili}</td></tr>
                                <tr><td>Kelurahan</td><td>${warga.kelurahan_domisili}</td></tr>
                                <tr><td>No. Telepon</td><td>${warga.no_telepon || '-'}</td></tr>
                                <tr><td>Email</td><td>${warga.email || '-'}</td></tr>
                                <tr><td>Status Domisili</td><td>${warga.status_domisili_label || warga.status_domisili}</td></tr>
                                <tr><td>Tanggal Mulai Domisili</td><td>${warga.tanggal_mulai_domisili || '-'}</td></tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6><i class="fas fa-users mr-2"></i>Data Keluarga</h6>
                            <table class="table table-sm">
                                <tr><td>KK</td><td>${warga.keluarga ? warga.keluarga.no_kk_format || warga.keluarga.no_kk : 'Tanpa KK'}</td></tr>
                                <tr><td>Hubungan</td><td>${warga.hubungan_keluarga || '-'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-camera mr-2"></i>Foto KTP</h6>
                            ${warga.foto_ktp_url ? `<img src="${warga.foto_ktp_url}" class="img-thumbnail" style="max-width: 200px;">` : '<p>Tidak ada foto</p>'}
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <small class="text-muted">
                                Dibuat: ${warga.created_by?.name || 'System'} - ${warga.created_at}<br>
                                Diupdate: ${warga.updated_by?.name || '-'} - ${warga.updated_at}
                            </small>
                        </div>
                    </div>
                `;

                $('#viewWargaContent').html(content);
                $('#editWargaBtn').off('click').on('click', function() {
                    $('#viewWargaModal').modal('hide');
                    editWarga(id);
                });
                $('#viewWargaModal').modal('show');
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal memuat data warga';
            showToast(message, 'error');
        }
    });
}

function confirmDelete(id, nama, nik) {
    $('#delete_warga_id').val(id);
    $('#delete_nama').text(nama);
    $('#delete_nik').text(nik);
    $('#deleteModal').modal('show');
}

function deleteWarga() {
    var id = $('#delete_warga_id').val();

    $.ajax({
        url: '/api/warga/' + id,
        type: 'DELETE',
        beforeSend: function() {
            showLoading();
        },
        success: function(response) {
            hideLoading();
            $('#deleteModal').modal('hide');

            if (response.success) {
                showToast(response.message, 'success');
                loadWarga();
                loadStatistics();
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal menghapus data warga';
            showToast(message, 'error');
        }
    });
}

function applyFilters() {
    loadWarga(1);
}

function resetFilters() {
    $('#filterForm')[0].reset();
    loadWarga(1);
}

function refreshData() {
    loadWarga();
    loadStatistics();
}

function showImportModal() {
    showToast('Fitur import akan segera tersedia', 'info');
}

function exportData() {
    showToast('Fitur export akan segera tersedia', 'info');
}

function showStatistics() {
    showToast('Fitur statistik akan segera tersedia', 'info');
}

function calculateAge(birthDate) {
    var today = new Date();
    var birthDate = new Date(birthDate);
    var age = today.getFullYear() - birthDate.getFullYear();
    var monthDiff = today.getMonth() - birthDate.getMonth();

    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    return age + ' th';
}

function displayValidationErrors(errors) {
    var errorMessages = [];
    for (var field in errors) {
        errorMessages.push(errors[field][0]);
    }
    showToast(errorMessages.join('<br>'), 'error');
}
</script>
@endpush