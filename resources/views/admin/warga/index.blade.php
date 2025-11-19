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
            <button type="button" class="btn btn-warning btn-user" onclick="redirectToKeluarga()">
                <i class="fas fa-home mr-2"></i>Tambah Warga (via Keluarga)
            </button>
            <button type="button" class="btn btn-info btn-user" onclick="showImportModal()">
                <i class="fas fa-file-import mr-2"></i>Import
            </button>
            <button type="button" class="btn btn-success btn-user" onclick="exportData()">
                <i class="fas fa-file-export mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Info Card -->
    <div class="alert alert-info mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle mr-3" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Workflow Management Warga:</strong>
                <div class="mt-1">
                    <small>📝 <strong>Tambah Warga Baru:</strong> Gunakan menu <strong>Data Keluarga</strong> → "Tambah Keluarga" → input data KK + tambah anggota keluarga</small><br>
                    <small>👥 <strong>Existing Warga:</strong> Edit, hapus, atau lihat detail warga langsung di menu ini</small>
                </div>
            </div>
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
                <i class="fas fa-search mr-2"></i>Pencarian dan Filter
            </h6>
        </div>
        <div class="card-body">
            <form id="filterForm">
                <div class="row align-items-end">
                    <!-- Search Input -->
                    <div class="col-md-6">
                        <label for="search">🔍 Cari Warga</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="NIK, Nama, No. KK, atau Alamat (auto-cari)">
                    </div>
                    <!-- Jenis Kelamin -->
                    <div class="col-md-6">
                        <label for="jenis_kelamin">⚧️ Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="">Semua</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
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
                    <i class="fas fa-user-edit mr-2"></i>Edit Data Warga
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
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Data Pribadi</h6>
                        </div>
                        <div class="card-body py-3">
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
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="jenis_kelamin">⚧️ Jenis Kelamin <span class="text-danger">*</span></label>
                                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="">Pilih</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="tempat_lahir">📍 Tempat Lahir <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="tanggal_lahir">📅 Tanggal Lahir <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label for="golongan_darah">🩸 Golongan Darah</label>
                                        <select class="form-control" id="golongan_darah" name="golongan_darah">
                                            <option value="">Pilih</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="AB">AB</option>
                                            <option value="O">O</option>
                                            <option value="A+">A+</option>
                                            <option value="B+">B+</option>
                                            <option value="AB+">AB+</option>
                                            <option value="O+">O+</option>
                                            <option value="A-">A-</option>
                                            <option value="B-">B-</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O-">O-</option>
                                            <option value="Tidak Tahu">Tidak Tahu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Data Orang Tua -->
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-users mr-2"></i>Data Orang Tua</h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nama_ayah">👨 Nama Ayah</label>
                                        <input type="text" class="form-control" id="nama_ayah" name="nama_ayah" placeholder="Nama ayah kandung">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="nama_ibu">👩 Nama Ibu</label>
                                        <input type="text" class="form-control" id="nama_ibu" name="nama_ibu" placeholder="Nama ibu kandung">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Data Kontak -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-phone mr-2"></i>Data Kontak</h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="no_telepon">📱 No. Telepon</label>
                                        <input type="text" class="form-control" id="no_telepon" name="no_telepon" placeholder="Nomor telepon aktif">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="email">📧 Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Alamat email">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Data Lainnya -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Data Lainnya</h6>
                        </div>
                        <div class="card-body py-4">
                            <!-- Row 1: Data Demografi -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="agama">⛪ Agama <span class="text-danger">*</span></label>
                                        <select class="form-control" id="agama" name="agama" required>
                                            <option value="">Pilih Agama</option>
                                            <option value="Islam">Islam</option>
                                            <option value="Kristen">Kristen</option>
                                            <option value="Katolik">Katolik</option>
                                            <option value="Hindu">Hindu</option>
                                            <option value="Buddha">Buddha</option>
                                            <option value="Konghucu">Konghucu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="status_perkawinan">💑 Status Perkawinan <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status_perkawinan" name="status_perkawinan" required>
                                            <option value="">Pilih Status</option>
                                            <option value="Belum Kawin">Belum Kawin</option>
                                            <option value="Kawin">Kawin</option>
                                            <option value="Cerai Hidup">Cerai Hidup</option>
                                            <option value="Cerai Mati">Cerai Mati</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="kewarganegaraan">🇮🇩 Kewarganegaraan <span class="text-danger">*</span></label>
                                        <select class="form-control" id="kewarganegaraan" name="kewarganegaraan" required>
                                            <option value="">Pilih</option>
                                            <option value="WNI">WNI</option>
                                            <option value="WNA">WNA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Data Pendidikan & Pekerjaan -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="pekerjaan">💼 Pekerjaan <span class="text-danger">*</span></label>
                                        <select class="form-control" id="pekerjaan" name="pekerjaan" required>
                                            <option value="">Pilih Pekerjaan</option>
                                            <option value="Belum/Tidak Bekerja">Belum/Tidak Bekerja</option>
                                            <option value="Mengurus Rumah Tangga">Mengurus Rumah Tangga</option>
                                            <option value="Pelajar/Mahasiswa">Pelajar/Mahasiswa</option>
                                            <option value="Pensiunan">Pensiunan</option>
                                            <option value="Pegawai Negeri Sipil">Pegawai Negeri Sipil</option>
                                            <option value="TNI/Polisi">TNI/Polisi</option>
                                            <option value="Guru/Dosen">Guru/Dosen</option>
                                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                                            <option value="Wiraswasta">Wiraswasta</option>
                                            <option value="Petani/Pekebun">Petani/Pekebun</option>
                                            <option value="Peternak">Peternak</option>
                                            <option value="Nelayan/Perikanan">Nelayan/Perikanan</option>
                                            <option value="Industri">Industri</option>
                                            <option value="Konstruksi">Konstruksi</option>
                                            <option value="Transportasi">Transportasi</option>
                                            <option value="Tenaga Kesehatan">Tenaga Kesehatan</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="pendidikan_terakhir">🎓 Pendidikan Terakhir <span class="text-danger">*</span></label>
                                        <select class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" required>
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
                            </div>

                            <!-- Row 3: Hubungan Keluarga -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="hubungan_keluarga">👥 Hubungan Keluarga <span class="text-danger">*</span></label>
                                        <select class="form-control" id="hubungan_keluarga" name="hubungan_keluarga" required>
                                            <option value="">Pilih Hubungan</option>
                                            <option value="Kepala Keluarga">Kepala Keluarga</option>
                                            <option value="Suami">Suami</option>
                                            <option value="Istri">Istri</option>
                                            <option value="Anak">Anak</option>
                                            <option value="Menantu">Menantu</option>
                                            <option value="Cucu">Cucu</option>
                                            <option value="Orang Tua">Orang Tua</option>
                                            <option value="Mertua">Mertua</option>
                                            <option value="Famili Lain">Famili Lain</option>
                                            <option value="Pembantu">Pembantu</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- Empty for balance -->
                                </div>
                            </div>

                            <!-- Row 4: Foto KTP -->
                            <div class="row">
                                <div class="col-md-12">
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

@push('styles')
<style>
/* Search input enhancements */
#search:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

#search.is-valid {
    border-color: #1cc88a;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%231cc88a' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    padding-right: calc(1.5em + 0.75rem);
}

#search.is-valid:focus {
    border-color: #1cc88a;
    box-shadow: 0 0 0 0.2rem rgba(28, 200, 138, 0.25);
}

/* Filter dropdown improvements */
select.form-control {
    transition: all 0.15s ease-in-out;
}

select.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

/* Button group improvements */
.btn-group .btn {
    transition: all 0.15s ease-in-out;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
}

/* Loading indicator for table */
.table-loading {
    opacity: 0.6;
    position: relative;
}

.table-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #4e73df;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadWarga();
    loadStatistics();
    loadFormData();

    
    $('#tanpa_keluarga').change(function() {
        if ($(this).is(':checked')) {
            // Centang: Buat KK baru, hide KK selection
            $('#keluargaFields').hide();
            $('#kk_id').val('');
            $('#hubungan_keluarga').val('');
        } else {
            // Tidak centang: Pilih KK yang ada
            $('#keluargaFields').show();
        }
    });

    // Search auto-trigger with debouncing
    let searchTimeout;
    $('#search').on('input keyup', function() {
        var searchValue = $(this).val();

        // Clear existing timeout
        clearTimeout(searchTimeout);

        // Add searching indicator
        if (searchValue.length > 0) {
            $(this).addClass('is-valid');
        } else {
            $(this).removeClass('is-valid');
        }

        // Set new timeout (500ms delay)
        searchTimeout = setTimeout(function() {
            applyFilters();
        }, 500);
    });

    // Filter form submission on Enter key
    $('#search').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            clearTimeout(searchTimeout); // Clear pending timeout
            applyFilters();
        }
    });

    // Filter changes on dropdown change
    $('#jenis_kelamin').on('change', function() {
        applyFilters();
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

            // Add foto_ktp_url for button access
            warga.foto_ktp_url = warga.foto_ktp ? '/storage/' + warga.foto_ktp : null;

            html += `
                <tr>
                    <td>${no}</td>
                    <td><code>${warga.nik_format || warga.nik}</code></td>
                    <td><strong>${warga.nama_lengkap}</strong></td>
                    <td><span class="badge ${warga.jenis_kelamin === 'L' ? 'badge-info' : 'badge-warning'}">${warga.jenis_kelamin_label || warga.jenis_kelamin}</span></td>
                    <td>${umur}</td>
                    <td><small>${warga.keluarga ? warga.keluarga.alamat_kk || 'Tidak ada alamat KK' : 'Tidak ada KK'}</small></td>
                    <td>${warga.no_telepon || '-'}</td>
                    <td><small>${kkInfo}</small></td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-info" onclick="viewWarga(${warga.id})" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info ${warga.foto_ktp ? '' : 'disabled'}" onclick="${warga.foto_ktp ? `showFotoModal('${warga.foto_ktp_url || ('/storage/' + warga.foto_ktp)}', '${warga.nama_lengkap}')` : 'return false;'}" title="${warga.foto_ktp ? 'Lihat Foto KTP' : 'Tidak ada Foto KTP'}">
                                <i class="fas fa-camera"></i>
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

                // Fill form data - only fields that exist in warga table - target elements within wargaModal
                $('#wargaModal #warga_id').val(warga.id);
                $('#wargaModal #nik').val(warga.nik);
                $('#wargaModal #nama_lengkap').val(warga.nama_lengkap);
                $('#wargaModal #tempat_lahir').val(warga.tempat_lahir);

                // Debug critical fields
                console.log('=== Warga Data Debug ===');
                console.log('ID:', warga.id);
                console.log('Nama:', warga.nama_lengkap);
                console.log('Tanggal lahir data:', warga.tanggal_lahir);
                console.log('Tanggal lahir type:', typeof warga.tanggal_lahir);
                console.log('Pendidikan terakhir data:', warga.pendidikan_terakhir);
                console.log('Jenis kelamin data:', warga.jenis_kelamin);
                console.log('=========================');

                // Format tanggal untuk input type="date" (YYYY-MM-DD)
                if (warga.tanggal_lahir) {
                    var tanggal = new Date(warga.tanggal_lahir);
                    var formattedDate = tanggal.getFullYear() + '-' +
                                      String(tanggal.getMonth() + 1).padStart(2, '0') + '-' +
                                      String(tanggal.getDate()).padStart(2, '0');
                    console.log('Formatted date:', formattedDate);
                    $('#wargaModal #tanggal_lahir').val(formattedDate);
                } else {
                    $('#wargaModal #tanggal_lahir').val('');
                }

                $('#wargaModal #golongan_darah').val(warga.golongan_darah);

                // Populate dropdowns dynamically if empty
                if ($('#wargaModal #jenis_kelamin option').length <= 1) {
                    $('#wargaModal #jenis_kelamin').html('<option value="">Pilih</option><option value="L">Laki-laki</option><option value="P">Perempuan</option>');
                }
                $('#wargaModal #jenis_kelamin').val(warga.jenis_kelamin);

                if ($('#wargaModal #agama option').length <= 1) {
                    $('#wargaModal #agama').html('<option value="">Pilih Agama</option><option value="Islam">Islam</option><option value="Kristen">Kristen</option><option value="Katolik">Katolik</option><option value="Hindu">Hindu</option><option value="Buddha">Buddha</option><option value="Konghucu">Konghucu</option>');
                }
                $('#wargaModal #agama').val(warga.agama);

                if ($('#wargaModal #status_perkawinan option').length <= 1) {
                    $('#wargaModal #status_perkawinan').html('<option value="">Pilih Status</option><option value="Belum Kawin">Belum Kawin</option><option value="Kawin">Kawin</option><option value="Cerai Hidup">Cerai Hidup</option><option value="Cerai Mati">Cerai Mati</option>');
                }
                $('#wargaModal #status_perkawinan').val(warga.status_perkawinan);

                if ($('#wargaModal #pekerjaan option').length <= 1) {
                    $('#wargaModal #pekerjaan').html('<option value="">Pilih Pekerjaan</option><option value="Belum/Tidak Bekerja">Belum/Tidak Bekerja</option><option value="Mengurus Rumah Tangga">Mengurus Rumah Tangga</option><option value="Pelajar/Mahasiswa">Pelajar/Mahasiswa</option><option value="Pensiunan">Pensiunan</option><option value="Pegawai Negeri Sipil">Pegawai Negeri Sipil</option><option value="TNI/Polisi">TNI/Polisi</option><option value="Guru/Dosen">Guru/Dosen</option><option value="Pegawai Swasta">Pegawai Swasta</option><option value="Wiraswasta">Wiraswasta</option><option value="Petani/Pekebun">Petani/Pekebun</option><option value="Peternak">Peternak</option><option value="Nelayan/Perikanan">Nelayan/Perikanan</option><option value="Industri">Industri</option><option value="Konstruksi">Konstruksi</option><option value="Transportasi">Transportasi</option><option value="Tenaga Kesehatan">Tenaga Kesehatan</option><option value="Lainnya">Lainnya</option>');
                }
                $('#wargaModal #pekerjaan').val(warga.pekerjaan);

                // Debug pendidikan_terakhir
                console.log('Pendidikan terakhir data:', warga.pendidikan_terakhir);
                console.log('Pendidikan options length:', $('#wargaModal #pendidikan_terakhir option').length);

                // Always update pendidikan options to match keluarga form
                var pendidikanOptions = '<option value="">Pilih Pendidikan</option>' +
                                     '<option value="Tidak Sekolah">📝 Tidak Sekolah</option>' +
                                     '<option value="SD/sederajat">📖 SD/sederajat</option>' +
                                     '<option value="SMP/sederajat">📚 SMP/sederajat</option>' +
                                     '<option value="SMA/sederajat">📚 SMA/sederajat</option>' +
                                     '<option value="D1">🎓 D1</option>' +
                                     '<option value="D2">🎓 D2</option>' +
                                     '<option value="D3">🎓 D3</option>' +
                                     '<option value="D4/S1">🎓 D4/S1</option>' +
                                     '<option value="S2">🎓 S2</option>' +
                                     '<option value="S3">🎓 S3</option>';
                $('#wargaModal #pendidikan_terakhir').html(pendidikanOptions);

                console.log('Available options:', $('#wargaModal #pendidikan_terakhir').html());
                $('#wargaModal #pendidikan_terakhir').val(warga.pendidikan_terakhir);
                console.log('After setting value:', $('#wargaModal #pendidikan_terakhir').val());

                if ($('#wargaModal #kewarganegaraan option').length <= 1) {
                    $('#wargaModal #kewarganegaraan').html('<option value="">Pilih</option><option value="WNI">WNI</option><option value="WNA">WNA</option>');
                }
                $('#wargaModal #kewarganegaraan').val(warga.kewarganegaraan);

                if ($('#wargaModal #hubungan_keluarga option').length <= 1) {
                    $('#wargaModal #hubungan_keluarga').html('<option value="">Pilih Hubungan</option><option value="Kepala Keluarga">Kepala Keluarga</option><option value="Suami">Suami</option><option value="Istri">Istri</option><option value="Anak">Anak</option><option value="Menantu">Menantu</option><option value="Cucu">Cucu</option><option value="Orang Tua">Orang Tua</option><option value="Mertua">Mertua</option><option value="Famili Lain">Famili Lain</option><option value="Pembantu">Pembantu</option><option value="Lainnya">Lainnya</option>');
                }
                $('#wargaModal #hubungan_keluarga').val(warga.hubungan_keluarga || '');

                // Orang Tua data
                $('#wargaModal #nama_ayah').val(warga.nama_ayah || '');
                $('#wargaModal #nama_ibu').val(warga.nama_ibu || '');

                // Kontak data
                $('#wargaModal #no_telepon').val(warga.no_telepon || '');
                $('#wargaModal #email').val(warga.email || '');

                // Show foto if exists
                if (warga.foto_ktp_url) {
                    $('#wargaModal #fotoPreview').html('<img src="' + warga.foto_ktp_url + '" class="img-thumbnail" style="max-height: 200px;">');
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

    // Always update (no create functionality)
    var url = '/api/warga/' + wargaId;
    formData.append('_method', 'PUT');

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
        type: 'POST',
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

                // Append foto_ktp_url accessor
                warga.foto_ktp_url = warga.foto_ktp ? '/storage/' + warga.foto_ktp : null;

                // Debug foto KTP
                console.log('=== Warga Debug ===');
                console.log('Warga object:', warga);
                console.log('foto_ktp_url:', warga.foto_ktp_url);
                console.log('foto_ktp:', warga.foto_ktp);
                console.log('===================');

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
                                <tr><td>No. Telepon</td><td>${warga.no_telepon || '-'}</td></tr>
                                <tr><td>Email</td><td>${warga.email || '-'}</td></tr>
                              </table>
                        </div>
                        <div class="col-md-6">
                            <!-- Alamat KK -->
                            <h6><i class="fas fa-id-card mr-2 text-primary"></i>Alamat KK</h6>
                            <table class="table table-sm mb-4">
                                <tr><td width="120">Alamat</td><td>${warga.keluarga ? warga.keluarga.alamat_kk || '-' : '-'}</td></tr>
                                <tr><td>RT</td><td>${warga.keluarga ? warga.keluarga.rt_kk || '-' : '-'}</td></tr>
                                <tr><td>RW</td><td>${warga.keluarga ? warga.keluarga.rw_kk || '-' : '-'}</td></tr>
                                <tr><td>Kelurahan</td><td>${warga.keluarga ? warga.keluarga.kelurahan_kk || '-' : '-'}</td></tr>
                                <tr><td>Kecamatan</td><td>${warga.keluarga ? warga.keluarga.kecamatan_kk || '-' : '-'}</td></tr>
                                <tr><td>Kabupaten</td><td>${warga.keluarga ? warga.keluarga.kabupaten_kk || '-' : '-'}</td></tr>
                                <tr><td>Provinsi</td><td>${warga.keluarga ? warga.keluarga.provinsi_kk || '-' : '-'}</td></tr>
                            </table>

                            <!-- Alamat Domisili -->
                            <h6><i class="fas fa-map-marker-alt mr-2 text-warning"></i>Alamat Domisili</h6>
                            <table class="table table-sm">
                                <tr><td width="120">Alamat</td><td>${warga.keluarga ? warga.keluarga.alamat_domisili || '-' : '-'}</td></tr>
                                <tr><td>RT</td><td>${warga.keluarga && warga.keluarga.wilayah ? warga.keluarga.wilayah.nama || '-' : '-'}</td></tr>
                                <tr><td>RW</td><td>${warga.keluarga && warga.keluarga.wilayah && warga.keluarga.wilayah.parent ? warga.keluarga.wilayah.parent.nama || '-' : '-'}</td></tr>
                                <tr><td>Kelurahan</td><td>${warga.keluarga && warga.keluarga.wilayah && warga.keluarga.wilayah.parent && warga.keluarga.wilayah.parent.parent ? warga.keluarga.wilayah.parent.parent.nama || '-' : '-'}</td></tr>
                                <tr><td>Status</td><td>${warga.keluarga ? warga.keluarga.status_domisili_keluarga || '-' : '-'}</td></tr>
                                <tr><td>Tanggal Mulai</td><td>${warga.keluarga && warga.keluarga.tanggal_mulai_domisili_keluarga ? formatTanggalIndo(warga.keluarga.tanggal_mulai_domisili_keluarga) : '-'}</td></tr>
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


function refreshData() {
    loadWarga();
    loadStatistics();
}

function redirectToKeluarga() {
    // Direct redirect to keluarga module
    window.location.href = '/keluarga';
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

function formatTanggalIndo(tanggal) {
    if (!tanggal) return '-';

    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    const date = new Date(tanggal);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();

    return day + ' ' + month + ' ' + year;
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

// Handle same as KTP checkbox with auto-match
$('#sama_dengan_ktp').change(function() {
    var isChecked = $(this).is(':checked');

    if (isChecked) {
        // Auto-match alamat KTP ke sistem wilayah
        autoMatchAlamatKtpToWilayah();
    } else {
        // Show manual dropdown selection
        $('#alamatDomisiliFields').show();
        loadKelurahanDomisili().then(function() {
            $('#domisili_kelurahan').prop('disabled', false);
        });
    }
});

// Function untuk auto-match alamat KTP ke sistem wilayah
function autoMatchAlamatKtpToWilayah() {
    // Check if address fields exist (warga form doesn't have these fields)
    if (!$('#rt_kk').length || !$('#rw_kk').length || !$('#kelurahan_kk').length) {
        showToast('⚠️ Fitur auto-match hanya tersedia di form keluarga', 'info');
        return;
    }

    var rtKtp = $('#rt_kk').val();
    var rwKtp = $('#rw_kk').val();
    var kelurahanKtp = $('#kelurahan_kk').val();

    // Add null checks
    if (!rtKtp || !rtKtp.trim || !rwKtp || !rwKtp.trim || !kelurahanKtp || !kelurahanKtp.trim) {
        showToast('⚠️ Data alamat tidak valid', 'warning');
        return;
    }

    rtKtp = rtKtp.trim();
    rwKtp = rwKtp.trim();
    kelurahanKtp = kelurahanKtp.trim();

    // Hide domisili fields saat matching
    $('#alamatDomisiliFields').hide();

    if (!rtKtp || !rwKtp || !kelurahanKtp) {
        showToast('⚠️ Lengkapi data alamat KTP terlebih dahulu untuk auto-match', 'warning');
        $('#rt_id').val('');
        return;
    }

    // Show loading
    showToast('🔍 Mencocokkan alamat KTP dengan sistem wilayah...', 'info');

    // Cari di tabel wilayahs
    $.ajax({
        url: '/api/keluarga/wilayah',
        type: 'GET',
        data: {
            level: 'rt',
            rt_name: rtKtp,
            rw_name: rwKtp,
            kelurahan_name: kelurahanKtp
        },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                // Found match
                var rtData = response.data[0];
                $('#rt_id').val(rtData.id);
                showToast('✅ Alamat KTP cocok dengan sistem wilayah: ' + rtData.nama_lengkap || rtData.nama, 'success');
            } else {
                // No match found
                $('#rt_id').val('');
                showToast('⚠️ Alamat KTP tidak ditemukan di sistem wilayah. Silakan pilih manual atau cek penulisan data.', 'warning');

                // Show dropdown for manual selection
                $('#alamatDomisiliFields').show();
                loadKelurahanDomisili().then(function() {
                    $('#domisili_kelurahan').prop('disabled', false);
                });
            }
        },
        error: function(xhr) {
            $('#rt_id').val('');
            showToast('❌ Gagal mencocokkan alamat dengan sistem wilayah', 'error');

            // Show dropdown for manual selection
            $('#alamatDomisiliFields').show();
            loadKelurahanDomisili().then(function() {
                $('#domisili_kelurahan').prop('disabled', false);
            });
        }
    });
}

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

// Foto KTP Modal
function showFotoModal(fotoUrl, namaWarga) {
    var modalHtml = `
        <div class="modal fade" id="fotoKtpModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-camera mr-2"></i>Foto KTP - ${namaWarga}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${fotoUrl}" class="img-fluid" alt="Foto KTP" style="max-height: 500px;">
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary" onclick="downloadFoto('${fotoUrl}', '${namaWarga}')" title="Download Foto">
                            <i class="fas fa-download mr-2"></i>Download
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if any
    $('#fotoKtpModal').remove();

    // Add modal to body and show
    $('body').append(modalHtml);
    $('#fotoKtpModal').modal('show');
}

// Download foto function
function downloadFoto(url, nama) {
    const link = document.createElement('a');
    link.href = url;
    link.download = `KTP_${nama.replace(/\s+/g, '_')}.jpg`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

</script>
@endpush