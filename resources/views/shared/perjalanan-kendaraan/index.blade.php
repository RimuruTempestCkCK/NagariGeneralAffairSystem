@extends('layout.app')

@section('title', 'Perjalanan Kendaraan')
@section('active_menu', 'perjalanan_kendaraan')
@section('breadcrumbs', 'Operasional | Perjalanan Kendaraan')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Operasional</span>
        <h1 class="hero-title">Perjalanan Kendaraan</h1>
        <p class="hero-sub">Pencatatan tujuan dan jarak tempuh kendaraan dinas operasional.</p>
    </div>
    <div class="hero-actions">
        <button type="button" onclick="openFormModal()" class="btn btn-primary">
            + Catat Perjalanan
        </button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Riwayat Perjalanan Kendaraan</h2>
        </div>
        <form method="GET" action="{{ route(Auth::user()->role . '.perjalanan-kendaraan.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <select name="kendaraan_id" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Kendaraan</option>
                @foreach($kendaraans as $k)
                <option value="{{ $k->id }}" {{ request('kendaraan_id') == $k->id ? 'selected' : '' }}>{{ $k->nomor_kendaraan }}</option>
                @endforeach
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
                    <th>Kendaraan</th>
                    <th>Tujuan</th>
                    <th>Jarak Tempuh</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($perjalanans as $p)
                <tr>
                    <td class="cell-date">{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                    <td class="cell-name">{{ $p->kendaraan->nomor_kendaraan }}</td>
                    <td>{{ $p->tujuan }}</td>
                    <td>
                        <strong style="color: var(--accent);">{{ number_format($p->jarak_tempuh, 0, ',', '.') }} Km</strong>
                        <div style="font-size: 12px; color: var(--t-muted);">{{ $p->kilometer_awal }} - {{ $p->kilometer_akhir }}</div>
                    </td>
                    <td style="text-align: right;">
                        <div class="data-cell-actions" style="justify-content: flex-end;">
                            <button type="button" onclick="openFormModal({{ $p->id }})" class="btn--icon" title="Edit">
                                <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg>
                            </button>
                            <button type="button" onclick="deleteData({{ $p->id }}, 'perjalanan ke {{ $p->tujuan }}')" class="btn--icon" style="color:var(--danger)" title="Hapus">
                                <svg viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--t-muted);">
                        Tidak ada data perjalanan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $perjalanans->links() }}
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
        border: 1px solid var(--border-soft); width: 100%; max-width: 500px;
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
            <h3 id="modalFormTitle">Catat Perjalanan</h3>
            <button class="modal-close" onclick="closeFormModal()">&times;</button>
        </div>
        <form id="formData" onsubmit="handleFormSubmit(event)">
            <input type="hidden" id="perjalanan_id" name="id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Kendaraan *</label>
                    <select id="kendaraan_id" required>
                        <option value="">Pilih Kendaraan...</option>
                        @foreach($kendaraans as $k)
                        <option value="{{ $k->id }}">{{ $k->nomor_kendaraan }} - {{ $k->jenis_kendaraan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Perjalanan *</label>
                    <input type="date" id="tanggal" required>
                </div>
                <div class="form-group">
                    <label>Kilometer Awal *</label>
                    <input type="number" id="kilometer_awal" required min="0" oninput="calculateJarak()">
                </div>
                <div class="form-group">
                    <label>Kilometer Akhir *</label>
                    <input type="number" id="kilometer_akhir" required min="0" oninput="calculateJarak()">
                </div>
            </div>
            <div class="form-group">
                <label>Jarak Tempuh (Otomatis)</label>
                <input type="text" id="jarak_tempuh" readonly style="background: var(--bg-muted); font-weight: bold;">
            </div>
            <div class="form-group">
                <label>Tujuan *</label>
                <input type="text" id="tujuan" required>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea id="keterangan" rows="2"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeFormModal()" class="btn btn--ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perjalanan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateJarak() {
        const awal = parseInt(document.getElementById('kilometer_awal').value) || 0;
        const akhir = parseInt(document.getElementById('kilometer_akhir').value) || 0;
        document.getElementById('jarak_tempuh').value = (akhir >= awal ? akhir - awal : 0) + ' Km';
    }

    function openFormModal(id = null) {
        if (!id) {
            document.getElementById('modalFormTitle').textContent = 'Catat Perjalanan';
            document.getElementById('formData').reset();
            document.getElementById('perjalanan_id').value = '';
            document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
            document.getElementById('modalForm').classList.add('flex');
        } else {
            document.getElementById('modalFormTitle').textContent = 'Edit Perjalanan';
            fetch(`/${window.GAS_USER_ROLE}/perjalanan-kendaraan/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        document.getElementById('perjalanan_id').value = p.id;
                        document.getElementById('kendaraan_id').value = p.kendaraan_id;
                        document.getElementById('tanggal').value = p.tanggal;
                        document.getElementById('kilometer_awal').value = p.kilometer_awal;
                        document.getElementById('kilometer_akhir').value = p.kilometer_akhir;
                        document.getElementById('tujuan').value = p.tujuan;
                        document.getElementById('keterangan').value = p.keterangan || '';
                        calculateJarak();
                        document.getElementById('modalForm').classList.add('flex');
                    }
                });
        }
    }

    function closeFormModal() { document.getElementById('modalForm').classList.remove('flex'); }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const awal = parseInt(document.getElementById('kilometer_awal').value) || 0;
        const akhir = parseInt(document.getElementById('kilometer_akhir').value) || 0;
        if (akhir < awal) return Swal.fire('Validasi', 'Kilometer akhir harus lebih besar atau sama dengan awal!', 'warning');

        const id = document.getElementById('perjalanan_id').value;
        const url = id ? `/${window.GAS_USER_ROLE}/perjalanan-kendaraan/${id}` : `/${window.GAS_USER_ROLE}/perjalanan-kendaraan`;
        const method = id ? 'PUT' : 'POST';

        const payload = {
            kendaraan_id: document.getElementById('kendaraan_id').value, tanggal: document.getElementById('tanggal').value,
            kilometer_awal: awal, kilometer_akhir: akhir, tujuan: document.getElementById('tujuan').value,
            keterangan: document.getElementById('keterangan').value, _token: '{{ csrf_token() }}'
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
            title: 'Hapus Data?', text: `Yakin hapus ${desc}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Ya, Hapus!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/${window.GAS_USER_ROLE}/perjalanan-kendaraan/${id}`, { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ _token: '{{ csrf_token() }}' }) });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) { Swal.fire('Error', 'Terjadi kesalahan.', 'error'); }
            }
        });
    }
</script>
@endsection
