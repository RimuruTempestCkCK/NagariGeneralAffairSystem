@extends('layout.app')

@section('title', 'Laporan Keamanan')
@section('active_menu', 'keamanan')
@section('breadcrumbs', 'Keamanan | Laporan Keamanan Harian')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Keamanan</span>
        <h1 class="hero-title">Laporan Keamanan Harian</h1>
        <p class="hero-sub">Pencatatan kondisi keamanan dan aktivitas patroli di berbagai lokasi.</p>
    </div>
    <div class="hero-actions">
        <button type="button" onclick="openFormModal()" class="btn btn-primary">
            + Catat Laporan
        </button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Riwayat Laporan Keamanan</h2>
        </div>
        <form method="GET" action="{{ route('keamanan.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <select name="lokasi_pengamanan" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Lokasi</option>
                <option value="Kantor Pusat" {{ request('lokasi_pengamanan') === 'Kantor Pusat' ? 'selected' : '' }}>Kantor Pusat</option>
                <option value="Kantor Cabang" {{ request('lokasi_pengamanan') === 'Kantor Cabang' ? 'selected' : '' }}>Kantor Cabang</option>
                <option value="Unit Kerja / KCP / Kas" {{ request('lokasi_pengamanan') === 'Unit Kerja / KCP / Kas' ? 'selected' : '' }}>Unit Kerja / KCP / Kas</option>
            </select>
            <select name="kondisi_keamanan" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Kondisi</option>
                <option value="Aman Kondusif" {{ request('kondisi_keamanan') === 'Aman Kondusif' ? 'selected' : '' }}>Aman Kondusif</option>
                <option value="Insiden / Masalah" {{ request('kondisi_keamanan') === 'Insiden / Masalah' ? 'selected' : '' }}>Insiden / Masalah</option>
            </select>
            <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <button type="submit" class="btn btn-primary" style="padding: 5px 15px;">Filter</button>
        </form>
    </div>

    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Shift / Petugas</th>
                    <th>Kondisi</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $p)
                <tr>
                    <td class="cell-date">{{ date('d M Y', strtotime($p->tanggal_laporan)) }}</td>
                    <td class="cell-name">
                        {{ $p->nama_lokasi }}
                        <div style="font-size: 12px; color: var(--t-muted);">{{ $p->lokasi_pengamanan }}</div>
                    </td>
                    <td>
                        <span class="tag" style="background:var(--bg-muted)">{{ $p->shift }}</span>
                        <span style="margin-left: 8px;">{{ $p->petugas }}</span>
                    </td>
                    <td>
                        @if($p->kondisi_keamanan === 'Aman Kondusif')
                            <span class="tag t-active">Aman Kondusif</span>
                        @else
                            <span class="tag t-unavail">Insiden / Masalah</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="data-cell-actions" style="justify-content: flex-end;">
                            <button type="button" onclick="openDetailModal({{ $p->id }})" class="btn--icon" title="Detail">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button type="button" onclick="openFormModal({{ $p->id }})" class="btn--icon" title="Edit">
                                <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg>
                            </button>
                            <button type="button" onclick="deleteData({{ $p->id }}, 'laporan keamanan di {{ $p->nama_lokasi }}')" class="btn--icon" style="color:var(--danger)" title="Hapus">
                                <svg viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--t-muted);">
                        Tidak ada laporan keamanan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $laporans->links() }}
    </div>
</section>

<style>
    .modal {
        display: none; position: fixed; z-index: 1000; left: 0; top: 0;
        width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);
        align-items: center; justify-content: center;
    }
    .modal.flex { display: flex; }
    .modal-content {
        background-color: var(--bg-card, #fff); margin: auto; padding: 20px;
        border: 1px solid var(--border-soft); width: 100%; max-width: 600px;
        border-radius: 8px; color: var(--t-base);
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 10px; margin-bottom: 15px; }
    .modal-header h3 { margin: 0; font-size: 18px; }
    .modal-close { cursor: pointer; background: none; border: none; font-size: 20px; color: var(--t-muted); }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-size: 14px; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid var(--border-soft); border-radius: 4px; background: var(--bg-body); color: var(--t-base); }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border-soft); padding-top: 15px; margin-top: 15px; }
</style>

<div id="modalForm" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalFormTitle">Catat Laporan Keamanan</h3>
            <button class="modal-close" onclick="closeFormModal()">&times;</button>
        </div>
        <form id="formData" onsubmit="handleFormSubmit(event)">
            <input type="hidden" id="keamanan_id" name="id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Jenis Lokasi *</label>
                    <select id="lokasi_pengamanan" required>
                        <option value="Kantor Pusat">Kantor Pusat</option>
                        <option value="Kantor Cabang">Kantor Cabang</option>
                        <option value="Unit Kerja / KCP / Kas">Unit Kerja / KCP / Kas</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Lokasi *</label>
                    <input type="text" id="nama_lokasi" required placeholder="Cth: Cabang Padang">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Tanggal *</label>
                    <input type="date" id="tanggal_laporan" required>
                </div>
                <div class="form-group">
                    <label>Shift *</label>
                    <select id="shift" required>
                        <option value="Pagi">Pagi</option>
                        <option value="Siang">Siang</option>
                        <option value="Malam">Malam</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Petugas *</label>
                    <input type="text" id="petugas" required>
                </div>
            </div>
            <div class="form-group">
                <label>Kondisi Keamanan *</label>
                <select id="kondisi_keamanan" required>
                    <option value="Aman Kondusif">Aman Kondusif</option>
                    <option value="Insiden / Masalah">Insiden / Masalah</option>
                </select>
            </div>
            <div class="form-group">
                <label>Uraian Kegiatan / Laporan *</label>
                <textarea id="uraian_kegiatan" required rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Tindakan Lanjutan (Jika Ada Insiden)</label>
                <textarea id="tindakan_lanjutan" rows="2"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeFormModal()" class="btn btn--ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Laporan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDetail" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Laporan Keamanan</h3>
            <button class="modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div id="detailContent" style="font-size: 14px; line-height: 1.6;">
            <!-- Injected via JS -->
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeDetailModal()" class="btn btn-primary">Tutup</button>
        </div>
    </div>
</div>

<script>
    function openFormModal(id = null) {
        if (!id) {
            document.getElementById('modalFormTitle').textContent = 'Catat Laporan Keamanan';
            document.getElementById('formData').reset();
            document.getElementById('keamanan_id').value = '';
            document.getElementById('tanggal_laporan').value = new Date().toISOString().split('T')[0];
            document.getElementById('modalForm').classList.add('flex');
        } else {
            document.getElementById('modalFormTitle').textContent = 'Edit Laporan Keamanan';
            fetch(`/${window.GAS_USER_ROLE}/keamanan/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        document.getElementById('keamanan_id').value = p.id;
                        document.getElementById('lokasi_pengamanan').value = p.lokasi_pengamanan;
                        document.getElementById('nama_lokasi').value = p.nama_lokasi;
                        document.getElementById('tanggal_laporan').value = p.tanggal_laporan;
                        document.getElementById('shift').value = p.shift;
                        document.getElementById('petugas').value = p.petugas;
                        document.getElementById('kondisi_keamanan').value = p.kondisi_keamanan;
                        document.getElementById('uraian_kegiatan').value = p.uraian_kegiatan;
                        document.getElementById('tindakan_lanjutan').value = p.tindakan_lanjutan || '';
                        document.getElementById('modalForm').classList.add('flex');
                    }
                });
        }
    }

    function closeFormModal() { document.getElementById('modalForm').classList.remove('flex'); }
    function closeDetailModal() { document.getElementById('modalDetail').classList.remove('flex'); }

    function openDetailModal(id) {
        fetch(`/${window.GAS_USER_ROLE}/keamanan/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const p = data.data;
                    const statusClass = p.kondisi_keamanan === 'Aman Kondusif' ? 'color: var(--success); font-weight: bold;' : 'color: var(--danger); font-weight: bold;';

                    document.getElementById('detailContent').innerHTML = `
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <p><strong>Lokasi:</strong> <span style="text-transform:uppercase">${p.nama_lokasi}</span> (${p.lokasi_pengamanan})</p>
                            <p><strong>Tanggal:</strong> ${p.tanggal_laporan}</p>
                            <p><strong>Kondisi:</strong> <span style="${statusClass}">${p.kondisi_keamanan}</span></p>
                            <p><strong>Shift / Petugas:</strong> ${p.shift} - ${p.petugas}</p>
                        </div>
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <p><strong>Uraian Kegiatan:</strong><br>${p.uraian_kegiatan.replace(/\n/g, '<br>')}</p>
                        </div>
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px;">
                            <p><strong>Tindakan Lanjutan:</strong><br>${(p.tindakan_lanjutan || '-').replace(/\n/g, '<br>')}</p>
                        </div>
                    `;
                    document.getElementById('modalDetail').classList.add('flex');
                }
            });
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('keamanan_id').value;
        const url = id ? `/${window.GAS_USER_ROLE}/keamanan/${id}` : `/${window.GAS_USER_ROLE}/keamanan`;
        const method = id ? 'PUT' : 'POST';

        const payload = {
            lokasi_pengamanan: document.getElementById('lokasi_pengamanan').value,
            nama_lokasi: document.getElementById('nama_lokasi').value,
            tanggal_laporan: document.getElementById('tanggal_laporan').value,
            shift: document.getElementById('shift').value,
            petugas: document.getElementById('petugas').value,
            kondisi_keamanan: document.getElementById('kondisi_keamanan').value,
            uraian_kegiatan: document.getElementById('uraian_kegiatan').value,
            tindakan_lanjutan: document.getElementById('tindakan_lanjutan').value,
            _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch(url, { method: method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
            const data = await res.json();
            if (data.success) {
                closeFormModal();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false }).then(() => location.reload());
            } else Swal.fire('Gagal', data.message || 'Periksa kembali form.', 'error');
        } catch (err) { Swal.fire('Error', 'Terjadi gangguan.', 'error'); }
    }

    function deleteData(id, desc) {
        Swal.fire({
            title: 'Hapus Laporan?', text: `Yakin hapus ${desc}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Ya, Hapus!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/${window.GAS_USER_ROLE}/keamanan/${id}`, { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ _token: '{{ csrf_token() }}' }) });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) { Swal.fire('Error', 'Terjadi kesalahan.', 'error'); }
            }
        });
    }
</script>
@endsection
