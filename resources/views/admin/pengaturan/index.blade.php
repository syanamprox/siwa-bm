@extends('layouts.app')

@section('title', 'Pengaturan Sistem - SIWA')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cogs mr-2"></i>
            Pengaturan Sistem
        </h1>
        <button type="button" class="btn btn-primary btn-user" data-toggle="modal" data-target="#addModal">
            <i class="fas fa-plus mr-2"></i>Tambah Pengaturan
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data Pengaturan Sistem</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="#" onclick="refreshData()">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                    </a>
                    <a class="dropdown-item" href="#" onclick="exportSettings()">
                        <i class="fas fa-download mr-2"></i>Export Settings
                    </a>
                    <a class="dropdown-item" href="#" onclick="resetToDefaults()">
                        <i class="fas fa-undo mr-2"></i>Reset to Defaults
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kunci</th>
                            <th>Nilai</th>
                            <th>Deskripsi</th>
                            <th>Tipe</th>
                            <th>Diupdate</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Settings Groups -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users mr-2"></i>Pengaturan User
                    </h6>
                </div>
                <div class="card-body">
                    <form id="userSettingsForm">
                        <div class="form-group">
                            <label for="max_login_attempts">Maksimal Percobaan Login</label>
                            <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" min="1" max="10" value="5">
                            <small class="form-text text-muted">Maksimal percobaan login sebelum akun dikunci</small>
                        </div>
                        <div class="form-group">
                            <label for="password_min_length">Minimal Panjang Password</label>
                            <input type="number" class="form-control" id="password_min_length" name="password_min_length" min="6" max="20" value="8">
                            <small class="form-text text-muted">Minimal panjang password untuk user baru</small>
                        </div>
                        <div class="form-group">
                            <label for="session_timeout">Session Timeout (menit)</label>
                            <input type="number" class="form-control" id="session_timeout" name="session_timeout" min="15" max="480" value="120">
                            <small class="form-text text-muted">Durasi session sebelum otomatis logout</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Simpan Pengaturan User
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-envelope mr-2"></i>Pengaturan Email
                    </h6>
                </div>
                <div class="card-body">
                    <form id="emailSettingsForm">
                        <div class="form-group">
                            <label for="smtp_host">SMTP Host</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" placeholder="smtp.gmail.com">
                            <small class="form-text text-muted">Server SMTP untuk pengiriman email</small>
                        </div>
                        <div class="form-group">
                            <label for="smtp_port">SMTP Port</label>
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" min="1" max="65535" value="587">
                            <small class="form-text text-muted">Port SMTP (umumnya 587 atau 465)</small>
                        </div>
                        <div class="form-group">
                            <label for="email_from">Email From</label>
                            <input type="email" class="form-control" id="email_from" name="email_from" placeholder="noreply@siwa.local">
                            <small class="form-text text-muted">Email pengirim untuk notifikasi sistem</small>
                        </div>
                        <div class="form-group">
                            <label for="email_from_name">From Name</label>
                            <input type="text" class="form-control" id="email_from_name" name="email_from_name" value="Sistem SIWA">
                            <small class="form-text text-muted">Nama pengirim email</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Simpan Pengaturan Email
                        </button>
                        <button type="button" class="btn btn-info ml-2" onclick="testEmailSettings()">
                            <i class="fas fa-paper-plane mr-2"></i>Test Email
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-2"></i>Pengaturan Aplikasi
                    </h6>
                </div>
                <div class="card-body">
                    <form id="appSettingsForm">
                        <div class="form-group">
                            <label for="app_name">Nama Aplikasi</label>
                            <input type="text" class="form-control" id="app_name" name="app_name" value="SIWA - Sistem Informasi Warga">
                            <small class="form-text text-muted">Nama aplikasi yang ditampilkan di header</small>
                        </div>
                        <div class="form-group">
                            <label for="app_version">Versi Aplikasi</label>
                            <input type="text" class="form-control" id="app_version" name="app_version" value="1.0.0" readonly>
                            <small class="form-text text-muted">Versi aplikasi saat ini</small>
                        </div>
                        <div class="form-group">
                            <label for="timezone">Timezone</label>
                            <select class="form-control" id="timezone" name="timezone">
                                <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                            </select>
                            <small class="form-text text-muted">Zona waktu aplikasi</small>
                        </div>
                        <div class="form-group">
                            <label for="date_format">Format Tanggal</label>
                            <select class="form-control" id="date_format" name="date_format">
                                <option value="d/m/Y" selected>dd/mm/yyyy (17/11/2025)</option>
                                <option value="Y-m-d">yyyy-mm-dd (2025-11-17)</option>
                                <option value="d F Y">dd Month yyyy (17 November 2025)</option>
                            </select>
                            <small class="form-text text-muted">Format tampilan tanggal</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Simpan Pengaturan Aplikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shield-alt mr-2"></i>Pengaturan Keamanan
                    </h6>
                </div>
                <div class="card-body">
                    <form id="securitySettingsForm">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="require_2fa" name="require_2fa">
                                <label class="form-check-label" for="require_2fa">
                                    Wajib 2-Factor Authentication
                                </label>
                                <small class="form-text text-muted">Memaksa user menggunakan 2FA untuk login</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="log_all_activities" name="log_all_activities" checked>
                                <label class="form-check-label" for="log_all_activities">
                                    Log Semua Aktivitas
                                </label>
                                <small class="form-text text-muted">Mencatat semua aktivitas user di sistem</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ip_whitelist" name="ip_whitelist">
                                <label class="form-check-label" for="ip_whitelist">
                                    Whitelist IP Address
                                </label>
                                <small class="form-text text-muted">Hanya IP tertentu yang bisa akses admin panel</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="allowed_ips">IP Addresses yang Diizinkan</label>
                            <textarea class="form-control" id="allowed_ips" name="allowed_ips" rows="3" placeholder="127.0.0.1&#10;192.168.1.0/24&#10;10.0.0.0/8" disabled></textarea>
                            <small class="form-text text-muted">Daftar IP yang diizinkan (satu per baris)</small>
                        </div>
                        <div class="form-group">
                            <label for="backup_frequency">Frekuensi Backup Otomatis</label>
                            <select class="form-control" id="backup_frequency" name="backup_frequency">
                                <option value="daily">Harian</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly" selected>Bulanan</option>
                                <option value="never">Tidak Ada</option>
                            </select>
                            <small class="form-text text-muted">Frekuensi backup database otomatis</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Simpan Pengaturan Keamanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus mr-2"></i>Tambah Pengaturan Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="key">Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="key" name="key" required>
                        <small class="form-text text-muted">Nama unik untuk pengaturan (contoh: app_name)</small>
                    </div>
                    <div class="form-group">
                        <label for="value">Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="value" name="value" required>
                        <small class="form-text text-muted">Nilai dari pengaturan</small>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        <small class="form-text text-muted">Penjelasan tentang fungsi pengaturan</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>Ubah Pengaturan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm">
                @csrf
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_key">Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_key" name="key" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_value">Value <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_value" name="value" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_keterangan">Keterangan</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash mr-2 text-danger"></i>Hapus Pengaturan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm">
                @csrf
                <input type="hidden" id="delete_id" name="id">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pengaturan ini?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Pengaturan yang dihapus tidak dapat dikembalikan dan dapat mempengaruhi fungsi sistem.
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
    loadSettings();

    // Add form submit
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '/admin/pengaturan',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                showLoading();
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    $('#addModal').modal('hide');
                    showToast(response.message, 'success');
                    document.getElementById('addForm').reset();
                    loadSettings();
                } else {
                    showToast(response.message, 'error');
                    if (response.errors) {
                        showValidationErrors(response.errors);
                    }
                }
            },
            error: function(xhr) {
                hideLoading();
                var message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menambah data';
                showToast(message, 'error');
            }
        });
    });

    // Edit form submit
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit_id').val();
        var formData = new FormData(this);

        $.ajax({
            url: '/admin/pengaturan/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                showLoading();
                formData.append('_method', 'PUT');
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    $('#editModal').modal('hide');
                    showToast(response.message, 'success');
                    loadSettings();
                } else {
                    showToast(response.message, 'error');
                    if (response.errors) {
                        showValidationErrors(response.errors);
                    }
                }
            },
            error: function(xhr) {
                hideLoading();
                var message = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengupdate data';
                showToast(message, 'error');
            }
        });
    });

    // Delete form submit
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('#delete_id').val();

        $.ajax({
            url: '/admin/pengaturan/' + id,
            type: 'DELETE',
            beforeSend: function() {
                showLoading();
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    showToast(response.message, 'success');
                    loadSettings();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                hideLoading();
                var message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data';
                showToast(message, 'error');
            }
        });
    });

    // Settings forms
    $('#userSettingsForm, #emailSettingsForm, #appSettingsForm, #securitySettingsForm').on('submit', function(e) {
        e.preventDefault();
        var formId = $(this).attr('id');
        var formData = $(this).serialize();

        $.ajax({
            url: '/admin/pengaturan/update-multiple',
            type: 'POST',
            data: formData + '&_token={{ csrf_token() }}',
            beforeSend: function() {
                showLoading();
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showToast(response.message, 'success');
                    loadSettings();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                hideLoading();
                var message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan pengaturan';
                showToast(message, 'error');
            }
        });
    });

    // IP whitelist toggle
    $('#ip_whitelist').change(function() {
        $('#allowed_ips').prop('disabled', !$(this).is(':checked'));
    });
});

function loadSettings() {
    $.ajax({
        url: '/admin/api/pengaturan',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderTable(response.data);
                loadGroupSettings();
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Gagal memuat data';
            showToast(message, 'error');
        }
    });
}

function loadGroupSettings() {
    // Load individual settings values
    $.ajax({
        url: '/admin/pengaturan/group',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                // User settings
                if (response.data.user) {
                    $('#max_login_attempts').val(response.data.user.max_login_attempts || 5);
                    $('#password_min_length').val(response.data.user.password_min_length || 8);
                    $('#session_timeout').val(response.data.user.session_timeout || 120);
                }

                // Email settings
                if (response.data.email) {
                    $('#smtp_host').val(response.data.email.smtp_host || '');
                    $('#smtp_port').val(response.data.email.smtp_port || 587);
                    $('#email_from').val(response.data.email.email_from || '');
                    $('#email_from_name').val(response.data.email.email_from_name || 'Sistem SIWA');
                }

                // App settings
                if (response.data.app) {
                    $('#app_name').val(response.data.app.app_name || 'SIWA - Sistem Informasi Warga');
                    $('#app_version').val(response.data.app.app_version || '1.0.0');
                    $('#timezone').val(response.data.app.timezone || 'Asia/Jakarta');
                    $('#date_format').val(response.data.app.date_format || 'd/m/Y');
                }

                // Security settings
                if (response.data.security) {
                    $('#require_2fa').prop('checked', response.data.security.require_2fa == '1');
                    $('#log_all_activities').prop('checked', response.data.security.log_all_activities == '1');
                    $('#ip_whitelist').prop('checked', response.data.security.ip_whitelist == '1');
                    $('#allowed_ips').val(response.data.security.allowed_ips || '');
                    $('#allowed_ips').prop('disabled', response.data.security.ip_whitelist != '1');
                    $('#backup_frequency').val(response.data.security.backup_frequency || 'monthly');
                }
            }
        }
    });
}

function renderTable(data) {
    var html = '';
    var no = 1;

    data.forEach(function(item) {
        var updatedAt = item.updated_at ? new Date(item.updated_at).toLocaleString('id-ID') : '-';

        html += `
            <tr>
                <td>${no++}</td>
                <td><strong>${item.key}</strong></td>
                <td>${item.value}</td>
                <td>${item.keterangan || '-'}</td>
                <td><span class="badge badge-info">${getSettingType(item.key)}</span></td>
                <td><small>${updatedAt}</small></td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-warning" onclick="editData(${item.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteData(${item.id})" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    $('#dataTable tbody').html(html);
}

function getSettingType(key) {
    if (key.startsWith('app_')) return 'Aplikasi';
    if (key.startsWith('email_')) return 'Email';
    if (key.startsWith('user_')) return 'User';
    if (key.startsWith('security_')) return 'Keamanan';
    if (key.startsWith('smtp_')) return 'Email';
    return 'Umum';
}

function editData(id) {
    $.ajax({
        url: '/admin/pengaturan/' + id + '/edit',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var setting = response.data;
                $('#edit_id').val(setting.id);
                $('#edit_key').val(setting.key);
                $('#edit_value').val(setting.value);
                $('#edit_keterangan').val(setting.keterangan || '');
                $('#editModal').modal('show');
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Gagal memuat data';
            showToast(message, 'error');
        }
    });
}

function deleteData(id) {
    $('#delete_id').val(id);
    $('#deleteModal').modal('show');
}

function refreshData() {
    loadSettings();
}

function exportSettings() {
    showToast('Fitur export akan segera tersedia', 'info');
}

function resetToDefaults() {
    if (confirm('Apakah Anda yakin ingin mereset semua pengaturan ke nilai default?')) {
        showToast('Fitur reset ke defaults akan segera tersedia', 'info');
    }
}

function testEmailSettings() {
    showToast('Fitur test email akan segera tersedia', 'info');
}

function showLoading() {
    $('#loadingOverlay').show();
    $('.loading-spinner').show();
}

function hideLoading() {
    $('#loadingOverlay').hide();
    $('.loading-spinner').hide();
}

function showValidationErrors(errors) {
    // Implementation for showing validation errors
    Object.keys(errors).forEach(function(key) {
        showToast(errors[key][0], 'error');
    });
}
</script>
@endpush