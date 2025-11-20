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

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Existing Connections -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Koneksi Iuran Aktif</h6>
        </div>
        <div class="card-body">
            @forelse($connections as $connection)
            <div class="border rounded p-3 mb-3 connection-item" data-id="{{ $connection->jenis_iuran_id }}">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h6 class="mb-1">{{ $connection->jenisIuran->nama }}</h6>
                        <small class="text-muted">Kode: {{ $connection->jenisIuran->kode }}</small>
                        <br><small class="badge bg-info">{{ $connection->jenisIuran->periode_label }}</small>
                    </div>
                    <div class="col-md-2">
                        <div>
                            <small class="text-muted">Nominal Default:</small><br>
                            <strong>Rp {{ number_format($connection->jenisIuran->jumlah, 0) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div>
                            <small class="text-muted">Nominal Custom:</small><br>
                            @if($connection->nominal_custom)
                                <span class="text-warning">Rp {{ number_format($connection->nominal_custom, 0) }}</span>
                            @else
                                <span class="text-muted">Default</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div>
                            <small class="text-muted">Status:</small><br>
                            <span class="badge {{ $connection->status_aktif ? 'bg-success' : 'bg-secondary' }}">
                                {{ $connection->status_aktif ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-1 text-end">
                        <div class="btn-group" role="group">
                            <button type="button"
                                    class="btn btn-sm btn-warning"
                                    onclick="editConnection({{ $connection->jenis_iuran_id }})"
                                    title="Edit Koneksi">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-danger"
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
                            <select class="form-select" id="jenis_iuran_id" name="jenis_iuran_id" required>
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
                            <button type="submit" class="btn btn-primary">
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add connection form
    $('#addConnectionForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const jenisIuranId = $('#jenis_iuran_id').val();

        $.ajax({
            url: `/admin/keluarga/${{ $keluarga->id }}/iuran`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Gagal menambah koneksi: ' + response.message);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMessage = 'Terjadi kesalahan saat menambah koneksi';

                for (const field in errors) {
                    errorMessage += '\n' + errors[field][0];
                    break;
                }

                alert(errorMessage);
            }
        });
    });

    // Edit connection form
    $('#editConnectionForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const jenisIuranId = $('#edit_jenis_iuran_id').val();

        formData.append('_method', 'PUT');

        $.ajax({
            url: `/admin/keluarga/${{ $keluarga->id }}/iuran/${jenisIuranId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Gagal memperbarui koneksi: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat memperbarui koneksi');
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
            url: `/admin/keluarga/${{ $keluarga->id }}/iuran/${jenisIuranId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus koneksi: ' + response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'Terjadi kesalahan saat menghapus koneksi');
            }
        });
    });
});

function editConnection(jenisIuranId) {
    const connectionElement = $(`.connection-item[data-id="${jenisIuranId}"]`);

    // Get connection data from the page (in real app, you might want to fetch via AJAX)
    // For now, we'll populate with basic info
    const jenisNama = connectionElement.find('h6').text().trim();
    const nominalDefault = connectionElement.find('.col-md-2 strong').text();

    $('#edit_jenis_iuran_id').val(jenisIuranId);
    $('#edit_nama_jenis').val(jenisNama);
    $('#edit_status_aktif').prop('checked', connectionElement.find('.badge-success').length > 0);

    $('#editModal').modal('show');
}

function deleteConnection(jenisIuranId) {
    const connectionElement = $(`.connection-item[data-id="${jenisIuranId}"]`);
    const jenisNama = connectionElement.find('h6').text().trim();

    $('#delete_jenis_iuran_id').val(jenisIuranId);
    $('#delete_nama_jenis').text(jenisNama);

    $('#deleteModal').modal('show');
}
</script>
@endpush