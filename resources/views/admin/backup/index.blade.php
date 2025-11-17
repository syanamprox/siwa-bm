@extends('layouts.app')

@section('title', 'Backup & Restore - SIWA')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-database mr-2"></i>
            Backup & Restore
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-user" onclick="createBackup()">
                <i class="fas fa-plus mr-2"></i>Buat Backup
            </button>
            <button type="button" class="btn btn-info btn-user" onclick="showRestoreModal()">
                <i class="fas fa-upload mr-2"></i>Restore
            </button>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Backup
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBackups">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-archive fa-2x text-gray-300"></i>
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
                                Database Size
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="databaseSize">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-database fa-2x text-gray-300"></i>
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
                                Total Size
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSize">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hdd fa-2x text-gray-300"></i>
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
                                Last Backup
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="lastBackup">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Settings -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs mr-2"></i>Pengaturan Backup Otomatis
                    </h6>
                </div>
                <div class="card-body">
                    <form id="backupSettingsForm">
                        <div class="form-group">
                            <label for="backup_schedule">Jadwal Backup</label>
                            <select class="form-control" id="backup_schedule" name="backup_schedule">
                                <option value="disabled">Dinonaktif</option>
                                <option value="daily">Setiap Hari</option>
                                <option value="weekly">Setiap Minggu</option>
                                <option value="monthly">Setiap Bulan</option>
                            </select>
                            <small class="form-text text-muted">Frekuensi backup otomatis</small>
                        </div>
                        <div class="form-group">
                            <label for="backup_retention">Simpan Backup (hari)</label>
                            <input type="number" class="form-control" id="backup_retention" name="backup_retention" min="1" max="365" value="30">
                            <small class="form-text text-muted">Backup lama akan dihapus otomatis</small>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_files" name="include_files" checked>
                                <label class="form-check-label" for="include_files">
                                    Include Files (Uploads)
                                </label>
                                <small class="form-text text-muted">Backup juga menyertakan file uploads</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="email_notification" name="email_notification">
                                <label class="form-check-label" for="email_notification">
                                    Email Notification
                                </label>
                                <small class="form-text text-muted">Kirim notifikasi email saat backup</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Daftar Backup
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="#" onclick="refreshBackups()">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </a>
                    <a class="dropdown-item" href="#" onclick="cleanOldBackups()">
                        <i class="fas fa-broom mr-2"></i>Bersihkan Lama
                    </a>
                    <a class="dropdown-item" href="#" onclick="downloadAllBackups()">
                        <i class="fas fa-download mr-2"></i>Download Semua
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="backupsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Filename</th>
                            <th>Ukuran</th>
                            <th>Tanggal Dibuat</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                        <tr>
                            <td colspan="5" class="text-center">
                                <i class="fas fa-spinner fa-spin"></i>
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload mr-2"></i>Restore Database
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="restoreForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Peringatan:</strong> Restore database akan menghapus semua data yang ada saat ini dan menggantinya dengan data dari backup file. Pastikan Anda sudah mendownload backup terbaru sebelum melakukan restore.
                    </div>

                    <div class="form-group">
                        <label for="backup_file">File Backup <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="backup_file" name="backup_file" accept=".zip" required>
                            <label class="custom-file-label" for="backup_file">Pilih file backup (.zip)</label>
                        </div>
                        <small class="form-text text-muted">Pilih file backup yang akan direstore</small>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirm_restore" name="confirm_restore" required>
                            <label class="form-check-label" for="confirm_restore">
                                Saya mengerti bahwa restore akan menghapus semua data saat ini
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Restore
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Backup Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash mr-2 text-danger"></i>Hapus Backup
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm">
                @csrf
                <input type="hidden" id="delete_filename" name="filename">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus backup file ini?</p>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> File yang dihapus tidak dapat dikembalikan.
                    </div>
                    <div class="alert alert-info">
                        <strong>File:</strong> <span id="delete_filename_display"></span>
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
    loadBackups();
    loadBackupStatus();

    // Restore form submit
    $('#restoreForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: '/admin/backup/restore',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                showLoading();
                $('#restoreModal button[type="submit"]').prop('disabled', true);
            },
            success: function(response) {
                hideLoading();
                $('#restoreModal button[type="submit"]').prop('disabled', false);

                if (response.success) {
                    $('#restoreModal').modal('hide');
                    showToast(response.message, 'success');
                    $('#restoreForm')[0].reset();
                    loadBackups();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                hideLoading();
                $('#restoreModal button[type="submit"]').prop('disabled', false);
                var message = xhr.responseJSON?.message || 'Gagal melakukan restore';
                showToast(message, 'error');
            }
        });
    });

    // Delete form submit
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();

        var filename = $('#delete_filename').val();

        $.ajax({
            url: '/admin/backup/delete/' + filename,
            type: 'DELETE',
            beforeSend: function() {
                showLoading();
            },
            success: function(response) {
                hideLoading();

                if (response.success) {
                    $('#deleteModal').modal('hide');
                    showToast(response.message, 'success');
                    loadBackups();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                hideLoading();
                var message = xhr.responseJSON?.message || 'Gagal menghapus backup';
                showToast(message, 'error');
            }
        });
    });

    // Backup settings form
    $('#backupSettingsForm').on('submit', function(e) {
        e.preventDefault();

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
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                hideLoading();
                var message = xhr.responseJSON?.message || 'Gagal menyimpan pengaturan';
                showToast(message, 'error');
            }
        });
    });

    // File input change handler
    $('#backup_file').on('change', function() {
        var fileName = $(this).val();
        var fileExt = fileName.split('.').pop().toLowerCase();

        if (fileExt !== 'zip') {
            alert('Hanya file .zip yang diperbolehkan');
            $(this).val('');
        }
    });
});

function loadBackups() {
    $.ajax({
        url: '/admin/backup',
        type: 'GET',
        success: function(response) {
            renderBackupTable(response.data);
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Gagal memuat data backup';
            showToast(message, 'error');
            $('#backupsTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>');
        }
    });
}

function loadBackupStatus() {
    $.ajax({
        url: '/admin/backup/status',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                $('#totalBackups').text(response.data.total_backups);
                $('#databaseSize').text(response.data.database_size);
                $('#totalSize').text(response.data.total_size);
                $('#lastBackup').text(response.data.last_backup || 'Belum ada');
            }
        },
        error: function(xhr) {
            console.error('Failed to load backup status');
        }
    });
}

function renderBackupTable(backups) {
    var html = '';

    if (backups.length === 0) {
        html = '<tr><td colspan="5" class="text-center text-muted">Belum ada backup tersimpan</td></tr>';
    } else {
        backups.forEach(function(backup, index) {
            var downloadUrl = '/admin/backup/download/' + backup.filename;
            var deleteUrl = 'javascript:deleteBackup(\'' + backup.filename + '\')';

            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${backup.filename}</strong></td>
                    <td>${backup.size}</td>
                    <td><small>${backup.created_at}</small></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="${downloadUrl}" class="btn btn-sm btn-info" title="Download" target="_blank">
                                <i class="fas fa-download"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-warning" onclick="downloadBackup('${backup.filename}')" title="Download">
                                <i class="fas fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteBackup('${backup.filename}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    $('#backupsTable tbody').html(html);
}

function createBackup() {
    if (confirm('Apakah Anda yakin ingin membuat backup sekarang?')) {
        showLoading();

        $.ajax({
            url: '/admin/backup/create',
            type: 'POST',
            beforeSend: function() {
                // Loading already shown
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    showToast(response.message, 'success');
                    loadBackups();
                    loadBackupStatus();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                hideLoading();
                var message = xhr.responseJSON?.message || 'Gagal membuat backup';
                showToast(message, 'error');
            }
        });
    }
}

function showRestoreModal() {
    $('#restoreModal').modal('show');
    $('#restoreForm')[0].reset();
    $('.custom-file-label').text('Pilih file backup (.zip)');
}

function deleteBackup(filename) {
    $('#delete_filename').val(filename);
    $('#delete_filename_display').text(filename);
    $('#deleteModal').modal('show');
}

function downloadBackup(filename) {
    window.open('/admin/backup/download/' + filename, '_blank');
}

function refreshBackups() {
    showToast('Memperbarui data backup...', 'info');
    loadBackups();
    loadBackupStatus();
}

function cleanOldBackups() {
    if (confirm('Apakah Anda yakin ingin menghapus backup lama? Backup yang lebih tua dari 30 hari akan dihapus.')) {
        showToast('Fitur ini akan segera tersedia', 'info');
    }
}

function downloadAllBackups() {
    showToast('Fitur ini akan segera tersedia', 'info');
}

function showLoading() {
    $('#loadingOverlay').show();
    $('.loading-spinner').show();
}

function hideLoading() {
    $('#loadingOverlay').hide();
    $('.loading-spinner').hide();
}
</script>
@endsection