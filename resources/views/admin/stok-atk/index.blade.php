@extends('layout.app')

@section('title', 'Stok ATK & Jurnal BYD')
@section('active_menu', 'stok_atk')
@section('breadcrumbs', 'Inventaris | Stok ATK')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Inventaris</span>
        <h1 class="hero-title">Pengelolaan Stok Awal & Stok Masuk ATK</h1>
        <p class="hero-sub">Manajemen fisik stok, harga, dan Jurnal BYD.</p>
    </div>
    <div class="hero-actions">
        <button type="button" onclick="openCreateModal('Stok Awal')" class="btn btn--ghost" style="margin-right: 10px;">
            + Catat Stok Awal
        </button>
        <button type="button" onclick="openCreateModal('Stok Masuk')" class="btn btn-primary">
            + Tambah Stok Masuk
        </button>
    </div>
</section>

<!-- Metric Cards -->
<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
    <div class="card" style="padding: 20px;">
        <span style="font-size: 12px; color: var(--t-muted); text-transform: uppercase;">Total Jenis ATK</span>
        <h3 style="margin-top: 10px; font-size: 24px;">{{ number_format($totalItemAtk) }} Item</h3>
    </div>
    <div class="card" style="padding: 20px;">
        <span style="font-size: 12px; color: var(--t-muted); text-transform: uppercase;">Total Fisik Stok</span>
        <h3 style="margin-top: 10px; font-size: 24px; color: var(--accent);">{{ number_format($totalStokFisik) }} Unit</h3>
    </div>
    <div class="card" style="padding: 20px;">
        <span style="font-size: 12px; color: var(--t-muted); text-transform: uppercase;">Total Nilai Persediaan</span>
        <h3 style="margin-top: 10px; font-size: 24px; color: var(--success);">Rp {{ number_format($totalNilaiPersediaan, 0, ',', '.') }}</h3>
    </div>
</div>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Riwayat Transaksi Stok</h2>
        </div>
        <form method="GET" action="{{ route('stok-atk.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Cari No. Jurnal / Keterangan / ATK..." style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft); width: 250px;">
            <select name="jenis_transaksi" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Transaksi</option>
                <option value="Stok Awal" {{ request('jenis_transaksi') === 'Stok Awal' ? 'selected' : '' }}>Stok Awal</option>
                <option value="Stok Masuk" {{ request('jenis_transaksi') === 'Stok Masuk' ? 'selected' : '' }}>Stok Masuk</option>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 5px 15px;">Filter</button>
        </form>
    </div>

    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis Transaksi</th>
                    <th>Barang ATK</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                    <th>No. Jurnal (BYD)</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiStok as $stok)
                <tr>
                    <td class="cell-date">{{ date('d M Y', strtotime($stok->tanggal)) }}</td>
                    <td>
                        @if($stok->jenis_transaksi === 'Stok Awal')
                            <span class="tag t-unavail">Stok Awal</span>
                        @else
                            <span class="tag t-active">Stok Masuk</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $stok->atk->nama_atk ?? '-' }}</strong>
                        <div style="font-size: 12px; color: var(--t-muted);">{{ $stok->atk->kode_atk ?? '' }}</div>
                    </td>
                    <td style="color: var(--success); font-weight: bold;">+{{ number_format($stok->jumlah) }} {{ $stok->atk->satuan ?? 'PCS' }}</td>
                    <td>
                        Rp {{ number_format($stok->harga_satuan, 0, ',', '.') }}
                        @if($stok->harga_sebelumnya && $stok->harga_sebelumnya != $stok->harga_satuan)
                            <div style="font-size: 11px; color: var(--warning);">(Lama: Rp {{ number_format($stok->harga_sebelumnya, 0, ',', '.') }})</div>
                        @endif
                    </td>
                    <td style="font-weight: bold;">Rp {{ number_format($stok->total_harga, 0, ',', '.') }}</td>
                    <td style="font-family: monospace; color: var(--t-muted);">{{ $stok->no_jurnal ?? '-' }}</td>
                    <td style="text-align: right;">
                        <div class="data-cell-actions" style="justify-content: flex-end;">
                            <button type="button" onclick="openDetailModal({{ $stok->id }})" class="btn--icon" title="Detail">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: var(--t-muted);">
                        Belum ada riwayat transaksi stok ATK.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $transaksiStok->links() }}
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

<div id="modalFormStok" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalStokTitle">Tambah Stok Masuk ATK</h3>
            <button class="modal-close" onclick="closeFormModal()">&times;</button>
        </div>
        <form id="formStok" onsubmit="handleStokSubmit(event)">
            <input type="hidden" id="jenis_transaksi" name="jenis_transaksi" value="Stok Masuk">
            <div class="form-group">
                <label>Pilih Barang ATK *</label>
                <select id="atk_id" required onchange="autoFillAtkPrice()">
                    <option value="">-- Pilih Barang ATK --</option>
                    @foreach($atks as $atk)
                        <option value="{{ $atk->id }}" data-harga="{{ $atk->harga }}" data-satuan="{{ $atk->satuan }}" data-stok="{{ $atk->jumlah }}">
                            {{ $atk->kode_atk }} - {{ $atk->nama_atk }} (Stok: {{ $atk->jumlah }} {{ $atk->satuan }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Jumlah Masuk *</label>
                    <input type="number" id="jumlah" min="1" required placeholder="Contoh: 50" oninput="calculateTotal()">
                </div>
                <div class="form-group">
                    <label>Harga Satuan (Rp) *</label>
                    <input type="number" id="harga_satuan" min="0" required placeholder="0" oninput="calculateTotal()">
                </div>
            </div>
            <div style="background: var(--bg-muted); padding: 10px; border-radius: 4px; display: flex; justify-content: space-between; margin-bottom: 15px;">
                <span>Estimasi Total Pembukuan:</span>
                <strong style="color: var(--accent);" id="previewTotal">Rp 0</strong>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Tanggal Transaksi *</label>
                    <input type="date" id="tanggal" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label>No. Jurnal Pembukuan BYD</label>
                    <input type="text" id="no_jurnal" placeholder="Contoh: JRN-BYD-2026-001">
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan / Faktur</label>
                <textarea id="keterangan" rows="2" placeholder="Catatan..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeFormModal()" class="btn btn--ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDetailStok" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Riwayat Transaksi Stok</h3>
            <button class="modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div id="detailStokContent" style="font-size: 14px; line-height: 1.6;">
            <!-- Injected via JS -->
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeDetailModal()" class="btn btn-primary">Tutup</button>
        </div>
    </div>
</div>

<script>
    function autoFillAtkPrice() {
        const select = document.getElementById('atk_id');
        const opt = select.options[select.selectedIndex];
        if (opt && opt.dataset.harga) {
            document.getElementById('harga_satuan').value = opt.dataset.harga;
        }
        calculateTotal();
    }

    function calculateTotal() {
        const qty = parseFloat(document.getElementById('jumlah').value) || 0;
        const harga = parseFloat(document.getElementById('harga_satuan').value) || 0;
        document.getElementById('previewTotal').textContent = 'Rp ' + (qty * harga).toLocaleString('id-ID');
    }

    function openCreateModal(jenis) {
        document.getElementById('modalStokTitle').textContent = `Catat ${jenis} ATK`;
        document.getElementById('jenis_transaksi').value = jenis;
        document.getElementById('atk_id').value = '';
        document.getElementById('jumlah').value = '';
        document.getElementById('harga_satuan').value = '';
        document.getElementById('no_jurnal').value = '';
        document.getElementById('keterangan').value = '';
        document.getElementById('previewTotal').textContent = 'Rp 0';
        document.getElementById('modalFormStok').classList.add('flex');
    }

    function closeFormModal() { document.getElementById('modalFormStok').classList.remove('flex'); }
    function closeDetailModal() { document.getElementById('modalDetailStok').classList.remove('flex'); }

    async function openDetailModal(id) {
        try {
            const res = await fetch(`/stok-atk/${id}`);
            const data = await res.json();
            if (!data.success) return Swal.fire('Error', data.message, 'error');

            const s = data.data;
            document.getElementById('detailStokContent').innerHTML = `
                <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div style="grid-column: 1 / -1;"><strong>Jenis:</strong> <span class="tag t-active">${s.jenis_transaksi}</span></div>
                    <div style="grid-column: 1 / -1;"><strong>Barang:</strong> ${s.atk ? s.atk.nama_atk : '-'}</div>
                    <div><strong>Jumlah Masuk:</strong> <br><span style="color:var(--success)">+${s.jumlah} ${s.atk ? s.atk.satuan : ''}</span></div>
                    <div><strong>Tanggal:</strong> <br>${s.tanggal}</div>
                </div>
                <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div><strong>Harga Satuan:</strong><br>Rp ${parseInt(s.harga_satuan).toLocaleString('id-ID')}</div>
                    <div><strong>Harga Sebelumnya:</strong><br>${s.harga_sebelumnya ? 'Rp ' + parseInt(s.harga_sebelumnya).toLocaleString('id-ID') : '-'}</div>
                    <div style="grid-column: 1 / -1; margin-top: 10px;"><strong>Total Nilai Transaksi:</strong> <strong style="color:var(--success); font-size: 16px;">Rp ${parseInt(s.total_harga).toLocaleString('id-ID')}</strong></div>
                </div>
                <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px;">
                    <p><strong>No. Jurnal Rekening BYD:</strong> <span style="font-family: monospace;">${s.no_jurnal || '-'}</span></p>
                    <p><strong>Petugas Input:</strong> ${s.user ? s.user.name : '-'}</p>
                    <p><strong>Keterangan:</strong><br>${s.keterangan || '-'}</p>
                </div>
            `;
            document.getElementById('modalDetailStok').classList.add('flex');
        } catch (e) { Swal.fire('Error', 'Gagal memuat detail transaksi.', 'error'); }
    }

    async function handleStokSubmit(e) {
        e.preventDefault();
        const payload = {
            atk_id: document.getElementById('atk_id').value, jenis_transaksi: document.getElementById('jenis_transaksi').value,
            tanggal: document.getElementById('tanggal').value, jumlah: document.getElementById('jumlah').value,
            harga_satuan: document.getElementById('harga_satuan').value, no_jurnal: document.getElementById('no_jurnal').value,
            keterangan: document.getElementById('keterangan').value, _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch('/admin/stok-atk', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
            const data = await res.json();
            if (data.success) location.reload();
            else Swal.fire('Gagal', data.message, 'error');
        } catch (err) { Swal.fire('Error', 'Terjadi gangguan.', 'error'); }
    }
</script>
@endsection
