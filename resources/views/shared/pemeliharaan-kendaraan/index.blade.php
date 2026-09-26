@extends('layout.app')

@section('title', 'Pemeliharaan Kendaraan')
@section('active_menu', 'pemeliharaan_kendaraan')
@section('breadcrumbs', 'Operasional | Pemeliharaan Kendaraan')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Operasional</span>
        <h1 class="hero-title">Pemeliharaan Kendaraan</h1>
        <p class="hero-sub">Pencatatan biaya service dan perbaikan kendaraan operasional.</p>
    </div>
    <div class="hero-actions">
        <button type="button" onclick="openFormModal()" class="btn btn--primary"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Catat Pemeliharaan</button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Riwayat Pemeliharaan Kendaraan</h2>
        </div>
        <form method="GET" action="{{ route(Auth::user()->role . '.pemeliharaan-kendaraan.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <select name="kendaraan_id" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Kendaraan</option>
                @foreach($kendaraans as $k)
                <option value="{{ $k->id }}" {{ request('kendaraan_id') == $k->id ? 'selected' : '' }}>{{ $k->nomor_kendaraan }}</option>
                @endforeach
            </select>
            <select name="jenis_perbaikan" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Jenis</option>
                <option value="Ban" {{ request('jenis_perbaikan') === 'Ban' ? 'selected' : '' }}>Ban</option>
                <option value="Sparepart" {{ request('jenis_perbaikan') === 'Sparepart' ? 'selected' : '' }}>Sparepart</option>
                <option value="Service" {{ request('jenis_perbaikan') === 'Service' ? 'selected' : '' }}>Service Rutin</option>
                <option value="Perbaikan" {{ request('jenis_perbaikan') === 'Perbaikan' ? 'selected' : '' }}>Perbaikan Lainnya</option>
            </select>
            <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <button type="submit" class="btn btn--primary" style="padding: 5px 15px;">Filter</button>
        </form>
    </div>

    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kendaraan</th>
                    <th>Jenis</th>
                    <th>Total Biaya</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemeliharaans as $p)
                <tr>
                    <td class="cell-date">{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                    <td class="cell-name">{{ $p->kendaraan->nomor_kendaraan }}</td>
                    <td>
                        <span class="tag t-active">{{ $p->jenis_perbaikan }}</span>
                        @if($p->bengkel)
                            <div style="font-size: 12px; color: var(--t-muted); margin-top: 4px;">{{ $p->bengkel }}</div>
                        @endif
                    </td>
                    <td style="font-weight: bold; color: var(--accent);">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                    <td style="text-align: right;">
                        <div class="data-cell-actions" style="justify-content: flex-end;">
                            <button type="button" onclick="openDetailModal({{ $p->id }})" class="btn--icon" title="Detail">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button type="button" onclick="openFormModal({{ $p->id }})" class="btn--icon" title="Edit">
                                <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg>
                            </button>
                            <button type="button" onclick="deleteData({{ $p->id }}, 'pemeliharaan untuk {{ $p->kendaraan->nomor_kendaraan }}')" class="btn--icon" style="color:var(--danger)" title="Hapus">
                                <svg viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: var(--t-muted);">
                        Tidak ada data pemeliharaan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $pemeliharaans->links() }}
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
            <h3 id="modalFormTitle">Catat Pemeliharaan Kendaraan</h3>
            <button class="modal-close" onclick="closeFormModal()">&times;</button>
        </div>
        <form id="formData" onsubmit="handleFormSubmit(event)">
            <input type="hidden" id="pemeliharaan_id" name="id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Kendaraan *</label>
                    <select class="select" id="kendaraan_id" required>
                        <option value="">Pilih Kendaraan...</option>
                        @foreach($kendaraans as $k)
                        <option value="{{ $k->id }}">{{ $k->nomor_kendaraan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Pemeliharaan *</label>
                    <input class="input" type="date" id="tanggal" required>
                </div>
                <div class="form-group">
                    <label>Jenis Pemeliharaan *</label>
                    <select class="select" id="jenis_perbaikan" required>
                        <option value="">Pilih Jenis...</option>
                        <option value="Ban">Penggantian Ban</option>
                        <option value="Sparepart">Penggantian Sparepart</option>
                        <option value="Service">Service Rutin</option>
                        <option value="Perbaikan">Perbaikan Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Bengkel</label>
                    <input class="input" type="text" id="bengkel">
                </div>
            </div>
            <div class="form-group">
                <label>Rincian Onderdil / Sparepart</label>
                <textarea class="textarea" id="onderdil" rows="2"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Biaya Onderdil (Rp) *</label>
                    <input class="input" type="number" id="harga_onderdil" required min="0" value="0" oninput="calculateTotal()">
                </div>
                <div class="form-group">
                    <label>Biaya Jasa (Rp) *</label>
                    <input class="input" type="number" id="biaya_jasa" required min="0" value="0" oninput="calculateTotal()">
                </div>
                <div class="form-group">
                    <label>Total Biaya (Otomatis)</label>
                    <input class="input" type="text" id="total_biaya" readonly style="background: var(--bg-muted); font-weight: bold;" value="Rp 0">
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan Tambahan</label>
                <textarea class="textarea" id="catatan" rows="2"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeFormModal()" class="btn btn--ghost">Batal</button>
                <button type="submit" class="btn btn--primary">Simpan Pemeliharaan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDetail" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Pemeliharaan</h3>
            <button class="modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div id="detailContent" style="font-size: 14px; line-height: 1.6;">
            <!-- Injected via JS -->
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeDetailModal()" class="btn btn--primary">Tutup</button>
        </div>
    </div>
</div>

<script>
    function formatRupiah(angka) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka); }

    function calculateTotal() {
        const onderdil = parseFloat(document.getElementById('harga_onderdil').value) || 0;
        const jasa = parseFloat(document.getElementById('biaya_jasa').value) || 0;
        document.getElementById('total_biaya').value = formatRupiah(onderdil + jasa);
    }

    function openFormModal(id = null) {
        if (!id) {
            document.getElementById('modalFormTitle').textContent = 'Catat Pemeliharaan Kendaraan';
            document.getElementById('formData').reset();
            document.getElementById('pemeliharaan_id').value = '';
            document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
            document.getElementById('harga_onderdil').value = 0;
            document.getElementById('biaya_jasa').value = 0;
            document.getElementById('total_biaya').value = 'Rp 0';
            document.getElementById('modalForm').classList.add('flex');
        } else {
            document.getElementById('modalFormTitle').textContent = 'Edit Data Pemeliharaan';
            fetch(`/${window.GAS_USER_ROLE}/pemeliharaan-kendaraan/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        document.getElementById('pemeliharaan_id').value = p.id;
                        document.getElementById('kendaraan_id').value = p.kendaraan_id;
                        document.getElementById('tanggal').value = p.tanggal;
                        document.getElementById('jenis_perbaikan').value = p.jenis_perbaikan;
                        document.getElementById('bengkel').value = p.bengkel || '';
                        document.getElementById('onderdil').value = p.onderdil || '';
                        document.getElementById('harga_onderdil').value = p.harga_onderdil;
                        document.getElementById('biaya_jasa').value = p.biaya_jasa;
                        document.getElementById('catatan').value = p.catatan || '';
                        calculateTotal();
                        document.getElementById('modalForm').classList.add('flex');
                    }
                });
        }
    }

    function closeFormModal() { document.getElementById('modalForm').classList.remove('flex'); }
    function closeDetailModal() { document.getElementById('modalDetail').classList.remove('flex'); }

    function openDetailModal(id) {
        fetch(`/${window.GAS_USER_ROLE}/pemeliharaan-kendaraan/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const p = data.data;
                    document.getElementById('detailContent').innerHTML = `
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div style="grid-column: 1 / -1;"><strong>Kendaraan:</strong> <span style="text-transform:uppercase">${p.kendaraan.nomor_kendaraan}</span></div>
                            <div><strong>Tanggal:</strong> ${p.tanggal}</div>
                            <div><strong>Jenis:</strong> <span class="tag t-active">${p.jenis_perbaikan}</span></div>
                            <div><strong>Bengkel:</strong> ${p.bengkel || '-'}</div>
                            <div><strong>Onderdil:</strong> ${p.onderdil || '-'}</div>
                        </div>
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div><strong>Biaya Onderdil:</strong><br>${formatRupiah(p.harga_onderdil)}</div>
                            <div><strong>Biaya Jasa:</strong><br>${formatRupiah(p.biaya_jasa)}</div>
                            <div style="grid-column: 1 / -1; margin-top: 10px;"><strong>Total Biaya:</strong> <strong style="color:var(--accent); font-size: 16px;">${formatRupiah(p.total_biaya)}</strong></div>
                        </div>
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px;">
                            <p><strong>Catatan:</strong><br>${p.catatan || '-'}</p>
                        </div>
                    `;
                    document.getElementById('modalDetail').classList.add('flex');
                }
            });
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('pemeliharaan_id').value;
        const url = id ? `/${window.GAS_USER_ROLE}/pemeliharaan-kendaraan/${id}` : `/${window.GAS_USER_ROLE}/pemeliharaan-kendaraan`;
        const method = id ? 'PUT' : 'POST';

        const payload = {
            kendaraan_id: document.getElementById('kendaraan_id').value, tanggal: document.getElementById('tanggal').value,
            jenis_perbaikan: document.getElementById('jenis_perbaikan').value, bengkel: document.getElementById('bengkel').value,
            onderdil: document.getElementById('onderdil').value, harga_onderdil: document.getElementById('harga_onderdil').value,
            biaya_jasa: document.getElementById('biaya_jasa').value, catatan: document.getElementById('catatan').value,
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
            title: 'Hapus Data?', text: `Yakin hapus ${desc}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#EF4444', confirmButtonText: 'Ya, Hapus!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/${window.GAS_USER_ROLE}/pemeliharaan-kendaraan/${id}`, { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ _token: '{{ csrf_token() }}' }) });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) { Swal.fire('Error', 'Terjadi kesalahan.', 'error'); }
            }
        });
    }
</script>
@endsection
