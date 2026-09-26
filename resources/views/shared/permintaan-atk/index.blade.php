@extends('layout.app')

@section('title', 'Permintaan ATK')
@section('active_menu', 'permintaan_atk')
@section('breadcrumbs', 'Transaksi | Permintaan ATK')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Transaksi</span>
        <h1 class="hero-title">Permintaan ATK / PO</h1>
        <p class="hero-sub">Daftar Purchase Order dan permohonan pengambilan ATK.</p>
    </div>
    <div class="hero-actions">
        <button type="button" onclick="openCreateModal()" class="btn btn--primary"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Buat Permintaan ATK</button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar PO</span>
            <h2 class="card-title">Purchase Order ATK</h2>
        </div>
        <form method="GET" action="{{ route(Auth::user()->role . '.permintaan-atk.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Cari No. PO / Unit..." style="width: 200px; padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <select name="status" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Status</option>
                <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
            </select>
            <button type="submit" class="btn btn--primary" style="padding: 5px 15px;">Filter</button>
        </form>
    </div>

    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>Nomor PO</th>
                    <th>Pemohon</th>
                    <th>Unit Kerja</th>
                    <th>Tanggal</th>
                    <th>Total Item</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permintaans as $p)
                <tr>
                    <td class="cell-name">{{ $p->nomor_po }}</td>
                    <td>{{ $p->user->name ?? '-' }}</td>
                    <td>{{ $p->unit_kerja }}</td>
                    <td class="cell-date">{{ date('d M Y', strtotime($p->tanggal_permintaan)) }}</td>
                    <td><strong>{{ $p->items->count() }}</strong> jenis</td>
                    <td>
                        @if($p->status === 'DRAFT')
                            <span class="tag" style="background:var(--bg-muted);color:var(--t-muted)">DRAFT</span>
                        @elseif($p->status === 'PENDING')
                            <span class="tag t-used">PENDING</span>
                        @elseif($p->status === 'APPROVED')
                            <span class="tag t-active">APPROVED</span>
                        @elseif($p->status === 'REJECTED')
                            <span class="tag t-unavail">REJECTED</span>
                        @else
                            <span class="tag">{{ $p->status }}</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="data-cell-actions" style="justify-content: flex-end;">
                            <button type="button" onclick="openDetailModal({{ $p->id }})" class="btn--icon" title="Detail">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>

                            @if($p->status === 'DRAFT' && (Auth::user()->role === 'staff' || Auth::user()->role === 'admin'))
                                <button type="button" onclick="submitPo({{ $p->id }}, '{{ $p->nomor_po }}')" class="btn--icon" style="color:var(--success)" title="Submit">
                                    <svg viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                                </button>
                                <button type="button" onclick="openEditModal({{ $p->id }})" class="btn--icon" title="Edit">
                                    <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg>
                                </button>
                                <button type="button" onclick="deletePo({{ $p->id }}, '{{ $p->nomor_po }}')" class="btn--icon" style="color:var(--danger)" title="Hapus">
                                    <svg viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </button>
                            @endif

                            @if(Auth::user()->role === 'admin' && $p->status === 'PENDING')
                                <button type="button" onclick="approvePo({{ $p->id }}, '{{ $p->nomor_po }}')" class="btn--icon" style="color:var(--success)" title="Approve">
                                    <svg viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg>
                                </button>
                                <button type="button" onclick="rejectPo({{ $p->id }}, '{{ $p->nomor_po }}')" class="btn--icon" style="color:var(--danger)" title="Reject">
                                    <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: var(--t-muted);">
                        Tidak ada data permintaan ATK ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $permintaans->links() }}
    </div>
</section>

<style>
    .modal {
        display: none; 
        position: fixed; z-index: 1000; left: 0; top: 0;
        width: 100%; height: 100%; overflow: auto;
        background-color: rgba(0,0,0,0.5);
        align-items: center; justify-content: center;
    }
    .modal.flex { display: flex; }
    .modal-content {
        background-color: var(--bg-card, #fff);
        margin: auto; padding: 20px;
        border: 1px solid var(--border-soft);
        width: 100%; max-width: 650px;
        border-radius: 8px;
        color: var(--t-base);
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 10px; margin-bottom: 15px; }
    .modal-header h3 { margin: 0; font-size: 18px; }
    .modal-close { cursor: pointer; background: none; border: none; font-size: 20px; color: var(--t-muted); }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-size: 14px; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid var(--border-soft); border-radius: 4px; background: var(--bg-body); color: var(--t-base); }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border-soft); padding-top: 15px; margin-top: 15px; }
</style>

<!-- MODAL FORM -->
<div id="modalFormPermintaan" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalFormTitle">Buat Permintaan ATK Baru</h3>
            <button class="modal-close" onclick="closeFormModal()">&times;</button>
        </div>
        <form id="formPermintaan" onsubmit="handleFormSubmit(event)">
            <input type="hidden" id="permintaan_id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Unit Kerja / Cabang *</label>
                    <input class="input" type="text" id="unit_kerja" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Permintaan *</label>
                    <input class="input" type="date" id="tanggal_permintaan" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="form-group">
                <label>Catatan / Keterangan</label>
                <textarea class="textarea" id="catatan" rows="2"></textarea>
            </div>
            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                    <label style="margin: 0;">Daftar Item Barang ATK *</label>
                    <button type="button" onclick="addItemRow()" class="btn btn--ghost" style="padding: 2px 8px; font-size: 12px;"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Tambah</button>
                </div>
                <div id="itemsContainer" style="max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid var(--border-soft); border-radius: 4px; background: var(--bg-body);">
                    <!-- Rows injected via JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeFormModal()" class="btn btn--ghost">Batal</button>
                <button type="submit" name="submit_action" value="draft" class="btn btn--ghost" style="background:var(--bg-muted);">Simpan Draft</button>
                <button type="submit" name="submit_action" value="submit" class="btn btn--primary">Submit Langsung</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL -->
<div id="modalDetailPermintaan" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Purchase Order ATK</h3>
            <button class="modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div id="detailContent" style="font-size: 14px; line-height: 1.6;">
            <!-- Detail injected via JS -->
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeDetailModal()" class="btn btn--primary">Tutup</button>
        </div>
    </div>
</div>

<script>
    const atksMaster = @json($atks);

    function addItemRow(selectedAtkId = '', jumlah = 1) {
        const container = document.getElementById('itemsContainer');
        const rowId = Date.now() + Math.random().toString(36).substring(2, 7);
        
        let optionsHtml = '<option value="">-- Pilih Barang ATK --</option>';
        atksMaster.forEach(atk => {
            const isSel = atk.id == selectedAtkId ? 'selected' : '';
            optionsHtml += `<option value="${atk.id}" ${isSel} data-satuan="${atk.satuan}" data-stok="${atk.jumlah}">${atk.kode_atk} - ${atk.nama_atk} (Stok: ${atk.jumlah})</option>`;
        });

        const rowHtml = `
            <div class="item-row" id="row_${rowId}" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                <select name="atk_id" required class="input" style="flex:1; padding:5px;" onchange="updateItemMeta('${rowId}')">
                    ${optionsHtml}
                </select>
                <input type="number" name="jumlah_diminta" value="${jumlah}" min="1" required class="input" style="width: 70px; padding:5px;" placeholder="Qty">
                <span id="satuan_${rowId}" style="width: 50px; font-size: 12px; color: var(--t-muted);">Satuan</span>
                <button type="button" onclick="removeItemRow('${rowId}')" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:18px;">&times;</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHtml);
        updateItemMeta(rowId);
    }

    function removeItemRow(rowId) {
        const row = document.getElementById('row_' + rowId);
        if (document.querySelectorAll('.item-row').length > 1) row.remove();
        else Swal.fire('Info', 'Minimal harus ada 1 item barang yang diajukan.', 'info');
    }

    function updateItemMeta(rowId) {
        const row = document.getElementById('row_' + rowId);
        const select = row.querySelector('select[name="atk_id"]');
        const selectedOption = select.options[select.selectedIndex];
        document.getElementById('satuan_' + rowId).textContent = selectedOption && selectedOption.dataset.satuan ? selectedOption.dataset.satuan : '-';
    }

    function openCreateModal() {
        document.getElementById('modalFormTitle').textContent = 'Buat Permintaan ATK Baru';
        document.getElementById('permintaan_id').value = '';
        document.getElementById('unit_kerja').value = '';
        document.getElementById('tanggal_permintaan').value = new Date().toISOString().split('T')[0];
        document.getElementById('catatan').value = '';
        document.getElementById('itemsContainer').innerHTML = '';
        addItemRow(); 
        document.getElementById('modalFormPermintaan').classList.add('flex');
    }

    function closeFormModal() { document.getElementById('modalFormPermintaan').classList.remove('flex'); }
    function closeDetailModal() { document.getElementById('modalDetailPermintaan').classList.remove('flex'); }

    async function openEditModal(id) {
        try {
            const res = await fetch(`/${window.GAS_USER_ROLE}/permintaan-atk/${id}`);
            const data = await res.json();
            if (data.success) {
                const p = data.data;
                document.getElementById('modalFormTitle').textContent = `Edit Permintaan ATK (${p.nomor_po})`;
                document.getElementById('permintaan_id').value = p.id;
                document.getElementById('unit_kerja').value = p.unit_kerja;
                document.getElementById('tanggal_permintaan').value = p.tanggal_permintaan;
                document.getElementById('catatan').value = p.catatan || '';
                
                document.getElementById('itemsContainer').innerHTML = '';
                p.items.forEach(item => addItemRow(item.atk_id, item.jumlah_diminta));
                document.getElementById('modalFormPermintaan').classList.add('flex');
            }
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat data permintaan.', 'error');
        }
    }

    async function openDetailModal(id) {
        try {
            const res = await fetch(`/${window.GAS_USER_ROLE}/permintaan-atk/${id}`);
            const data = await res.json();
            if (data.success) {
                const p = data.data;
                let itemsRows = '';
                p.items.forEach((item, idx) => {
                    itemsRows += `
                        <tr style="border-bottom: 1px solid var(--border-soft);">
                            <td style="padding: 5px 0;">${idx + 1}</td>
                            <td>${item.atk ? item.atk.nama_atk : '-'}</td>
                            <td>${item.jumlah_diminta} ${item.atk ? item.atk.satuan : ''}</td>
                            <td style="color:var(--success); font-weight:bold;">${item.jumlah_disetujui !== null ? item.jumlah_disetujui : '-'}</td>
                        </tr>
                    `;
                });

                document.getElementById('detailContent').innerHTML = `
                    <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div><strong>Nomor PO:</strong> ${p.nomor_po}</div>
                        <div><strong>Status:</strong> ${p.status}</div>
                        <div><strong>Pemohon:</strong> ${p.user ? p.user.name : '-'}</div>
                        <div><strong>Unit Kerja:</strong> ${p.unit_kerja}</div>
                        <div><strong>Tanggal:</strong> ${p.tanggal_permintaan}</div>
                        <div><strong>Disetujui Oleh:</strong> ${p.approver ? p.approver.name : '-'}</div>
                    </div>
                    ${p.alasan_reject ? `<div style="padding: 10px; background: #fee2e2; color: #b91c1c; border-radius: 4px; margin-bottom: 15px;"><strong>Alasan Penolakan:</strong> ${p.alasan_reject}</div>` : ''}
                    <div>
                        <h4 style="margin-bottom: 10px;">Rincian Barang yang Diminta:</h4>
                        <table style="width: 100%; text-align: left; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--border-soft);">
                                    <th style="padding-bottom: 5px;">#</th><th>Nama Barang</th><th>Diminta</th><th>Disetujui</th>
                                </tr>
                            </thead>
                            <tbody>${itemsRows}</tbody>
                        </table>
                    </div>
                `;
                document.getElementById('modalDetailPermintaan').classList.add('flex');
            }
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat detail permintaan.', 'error');
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const actionType = e.submitter ? e.submitter.value : 'draft';
        const id = document.getElementById('permintaan_id').value;
        const url = id ? `/${window.GAS_USER_ROLE}/permintaan-atk/${id}` : `/${window.GAS_USER_ROLE}/permintaan-atk`;
        const method = id ? 'PUT' : 'POST';

        const itemRows = document.querySelectorAll('.item-row');
        const items = [];
        itemRows.forEach(row => {
            const atkId = row.querySelector('select[name="atk_id"]').value;
            const qty = row.querySelector('input[name="jumlah_diminta"]').value;
            if (atkId && qty) items.push({ atk_id: atkId, jumlah_diminta: qty });
        });

        if (items.length === 0) return Swal.fire('Perhatian', 'Pilih minimal satu barang ATK.', 'warning');

        const payload = {
            unit_kerja: document.getElementById('unit_kerja').value,
            tanggal_permintaan: document.getElementById('tanggal_permintaan').value,
            catatan: document.getElementById('catatan').value,
            action: actionType,
            items: items,
            _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch(url, { method: method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
            const data = await res.json();
            if (data.success) {
                closeFormModal();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false }).then(() => location.reload());
            } else Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
        } catch (err) {
            Swal.fire('Error', 'Terjadi gangguan.', 'error');
        }
    }

    function submitPo(id, nomorPo) {
        Swal.fire({
            title: 'Kirim Permintaan?', text: `Kirim ${nomorPo} untuk ditinjau?`, icon: 'question',
            showCancelButton: true, confirmButtonText: 'Ya, Kirim', cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/${window.GAS_USER_ROLE}/permintaan-atk/${id}/submit`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ _token: '{{ csrf_token() }}' }) });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) { Swal.fire('Error', 'Terjadi kesalahan.', 'error'); }
            }
        });
    }

    function deletePo(id, nomorPo) {
        Swal.fire({
            title: 'Hapus?', text: `Hapus ${nomorPo}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Ya, Hapus'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/${window.GAS_USER_ROLE}/permintaan-atk/${id}`, { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ _token: '{{ csrf_token() }}' }) });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) { Swal.fire('Error', 'Terjadi kesalahan.', 'error'); }
            }
        });
    }

    function approvePo(id, nomorPo) {
        Swal.fire({
            title: 'Setujui?', text: `Setujui ${nomorPo}?`, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Setujui'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/admin/permintaan-atk/${id}/approve`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ _token: '{{ csrf_token() }}' }) });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) { Swal.fire('Error', 'Terjadi kesalahan.', 'error'); }
            }
        });
    }

    function rejectPo(id, nomorPo) {
        Swal.fire({
            title: 'Tolak?', text: `Alasan penolakan untuk ${nomorPo}:`, input: 'textarea', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Tolak'
        }).then(async (result) => {
            if (result.isConfirmed && result.value) {
                try {
                    const res = await fetch(`/admin/permintaan-atk/${id}/reject`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ alasan_reject: result.value, _token: '{{ csrf_token() }}' }) });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) { Swal.fire('Error', 'Terjadi kesalahan.', 'error'); }
            }
        });
    }
</script>
@endsection
