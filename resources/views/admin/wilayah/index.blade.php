@extends('layouts.app')

@section('title', 'Manajemen Wilayah - SIWA')

@section('content')
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Manajemen Wilayah</h6>
        <div>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">
                <i class="fas fa-plus fa-sm"></i> Tambah Wilayah
            </button>
            <div class="dropdown no-arrow d-inline-block ml-2">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="#" onclick="refreshData()">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                    </a>
                    <a class="dropdown-item" href="#" onclick="exportData()">
                        <i class="fas fa-download mr-2"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
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
        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select id="filterTingkat" class="form-control form-control-sm">
                    <option value="">Semua Tingkat</option>
                    <option value="Kelurahan">Kelurahan</option>
                    <option value="RW">RW</option>
                    <option value="RT">RT</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterParent" class="form-control form-control-sm">
                    <option value="">Semua Parent</option>
                    <!-- Parent options will be loaded via AJAX -->
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Cari nama atau kode wilayah...">
            </div>
            <div class="col-md-2">
                <div class="btn-group btn-block" role="group">
                    <button class="btn btn-info btn-sm" onclick="applyFilters()">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="resetFilters()" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Wilayah Table -->
        <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Tingkat</th>
                            <th>Parent</th>
                            <th>Hierarki</th>
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

</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus mr-2"></i>Tambah Wilayah Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode">Kode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode" name="kode" required>
                                <small class="form-text text-muted">Kode unik wilayah (contoh: 01, RW-01)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tingkat">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-control" id="tingkat" name="tingkat" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="Kelurahan">Kelurahan</option>
                                    <option value="RW">RW</option>
                                    <option value="RT">RT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nama">Nama Wilayah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                                <small class="form-text text-muted">Nama lengkap wilayah</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="parent_id">Parent Wilayah</label>
                                <select class="form-control" id="parent_id" name="parent_id">
                                    <option value="">-- Pilih Parent (kosongkan untuk Kelurahan) --</option>
                                    <!-- Parent options will be loaded via AJAX -->
                                </select>
                                <small class="form-text text-muted">
                                    Kelurahan: tanpa parent<br>
                                    RW: parent = Kelurahan<br>
                                    RT: parent = RW
                                </small>
                            </div>
                        </div>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>Ubah Wilayah
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm">
                @csrf
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_kode">Kode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_kode" name="kode" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tingkat">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_tingkat" name="tingkat" required>
                                    <option value="Kelurahan">Kelurahan</option>
                                    <option value="RW">RW</option>
                                    <option value="RT">RT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_nama">Nama Wilayah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama" name="nama" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_parent_id">Parent Wilayah</label>
                                <select class="form-control" id="edit_parent_id" name="parent_id">
                                    <option value="">-- Pilih Parent (kosongkan untuk Kelurahan) --</option>
                                    <!-- Parent options will be loaded via AJAX -->
                                </select>
                            </div>
                        </div>
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
                    <i class="fas fa-trash mr-2 text-danger"></i>Hapus Wilayah
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteForm">
                @csrf
                <input type="hidden" id="delete_id" name="id">
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus wilayah ini?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Wilayah tidak bisa dihapus jika masih memiliki:
                        <ul class="mb-0 mt-2">
                            <li>Child wilayah (sub-wilayah)</li>
                            <li>User yang terhubung</li>
                            <li>Warga yang terdaftar</li>
                        </ul>
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
    loadData();

    // Handle tingkat change for add form
    $('#tingkat').change(function() {
        loadParentOptions($(this).val(), 'parent_id');
    });

    // Handle tingkat change for edit form
    $('#edit_tingkat').change(function() {
        loadParentOptions($(this).val(), 'edit_parent_id');
    });

    // Event listeners for filters
    $('#searchInput').on('keyup', function(e) {
        if (e.which === 13) { // Enter key
            applyFilters();
        }
    });

    // Auto-apply filter when tingkat changes
    $('#filterTingkat').change(function() {
        applyFilters();
    });

    // Auto-apply filter when parent changes
    $('#filterParent').change(function() {
        applyFilters();
    });

    // Add form submit
    $('#addForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '/admin/wilayah',
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
                    resetForm();
                    loadData();
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
            url: '/admin/wilayah/' + id,
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
                    loadData();
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
            url: '/admin/wilayah/' + id,
            type: 'DELETE',
            beforeSend: function() {
                showLoading();
            },
            success: function(response) {
                hideLoading();
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    showToast(response.message, 'success');
                    loadData();
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
});

function loadData() {
    $.ajax({
        url: '/admin/api/wilayah',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                renderTable(response.data);
                loadParentFilterOptions(response.data);
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Gagal memuat data';
            showToast(message, 'error');
        }
    });
}

function loadParentFilterOptions(data) {
    var parentOptions = '<option value="">Semua Parent</option>';

    // Add Kelurahan options for Parent filter
    var kelurahans = data.filter(item => item.tingkat === 'Kelurahan');
    kelurahans.forEach(function(kelurahan) {
        parentOptions += `<option value="${kelurahan.id}">${kelurahan.nama}</option>`;
    });

    $('#filterParent').html(parentOptions);
}

function applyFilters() {
    var tingkatFilter = $('#filterTingkat').val();
    var parentFilter = $('#filterParent').val();
    var searchTerm = $('#searchInput').val().toLowerCase();

    $.ajax({
        url: '/admin/api/wilayah',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var filteredData = response.data;

                // Filter by tingkat
                if (tingkatFilter) {
                    filteredData = filteredData.filter(function(item) {
                        return item.tingkat === tingkatFilter;
                    });
                }

                // Filter by parent
                if (parentFilter) {
                    filteredData = filteredData.filter(function(item) {
                        return item.parent_id == parentFilter;
                    });
                }

                // Filter by search term
                if (searchTerm) {
                    filteredData = filteredData.filter(function(item) {
                        return item.nama.toLowerCase().includes(searchTerm) ||
                               item.kode.toLowerCase().includes(searchTerm);
                    });
                }

                renderTable(filteredData);

                // Show result count
                var resultText = `Menampilkan ${filteredData.length} dari ${response.data.length} data`;
                if (filteredData.length === 0) {
                    showToast('Tidak ada data yang cocok dengan filter', 'warning', 3000);
                } else {
                    showToast(resultText, 'success', 2000);
                }
            }
        },
        error: function(xhr) {
            var message = xhr.responseJSON?.message || 'Gagal memuat data';
            showToast(message, 'error');
        }
    });
}

function resetFilters() {
    $('#filterTingkat').val('');
    $('#filterParent').val('');
    $('#searchInput').val('');
    loadData();
    showToast('Filter telah direset', 'success', 2000);
}

function renderTable(data) {
    var html = '';
    var no = 1;

    // Sort by hierarchy
    data.sort(function(a, b) {
        if (a.tingkat !== b.tingkat) {
            var levelOrder = {'Kelurahan': 1, 'RW': 2, 'RT': 3};
            return levelOrder[a.tingkat] - levelOrder[b.tingkat];
        }
        return a.kode.localeCompare(b.kode);
    });

    data.forEach(function(item) {
        var tingkatBadge = '';
        switch(item.tingkat) {
            case 'Kelurahan':
                tingkatBadge = '<span class="badge badge-primary">Kelurahan</span>';
                break;
            case 'RW':
                tingkatBadge = '<span class="badge badge-success">RW</span>';
                break;
            case 'RT':
                tingkatBadge = '<span class="badge badge-info">RT</span>';
                break;
        }

        html += `
            <tr>
                <td>${no++}</td>
                <td><strong>${item.kode_display}</strong></td>
                <td>${item.nama}</td>
                <td>${tingkatBadge}</td>
                <td>${item.parent ? item.parent.kode_display + ' - ' + item.parent.nama : '-'}</td>
                <td><small class="text-muted">${item.nama_hierarki || '-'}</small></td>
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

function editData(id) {
    $.ajax({
        url: '/admin/api/wilayah/' + id + '/edit',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var wilayah = response.data.wilayah;
                var parents = response.data.parents;

                console.log('Edit wilayah data:', wilayah);
                console.log('Tingkat value:', "'" + wilayah.tingkat + "'");
                console.log('Tingkat length:', wilayah.tingkat.length);

                $('#edit_id').val(wilayah.id);
                $('#edit_kode').val(wilayah.kode);
                $('#edit_nama').val(wilayah.nama);
                $('#edit_tingkat').val(wilayah.tingkat);

                // Debug tingkat dropdown
                console.log('Selected tingkat:', $('#edit_tingkat').val());

                // Load parent options
                loadParentOptions(wilayah.tingkat, 'edit_parent_id', parents, wilayah.parent_id);

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

function loadParentOptions(tingkat, targetId, parents = null, selectedId = null) {
    if (tingkat === 'Kelurahan') {
        $('#' + targetId).html('<option value="">-- Tidak ada Parent (Kelurahan) --</option>');
        return;
    }

    if (parents === null) {
        // Load parents via AJAX
        $.ajax({
            url: '/admin/api/wilayah/create',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    renderParentOptions(response.data.parents, targetId, tingkat, selectedId);
                }
            }
        });
    } else {
        renderParentOptions(parents, targetId, tingkat, selectedId);
    }
}

function renderParentOptions(parents, targetId, tingkat, selectedId) {
    var html = '<option value="">-- Pilih Parent --</option>';

    parents.forEach(function(parent) {
        if (tingkat === 'RW' && parent.tingkat === 'Kelurahan') {
            html += `<option value="${parent.id}" ${selectedId == parent.id ? 'selected' : ''}>${parent.kode_display} - ${parent.nama}</option>`;
        } else if (tingkat === 'RT' && parent.tingkat === 'RW') {
            html += `<option value="${parent.id}" ${selectedId == parent.id ? 'selected' : ''}>${parent.kode_display} - ${parent.nama}</option>`;
        }
    });

    $('#' + targetId).html(html);
}

function resetForm() {
    $('#addForm')[0].reset();
    $('#parent_id').html('<option value="">-- Pilih Parent --</option>');
}

function refreshData() {
    loadData();
}

function exportData() {
    // Implementation for export functionality
    showToast('Fitur export akan segera tersedia', 'info');
}
</script>
@endpush