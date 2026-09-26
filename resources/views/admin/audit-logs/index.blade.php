@extends('layout.app')

@section('title', 'Audit Logs')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">System</span>
        <h1 class="hero-title">Audit Logs</h1>
        <p class="hero-sub">Laporan aktivitas dan riwayat sistem.</p>
    </div>
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Data Audit Log</h2>
        </div>
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <select name="user_id" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua User</option>
                @foreach( as )
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <select name="module" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Module</option>
                <option value="Auth" {{ request('module') == 'Auth' ? 'selected' : '' }}>Auth</option>
                <option value="Aset" {{ request('module') == 'Aset' ? 'selected' : '' }}>Aset</option>
                <option value="ATK" {{ request('module') == 'ATK' ? 'selected' : '' }}>ATK</option>
                <option value="Kendaraan" {{ request('module') == 'Kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                <option value="PO" {{ request('module') == 'PO' ? 'selected' : '' }}>PO</option>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 5px 15px;">Filter</button>
            @if(request()->anyFilled(['user_id', 'module']))
                <a href="{{ route('admin.audit-logs.index') }}" style="color: var(--t-muted); font-size: 14px;">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-scroll">
        <table class="table">
            <thead>
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
                @forelse($logs as )
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->user ? ->user->name : 'Sistem/Dihapus' }}</td>
                    <td>{{ $log->module }}</td>
                    <td><span class="tag {{ $log->action == 'CREATE' || ->action == 'LOGIN' ? 't-active' : (->action == 'DELETE' || ->action == 'LOGOUT' ? 't-unavail' : 't-new') }}">{{ $log->action }}</span></td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>
                        <div class="data-cell-actions">
                            <button type="button" onclick="showDetailModal({{ $log->id }})" class="btn--icon" title="Lihat Detail">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data audit log.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="padding: 20px;">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
</section>

<!-- Modal View Detail -->
<div id="viewModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h3>Detail Audit Log</h3>
            <button class="modal-close" onclick="closeModal('viewModal')">&times;</button>
        </div>
        <div style="line-height: 1.6; margin-top: 15px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
                <div><strong>User:</strong> <span id="detailUser"></span></div>
                <div><strong>Waktu:</strong> <span id="detailTime"></span></div>
                <div><strong>Module:</strong> <span id="detailModule"></span></div>
                <div><strong>Action:</strong> <span id="detailAction"></span></div>
                <div style="grid-column: span 2;"><strong>Description:</strong> <span id="detailDescription"></span></div>
                <div><strong>IP Address:</strong> <span id="detailIp"></span></div>
                <div><strong>User Agent:</strong> <span id="detailAgent"></span></div>
                <div style="grid-column: span 2;"><strong>Subject:</strong> <span id="detailSubject"></span></div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <h4 style="margin-bottom: 10px; font-size: 14px;">Old Values</h4>
                    <pre id="detailOldValues" style="background: var(--bg-muted, #f9f9f9); padding: 10px; border-radius: 4px; border: 1px solid var(--border-soft, #ddd); font-size: 12px; white-space: pre-wrap; word-wrap: break-word; min-height: 100px;"></pre>
                </div>
                <div>
                    <h4 style="margin-bottom: 10px; font-size: 14px;">New Values</h4>
                    <pre id="detailNewValues" style="background: var(--bg-muted, #f9f9f9); padding: 10px; border-radius: 4px; border: 1px solid var(--border-soft, #ddd); font-size: 12px; white-space: pre-wrap; word-wrap: break-word; min-height: 100px;"></pre>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="margin-top: 20px; display: flex; justify-content: flex-end;">
            <button type="button" class="btn btn-primary" onclick="closeModal('viewModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function showDetailModal(id) {
        fetch(/admin/audit-logs/+id)
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

                    document.getElementById('viewModal').style.display = 'flex';
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat detail log.', 'error');
            });
    }
</script>
@endsection