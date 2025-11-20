@extends('layouts.app')

@section('title', 'Data Keluarga - SIWA')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-id-card mr-2"></i>Data Keluarga
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-user" onclick="showCreateModal()">
                <i class="fas fa-plus mr-2"></i>Tambah Keluarga
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
                                Total KK
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalKeluarga">
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
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Anggota
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalAnggota">
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Rata-rata Anggota
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rataRataAnggota">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                                KK Tanpa Kepala
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="kkTanpaKepala">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="search" class="font-weight-bold">
                            <i class="fas fa-search mr-1"></i>Cari No. KK atau Nama Kepala Keluarga
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search"
                                   placeholder="Masukkan No. KK atau nama kepala keluarga...">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label for="rt" class="font-weight-bold">
                            <i class="fas fa-map-marker-alt mr-1"></i>RT
                        </label>
                        <input type="text" class="form-control" id="rt" name="rt"
                               placeholder="01" maxlength="3">
                    </div>
                    <div class="col-md-1">
                        <label for="rw" class="font-weight-bold">
                            <i class="fas fa-map-marker-alt mr-1"></i>RW
                        </label>
                        <input type="text" class="form-control" id="rw" name="rw"
                               placeholder="01" maxlength="3">
                    </div>
                    <div class="col-md-2">
                        <label for="kelurahan" class="font-weight-bold">
                            <i class="fas fa-map-marker-alt mr-1"></i>Kelurahan
                        </label>
                        <select class="form-control" id="kelurahan" name="kelurahan">
                            <option value="">Semua Kelurahan</option>
                            <option value="Jemur Wonosari">Jemur Wonosari</option>
                            <option value="Margorejo">Margorejo</option>
                            <option value="Sidosermo">Sidosermo</option>
                            <option value="Bringin">Bringin</option>
                            <option value="Darmo Harapan">Darmo Harapan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status_filter" class="font-weight-bold">
                            <i class="fas fa-flag mr-1"></i>Status Keluarga
                        </label>
                        <select class="form-control" id="status_filter" name="status_filter">
                            <option value="">Semua Status</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Pindah">Pindah</option>
                            <option value="Non-Aktif">Non-Aktif</option>
                            <option value="Dibubarkan">Dibubarkan</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label for="reset" class="font-weight-bold text-white">.</label>
                        <button type="button" class="btn btn-secondary w-100" onclick="resetFilters()" title="Reset Filter" style="height: 38px; font-size: 0.875rem;">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Keluarga Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>Daftar Keluarga
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
                <table class="table table-bordered" id="keluargaTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. KK</th>
                            <th>Kepala Keluarga</th>
                            <th>Alamat</th>
                            <th>RT/RW</th>
                            <th>Jumlah Anggota</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">
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

<!-- Create/Edit Keluarga Modal -->
<div class="modal fade" id="keluargaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="keluargaModalTitle">
                    <i class="fas fa-id-card mr-2"></i>Tambah Data Keluarga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="keluargaForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="keluarga_id" name="keluarga_id">
                    <input type="hidden" id="input_mode" name="input_mode" value="multi">

                    <!-- Progress Indicator -->
                    <div class="progress mb-4" style="height: 4px;">
                        <div class="progress-bar" id="progressBar" role="progressbar" style="width: 25%"></div>
                    </div>

                    <!-- Step Indicators -->
                    <div class="d-flex justify-content-between mb-4">
                        <div class="text-center" id="step1Indicator">
                            <i class="fas fa-home fa-2x text-primary"></i>
                            <div class="small mt-1">Data Keluarga</div>
                        </div>
                        <div class="text-center" id="step2Indicator">
                            <i class="fas fa-users fa-2x text-muted"></i>
                            <div class="small mt-1">Data Anggota</div>
                        </div>
                        <div class="text-center" id="step3Indicator">
                            <i class="fas fa-check fa-2x text-muted"></i>
                            <div class="small mt-1">Konfirmasi</div>
                        </div>
                    </div>

                    <!-- Step 1: Data Keluarga -->
                    <div id="step1" class="step-content">
                        <h6><i class="fas fa-home mr-2"></i>Informasi Keluarga</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_kk">No. KK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="no_kk" name="no_kk" maxlength="16" required>
                                    <small class="form-text text-muted">16 digit angka</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status_domisili">Status Domisili <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status_domisili" name="status_domisili" required>
                                        <option value="">Pilih Status Domisili</option>
                                        <option value="Tetap">Tetap</option>
                                        <option value="Non Domisili">Non Domisili</option>
                                        <option value="Luar">Luar</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="foto_kk">📷 Foto Kartu Keluarga</label>
                                    <input type="file" class="form-control-file" id="foto_kk" name="foto_kk" accept="image/*">
                                    <small class="form-text text-muted">Format: JPG, PNG. Max: 2MB. Upload foto Kartu Keluarga yang jelas dan lengkap</small>
                                    <div id="fotoKkPreview" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 1: Alamat KTP (Manual Input) -->
                        <div class="card border-left-primary mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-id-card mr-2"></i>Alamat KTP (Manual Input)</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="alamat_kk">Alamat Lengkap KK <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamat_kk" name="alamat_kk" rows="2" required placeholder="Contoh: Jl. Ketintang Baru No. 23"></textarea>
                                    <small class="form-text text-muted">Alamat lengkap sesuai Kartu Keluarga</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="rt_kk">RT KTP <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="rt_kk" name="rt_kk" maxlength="10" placeholder="01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="rw_kk">RW KTP <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="rw_kk" name="rw_kk" maxlength="10" placeholder="01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kelurahan_kk">Kelurahan KTP <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="kelurahan_kk" name="kelurahan_kk" placeholder="Bendul Merisi" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="kecamatan_kk">Kecamatan KTP <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="kecamatan_kk" name="kecamatan_kk" value="Wonocolo" required>
                                            <small class="form-text text-muted">Fixed: Wonocolo</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="kabupaten_kk">Kabupaten/Kota KTP <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="kabupaten_kk" name="kabupaten_kk" value="Surabaya" required>
                                            <small class="form-text text-muted">Fixed: Surabaya</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="provinsi_kk">Provinsi KTP <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="provinsi_kk" name="provinsi_kk" value="Jawa Timur" required>
                                            <small class="form-text text-muted">Fixed: Jawa Timur</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Alamat Domisili (Cascading Dropdown) -->
                        <div class="card border-left-success mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-home mr-2"></i>Alamat Domisili (Cascading Dropdown)</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="alamat_domisili">Alamat Domisili <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="alamat_domisili" name="alamat_domisili" rows="1" required placeholder="Contoh: Jl. Ketintang Baru No. 23"></textarea>
                                    <small class="form-text text-muted">Alamat jalan saja untuk domisili</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="domisili_kelurahan">Pilih Kelurahan Domisili <span class="text-danger">*</span></label>
                                            <select class="form-control" id="domisili_kelurahan" name="domisili_kelurahan" required>
                                                <option value="">Pilih Kelurahan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="domisili_rw">Pilih RW Domisili <span class="text-danger">*</span></label>
                                            <select class="form-control" id="domisili_rw" name="domisili_rw" required disabled>
                                                <option value="">Pilih RW</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="domisili_rt">Pilih RT Domisili <span class="text-danger">*</span></label>
                                            <select class="form-control" id="domisili_rt" name="domisili_rt" required disabled>
                                                <option value="">Pilih RT</option>
                                            </select>
                                            <div class="mt-2">
                                                <div class="alert alert-info mb-0 p-2">
                                                    <small class="font-weight-bold" id="alamat_display">
                                                        <i class="fas fa-info-circle mr-1"></i>Alamat akan tampil otomatis saat RT dipilih
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-primary" onclick="nextStep()">
                                Lanjut <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Data Anggota -->
                    <div id="step2" class="step-content" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6><i class="fas fa-users mr-2"></i>Data Anggota Keluarga</h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-info mr-2" onclick="toggleInputMode()">
                                    <i class="fas fa-exchange-alt mr-1"></i>Mode: <span id="modeText">Multi</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-success" onclick="addWargaRow()">
                                    <i class="fas fa-plus mr-1"></i>Tambah Anggota
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Panduan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Tambahkan minimal 1 anggota (Kepala Keluarga)</li>
                                <li>Isi data NIK, nama, dan informasi lengkap setiap anggota</li>
                                <li> pastikan untuk menandai salah satu anggota sebagai "Kepala Keluarga"</li>
                                <li>Gunakan <strong>Mode Multi</strong> untuk input beberapa anggota sekaligus</li>
                                <li>Gunakan <strong>Mode Single</strong> untuk input satu anggota (Kepala Keluarga) saja</li>
                            </ul>
                        </div>

                        <div id="wargaContainer">
                            <!-- Warga rows will be dynamically added here -->
                        </div>

                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-secondary mr-2" onclick="prevStep()">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">
                                Lanjut <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Konfirmasi -->
                    <div id="step3" class="step-content" style="display: none;">
                        <h6><i class="fas fa-check mr-2"></i>Konfirmasi Data</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Data Keluarga</h6>
                                    </div>
                                    <div class="card-body" id="confirmKeluargaData">
                                        <!-- Will be populated dynamically -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Data Anggota (<span id="confirmAnggotaCount">0</span>)</h6>
                                    </div>
                                    <div class="card-body" id="confirmAnggotaData">
                                        <!-- Will be populated dynamically -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Periksa kembali data Anda sebelum menyimpan.</strong> Pastikan semua data telah terisi dengan benar.
                        </div>

                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-secondary mr-2" onclick="prevStep()">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-primary" onclick="confirmSave()">
                                <i class="fas fa-save mr-1"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Keluarga Modal -->
<div class="modal fade" id="viewKeluargaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-id-card mr-2"></i>Detail Data Keluarga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewKeluargaContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="editKeluargaBtn">Edit</button>
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
                    <i class="fas fa-trash mr-2 text-danger"></i>Hapus Data Keluarga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm">
                @csrf
                <input type="hidden" id="delete_keluarga_id">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data keluarga ini?</p>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Semua anggota keluarga akan terlepas dari KK ini dan data yang dihapus tidak dapat dikembalikan.
                    </div>
                    <div class="alert alert-info">
                        <strong>No. KK:</strong> <span id="delete_no_kk"></span>
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

<!-- Foto KK Modal -->
<div class="modal fade" id="fotoKkModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-camera mr-2"></i>Foto Kartu Keluarga
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="fotoKkContent">
                    <!-- Foto will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>
                <a href="#" id="downloadFotoKk" class="btn btn-primary" target="_blank" download>
                    <i class="fas fa-download mr-2"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.step-content {
    min-height: 400px;
}
.warga-row {
    margin-bottom: 1rem;
}
.warga-row .card {
    transition: all 0.3s ease;
}
.warga-row .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.warga-row .form-group {
    margin-bottom: 0;
}
.warga-row .form-group.mb-3 {
    margin-bottom: 1rem;
}
.warga-row .card-header h6 {
    font-weight: 600;
}
.warga-row .section-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    opacity: 0.8;
}
.warga-row .invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #e74a3b;
}

/* Custom border-left utilities for cards */
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

/* Improve form field spacing */
.warga-row .form-group label {
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.warga-row .form-text {
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

/* Add visual hierarchy for sections */
.warga-row .text-primary {
    border-bottom: 2px solid #4e73df;
    padding-bottom: 0.25rem;
}

.warga-row .text-secondary {
    border-bottom: 2px solid #858796;
    padding-bottom: 0.25rem;
}

.warga-row .text-success {
    border-bottom: 2px solid #1cc88a;
    padding-bottom: 0.25rem;
}
.warga-container {
    position: relative;
}
.is-invalid {
    border-color: #e74a3b;
}
.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #e74a3b;
}

/* Foto KK Modal Styles */
.foto-container img {
    transition: transform 0.3s ease;
    cursor: zoom-in;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.foto-container img.zoomed {
    transform: scale(1.5);
    cursor: zoom-out;
}

.btn-group .btn[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endpush

@push('scripts')
<script>
let currentStep = 1;
let wargaRowCounter = 0;
let formData = null;

$(document).ready(function() {
    loadKeluarga();
    loadStatistics();
    loadFormData();

    // Form submission
    $('#keluargaForm').on('submit', function(e) {
        e.preventDefault();
        saveKeluarga();
    });

    // Delete form submission
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        deleteKeluarga();
    });

    // Per page change
    $('#perPage').on('change', function() {
        loadKeluarga();
    });

    // Auto-trigger search with debounce
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadKeluarga(1);
        }, 500); // 500ms debounce
    });

    // Auto-trigger RT filter with debounce
    let rtTimeout;
    $('#rt').on('input', function() {
        clearTimeout(rtTimeout);
        rtTimeout = setTimeout(() => {
            loadKeluarga(1);
        }, 300); // 300ms debounce untuk RT
    });

    // Auto-trigger RW filter with debounce
    let rwTimeout;
    $('#rw').on('input', function() {
        clearTimeout(rwTimeout);
        rwTimeout = setTimeout(() => {
            loadKeluarga(1);
        }, 300); // 300ms debounce untuk RW
    });

    // Auto-trigger kelurahan filter
    $('#kelurahan').on('change', function() {
        loadKeluarga(1);
    });

    // Auto-trigger status filter
    $('#status_filter').on('change', function() {
        loadKeluarga(1);
    });

    // Wilayah cascading select untuk alamat domisili
    $('#domisili_kelurahan').on('change', function() {
        loadRWDomisili($(this).val());
    });

    $('#domisili_rw').on('change', function() {
        loadRTDomisili($(this).val());
    });

    $('#domisili_rt').on('change', function() {
        updateAlamatDisplay($(this).val());
    });

    // Initialize with one warga row
    initializeForm();
});

function initializeForm() {
    // Start with one warga row in step 2
    setTimeout(function() {
        addWargaRow();
    }, 100);
}

// Handle foto KK upload
$('#foto_kk').on('change', function() {
    var file = this.files[0];
    if (file) {
        // Check file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            showToast('Ukuran file terlalu besar. Maksimal 2MB', 'error');
            $(this).val('');
            $('#fotoKkPreview').html('');
            return;
        }

        // Check file type
        if (!file.type.match('image.*')) {
            showToast('File harus berupa gambar (JPG, PNG)', 'error');
            $(this).val('');
            $('#fotoKkPreview').html('');
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            $('#fotoKkPreview').html(`
                <div class="mt-2">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 300px; max-width: 100%;" alt="Preview Foto KK">
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="fas fa-check-circle"></i> File siap diupload: ${file.name}
                        </small>
                    </div>
                </div>
            `);
        };
        reader.readAsDataURL(file);
    }
});

function loadFormData() {
    $.ajax({
        url: '/admin/api/keluarga/create',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                populateWilayahOptions(response.data);
                formData = response.data;
                // Load kelurahan untuk dropdown domisili
                loadKelurahanDomisili();
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Gagal memuat form data';
            showToast(message, 'error');
        }
    });
}

function populateWilayahOptions(data) {
    // Populate kelurahan
    var kelurahanHtml = '<option value="">Pilih Kelurahan</option>';
    data.kelurahan_list.forEach(function(kelurahan) {
        kelurahanHtml += `<option value="${kelurahan.id}">${kelurahan.nama}</option>`;
    });
    $('#kelurahan_id').html(kelurahanHtml);
}

// Functions untuk cascading dropdown domisili
function loadKelurahanDomisili() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/admin/api/keluarga/wilayah?level=kelurahan',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var html = '<option value="">Pilih Kelurahan</option>';
                    response.data.forEach(function(kelurahan) {
                        html += `<option value="${kelurahan.id}">${kelurahan.nama}</option>`;
                    });
                    $('#domisili_kelurahan').html(html);
                    resolve();
                } else {
                    reject(response.message || 'Failed to load kelurahan options');
                }
            },
            error: function() {
                reject('Failed to load kelurahan options');
            }
        });
    });
}

function loadRWDomisili(kelurahanId) {
    if (!kelurahanId) {
        $('#domisili_rw').html('<option value="">Pilih RW</option>').prop('disabled', true);
        $('#domisili_rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
        $('#alamat_display').text('');
        return;
    }

    $.ajax({
        url: '/admin/api/keluarga/wilayah?level=rw&parent_id=' + kelurahanId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var html = '<option value="">Pilih RW</option>';
                response.data.forEach(function(rw) {
                    html += `<option value="${rw.id}">${rw.kode} ${rw.nama}</option>`;
                });
                $('#domisili_rw').html(html).prop('disabled', false);
                $('#domisili_rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
            }
        },
        error: function() {
            showToast('Gagal memuat data RW', 'error');
        }
    });
}

function loadRTDomisili(rwId) {
    if (!rwId) {
        $('#domisili_rt').html('<option value="">Pilih RT</option>').prop('disabled', true);
        $('#alamat_display').text('');
        return;
    }

    $.ajax({
        url: '/admin/api/keluarga/wilayah?level=rt&parent_id=' + rwId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var html = '<option value="">Pilih RT</option>';
                response.data.forEach(function(rt) {
                    html += `<option value="${rt.id}">${rt.kode} ${rt.nama}</option>`;
                });
                $('#domisili_rt').html(html).prop('disabled', false);
            }
        },
        error: function() {
            showToast('Gagal memuat data RT', 'error');
        }
    });
}

function updateAlamatDisplay(rtId) {
    if (!rtId) {
        $('#alamat_display').text('');
        return;
    }

    $.ajax({
        url: '/admin/api/keluarga/rt-info?rt_id=' + rtId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#alamat_display').text('Alamat: ' + response.data.alamat_lengkap);
            }
        },
        error: function() {
            $('#alamat_display').text('');
        }
    });
}

// Functions untuk backward compatibility (old system)
function loadRW(kelurahanId) {
    // Legacy function - tidak digunakan lagi
}

function loadRT(rwId) {
    // Legacy function - tidak digunakan lagi
}

function nextStep() {
    if (currentStep === 1) {
        if (validateStep1()) {
            showStep(2);
        }
    } else if (currentStep === 2) {
        if (validateStep2()) {
            showConfirmation();
            showStep(3);
        }
    }
}

function prevStep() {
    if (currentStep === 2) {
        showStep(1);
    } else if (currentStep === 3) {
        showStep(2);
    }
}

function showStep(step) {
    currentStep = step;

    // Hide all steps
    $('.step-content').hide();

    // Show current step
    $('#step' + step).show();

    // Update progress bar
    var progress = (step / 3) * 100;
    $('#progressBar').css('width', progress + '%');

    // Update indicators
    $('.step-indicator i').removeClass('text-primary').addClass('text-muted');
    $('#step' + step + 'Indicator i').removeClass('text-muted').addClass('text-primary');
}

function validateStep1() {
    var isValid = true;
    // Update required fields untuk form baru
    var requiredFields = [
        'no_kk', 'status_domisili',
        // Alamat KTP fields
        'alamat_kk', 'rt_kk', 'rw_kk', 'kelurahan_kk', 'kecamatan_kk', 'kabupaten_kk', 'provinsi_kk',
        // Alamat domisili fields
        'alamat_domisili', 'domisili_kelurahan', 'domisili_rw', 'domisili_rt'
    ];

    requiredFields.forEach(function(field) {
        var element = $('#' + field);
        if (!element.val()) {
            element.addClass('is-invalid');
            isValid = false;
        } else {
            element.removeClass('is-invalid');
        }
    });

    // Validate No KK format (16 digits)
    var noKk = $('#no_kk').val();
    if (noKk && !/^\d{16}$/.test(noKk)) {
        $('#no_kk').addClass('is-invalid');
        showToast('No. KK harus 16 digit angka', 'error');
        isValid = false;
    }

    if (!isValid) {
        showToast('Mohon lengkapi semua field yang wajib diisi', 'error');
    }

    return isValid;
}

function validateStep2() {
    var wargaRows = $('.warga-row');
    var hasKepalaKeluarga = false;
    var isValid = true;

    if (wargaRows.length === 0) {
        showToast('Tambahkan minimal 1 anggota keluarga', 'error');
        return false;
    }

    wargaRows.each(function() {
        var row = $(this);
        var nik = row.find('[name$="[nik]"]').val();
        var nama = row.find('[name$="[nama_lengkap]"]').val();
        var hubungan = row.find('[name$="[hubungan_keluarga]"]').val();
        var rowIndex = row.data('index');

        // Validate NIK
        if (!nik || !/^\d{16}$/.test(nik)) {
            row.find('[name$="[nik]"]').addClass('is-invalid');
            isValid = false;
        } else {
            row.find('[name$="[nik]"]').removeClass('is-invalid');
        }

        // Validate nama
        if (!nama) {
            row.find('[name$="[nama_lengkap]"]').addClass('is-invalid');
            isValid = false;
        } else {
            row.find('[name$="[nama_lengkap]"]').removeClass('is-invalid');
        }

        // Check for Kepala Keluarga
        if (hubungan === 'Kepala Keluarga') {
            hasKepalaKeluarga = true;
        }
    });

    if (!hasKepalaKeluarga) {
        showToast('Harus ada minimal satu Kepala Keluarga', 'error');
        isValid = false;
    }

    if (!isValid) {
        showToast('Mohon periksa kembali data anggota keluarga', 'error');
    }

    return isValid;
}

function toggleInputMode() {
    var currentMode = $('#input_mode').val();
    var newMode = currentMode === 'single' ? 'multi' : 'single';
    $('#input_mode').val(newMode);
    $('#modeText').text(newMode === 'single' ? 'Single' : 'Multi');

    if (newMode === 'single') {
        // Keep only first row
        $('.warga-row:not(:first)').remove();
        $('.remove-warga').hide();
    } else {
        $('.remove-warga').show();
    }
}

function addWargaRow() {
    wargaRowCounter++;
    var index = new Date().getTime(); // Unique index

    var wargaRow = `
        <div class="warga-row" data-index="${index}">
            <div class="card border-left-info shadow-sm mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-user mr-2"></i>Anggota #${wargaRowCounter}
                    </h6>
                    <div class="remove-warga">
                        <button type="button" class="btn btn-sm btn-outline-light" onclick="removeWargaRow(this)">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Section 1: Data Identitas Utama -->
                    <div class="mb-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-id-badge mr-2"></i>Data Identitas Utama</h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="nik_${index}">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nik_${index}" name="warga_data[${index}][nik]"
                                           maxlength="16" placeholder="16 digit NIK" required>
                                    <small class="form-text text-muted">Nomor Induk Kependudukan 16 digit</small>
                                    <div class="invalid-feedback">NIK harus 16 digit</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="nama_${index}">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_${index}" name="warga_data[${index}][nama_lengkap]"
                                           placeholder="Nama lengkap sesuai KTP" required>
                                    <small class="form-text text-muted">Nama sesuai dokumen resmi</small>
                                    <div class="invalid-feedback">Nama wajib diisi</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="jenis_kelamin_${index}">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenis_kelamin_${index}" name="warga_data[${index}][jenis_kelamin]" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="hubungan_${index}">Hubungan Keluarga <span class="text-danger">*</span></label>
                                    <select class="form-control hubungan-select" id="hubungan_${index}" name="warga_data[${index}][hubungan_keluarga]" required>
                                        <option value="">Pilih Hubungan Keluarga</option>
                                        <option value="Kepala Keluarga">👨‍👩‍👧‍👦 Kepala Keluarga</option>
                                        <option value="Suami">👨 Suami</option>
                                        <option value="Istri">👩 Istri</option>
                                        <option value="Anak">👶 Anak</option>
                                        <option value="Menantu">👨‍👩‍👧 Menantu</option>
                                        <option value="Cucu">👼 Cucu</option>
                                        <option value="Orang Tua">👴👵 Orang Tua</option>
                                        <option value="Mertua">👴👵 Mertua</option>
                                        <option value="Famili Lain">👨‍👩‍👧‍👦 Famili Lain</option>
                                        <option value="Pembantu">🧑‍🍳 Pembantu</option>
                                        <option value="Lainnya">📋 Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Kelahiran dan Agama -->
                    <div class="mb-4">
                        <h6 class="text-secondary mb-3"><i class="fas fa-calendar-alt mr-2"></i>Data Kelahiran dan Agama</h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="tempat_lahir_${index}">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir_${index}" name="warga_data[${index}][tempat_lahir]"
                                           placeholder="Kota/Kabupaten">
                                    <small class="form-text text-muted">Tempat kelahiran</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="tanggal_lahir_${index}">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tanggal_lahir_${index}" name="warga_data[${index}][tanggal_lahir]">
                                    <small class="form-text text-muted">Format: DD/MM/YYYY</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="agama_${index}">Agama</label>
                                    <select class="form-control" id="agama_${index}" name="warga_data[${index}][agama]">
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam">☪️ Islam</option>
                                        <option value="Kristen">✝️ Kristen</option>
                                        <option value="Katolik">✝️ Katolik</option>
                                        <option value="Hindu">🕉️ Hindu</option>
                                        <option value="Buddha">☸️ Buddha</option>
                                        <option value="Konghucu">☯️ Konghucu</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Data Pekerjaan dan Sosial -->
                    <div class="mb-4">
                        <h6 class="text-success mb-3"><i class="fas fa-briefcase mr-2"></i>Data Pekerjaan dan Sosial</h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="pendidikan_${index}">Pendidikan Terakhir</label>
                                    <select class="form-control" id="pendidikan_${index}" name="warga_data[${index}][pendidikan]">
                                        <option value="">Pilih Pendidikan</option>
                                        <option value="Tidak Sekolah">📝 Tidak Sekolah</option>
                                        <option value="SD/sederajat">📖 SD/sederajat</option>
                                        <option value="SMP/sederajat">📚 SMP/sederajat</option>
                                        <option value="SMA/sederajat">📚 SMA/sederajat</option>
                                        <option value="D1">🎓 D1</option>
                                        <option value="D2">🎓 D2</option>
                                        <option value="D3">🎓 D3</option>
                                        <option value="D4/S1">🎓 D4/S1</option>
                                        <option value="S2">🎓 S2</option>
                                        <option value="S3">🎓 S3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="pekerjaan_${index}">Pekerjaan</label>
                                    <select class="form-control" id="pekerjaan_${index}" name="warga_data[${index}][pekerjaan]">
                                        <option value="">Pilih Pekerjaan</option>
                                        <option value="Belum/Tidak Bekerja">🔄 Belum/Tidak Bekerja</option>
                                        <option value="Mengurus Rumah Tangga">🏠 Mengurus Rumah Tangga</option>
                                        <option value="Pelajar/Mahasiswa">🎒 Pelajar/Mahasiswa</option>
                                        <option value="Pensiunan">👴 Pensiunan</option>
                                        <option value="Pegawai Negeri Sipil">👮 Pegawai Negeri Sipil</option>
                                        <option value="TNI/Polisi">🚔 TNI/Polisi</option>
                                        <option value="Guru/Dosen">👨‍🏫 Guru/Dosen</option>
                                        <option value="Pegawai Swasta">💼 Pegawai Swasta</option>
                                        <option value="Wiraswasta">🏪 Wiraswasta</option>
                                        <option value="Petani/Pekebun">🌾 Petani/Pekebun</option>
                                        <option value="Peternak">🐄 Peternak</option>
                                        <option value="Nelayan/Perikanan">🎣 Nelayan/Perikanan</option>
                                        <option value="Industri">🏭 Industri</option>
                                        <option value="Konstruksi">🏗️ Konstruksi</option>
                                        <option value="Transportasi">🚛 Transportasi</option>
                                        <option value="Karyawan Honorer">👷 Karyawan Honorer</option>
                                        <option value="Tenaga Kesehatan">👨‍⚕️ Tenaga Kesehatan</option>
                                        <option value="Lainnya">📋 Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="status_kawin_${index}">Status Perkawinan</label>
                                    <select class="form-control" id="status_kawin_${index}" name="warga_data[${index}][status_kawin]">
                                        <option value="">Pilih Status</option>
                                        <option value="Belum Kawin">💔 Belum Kawin</option>
                                        <option value="Kawin">💑 Kawin</option>
                                        <option value="Cerai Hidup">💔 Cerai Hidup</option>
                                        <option value="Cerai Mati">⚰️ Cerai Mati</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="golongan_darah_${index}">Golongan Darah</label>
                                    <select class="form-control" id="golongan_darah_${index}" name="warga_data[${index}][golongan_darah]">
                                        <option value="">Pilih Golongan Darah</option>
                                        <option value="A">🔴 A</option>
                                        <option value="B">🔵 B</option>
                                        <option value="AB">🟣 AB</option>
                                        <option value="O">⚪ O</option>
                                        <option value="A+">🔴 A+</option>
                                        <option value="B+">🔵 B+</option>
                                        <option value="AB+">🟣 AB+</option>
                                        <option value="O+">⚪ O+</option>
                                        <option value="Tidak Tahu">❓ Tidak Tahu</option>
                                    </select>
                                </div>
                            </div>
                    </div>

                    <!-- Section 4: Data Orang Tua -->
                    <div class="mb-4">
                        <h6 class="text-warning mb-3"><i class="fas fa-users mr-2"></i>Data Orang Tua</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nama_ayah_${index}">Nama Ayah</label>
                                    <input type="text" class="form-control" id="nama_ayah_${index}" name="warga_data[${index}][nama_ayah]"
                                           placeholder="Nama ayah kandung">
                                    <small class="form-text text-muted">Nama ayah kandung</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="nama_ibu_${index}">Nama Ibu</label>
                                    <input type="text" class="form-control" id="nama_ibu_${index}" name="warga_data[${index}][nama_ibu]"
                                           placeholder="Nama ibu kandung">
                                    <small class="form-text text-muted">Nama ibu kandung</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Data Kontak -->
                    <div class="mb-4">
                        <h6 class="text-info mb-3"><i class="fas fa-phone mr-2"></i>Data Kontak</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="no_hp_${index}">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="no_hp_${index}" name="warga_data[${index}][no_hp]"
                                           placeholder="08xx-xxxx-xxxx">
                                    <small class="form-text text-muted">Nomor HP aktif</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#wargaContainer').append(wargaRow);
    updateRemoveButtons();
}

function removeWargaRow(button) {
    $(button).closest('.warga-row').remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    var rows = $('.warga-row').length;
    $('.remove-warga button').toggle(rows > 1);
}

function showConfirmation() {
    // Collect alamat KTP data
    var keluargaHtml = `
        <table class="table table-sm">
            <tr><td>No. KK</td><td><strong>${$('#no_kk').val()}</strong></td></tr>
            <tr><td>Alamat KTP</td><td>${$('#alamat_kk').val()}</td></tr>
            <tr><td>Wilayah KTP</td><td>RT ${$('#rt_kk').val()}/RW ${$('#rw_kk').val()}, ${$('#kelurahan_kk').val()}, ${$('#kecamatan_kk').val()}, ${$('#kabupaten_kk').val()}, ${$('#provinsi_kk').val()}</td></tr>
            <tr><td>Alamat Domisili</td><td>${$('#alamat_domisili').val()}</td></tr>
            <tr><td>Wilayah Domisili</td><td>${$('#domisili_rt option:selected').text()}</td></tr>
            <tr><td>Status Domisili</td><td>${$('#status_domisili').val()}</td></tr>
        </table>
    `;
    $('#confirmKeluargaData').html(keluargaHtml);

    // Collect warga data
    var wargaHtml = '';
    var wargaCount = 0;

    $('.warga-row').each(function() {
        var row = $(this);
        var nama = row.find('[name$="[nama_lengkap]"]').val();
        var nik = row.find('[name$="[nik]"]').val();
        var hubungan = row.find('[name$="[hubungan_keluarga]"]').val();
        var jenisKelamin = row.find('[name$="[jenis_kelamin]"] option:selected').text();

        if (nama && nik) {
            wargaCount++;
            wargaHtml += `
                <div class="mb-2">
                    <strong>${nama}</strong> (${jenisKelamin})<br>
                    <small class="text-muted">NIK: ${nik} | Hubungan: ${hubungan}</small>
                </div>
            `;
        }
    });

    $('#confirmAnggotaCount').text(wargaCount);
    $('#confirmAnggotaData').html(wargaHtml || '<p class="text-muted">Belum ada data anggota</p>');
}

function confirmSave() {
    // Submit the form
    saveKeluarga();
}

function showCreateModal() {
    $('#keluargaModalTitle').html('<i class="fas fa-id-card mr-2"></i>Tambah Data Keluarga');
    $('#keluargaForm')[0].reset();
    $('#keluarga_id').val('');

    // Reset to step 1
    currentStep = 1;
    wargaRowCounter = 0;
    showStep(1);

    // Clear warga container
    $('#wargaContainer').html('');

    // Clear foto KK preview
    $('#fotoKkPreview').html('');

    // Add initial warga row
    setTimeout(function() {
        addWargaRow();
    }, 100);

    $('#keluargaModal').modal('show');
}

function saveKeluarga() {
    // Debug: Log form values before submission
    const noKk = $('#no_kk').val();
    const statusDomisili = $('#status_domisili').val();
    const alamatKk = $('#alamat_kk').val();
    const rtId = $('#domisili_rt').val();

    
    // Map field names untuk controller
    var form = $('#keluargaForm')[0];
    var formData = new FormData();

    // Basic data
    formData.append('no_kk', $('#no_kk').val().trim());
    formData.append('status_domisili_keluarga', $('#status_domisili').val().trim());

    // Foto KK (jika ada)
    var fotoKkFile = $('#foto_kk')[0].files[0];
    if (fotoKkFile) {
        formData.append('foto_kk', fotoKkFile);
    }

    // Alamat KTP (manual input)
    formData.append('alamat_kk', $('#alamat_kk').val().trim());
    formData.append('rt_kk', $('#rt_kk').val());
    formData.append('rw_kk', $('#rw_kk').val());
    formData.append('kelurahan_kk', $('#kelurahan_kk').val());
    formData.append('kecamatan_kk', $('#kecamatan_kk').val());
    formData.append('kabupaten_kk', $('#kabupaten_kk').val());
    formData.append('provinsi_kk', $('#provinsi_kk').val());

    // Alamat domisili (cascading dropdown)
    formData.append('alamat_domisili', $('#alamat_domisili').val());
    formData.append('rt_id', $('#domisili_rt').val()); // Map ke rt_id untuk controller

    // Input mode dan warga data
    formData.append('input_mode', $('#input_mode').val());

    // Add warga data
    $('.warga-row').each(function() {
        var row = $(this);
        var index = row.data('index');

        formData.append(`warga_data[${index}][nik]`, row.find('[name$="[nik]"]').val());
        formData.append(`warga_data[${index}][nama_lengkap]`, row.find('[name$="[nama_lengkap]"]').val());
        formData.append(`warga_data[${index}][jenis_kelamin]`, row.find('[name$="[jenis_kelamin]"]').val());
        formData.append(`warga_data[${index}][tempat_lahir]`, row.find('[name$="[tempat_lahir]"]').val());
        formData.append(`warga_data[${index}][tanggal_lahir]`, row.find('[name$="[tanggal_lahir]"]').val());
        formData.append(`warga_data[${index}][agama]`, row.find('[name$="[agama]"]').val());
        formData.append(`warga_data[${index}][pendidikan_terakhir]`, row.find('[name$="[pendidikan]"]').val());
        formData.append(`warga_data[${index}][pekerjaan]`, row.find('[name$="[pekerjaan]"]').val());
        formData.append(`warga_data[${index}][status_perkawinan]`, row.find('[name$="[status_kawin]"]').val());
        formData.append(`warga_data[${index}][golongan_darah]`, row.find('[name$="[golongan_darah]"]').val());
        formData.append(`warga_data[${index}][hubungan_keluarga]`, row.find('[name$="[hubungan_keluarga]"]').val());
        formData.append(`warga_data[${index}][no_telepon]`, row.find('[name$="[no_hp]"]').val());
        formData.append(`warga_data[${index}][nama_ayah]`, row.find('[name$="[nama_ayah]"]').val());
        formData.append(`warga_data[${index}][nama_ibu]`, row.find('[name$="[nama_ibu]"]').val());
    });

    var keluargaId = $('#keluarga_id').val();
    var url = keluargaId ? '/admin/api/keluarga/' + keluargaId : '/admin/api/keluarga';

    // Always use POST with _method for Laravel
    if (keluargaId) {
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            showLoading();
            $('#saveKeluargaBtn').prop('disabled', true);
        },
        success: function(response) {
            hideLoading();
            $('#saveKeluargaBtn').prop('disabled', false);

            if (response.success) {
                $('#keluargaModal').modal('hide');
                showToast(response.message, 'success');
                loadKeluarga();
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
            $('#saveKeluargaBtn').prop('disabled', false);

            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                displayValidationErrors(xhr.responseJSON.errors);
            } else {
                var message = xhr.responseJSON?.message || 'Gagal menyimpan data keluarga';
                showToast(message, 'error');
            }
        }
    });
}

function loadKeluarga(page = 1) {
    showLoading();

    var formData = $('#filterForm').serializeArray();
    var params = {};

    $.each(formData, function(i, field) {
        params[field.name] = field.value;
    });

    params.page = page;
    params.per_page = $('#perPage').val();

    $.ajax({
        url: '/admin/api/keluarga',
        type: 'GET',
        data: params,
        success: function(response) {
            hideLoading();
            if (response.success) {
                renderKeluargaTable(response.data, response.pagination);
                renderPagination(response.pagination);
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal memuat data keluarga';
            showToast(message, 'error');
        }
    });
}

function renderKeluargaTable(keluargaList, pagination) {
    var html = '';
    var no = (pagination.current_page - 1) * pagination.per_page;

    if (keluargaList.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted">Tidak ada data keluarga</td></tr>';
    } else {
        keluargaList.forEach(function(keluarga) {
            no++;
            var namaKepala = keluarga.kepala_keluarga?.nama_lengkap || 'Belum ditentukan';
            var jumlahAnggota = keluarga.anggota_keluarga?.length || 0;

            html += `
                <tr>
                    <td>${no}</td>
                    <td><code>${keluarga.no_kk}</code></td>
                    <td><strong>${namaKepala}</strong></td>
                    <td><small>${keluarga.alamat_kk}</small></td>
                    <td>${keluarga.rt_kk}/${keluarga.rw_kk}</td>
                    <td><span class="badge badge-info">${jumlahAnggota} orang</span></td>
                    <td><span class="badge badge-${keluarga.status_badge_class || 'secondary'}">${keluarga.status_label || 'Tidak Diketahui'}</span></td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary" onclick="editKeluarga(${keluarga.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-info" onclick="viewKeluarga(${keluarga.id})" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${keluarga.foto_kk && keluarga.foto_kk_url && keluarga.foto_kk_url !== 'undefined' ? `
                                <button type="button" class="btn btn-sm btn-success" onclick="viewFotoKk('${keluarga.foto_kk_url}', 'KK ${keluarga.no_kk}')" title="Lihat Foto KK">
                                    <i class="fas fa-camera"></i>
                                </button>
                            ` : `
                                <button type="button" class="btn btn-sm btn-secondary" disabled title="Belum ada Foto KK">
                                    <i class="fas fa-camera"></i>
                                </button>
                            `}
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" title="Ubah Status">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" onclick="updateStatus(${keluarga.id}, 'Aktif')">
                                        <i class="fas fa-check-circle text-success"></i> Aktif
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="updateStatus(${keluarga.id}, 'Pindah')">
                                        <i class="fas fa-arrow-right text-warning"></i> Pindah
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="updateStatus(${keluarga.id}, 'Non-Aktif')">
                                        <i class="fas fa-pause text-secondary"></i> Non-Aktif
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="updateStatus(${keluarga.id}, 'Dibubarkan')">
                                        <i class="fas fa-times-circle text-danger"></i> Dibubarkan
                                    </a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(${keluarga.id}, '${keluarga.no_kk}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    $('#keluargaTable tbody').html(html);
}

function renderPagination(pagination) {
    var html = '';

    // Previous button
    if (pagination.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadKeluarga(${pagination.current_page - 1})">Previous</a></li>`;
    }

    // Page numbers
    for (var i = 1; i <= pagination.last_page; i++) {
        var active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadKeluarga(${i})">${i}</a></li>`;
    }

    // Next button
    if (pagination.current_page < pagination.last_page) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadKeluarga(${pagination.current_page + 1})">Next</a></li>`;
    }

    $('#pagination').html(html);
}

function loadStatistics() {
    $.ajax({
        url: '/admin/api/keluarga/statistics',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#totalKeluarga').text(response.data.total_keluarga);
                $('#totalAnggota').text(response.data.total_anggota);
                $('#rataRataAnggota').text(response.data.rata_rata_anggota);
                $('#kkTanpaKepala').text(response.data.kk_tanpa_kepala);
            }
        },
        error: function(xhr) {
            // Statistics loading failed silently
        }
    });
}

function viewKeluarga(id) {
    showLoading();

    $.ajax({
        url: '/admin/api/keluarga/' + id,
        type: 'GET',
        success: function(response) {
            hideLoading();
            if (response.success) {
                var keluarga = response.data;

                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-id-card mr-2"></i>Data KK</h6>
                            <table class="table table-sm">
                                <tr><td>No. KK</td><td><strong>${keluarga.no_kk}</strong></td></tr>
                                <tr><td>Kepala Keluarga</td><td><strong>${keluarga.kepala_keluarga?.nama_lengkap || 'Belum ditentukan'}</strong></td></tr>
                                <tr><td>Alamat</td><td>${keluarga.alamat_kk}</td></tr>
                                <tr><td>RT/RW</td><td>${keluarga.rt_kk}/${keluarga.rw_kk}</td></tr>
                                <tr><td>Kelurahan</td><td>${keluarga.kelurahan_kk}</td></tr>
                                <tr><td>Status Domisili</td><td>${keluarga.status_domisili_keluarga || '-'}</td></tr>
                                <tr><td>Jumlah Anggota</td><td>${keluarga.anggota_keluarga?.length || 0} orang</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-users mr-2"></i>Daftar Anggota</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Hubungan</th>
                                            <th>NIK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;

                if (keluarga.anggota_keluarga && keluarga.anggota_keluarga.length > 0) {
                    keluarga.anggota_keluarga.forEach(function(anggota) {
                        content += `
                            <tr>
                                <td>${anggota.nama_lengkap}</td>
                                <td>${anggota.hubungan_keluarga || '-'}</td>
                                <td><code>${anggota.nik}</code></td>
                            </tr>
                        `;
                    });
                } else {
                    content += '<tr><td colspan="3" class="text-center text-muted">Belum ada anggota</td></tr>';
                }

                content += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <small class="text-muted">
                                Dibuat: ${keluarga.created_at} | Diupdate: ${keluarga.updated_at}
                            </small>
                        </div>
                    </div>
                `;

                $('#viewKeluargaContent').html(content);
                $('#editKeluargaBtn').off('click').on('click', function() {
                    $('#viewKeluargaModal').modal('hide');
                    editKeluarga(id);
                });
                $('#viewKeluargaModal').modal('show');
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal memuat data keluarga';
            showToast(message, 'error');
        }
    });
}

function confirmDelete(id, noKk) {
    $('#delete_keluarga_id').val(id);
    $('#delete_no_kk').text(noKk);
    $('#deleteModal').modal('show');
}

function deleteKeluarga() {
    var id = $('#delete_keluarga_id').val();

    $.ajax({
        url: '/admin/api/keluarga/' + id,
        type: 'DELETE',
        beforeSend: function() {
            showLoading();
        },
        success: function(response) {
            hideLoading();
            $('#deleteModal').modal('hide');

            if (response.success) {
                showToast(response.message, 'success');
                loadKeluarga();
                loadStatistics();
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal menghapus data keluarga';
            showToast(message, 'error');
        }
    });
}

function updateStatus(id, status) {
    // Langsung update status tanpa konfirmasi dialog
    $.ajax({
        url: '/admin/api/keluarga/' + id + '/status',
        type: 'PATCH',
        data: {
            status_keluarga: status,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            showLoading();
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showToast(response.message, 'success');
                // Reload table untuk update tampilan
                loadKeluarga();
                loadStatistics();
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal mengupdate status keluarga';
            var errors = xhr.responseJSON?.errors || {};

            // Tampilkan validation errors jika ada
            if (Object.keys(errors).length > 0) {
                var errorMessages = Object.values(errors).flat().join('\n');
                showToast(errorMessages, 'error');
            } else {
                showToast(message, 'error');
            }
        }
    });
}

function editKeluarga(id) {
    showLoading();

    $.ajax({
        url: '/admin/api/keluarga/' + id,
        type: 'GET',
        success: function(response) {
            hideLoading();
            if (response.success) {
                populateEditForm(response.data);
                $('#keluargaModalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Data Keluarga');
                $('#keluargaModal').modal('show');
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            hideLoading();
            var message = xhr.responseJSON?.message || 'Gagal memuat data keluarga';
            showToast(message, 'error');
        }
    });
}

function populateEditForm(keluarga) {
    // Reset form dan set mode edit
    $('#keluargaForm')[0].reset();
    $('#keluarga_id').val(keluarga.id);

    // Basic data
    $('#no_kk').val(keluarga.no_kk);
    $('#status_domisili').val(keluarga.status_domisili_keluarga);

    // Alamat KTP
    $('#alamat_kk').val(keluarga.alamat_kk);
    $('#rt_kk').val(keluarga.rt_kk);
    $('#rw_kk').val(keluarga.rw_kk);
    $('#kelurahan_kk').val(keluarga.kelurahan_kk);
    $('#kecamatan_kk').val(keluarga.kecamatan_kk);
    $('#kabupaten_kk').val(keluarga.kabupaten_kk);
    $('#provinsi_kk').val(keluarga.provinsi_kk);

    // Alamat Domisili
    $('#alamat_domisili').val(keluarga.alamat_domisili);

    // Load RT berdasarkan rt_id (hanya untuk mode edit)
    if (keluarga.rt_id) {
        // Add small delay to ensure DOM is ready
        setTimeout(() => {
            loadEditDomisiliData(keluarga.rt_id);
        }, 200);
    }

    // Load anggota data
    loadEditAnggotaData(keluarga.anggota_keluarga || []);

    // Set mode edit
    $('#input_mode').val('edit');

    // Show step 1
    currentStep = 1;
    showStep(1);
}

function loadEditDomisiliData(rtId) {

    // Reset dan enable dropdowns
    $('#domisili_kelurahan').prop('disabled', false);
    $('#domisili_rw').prop('disabled', true);
    $('#domisili_rt').prop('disabled', true);

    // Clear current values
    $('#domisili_kelurahan').val('');
    $('#domisili_rw').val('');
    $('#domisili_rt').val('');

    // Get RT info first to know the hierarchy
    $.ajax({
        url: '/admin/api/keluarga/rt-info?rt_id=' + rtId,
        type: 'GET',
        success: function(response) {

            if (response.success && response.data.rt) {
                const rt = response.data.rt;
                const rw = response.data.rw;
                const kelurahan = response.data.kelurahan;

                // Load kelurahan options first
                loadKelurahanDomisili().then(() => {

                    if (kelurahan) {
                        // Select kelurahan
                        $('#domisili_kelurahan').val(kelurahan.id);

                        // Trigger change event and load RW options
                        setTimeout(() => {
                            loadRwOptionsForEdit(kelurahan.id).then(() => {

                                if (rw) {
                                    // Select RW
                                    $('#domisili_rw').val(rw.id);
                                    $('#domisili_rw').prop('disabled', false);

                                    // Load RT options for this RW
                                    setTimeout(() => {
                                        loadRtOptionsForEdit(rw.id).then(() => {

                                            // Select RT
                                            $('#domisili_rt').val(rt.id);
                                            $('#domisili_rt').prop('disabled', false);
                                        }).catch(error => {
                                            showToast('Gagal memuat data RT', 'error');
                                        });
                                    }, 100);
                                }
                            }).catch(error => {
                                showToast('Gagal memuat data RW', 'error');
                            });
                        }, 100);
                    } else {
                        $('#domisili_rw').prop('disabled', false);
                        $('#domisili_rt').prop('disabled', false);
                    }
                }).catch(error => {
                    showToast('Gagal memuat data kelurahan', 'error');
                });
            } else {
                // Fallback: load all options
                loadKelurahanDomisili();
                $('#domisili_rw').prop('disabled', false);
                $('#domisili_rt').prop('disabled', false);
            }
        },
        error: function(xhr) {
            // Fallback: load all options
            loadKelurahanDomisili();
            $('#domisili_rw').prop('disabled', false);
            $('#domisili_rt').prop('disabled', false);
        }
    });
}

function loadRwOptionsForEdit(kelurahanId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/admin/api/keluarga/wilayah?level=rw&parent_id=' + kelurahanId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const rwSelect = $('#domisili_rw');
                    rwSelect.empty();
                    rwSelect.append('<option value="">Pilih RW</option>');

                    response.data.forEach(rw => {
                        rwSelect.append(`<option value="${rw.id}">${rw.nama}</option>`);
                    });
                    resolve();
                } else {
                    reject(response.message || 'Failed to load RW options');
                }
            },
            error: function() {
                reject('Failed to load RW options');
            }
        });
    });
}

function loadRtOptionsForEdit(rwId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/admin/api/keluarga/wilayah?level=rt&parent_id=' + rwId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const rtSelect = $('#domisili_rt');
                    rtSelect.empty();
                    rtSelect.append('<option value="">Pilih RT</option>');

                    response.data.forEach(rt => {
                        rtSelect.append(`<option value="${rt.id}">${rt.nama}</option>`);
                    });
                    resolve();
                } else {
                    reject(response.message || 'Failed to load RT options');
                }
            },
            error: function() {
                reject('Failed to load RT options');
            }
        });
    });
}

function loadEditAnggotaData(anggotaList) {
    $('#wargaContainer').empty();
    wargaRowCounter = 0;

    if (anggotaList && anggotaList.length > 0) {
        anggotaList.forEach((anggota, index) => {
            setTimeout(() => {
                addWargaRow();

                // Populate data anggota
                const currentRow = $('.warga-row').last();
                const rowIndex = currentRow.data('index');

                currentRow.find('[name$="[nik]"]').val(anggota.nik);
                currentRow.find('[name$="[nama_lengkap]"]').val(anggota.nama_lengkap);
                currentRow.find('[name$="[jenis_kelamin]"]').val(anggota.jenis_kelamin);
                currentRow.find('[name$="[hubungan_keluarga]"]').val(anggota.hubungan_keluarga);
                currentRow.find('[name$="[tempat_lahir]"]').val(anggota.tempat_lahir);
                // Format tanggal untuk HTML date input (YYYY-MM-DD)
                const tanggalLahir = anggota.tanggal_lahir ?
                    new Date(anggota.tanggal_lahir).toISOString().split('T')[0] : '';
                currentRow.find('[name$="[tanggal_lahir]"]').val(tanggalLahir);
                currentRow.find('[name$="[agama]"]').val(anggota.agama);
                currentRow.find('[name$="[pendidikan]"]').val(anggota.pendidikan_terakhir);
                currentRow.find('[name$="[pekerjaan]"]').val(anggota.pekerjaan);
                currentRow.find('[name$="[status_kawin]"]').val(anggota.status_perkawinan);
                currentRow.find('[name$="[golongan_darah]"]').val(anggota.golongan_darah);
                currentRow.find('[name$="[nama_ayah]"]').val(anggota.nama_ayah);
                currentRow.find('[name$="[nama_ibu]"]').val(anggota.nama_ibu);
                currentRow.find('[name$="[no_hp]"]').val(anggota.no_telepon);
            }, index * 100);
        });
    } else {
        // Add empty row jika tidak ada anggota
        setTimeout(() => {
            addWargaRow();
        }, 100);
    }
}


function resetFilters() {
    $('#filterForm')[0].reset();
    loadKeluarga(1);
}

function refreshData() {
    loadKeluarga();
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

function displayValidationErrors(errors) {
    var errorMessages = [];
    for (var field in errors) {
        errorMessages.push(errors[field][0]);
    }
    showToast(errorMessages.join('<br>'), 'error');
}

function viewFotoKk(fotoUrl, kkTitle) {

    if (!fotoUrl || fotoUrl === 'undefined' || fotoUrl === 'null') {
        showToast('Foto KK tidak tersedia', 'warning');
        return;
    }

    // Set modal title
    $('#fotoKkModal .modal-title').html(`<i class="fas fa-camera mr-2"></i>${kkTitle}`);

    // Set image content with better error handling
    $('#fotoKkContent').html(`
        <div class="foto-container">
            <img id="fotoKkImage"
                 src="${fotoUrl}"
                 class="img-fluid"
                 alt="Foto Kartu Keluarga - ${kkTitle}"
                 style="max-width: 100%; max-height: 70vh; object-fit: contain;">
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Klik gambar untuk zoom, scroll untuk navigasi
                </small>
            </div>
        </div>
    `);

    // Handle image load error
    $('#fotoKkImage').on('error', function() {
        $(this).attr('src', '/images/no-image.svg');
        $(this).attr('alt', 'Gambar tidak dapat dimuat');
        showToast('Gambar KK tidak dapat dimuat, menampilkan placeholder', 'warning');
    });

    // Set download link only if valid URL
    if (fotoUrl.startsWith('http')) {
        $('#downloadFotoKk').attr('href', fotoUrl).show();
    } else {
        $('#downloadFotoKk').hide();
    }
    $('#downloadFotoKk').attr('download', `Foto_KK_${kkTitle.replace(/[^a-zA-Z0-9]/g, '_')}.jpg`);

    // Show modal
    $('#fotoKkModal').modal('show');

    // Add click handler for image zoom
    $('#fotoKkContent img').on('click', function() {
        // Create zoom effect
        $(this).toggleClass('zoomed');
        if ($(this).hasClass('zoomed')) {
            $(this).css({
                'transform': 'scale(1.5)',
                'cursor': 'zoom-out'
            });
        } else {
            $(this).css({
                'transform': 'scale(1)',
                'cursor': 'zoom-in'
            });
        }
    });
}

</script>
@endpush