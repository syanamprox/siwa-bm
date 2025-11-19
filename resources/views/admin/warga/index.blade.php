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
                    <input type="hidden" id="warga_id" name="warga_id">

                    <!-- Section 1: Data Pribadi -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Data Pribadi</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nik">NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nik" name="nik" maxlength="16" required>
                                        <small class="form-text text-muted">16 digit angka</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="L">👨 Laki-laki</option>
                                            <option value="P">👩 Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="tempat_lahir">Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="agama">Agama <span class="text-danger">*</span></label>
                                        <select class="form-control" id="agama" name="agama" required>
                                            <option value="">Pilih Agama</option>
                                            <option value="Islam">🕌 Islam</option>
                                            <option value="Kristen">✝️ Kristen</option>
                                            <option value="Katolik">⛪ Katolik</option>
                                            <option value="Hindu">🕉️ Hindu</option>
                                            <option value="Buddha">☸️ Buddha</option>
                                            <option value="Konghucu">☯️ Konghucu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="pendidikan">Pendidikan Terakhir</label>
                                        <select class="form-control" id="pendidikan" name="pendidikan_terakhir">
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
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="pekerjaan">Pekerjaan</label>
                                        <select class="form-control" id="pekerjaan" name="pekerjaan">
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
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="status_kawin">Status Perkawinan</label>
                                        <select class="form-control" id="status_kawin" name="status_perkawinan">
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
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="golongan_darah">Golongan Darah</label>
                                        <select class="form-control" id="golongan_darah" name="golongan_darah">
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
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="kewarganegaraan">Kewarganegaraan</label>
                                        <select class="form-control" id="kewarganegaraan" name="kewarganegaraan">
                                            <option value="WNI">🇮🇩 WNI</option>
                                            <option value="WNA">🌍 WNA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="foto_ktp">Foto KTP</label>
                                        <input type="file" class="form-control-file" id="foto_ktp" name="foto_ktp" accept="image/*">
                                        <small class="form-text text-muted">Format: JPG, PNG. Max: 2MB</small>
                                        <div id="fotoPreview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Orang Tua -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-users mr-2"></i>Data Orang Tua</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nama_ayah">Nama Ayah</label>
                                        <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" placeholder="Nama ayah kandung">
                                        <small class="form-text text-muted">Nama ayah kandung</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nama_ibu">Nama Ibu</label>
                                        <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" placeholder="Nama ibu kandung">
                                        <small class="form-text text-muted">Nama ibu kandung</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Data Alamat KTP -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-id-card mr-2"></i>Data Alamat KTP</h6>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="warga_id" name="warga_id">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nik">🆔 NIK <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nik" name="nik" maxlength="16" required>
                                        <small class="form-text text-muted">16 digit angka</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nama_lengkap">👤 Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="tempat_lahir">📍 Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="tanggal_lahir">📅 Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="jenis_kelamin">⚧️ Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="golongan_darah">🩸 Golongan Darah</label>
                                        <select class="form-control" id="golongan_darah" name="golongan_darah">
                                            <option value="">Pilih</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                            <option value="Tidak Tahu">Tidak Tahu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="agama">🙏 Agama <span class="text-danger">*</span></label>
                                        <select class="form-control" id="agama" name="agama" required>
                                            <option value="">Pilih</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="alamat_ktp">🏠 Alamat KTP <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat_ktp" name="alamat_ktp" rows="2" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="rt_ktp">🏘️ RT <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="rt_ktp" name="rt_ktp" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="rw_ktp">🏘️ RW <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="rw_ktp" name="rw_ktp" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="kelurahan_ktp">🏛️ Kelurahan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kelurahan_ktp" name="kelurahan_ktp" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="kecamatan_ktp">🏛️ Kecamatan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kecamatan_ktp" name="kecamatan_ktp" value="Wonocolo" required>
                                        <small class="form-text text-muted">Default: Wonocolo</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="kabupaten_ktp">🏙️ Kabupaten <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="kabupaten_ktp" name="kabupaten_ktp" value="Surabaya" required>
                                        <small class="form-text text-muted">Default: Surabaya</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="provinsi_ktp">🗺️ Provinsi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="provinsi_ktp" name="provinsi_ktp" value="Jawa Timur" required>
                                        <small class="form-text text-muted">Default: Jawa Timur</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="status_perkawinan">💑 Status Perkawinan <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status_perkawinan" name="status_perkawinan" required>
                                            <option value="">Pilih</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="pekerjaan">💼 Pekerjaan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="pendidikan_terakhir">🎓 Pendidikan Terakhir <span class="text-danger">*</span></label>
                                        <select class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" required>
                                            <option value="">Pilih</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="kewarganegaraan">🇮🇩 Kewarganegaraan <span class="text-danger">*</span></label>
                                        <select class="form-control" id="kewarganegaraan" name="kewarganegaraan" required>
                                            <option value="">Pilih</option>
                                            <option value="WNI">WNI</option>
                                            <option value="WNA">WNA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="foto_ktp">📷 Foto KTP</label>
                                        <input type="file" class="form-control-file" id="foto_ktp" name="foto_ktp" accept="image/*">
                                        <small class="form-text text-muted">Format: JPG, PNG. Max: 2MB</small>
                                        <div id="fotoPreview" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Data Kontak -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-phone mr-2"></i>Data Kontak</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="telepon">📱 Telepon <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="telepon" name="telepon" required>
                                        <small class="form-text text-muted">Nomor telepon aktif</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email">📧 Email</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                        <small class="form-text text-muted">Opsional</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Data Domisili -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-home mr-2"></i>Data Domisili</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="sama_dengan_ktp" name="sama_dengan_ktp" checked>
                                            <label class="form-check-label" for="sama_dengan_ktp">
                                                ✅ Alamat domisili sama dengan alamat KTP
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="alamatDomisiliFields" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="alamat_domisili">🏠 Alamat Domisili</label>
                                    <textarea class="form-control" id="alamat_domisili" name="alamat_domisili" rows="2"></textarea>
                                </div>

                                <!-- Cascading Dropdown for Domisili -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="domisili_kelurahan">🏛️ Kelurahan Domisili</label>
                                            <select class="form-control" id="domisili_kelurahan" name="domisili_kelurahan" disabled>
                                                <option value="">Pilih Kelurahan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="domisili_rw">🏘️ RW Domisili</label>
                                            <select class="form-control" id="domisili_rw" name="domisili_rw" disabled>
                                                <option value="">Pilih RW</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="domisili_rt">🏘️ RT Domisili</label>
                                            <select class="form-control" id="domisili_rt" name="domisili_rt" disabled>
                                                <option value="">Pilih RT</option>
                                            </select>
                                            <input type="hidden" id="rt_id" name="rt_id">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 6: Data Keluarga -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-users mr-2"></i>Data Keluarga</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="tanpa_keluarga" name="tanpa_keluarga" checked>
                                            <label class="form-check-label" for="tanpa_keluarga">
                                                🚫 Tidak memiliki KK/bersendirian
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="keluargaFields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="kk_id">🆔 Nomor KK</label>
                                            <select class="form-control" id="kk_id" name="kk_id">
                                                <option value="">Pilih KK</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="hubungan_keluarga">👨‍👩‍👧‍👦 Hubungan dalam Keluarga</label>
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

// Cascading Dropdown Functions for Domisili
function loadKelurahanDomisili() {
    return $.ajax({
        url: '/api/keluarga/wilayah?level=kelurahan',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var html = '<option value="">Pilih Kelurahan</option>';
                response.data.forEach(function(kelurahan) {
                    html += '<option value="' + kelurahan.id + '">' + kelurahan.nama + '</option>';
                });
                $('#domisili_kelurahan').html(html);
            }
        },
        error: function(xhr) {
            console.error('Failed to load kelurahan:', xhr);
            showToast('Gagal memuat data kelurahan', 'error');
        }
    });
}

function loadRwOptionsForEdit(kelurahanId) {
    return $.ajax({
        url: '/api/keluarga/wilayah?level=rw&parent_id=' + kelurahanId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var html = '<option value="">Pilih RW</option>';
                response.data.forEach(function(rw) {
                    html += '<option value="' + rw.id + '">' + rw.nama + '</option>';
                });
                $('#domisili_rw').html(html);
            }
        },
        error: function(xhr) {
            console.error('Failed to load RW:', xhr);
            showToast('Gagal memuat data RW', 'error');
        }
    });
}

function loadRtOptionsForEdit(rwId) {
    return $.ajax({
        url: '/api/keluarga/wilayah?level=rt&parent_id=' + rwId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var html = '<option value="">Pilih RT</option>';
                response.data.forEach(function(rt) {
                    html += '<option value="' + rt.id + '">' + rt.nama + '</option>';
                });
                $('#domisili_rt').html(html);
            }
        },
        error: function(xhr) {
            console.error('Failed to load RT:', xhr);
            showToast('Gagal memuat data RT', 'error');
        }
    });
}

// Handle same as KTP checkbox
$('#sama_dengan_ktp').change(function() {
    var isChecked = $(this).is(':checked');
    if (isChecked) {
        $('#alamatDomisiliFields').hide();
        $('#domisili_kelurahan').prop('disabled', true);
        $('#domisili_rw').prop('disabled', true);
        $('#domisili_rt').prop('disabled', true);
        $('#rt_id').val('');
    } else {
        $('#alamatDomisiliFields').show();
        loadKelurahanDomisili().then(function() {
            $('#domisili_kelurahan').prop('disabled', false);
        });
    }
});

// Handle kelurahan change
$('#domisili_kelurahan').change(function() {
    var kelurahanId = $(this).val();
    if (kelurahanId) {
        $('#domisili_rw').prop('disabled', false);
        loadRwOptionsForEdit(kelurahanId);
    } else {
        $('#domisili_rw').prop('disabled', true);
        $('#domisili_rt').prop('disabled', true);
        $('#domisili_rw').html('<option value="">Pilih RW</option>');
        $('#domisili_rt').html('<option value="">Pilih RT</option>');
        $('#rt_id').val('');
    }
});

// Handle RW change
$('#domisili_rw').change(function() {
    var rwId = $(this).val();
    if (rwId) {
        $('#domisili_rt').prop('disabled', false);
        loadRtOptionsForEdit(rwId);
    } else {
        $('#domisili_rt').prop('disabled', true);
        $('#domisili_rt').html('<option value="">Pilih RT</option>');
        $('#rt_id').val('');
    }
});

// Handle RT change
$('#domisili_rt').change(function() {
    var rtId = $(this).val();
    if (rtId) {
        $('#rt_id').val(rtId);
    } else {
        $('#rt_id').val('');
    }
});
</script>
@endpush