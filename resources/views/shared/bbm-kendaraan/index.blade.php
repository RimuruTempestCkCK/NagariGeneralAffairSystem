@extends('layout.app')

@section('title', 'BBM Kendaraan')
@section('active_menu', 'bbm_kendaraan')
@section('breadcrumbs', 'Menu Utama | Pencatatan BBM')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Operasional</span>
        <h1 class="hero-title">BBM Kendaraan</h1>
        <p class="hero-sub">Pencatatan konsumsi bahan bakar kendaraan.</p>
    </div>
    <div class="hero-actions">
        <button type="button" onclick="openFormModal()" class="btn btn-primary">
            + Catat BBM
        </button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Riwayat Pembelian BBM</h2>
        </div>
        <form method="GET" action="{{ route('bbm-kendaraan.index') }}" style="display: flex; gap: 10px; align-items: center;">
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
                    <th>Jenis BBM</th>
                    <th>Liter / Harga</th>
                    <th>Total Biaya</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bbms as $p)
                <tr>
                    <td class="cell-date">{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                    <td class="cell-name">{{ $p->kendaraan->nomor_kendaraan }}</td>
                    <td>{{ $p->jenis_bbm }}</td>
                    <td>
                        {{ number_format($p->liter, 2, ',', '.') }} L
                        <div style="font-size: 12px; color: var(--t-muted);">@ Rp {{ number_format($p->harga_per_liter, 0, ',', '.') }}</div>
                    </td>
                    <td style="font-weight: bold; color: var(--accent);">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                    <td style="text-align: right;">
                        <div class="data-cell-actions" style="justify-content: flex-end;">
                            <button type="button" onclick="openFormModal({{ $p->id }})" class="btn--icon" title="Edit">
                                <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg>
                            </button>
                            <button type="button" onclick="deleteData({{ $p->id }}, 'pembelian BBM untuk {{ $p->kendaraan->nomor_kendaraan }}')" class="btn--icon" style="color:var(--danger)" title="Hapus">
                                <svg viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: var(--t-muted);">
                        Tidak ada data BBM.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $bbms->links() }}
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
            <h3 id="modalFormTitle">Catat BBM</h3>
            <button class="modal-close" onclick="closeFormModal()">&times;</button>
        </div>
        <form id="formData" onsubmit="handleFormSubmit(event)">
            <input type="hidden" id="bbm_id" name="id">
            <div class="form-group">
                <label>Kendaraan *</label>
                <select id="kendaraan_id" required>
                    <option value="">Pilih Kendaraan...</option>
                    @foreach($kendaraans as $k)
                    <option value="{{ $k->id }}">{{ $k->nomor_kendaraan }} - {{ $k->jenis_kendaraan }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Tanggal Pembelian *</label>
                    <input type="date" id="tanggal" required>
                </div>
                <div class="form-group">
                    <label>Jenis BBM *</label>
                    <input type="text" id="jenis_bbm" required>
                </div>
                <div class="form-group">
                    <label>Jumlah (Liter) *</label>
                    <input type="number" id="liter" required step="0.01" min="0.1" oninput="calculateTotal()">
                </div>
                <div class="form-group">
                    <label>Harga per Liter (Rp) *</label>
                    <input type="number" id="harga_per_liter" required min="0" oninput="calculateTotal()">
                </div>
            </div>
            <div class="form-group">
                <label>Total Biaya (Otomatis)</label>
                <input type="text" id="total_biaya" readonly style="background: var(--bg-muted); font-weight: bold;">
            </div>
            <div class="form-group">
                <label>Catatan Tambahan</label>
                <textarea id="catatan" rows="2"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeFormModal()" class="btn btn--ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan BBM</button>
            </div>
        </form>
    </div>
</div>

<script>
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }

    function calculateTotal() {
        const liter = parseFloat(document.getElementById('liter').value) || 0;
        const harga = parseFloat(document.getElementById('harga_per_liter').value) || 0;
        const total = liter * harga;
        document.getElementById('total_biaya').value = formatRupiah(total);
    }

    function openFormModal(id = null) {
        if (!id) {
            document.getElementById('modalFormTitle').textContent = 'Catat Pembelian BBM';
            document.getElementById('formData').reset();
            document.getElementById('bbm_id').value = '';
            document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
            document.getElementById('total_biaya').value = 'Rp 0';
            document.getElementById('modalForm').classList.add('flex');
        } else {
            document.getElementById('modalFormTitle').textContent = 'Edit Data BBM';
            fetch(`/bbm-kendaraan/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        document.getElementById('bbm_id').value = p.id;
                        document.getElementById('kendaraan_id').value = p.kendaraan_id;
                        document.getElementById('tanggal').value = p.tanggal;
                        document.getElementById('jenis_bbm').value = p.jenis_bbm;
                        document.getElementById('liter').value = p.liter;
                        document.getElementById('harga_per_liter').value = p.harga_per_liter;
                        document.getElementById('catatan').value = p.catatan || '';
                        calculateTotal();
                        document.getElementById('modalForm').classList.add('flex');
                    }
                });
        }
    }

    function closeFormModal() { document.getElementById('modalForm').classList.remove('flex'); }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('bbm_id').value;
        const url = id ? `/bbm-kendaraan/${id}` : '/bbm-kendaraan';
        const method = id ? 'PUT' : 'POST';

        const payload = {
            kendaraan_id: document.getElementById('kendaraan_id').value,
            tanggal: document.getElementById('tanggal').value,
            jenis_bbm: document.getElementById('jenis_bbm').value,
            liter: document.getElementById('liter').value,
            harga_per_liter: document.getElementById('harga_per_liter').value,
            catatan: document.getElementById('catatan').value,
            _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch(url, { method: method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
            const data = await res.json();
            if (data.success) {
                closeFormModal();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false }).then(() => location.reload());
            } else Swal.fire('Gagal', data.message || 'Periksa kembali isian form.', 'error');
        } catch (err) { Swal.fire('Error', 'Terjadi gangguan.', 'error'); }
    }

    function deleteData(id, desc) {
        Swal.fire({
            title: 'Hapus Data?', text: `Yakin hapus ${desc}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Ya, Hapus!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/bbm-kendaraan/${id}`, { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ _token: '{{ csrf_token() }}' }) });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) { Swal.fire('Error', 'Terjadi kesalahan.', 'error'); }
            }
        });
    }
</script>
@endsection
