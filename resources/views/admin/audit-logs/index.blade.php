@extends('layout.app')

@section('title', 'Audit Trail')

@section('content')
<div class="row gap-20 masonry pos-r">
    <div class="masonry-sizer col-md-6"></div>
    <div class="masonry-item w-100">
        <div class="row gap-20">
            <div class="col-md-12">
                <div class="layers bd bgc-white p-20">
                    <div class="layer w-100 mB-10">
                        <h4 class="lh-1">Audit Trail</h4>
                    </div>

                    <!-- Filter Form -->
                    <div class="layer w-100 mB-20">
                        <form action="{{ route('audit-logs.index') }}" method="GET" class="row align-items-center">
                            <div class="col-md-3 mb-2">
                                <label for="tanggal_mulai">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="tanggal_akhir">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="user_id">User</label>
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">Semua User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="module">Module</label>
                                <select name="module" id="module" class="form-control">
                                    <option value="">Semua Module</option>
                                    <option value="Auth" {{ request('module') == 'Auth' ? 'selected' : '' }}>Auth</option>
                                    <option value="Aset" {{ request('module') == 'Aset' ? 'selected' : '' }}>Aset</option>
                                    <option value="ATK" {{ request('module') == 'ATK' ? 'selected' : '' }}>ATK</option>
                                    <option value="Kendaraan" {{ request('module') == 'Kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                                    <option value="PO" {{ request('module') == 'PO' ? 'selected' : '' }}>PO</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>

                    <div class="layer w-100">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tanggal/Waktu</th>
                                        <th>User</th>
                                        <th>Module</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ $log->user ? $log->user->name : 'Sistem/Dihapus' }}</td>
                                        <td>{{ $log->module }}</td>
                                        <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                                        <td>{{ $log->description }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info btn-view" data-id="{{ $log->id }}">
                                                <i class="ti-eye"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7">Tidak ada data audit log.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Detail -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Detail Audit Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr><th width="30%">User</th><td id="detailUser"></td></tr>
                    <tr><th>Waktu</th><td id="detailTime"></td></tr>
                    <tr><th>Module</th><td id="detailModule"></td></tr>
                    <tr><th>Action</th><td id="detailAction"></td></tr>
                    <tr><th>Description</th><td id="detailDescription"></td></tr>
                    <tr><th>IP Address</th><td id="detailIp"></td></tr>
                    <tr><th>User Agent</th><td id="detailAgent"></td></tr>
                    <tr><th>Subject</th><td id="detailSubject"></td></tr>
                </table>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Old Values</h6>
                        <pre id="detailOldValues" class="bg-light p-2" style="font-size: 12px; white-space: pre-wrap; word-wrap: break-word;"></pre>
                    </div>
                    <div class="col-md-6">
                        <h6>New Values</h6>
                        <pre id="detailNewValues" class="bg-light p-2" style="font-size: 12px; white-space: pre-wrap; word-wrap: break-word;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewButtons = document.querySelectorAll('.btn-view');
    const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));

    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            fetch(`/admin/audit-logs/${id}`)
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const log = data.data;
                        document.getElementById('detailUser').innerText = log.user ? log.user.name : 'Unknown';
                        document.getElementById('detailTime').innerText = new Date(log.created_at).toLocaleString('id-ID');
                        document.getElementById('detailModule').innerText = log.module;
                        document.getElementById('detailAction').innerText = log.action;
                        document.getElementById('detailDescription').innerText = log.description;
                        document.getElementById('detailIp').innerText = log.ip_address;
                        document.getElementById('detailAgent').innerText = log.user_agent;
                        document.getElementById('detailSubject').innerText = log.subject_type ? (log.subject_type + ' (ID: ' + log.subject_id + ')') : '-';
                        
                        document.getElementById('detailOldValues').innerText = log.old_values_pretty || '-';
                        document.getElementById('detailNewValues').innerText = log.new_values_pretty || '-';

                        viewModal.show();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Gagal memuat detail log.', 'error');
                });
        });
    });
});
</script>
@endpush
