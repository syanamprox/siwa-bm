@extends('layouts.app')

@section('title', 'Koneksi Iuran - ' . $keluarga->no_kk)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-link me-2"></i>
                Koneksi Iuran
            </h1>
            <p class="text-muted mb-0">
                KK: {{ $keluarga->no_kk }} | Kepala Keluarga: {{ $keluarga->kepalaKeluarga->nama_lengkap ?? '-' }}
            </p>
        </div>
        <a href="{{ route('keluarga.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Kembali ke Daftar Keluarga
        </a>
    </div>

  
    <!-- Existing Connections -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Koneksi Iuran Aktif</h6>
        </div>
        <div class="card-body">
            @forelse($connections as $connection)
            @if($connection->jenisIuran)
            <div class="border rounded p-3 mb-3 connection-item" data-id="{{ $connection->jenis_iuran_id }}">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h6 class="mb-1">{{ $connection->jenisIuran->nama }}</h6>
                        <small class="text-muted">Kode: {{ $connection->jenisIuran->kode }}</small>
                        <br><small class="badge bg-info text-white font-weight-bold">{{ $connection->jenisIuran->periode_label }}</small>
                    </div>
                    <div class="col-md-2">
                        <div>
                            <small class="text-muted font-weight-bold">Nominal Default:</small><br>
                            <strong>Rp {{ number_format($connection->jenisIuran->jumlah, 0) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div>
                            <small class="text-muted font-weight-bold">Nominal Custom:</small><br>
                            @if($connection->nominal_custom)
                                <span class="text-warning">Rp {{ number_format($connection->nominal_custom, 0) }}</span>
                            @else
                                <span class="text-muted">Default</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div>
                            <small class="text-muted font-weight-bold">Status:</small><br>
                            <span class="badge {{ $connection->status_aktif ? 'bg-success' : 'bg-secondary' }} text-white font-weight-bold">
                                {{ $connection->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-1 text-end">
                        <div class="btn-group" role="group">
                            <button type="button"
                                    class="btn btn-sm btn-warning text-white"
                                    onclick="editConnection({{ $connection->jenis_iuran_id }})"
                                    title="Edit Koneksi">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-danger text-white"
                                    onclick="deleteConnection({{ $connection->jenis_iuran_id }})"
                                    title="Hapus Koneksi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @if($connection->alasan_custom)
                <div class="mt-2">
                    <small class="text-muted">Alasan Custom: {{ $connection->alasan_custom }}</small>
                </div>
                @endif
            </div>
            @else
            <div class="border rounded p-3 mb-3 bg-light">
                <div class="row align-items-center">
                    <div class="col-12">
                        <p class="text-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Data tidak lengkap:</strong> Jenis iuran untuk koneksi ini tidak ditemukan (mungkin telah dihapus)
                        </p>
                    </div>
                </div>
            </div>
            @endif
            @empty
            <div class="text-center py-4">
                <i class="fas fa-unlink fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada koneksi iuran untuk keluarga ini</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Add New Connection -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Koneksi Iuran</h6>
        </div>
        <div class="card-body">
            @if($availableIurans->count() > 0)
            <form id="addConnectionForm">
                @csrf
                <input type="hidden" name="keluarga_id" value="{{ $keluarga->id }}">

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="jenis_iuran_id" class="form-label">Jenis Iuran <span class="text-danger">*</span></label>
                            <select class="form-control" id="jenis_iuran_id" name="jenis_iuran_id" required>
                                <option value="">Pilih Jenis Iuran</option>
                                @foreach($availableIurans as $jenisIuran)
                                <option value="{{ $jenisIuran->id }}"
                                        data-nominal="{{ $jenisIuran->jumlah }}"
                                        data-nama="{{ $jenisIuran->nama }}">
                                    {{ $jenisIuran->nama }} (Rp {{ number_format($jenisIuran->jumlah, 0) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="nominal_custom" class="form-label">Nominal Custom (Opsional)</label>
                            <input type="number"
                                   class="form-control"
                                   id="nominal_custom"
                                   name="nominal_custom"
                                   placeholder="Kosongkan untuk gunakan default"
                                   min="0"
                                   step="1000">
                            <small class="text-muted">Kosongkan jika menggunakan nominal default</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="alasan_custom" class="form-label">Alasan Custom (Opsional)</label>
                            <input type="text"
                                   class="form-control"
                                   id="alasan_custom"
                                   name="alasan_custom"
                                   placeholder="Alasan perubahan nominal">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-success text-white">
                                <i class="fas fa-plus me-1"></i>
                                Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @else
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted">Semua jenis iuran sudah terhubung dengan keluarga ini</p>
                <a href="{{ route('jenis_iuran.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plus me-1"></i>
                    Tambah Jenis Iuran Baru
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Connection Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Koneksi Iuran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editConnectionForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_jenis_iuran_id" name="jenis_iuran_id">
                    <input type="hidden" name="keluarga_id" value="{{ $keluarga->id }}">

                    <div class="mb-3">
                        <label class="form-label">Jenis Iuran</label>
                        <input type="text" class="form-control" id="edit_nama_jenis" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="edit_nominal_custom" class="form-label">Nominal Custom</label>
                        <input type="number" class="form-control" id="edit_nominal_custom" name="nominal_custom" min="0" step="1000">
                        <small class="text-muted">Kosongkan untuk gunakan nominal default</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_alasan_custom" class="form-label">Alasan Custom</label>
                        <input type="text" class="form-control" id="edit_alasan_custom" name="alasan_custom">
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_status_aktif" name="status_aktif" value="1">
                            <label class="form-check-label" for="edit_status_aktif">
                                Aktif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary text-white">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteConnectionForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <input type="hidden" id="delete_jenis_iuran_id" name="jenis_iuran_id">
                    <input type="hidden" name="keluarga_id" value="{{ $keluarga->id }}">

                    <p>Apakah Anda yakin ingin menghapus koneksi iuran "<span id="delete_nama_jenis"></span>"?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger text-white">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Store all available jenis iuran data for dynamic dropdown management
    window.allAvailableIurans = @json($availableIurans);

    // Initialize add connection form binding
    bindAddConnectionForm();

    // Edit connection form
    $('#editConnectionForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const jenisIuranId = $('#edit_jenis_iuran_id').val();

        formData.append('_method', 'PUT');

        $.ajax({
            url: `/admin/keluarga/{{ $keluarga->id }}/iuran/${jenisIuranId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    // Update connection row dynamically
                    updateConnectionRow(response.data);
                    // Close the modal
                    $('#editModal').modal('hide');
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                showToast('error', 'Terjadi kesalahan saat memperbarui koneksi');
            }
        });
    });

    // Delete connection form
    $('#deleteConnectionForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const jenisIuranId = $('#delete_jenis_iuran_id').val();

        formData.append('_method', 'DELETE');

        $.ajax({
            url: `/admin/keluarga/{{ $keluarga->id }}/iuran/${jenisIuranId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Delete connection success:', response);
                if (response.success) {
                    showToast('success', response.message);
                    // Remove connection row dynamically
                    removeConnectionRow(jenisIuranId);

                    // Update available iurans dropdown
                    updateAvailableIuransDropdown(jenisIuranId, 'add-back');
                    // Close the modal
                    $('#deleteModal').modal('hide');
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showToast('error', response?.message || 'Terjadi kesalahan saat menghapus koneksi');
            }
        });
    });
});

function editConnection(jenisIuranId) {
    const connectionElement = $(`.connection-item[data-id="${jenisIuranId}"]`);

    // Get connection data from the page
    const jenisNama = connectionElement.find('h6').text().trim();

    // Extract nominal custom from the "Nominal Custom" column
    const nominalCustomContainer = connectionElement.find('.col-md-2').eq(1); // Second col-md-2 (Nominal Custom column)
    const nominalCustomElement = nominalCustomContainer.find('.text-warning');
    let nominalCustom = '';

    if (nominalCustomElement.length > 0) {
        // Extract numeric value from "Rp X.xxx" format
        nominalCustom = nominalCustomElement.text()
            .replace('Rp ', '')           // Remove "Rp "
            .replace(/\./g, '')           // Remove thousands separator dots
            .replace(/,/g, '');           // Remove comma decimal separator
    }

    // Extract alasan custom
    const alasanCustomText = connectionElement.find('.text-muted:contains("Alasan Custom:")').text();
    const alasanCustom = alasanCustomText.includes('Alasan Custom:') ?
        alasanCustomText.replace('Alasan Custom: ', '').trim() : '';

    // Check if status is active - look for "Aktif" text in the badge
    const statusBadge = connectionElement.find('.col-md-2').eq(2).find('.badge'); // Third col-md-2 (Status column)
    const statusText = statusBadge.text().trim();
    const isActive = statusText.toLowerCase() === 'aktif';

    console.log('Status detection:', {
        statusText,
        isActive,
        badgeClasses: statusBadge.attr('class')
    });

    console.log('Edit connection data:', {
        jenisIuranId,
        jenisNama,
        nominalCustom,
        alasanCustom,
        isActive
    });

    $('#edit_jenis_iuran_id').val(jenisIuranId);
    $('#edit_nama_jenis').val(jenisNama);
    $('#edit_nominal_custom').val(nominalCustom);
    $('#edit_alasan_custom').val(alasanCustom);
    $('#edit_status_aktif').prop('checked', isActive);

    // Debug values set to form fields
    console.log('Values set to form:', {
        nominalCustomField: $('#edit_nominal_custom').val(),
        alasanCustomField: $('#edit_alasan_custom').val(),
        statusField: $('#edit_status_aktif').prop('checked')
    });

    $('#editModal').modal('show');
}

function deleteConnection(jenisIuranId) {
    const connectionElement = $(`.connection-item[data-id="${jenisIuranId}"]`);
    const jenisNama = connectionElement.find('h6').text().trim();

    $('#delete_jenis_iuran_id').val(jenisIuranId);
    $('#delete_nama_jenis').text(jenisNama);

    $('#deleteModal').modal('show');
}

// Helper functions for dynamic DOM updates
function addConnectionRow(connection) {
    const nominalDefault = connection.jenisIuran.jumlah;
    const nominalCustom = connection.nominal_custom;
    const nominalEfektif = nominalCustom || nominalDefault;
    const statusBadge = connection.status_aktif ?
        '<span class="badge bg-success text-white font-weight-bold">Aktif</span>' :
        '<span class="badge bg-secondary text-white font-weight-bold">Non-Aktif</span>';

    const connectionHtml = `
        <div class="border rounded p-3 mb-3 connection-item" data-id="${connection.jenis_iuran_id}">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h6 class="mb-1">${connection.jenisIuran.nama}</h6>
                    <small class="text-muted">Kode: ${connection.jenisIuran.kode}</small>
                    <br><small class="badge bg-info text-white font-weight-bold">${connection.jenisIuran.periode_label}</small>
                </div>
                <div class="col-md-2">
                    <div>
                        <small class="text-muted font-weight-bold">Nominal Default:</small><br>
                        <strong>Rp ${numberFormat(nominalDefault, 0)}</strong>
                    </div>
                </div>
                <div class="col-md-2">
                    <div>
                        <small class="text-muted font-weight-bold">Nominal Custom:</small><br>
                        ${nominalCustom ?
                            `<span class="text-warning">Rp ${numberFormat(nominalCustom, 0)}</span>` :
                            '<span class="text-muted">Default</span>'}
                    </div>
                </div>
                <div class="col-md-2">
                    <div>
                        <small class="text-muted font-weight-bold">Status:</small><br>
                        ${statusBadge}
                    </div>
                </div>
                <div class="col-md-1 text-end">
                    <div class="btn-group" role="group">
                        <button type="button"
                                class="btn btn-sm btn-warning text-white"
                                onclick="editConnection(${connection.jenis_iuran_id})"
                                title="Edit Koneksi">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button"
                                class="btn btn-sm btn-danger text-white"
                                onclick="deleteConnection(${connection.jenis_iuran_id})"
                                title="Hapus Koneksi">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            ${connection.alasan_custom ?
                `<div class="mt-2">
                    <small class="text-muted">Alasan Custom: ${connection.alasan_custom}</small>
                </div>` : ''}
        </div>
    `;

    // Add the new connection row to the existing connections
    // Target only the first card (Koneksi Iuran Aktif) card
    const $container = $('.card').first().find('.card-body');
    const $emptyState = $container.find('.text-center.py-4');

    if ($emptyState.length > 0) {
        // If empty state exists, replace it with the new connection
        $emptyState.replaceWith(connectionHtml);
    } else {
        // If there are existing connections, add at the beginning
        $container.prepend(connectionHtml);
    }
}

function updateConnectionRow(connection) {
    const connectionElement = $(`.connection-item[data-id="${connection.jenis_iuran_id}"]`);
    if (connectionElement.length === 0) return;

    console.log('Updating connection row with data:', connection);

    // Check if jenisIuran exists in the response
    if (!connection.jenisIuran) {
        console.error('jenisIuran data not found in response:', connection);
        showToast('error', 'Data jenis iuran tidak ditemukan');
        return;
    }

    const nominalDefault = connection.jenisIuran.jumlah;
    const nominalCustom = connection.nominal_custom;
    const nominalEfektif = nominalCustom || nominalDefault;
    const statusBadge = connection.status_aktif ?
        '<span class="badge bg-success text-white font-weight-bold">Aktif</span>' :
        '<span class="badge bg-secondary text-white font-weight-bold">Non-Aktif</span>';

    // Update nominal custom column
    const nominalCustomContainer = connectionElement.find('.col-md-2').eq(1); // Second col-md-2 (Nominal Custom column)
    nominalCustomContainer.html(`
        <div>
            <small class="text-muted font-weight-bold">Nominal Custom:</small><br>
            ${nominalCustom ?
                `<span class="text-warning">Rp ${numberFormat(nominalCustom, 0)}</span>` :
                '<span class="text-muted">Default</span>'}
        </div>
    `);

    // Update status column
    const statusContainer = connectionElement.find('.col-md-2').eq(2); // Third col-md-2 (Status column)
    statusContainer.html(`
        <div>
            <small class="text-muted font-weight-bold">Status:</small><br>
            ${statusBadge}
        </div>
    `);

    // Update or remove alasan custom section
    const alasanCustomDiv = connectionElement.find('.mt-2');
    if (connection.alasan_custom) {
        if (alasanCustomDiv.length > 0) {
            alasanCustomDiv.html(`<small class="text-muted">Alasan Custom: ${connection.alasan_custom}</small>`);
        } else {
            connectionElement.append(`
                <div class="mt-2">
                    <small class="text-muted">Alasan Custom: ${connection.alasan_custom}</small>
                </div>
            `);
        }
    } else {
        alasanCustomDiv.remove();
    }
}

function removeConnectionRow(jenisIuranId) {
    const connectionElement = $(`.connection-item[data-id="${jenisIuranId}"]`);

    if (connectionElement.length === 0) {
        console.warn('Connection element not found for ID:', jenisIuranId);
        return;
    }

    connectionElement.fadeOut(300, function() {
        $(this).remove();

        // Check if there are no more connections in the first card (Koneksi Iuran Aktif)
        if ($('.card').first().find('.connection-item').length === 0) {
            // Show empty state message only in the first card
            const $container = $('.card').first().find('.card-body');
            const emptyHtml = `
                <div class="text-center py-4">
                    <i class="fas fa-unlink fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada koneksi iuran untuk keluarga ini</p>
                </div>
            `;
            $container.html(emptyHtml);
        }
    });
}

function updateAvailableIuransDropdown(jenisIuranId, action) {
    const select = $('#jenis_iuran_id');
    const formContainer = select.closest('.card-body');

    if (action === 'remove') {
        // Remove from available options (when adding new connection)
        select.find(`option[value="${jenisIuranId}"]`).remove();

        // Check if there are no more available options
        if (select.find('option[value!=""]').length === 0) {
            // Show "all connected" message
            formContainer.html(`
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Semua jenis iuran sudah terhubung dengan keluarga ini</p>
                    <a href="{{ route('jenis_iuran.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>
                        Tambah Jenis Iuran Baru
                    </a>
                </div>
            `);
        }
    } else if (action === 'add-back') {
        // When deleting, add the option back to dropdown using stored data
        const iuranData = window.allAvailableIurans.find(item => item.id == jenisIuranId);
        if (iuranData) {
            // Find where to insert the option (maintain alphabetical order)
            const newOption = `<option value="${iuranData.id}" data-nominal="${iuranData.jumlah}" data-nama="${iuranData.nama}">
                ${iuranData.nama} (Rp ${numberFormat(iuranData.jumlah, 0)})
            </option>`;

            // If select was disabled and showing "all connected" message, restore the form
            if (select.prop('disabled')) {
                formContainer.html(`
                    <form id="addConnectionForm">
                        @csrf
                        <input type="hidden" name="keluarga_id" value="{{ $keluarga->id }}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="jenis_iuran_id" class="form-label">Jenis Iuran <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenis_iuran_id" name="jenis_iuran_id" required>
                                        <option value="">Pilih Jenis Iuran</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="nominal_custom" class="form-label">Nominal Custom (Opsional)</label>
                                    <input type="number" class="form-control" id="nominal_custom" name="nominal_custom" placeholder="Kosongkan untuk gunakan default" min="0" step="1000">
                                    <small class="text-muted">Kosongkan jika menggunakan nominal default</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="alasan_custom" class="form-label">Alasan Custom (Opsional)</label>
                                    <input type="text" class="form-control" id="alasan_custom" name="alasan_custom" placeholder="Alasan perubahan nominal">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label>&nbsp;</label><br>
                                    <button type="submit" class="btn btn-success text-white">
                                        <i class="fas fa-plus me-1"></i>
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                `);

                // Re-bind the form submission handler
                bindAddConnectionForm();
            }

            // Add all available options to the restored select
            const restoredSelect = $('#jenis_iuran_id');
            window.allAvailableIurans.forEach(item => {
                if (item.id == jenisIuranId || !restoredSelect.find(`option[value="${item.id}"]`).length) {
                    const option = `<option value="${item.id}" data-nominal="${item.jumlah}" data-nama="${item.nama}">
                        ${item.nama} (Rp ${numberFormat(item.jumlah, 0)})
                    </option>`;
                    restoredSelect.append(option);
                }
            });
        }
    }
}

// Helper function to re-bind the add connection form after DOM changes
function bindAddConnectionForm() {
    $('#addConnectionForm').off('submit').on('submit', function(e) {
        e.preventDefault();

        console.log('Form submission started');

        const formData = new FormData(this);
        console.log('Form data:', Array.from(formData.entries()));

        $.ajax({
            url: `/admin/keluarga/{{ $keluarga->id }}/iuran`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Add connection success:', response);
                if (response.success) {
                    showToast('success', response.message);

                    // Add new connection row dynamically
                    if (response.data) {
                        addConnectionRow(response.data);
                        // Update available iurans dropdown
                        updateAvailableIuransDropdown(response.data.jenis_iuran_id, 'remove');
                    } else {
                        console.error('No data in response, reloading page');
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    showToast('error', response.message || 'Terjadi kesalahan saat menambah koneksi');
                }
            },
            error: function(xhr) {
                console.log('Error response:', xhr);
                console.log('Response text:', xhr.responseText);
                console.log('Status:', xhr.status);

                let errorMessage = 'Terjadi kesalahan saat menambah koneksi';

                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON?.errors || {};
                    const errorMessages = [];

                    for (const field in errors) {
                        if (errors[field] && errors[field][0]) {
                            errorMessages.push(errors[field][0]);
                        }
                    }

                    if (errorMessages.length > 0) {
                        errorMessage = errorMessages.join('<br>');
                    }
                } else if (xhr.responseJSON?.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const responseText = JSON.parse(xhr.responseText);
                        errorMessage = responseText.message || errorMessage;
                    } catch (e) {
                        errorMessage = `Server error (${xhr.status}): ${xhr.statusText}`;
                    }
                }

                showToast('error', errorMessage);
            }
        });
    });
}

// Helper function for number formatting (mimics PHP's number_format)
function numberFormat(number, decimals) {
    return new Intl.NumberFormat('id-ID').format(number);
}
</script>
@endpush