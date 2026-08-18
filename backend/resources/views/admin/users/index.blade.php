@extends('layouts.app')

@section('title', 'Manajemen User - SIWA')

@section('content')
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Manajemen User</h6>
        <div>
            <button class="btn btn-primary btn-sm" onclick="showCreateModal()">
                <i class="fas fa-plus fa-sm"></i> Tambah User
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="filterRole" class="form-control form-control-sm">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="lurah">Lurah</option>
                    <option value="rw">RW</option>
                    <option value="rt">RT</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterStatus" class="form-control form-control-sm">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Non-aktif</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari nama atau email...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-info btn-sm btn-block" onclick="applyFilters()">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Wilayah</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center">
            <div class="small text-gray-600" id="paginationInfo">
                Menampilkan 0 dari 0 data
            </div>
            <nav aria-label="Page navigation" id="paginationNav">
                <!-- Pagination will be loaded via AJAX -->
            </nav>
        </div>
    </div>
</div>

<!-- Create/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Tambah User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="userId" name="user_id">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="user@example.com" required>
                                <small class="form-text text-muted">Digunakan untuk login</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="password">Password <span class="text-danger" id="passwordRequired">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="form-text text-muted">Minimal 6 karakter</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="lurah">Lurah</option>
                                    <option value="rw">RW</option>
                                    <option value="rt">RT</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_aktif">Status Aktif <span class="text-danger">*</span></label>
                                <select class="form-control" id="status_aktif" name="status_aktif" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Non-aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="wilayah_ids">Akses Wilayah</label>
                        <div id="wilayahSelection">
                            <!-- Wilayah options will be loaded via AJAX -->
                        </div>
                        <small class="form-text text-muted">Pilih wilayah yang dapat diakses oleh user</small>
                    </div>

                    <div class="form-group">
                        <label for="foto_profile">Foto Profile</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="foto_profile" name="foto_profile" accept="image/*">
                            <label class="custom-file-label" for="foto_profile">Pilih foto...</label>
                        </div>
                        <small class="form-text text-muted">Format: JPG, PNG, GIF. Maks: 2MB</small>
                    </div>

                    <div id="currentPhoto" class="text-center mb-3" style="display: none;">
                        <img id="photoPreview" src="" alt="Current Photo" class="img-thumbnail" style="max-width: 200px;">
                        <br>
                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePhoto()">
                            <i class="fas fa-trash"></i> Hapus Foto
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus user <strong id="deleteUsername"></strong>?</p>
                <p class="text-danger small">User yang dihapus tidak dapat dikembalikan lagi.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mereset password user <strong id="resetUsername"></strong>?</p>
                <p>Password baru akan di-generate secara otomatis.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="confirmResetPassword()">
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let deleteUserId = null;
let resetPasswordUserId = null;

// Load initial data
$(document).ready(function() {
    loadUsers();

    // Reset form when modal is hidden
    $('#userModal').on('hidden.bs.modal', function () {
        resetUserForm();
    });
    loadWilayahOptions();
});

// Load users with filters
function loadUsers(page = 1) {
    const role = $('#filterRole').val();
    const status = $('#filterStatus').val();
    const search = $('#searchInput').val();

    const params = new URLSearchParams({
        role: role,
        status: status,
        search: search,
        page: page
    });

    showLoading();

    fetch(`/admin/api/users?${params}`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                renderUsersTable(data.data.data, data.data.from);
                renderPagination(data.data);
                updatePaginationInfo(data.data);
                currentPage = page;
            } else {
                showToast('Error loading users: ' + data.message, 'error');
            }
        })
        .catch(error => {
            hideLoading();
            showToast('Error loading users', 'error');
        });
}

// Render users table
function renderUsersTable(users, startFrom) {
    let html = '';

    if (users.length === 0) {
        html = '<tr><td colspan="7" class="text-center">Tidak ada data user</td></tr>';
    } else {
        users.forEach((user, index) => {
            const roleLabel = getRoleLabel(user.role);
            const statusBadge = user.status_aktif ?
                '<span class="badge badge-success">Aktif</span>' :
                '<span class="badge badge-danger">Non-aktif</span>';

            // Build wilayah names
            let wilayahNames = '-';
            if (user.user_wilayah && user.user_wilayah.length > 0) {
                const wilayahNamaArray = user.user_wilayah
                    .filter(uw => uw.wilayah)
                    .map(uw => uw.wilayah.nama);
                if (wilayahNamaArray.length > 0) {
                    wilayahNames = wilayahNamaArray.join(', ');
                }
            }

            const photo = user.avatar ?
                `<img src="/${user.avatar}" alt="${user.name}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">` :
                '<i class="fas fa-user-circle fa-2x text-gray-400"></i>';

            html += `
                <tr>
                    <td>${startFrom + index}</td>
                    <td>${user.name}</td>
                    <td><span class="badge badge-info">${roleLabel}</span></td>
                    <td>${statusBadge}</td>
                    <td>${wilayahNames}</td>
                    <td class="text-center">${photo}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info" onclick="editUser(${user.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-warning" onclick="toggleStatus(${user.id})" title="Toggle Status">
                                <i class="fas fa-power-off"></i>
                            </button>
                            <button class="btn btn-secondary" onclick="resetPassword(${user.id}, '${user.name}')" title="Reset Password">
                                <i class="fas fa-key"></i>
                            </button>
                            <button class="btn btn-danger" onclick="deleteUser(${user.id}, '${user.name}')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    $('#usersTableBody').html(html);
}

// Render pagination
function renderPagination(data) {
    let html = '';

    if (data.prev_page_url) {
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadUsers(${data.current_page - 1})">Previous</a>
        </li>`;
    }

    for (let i = 1; i <= data.last_page; i++) {
        const active = i === data.current_page ? 'active' : '';
        html += `<li class="page-item ${active}">
            <a class="page-link" href="#" onclick="loadUsers(${i})">${i}</a>
        </li>`;
    }

    if (data.next_page_url) {
        html += `<li class="page-item">
            <a class="page-link" href="#" onclick="loadUsers(${data.current_page + 1})">Next</a>
        </li>`;
    }

    $('#paginationNav').html(`<ul class="pagination">${html}</ul>`);
}

// Update pagination info
function updatePaginationInfo(data) {
    const info = `Menampilkan ${data.from ? data.from : 0} - ${data.to ? data.to : 0} dari ${data.total} data`;
    $('#paginationInfo').text(info);
}

// Get role label
function getRoleLabel(role) {
    const labels = {
        'admin': 'Admin',
        'lurah': 'Lurah',
        'rw': 'RW',
        'rt': 'RT'
    };
    return labels[role] || role;
}

// Reset user form to initial state
function resetUserForm() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#userModalTitle').text('Tambah User');

    // Reset password requirement
    $('#passwordRequired').show();
    $('#password').prop('required', true);

    // Clear photo preview
    $('#currentPhoto').hide();
    $('#photoPreview').attr('src', '');
    $('.custom-file-label').text('Pilih foto...');

    // Uncheck all wilayah checkboxes
    $('#wilayahSelection input[type="checkbox"]').prop('checked', false);
}

// Load wilayah options for form
function loadWilayahOptions() {
    fetch('/admin/api/users/create')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderWilayahOptions(data.data.wilayah);
            }
        })
        .catch(error => {
            console.error('Error loading wilayah options:', error);
        });
}

// Render wilayah options
function renderWilayahOptions(wilayahByTingkat) {
    let html = '';

    Object.keys(wilayahByTingkat).forEach(tingkat => {
        html += `<div class="mb-3">
            <h6>${tingkat.charAt(0).toUpperCase() + tingkat.slice(1)}</h6>
            <div class="row">`;

        wilayahByTingkat[tingkat].forEach(wilayah => {
            html += `
                <div class="col-md-6 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${wilayah.id}"
                               id="wilayah_${wilayah.id}" name="wilayah_ids[]">
                        <label class="form-check-label" for="wilayah_${wilayah.id}">
                            ${wilayah.nama}
                        </label>
                    </div>
                </div>
            `;
        });

        html += `</div></div>`;
    });

    $('#wilayahSelection').html(html);
}

// Show create modal
function showCreateModal() {
    resetUserForm();
    $('.custom-file-label').text('Pilih foto...');
    $('#userModal').modal('show');
}

// Edit user
function editUser(userId) {
    showLoading();

    fetch(`/admin/api/users/${userId}/edit`)
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                const user = data.data;

                // Debug: log user data
                console.log('Edit user data:', user);

                $('#userModalTitle').text('Edit User');
                $('#userId').val(user.id);
                $('#name').val(user.name);
                $('#email').val(user.email);
                $('#role').val(user.role);
                $('#status_aktif').val(user.status_aktif ? '1' : '0');

                // Password not required for edit
                $('#passwordRequired').hide();
                $('#password').prop('required', false);

                // Load current photo
                if (user.avatar) {
                    $('#photoPreview').attr('src', '/' + user.avatar);
                    $('#currentPhoto').show();
                } else {
                    $('#currentPhoto').hide();
                }

                // Check assigned wilayah
                $('#wilayahSelection input[type="checkbox"]').prop('checked', false);
                if (data.assigned_wilayah && data.assigned_wilayah.length > 0) {
                    data.assigned_wilayah.forEach(wilayahId => {
                        $(`#wilayah_${wilayahId}`).prop('checked', true);
                    });
                }

                $('#userModal').modal('show');
            } else {
                showToast('Error loading user: ' + data.message, 'error');
            }
        })
        .catch(error => {
            hideLoading();
            showToast('Error loading user', 'error');
        });
}

// Save user (create/update)
$('#userForm').on('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const userId = $('#userId').val();
    const isEdit = userId !== '';

    // Add password only if filled
    if (!$('#password').val()) {
        formData.delete('password');
    }

    // Add selected wilayah IDs manually (checkboxes are not automatically included in FormData)
    const selectedWilayah = [];
    $('#wilayahSelection input[type="checkbox"]:checked').each(function() {
        selectedWilayah.push($(this).val());
    });

    // Remove existing wilayah_ids and add new ones
    formData.delete('wilayah_ids');
    selectedWilayah.forEach(wilayahId => {
        formData.append('wilayah_ids[]', wilayahId);
    });

    const url = isEdit ? `/admin/api/users/${userId}` : '/admin/api/users';
    const method = isEdit ? 'POST' : 'POST';

    // Add method override for PUT
    if (isEdit) {
        formData.append('_method', 'PUT');
    }

    showLoading();

    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast(data.message, 'success');
            $('#userModal').modal('hide');
            loadUsers(currentPage);
        } else {
            if (data.errors) {
                let errorMessage = 'Validation errors:\n';
                Object.keys(data.errors).forEach(key => {
                    errorMessage += `- ${data.errors[key][0]}\n`;
                });
                showToast(errorMessage, 'error');
            } else {
                showToast(data.message, 'error');
            }
        }
    })
    .catch(error => {
        hideLoading();
        showToast('Error saving user', 'error');
    });
});

// Toggle user status
function toggleStatus(userId) {
    showLoading();

    fetch(`/admin/api/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast(data.message, 'success');
            loadUsers(currentPage);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showToast('Error toggling status', 'error');
    });
}

// Reset password
function resetPassword(userId, name) {
    resetPasswordUserId = userId;
    $('#resetUsername').text(name);
    $('#resetPasswordModal').modal('show');
}

// Confirm reset password
function confirmResetPassword() {
    showLoading();

    fetch(`/admin/api/users/${resetPasswordUserId}/reset-password`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        $('#resetPasswordModal').modal('hide');
        if (data.success) {
            const message = `${data.message}\nPassword baru: ${data.data.password}`;
            showToast(message, 'info', 5000);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showToast('Error resetting password', 'error');
    });
}

// Delete user
function deleteUser(userId, name) {
    deleteUserId = userId;
    $('#deleteUsername').text(name);
    $('#deleteModal').modal('show');
}

// Confirm delete
function confirmDelete() {
    showLoading();

    fetch(`/admin/api/users/${deleteUserId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        $('#deleteModal').modal('hide');
        if (data.success) {
            showToast(data.message, 'success');
            loadUsers(currentPage);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        hideLoading();
        showToast('Error deleting user', 'error');
    });
}

// Apply filters
function applyFilters() {
    loadUsers(1);
}

// Handle search input enter key
$('#searchInput').on('keypress', function(e) {
    if (e.which === 13) {
        applyFilters();
    }
});

// Handle file input change
$('#foto_profile').on('change', function() {
    const fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').addClass("selected").html(fileName);

    // Preview selected image
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#photoPreview').attr('src', e.target.result);
            $('#currentPhoto').show();
        };
        reader.readAsDataURL(file);
    }
});

// Remove photo
function removePhoto() {
    $('#currentPhoto').hide();
    $('#foto_profile').val('');
    $('.custom-file-label').text('Pilih foto...');
}

// Loading functions
function showLoading() {
    $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
}

function hideLoading() {
    $('#saveBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan');
}
</script>
@endpush