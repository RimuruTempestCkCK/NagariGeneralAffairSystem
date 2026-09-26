@extends('layout.app')

@section('title', 'Pemakaian ATK')
@section('active_menu', 'pemakaian_atk')
@section('breadcrumbs', 'Transaksi | Pemakaian ATK')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Transaksi</span>
        <h1 class="hero-title">Pemakaian ATK & Pembebanan</h1>
        <p class="hero-sub">Catatan Pemakaian ATK & Jurnal Pembebanan Biaya.</p>
    </div>
    <div class="hero-actions">
        <button type="button" onclick="openCreateModal()" class="btn btn-primary">
            + Catat Pemakaian ATK
        </button>
    </div>
</section>

<!-- Metric Cards -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
    <div class="card" style="padding: 20px;">
        <span style="font-size: 12px; color: var(--t-muted); text-transform: uppercase;">Total Pemakaian Bulan Ini</span>
        <h3 style="margin-top: 10px; font-size: 24px;">{{ number_format($totalItemDipakaiBulanIni) }} Unit</h3>
    </div>
    <div class="card" style="padding: 20px;">
        <span style="font-size: 12px; color: var(--t-muted); text-transform: uppercase;">Total Beban Biaya Bulan Ini</span>
        <h3 style="margin-top: 10px; font-size: 24px; color: var(--danger);">Rp {{ number_format($totalBebanBulanIni, 0, ',', '.') }}</h3>
    </div>
</div>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Riwayat Pemakaian ATK</h2>
        </div>
        <form method="GET" action="{{ route('pemakaian-atk.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Cari Unit / No. Jurnal / ATK..." style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft); width: 250px;">
            <button type="submit" class="btn btn-primary" style="padding: 5px 15px;">Cari</button>
        </form>
    </div>

    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Unit Kerja</th>
                    <th>Barang ATK</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total Beban</th>
                    <th>Jurnal Pembebanan</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemakaians as $pem)
                <tr>
                    <td class="cell-date">{{ date('d M Y', strtotime($pem->tanggal)) }}</td>
                    <td class="cell-name">{{ $pem->unit_kerja }}</td>
                    <td>
                        <strong>{{ $pem->atk->nama_atk ?? '-' }}</strong>
                        <div style="font-size: 12px; color: var(--t-muted);">{{ $pem->atk->kode_atk ?? '' }}</div>
                    </td>
                    <td style="color: var(--danger); font-weight: bold;">-{{ number_format($pem->jumlah) }} {{ $pem->atk->satuan ?? 'PCS' }}</td>
                    <td>Rp {{ number_format($pem->harga_satuan, 0, ',', '.') }}</td>
                    <td style="font-weight: bold;">Rp {{ number_format($pem->total_beban_biaya, 0, ',', '.') }}</td>
                    <td>
                        @if($pem->no_jurnal_beban)
                            <span class="tag t-active">{{ $pem->no_jurnal_beban }}</span>
                        @else
                            <span style="font-size: 12px; color: var(--warning); font-style: italic;">Belum dibukukan</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="data-cell-actions" style="justify-content: flex-end;">
                            <button type="button" onclick="openDetailModal({{ $pem->id }})" class="btn--icon" title="Detail">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            @if(Auth::user()->role === 'admin')
                                <button type="button" onclick="openJurnalModal({{ $pem->id }}, '{{ $pem->no_jurnal_beban ?? '' }}')" class="btn--icon" style="color: var(--accent);" title="Set Jurnal">
                                    <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: var(--t-muted);">
                        Belum ada riwayat pemakaian ATK.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $pemakaians->links() }}
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

<div id="modalFormPemakaian" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Catat Pemakaian ATK</h3>
            <button class="modal-close" onclick="closeFormModal()">&times;</button>
        </div>
        <form id="formPemakaian" onsubmit="handlePemakaianSubmit(event)">
            <div class="form-group">
                <label>Pilih Barang ATK *</label>
                <select id="atk_id" required onchange="autoFillPriceAndStock()">
                    <option value="">-- Pilih Barang ATK --</option>
                    @foreach($atks as $atk)
                        <option value="{{ $atk->id }}" data-harga="{{ $atk->harga }}" data-satuan="{{ $atk->satuan }}" data-stok="{{ $atk->jumlah }}">
                            {{ $atk->kode_atk }} - {{ $atk->nama_atk }} (Tersedia: {{ $atk->jumlah }} {{ $atk->satuan }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Unit Kerja / Cabang *</label>
                    <input type="text" id="unit_kerja" required placeholder="Contoh: Operasional Pusat">
                </div>
                <div class="form-group">
                    <label>Tanggal Pemakaian *</label>
                    <input type="date" id="tanggal" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label>Jumlah Pemakaian *</label>
                    <input type="number" id="jumlah" min="1" required placeholder="Contoh: 5" oninput="calculateTotal()">
                </div>
                <div class="form-group">
                    <label>Harga Satuan Berlaku</label>
                    <input type="text" id="harga_satuan_label" readonly style="background: var(--bg-muted);" value="Rp 0">
                </div>
            </div>
            <div style="background: var(--bg-muted); padding: 10px; border-radius: 4px; display: flex; justify-content: space-between; margin-bottom: 15px;">
                <span>Estimasi Beban Biaya:</span>
                <strong style="color: var(--danger);" id="previewBeban">Rp 0</strong>
            </div>
            <div class="form-group">
                <label>No. Jurnal Pembebanan Akhir Bulan</label>
                <input type="text" id="no_jurnal_beban" placeholder="Opsional">
            </div>
            <div class="form-group">
                <label>Keperluan Pemakaian</label>
                <textarea id="keperluan" rows="2" placeholder="Keperluan operasional..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeFormModal()" class="btn btn--ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan & Potong Stok</button>
            </div>
        </form>
    </div>
</div>

<div id="modalDetailPemakaian" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Catatan Pemakaian ATK</h3>
            <button class="modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div id="detailPemakaianContent" style="font-size: 14px; line-height: 1.6;">
            <!-- Injected via JS -->
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeDetailModal()" class="btn btn-primary">Tutup</button>
        </div>
    </div>
</div>

<script>
    let currentSelectedPrice = 0;
    let currentAvailableStock = 0;

    function autoFillPriceAndStock() {
        const select = document.getElementById('atk_id');
        const opt = select.options[select.selectedIndex];
        if (opt && opt.dataset.harga) {
            currentSelectedPrice = parseFloat(opt.dataset.harga) || 0;
            currentAvailableStock = parseInt(opt.dataset.stok) || 0;
            document.getElementById('harga_satuan_label').value = 'Rp ' + currentSelectedPrice.toLocaleString('id-ID');
        } else {
            currentSelectedPrice = 0;
            currentAvailableStock = 0;
            document.getElementById('harga_satuan_label').value = 'Rp 0';
        }
        calculateTotal();
    }

    function calculateTotal() {
        const qty = parseFloat(document.getElementById('jumlah').value) || 0;
        const total = qty * currentSelectedPrice;
        document.getElementById('previewBeban').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    function openCreateModal() {
        document.getElementById('atk_id').value = '';
        document.getElementById('unit_kerja').value = '';
        document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
        document.getElementById('jumlah').value = '';
        document.getElementById('harga_satuan_label').value = 'Rp 0';
        document.getElementById('previewBeban').textContent = 'Rp 0';
        document.getElementById('no_jurnal_beban').value = '';
        document.getElementById('keperluan').value = '';
        document.getElementById('modalFormPemakaian').classList.add('flex');
    }

    function closeFormModal() { document.getElementById('modalFormPemakaian').classList.remove('flex'); }
    function closeDetailModal() { document.getElementById('modalDetailPemakaian').classList.remove('flex'); }

    async function openDetailModal(id) {
        try {
            const res = await fetch(`/pemakaian-atk/${id}`);
            const data = await res.json();
            if (!data.success) return Swal.fire('Error', data.message, 'error');

            const p = data.data;
            document.getElementById('detailPemakaianContent').innerHTML = `
                <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div><strong>Unit Kerja:</strong> <br>${p.unit_kerja}</div>
                    <div><strong>Tanggal:</strong> <br>${p.tanggal}</div>
                    <div><strong>Barang ATK:</strong> <br>${p.atk ? p.atk.nama_atk : '-'}</div>
                    <div><strong>Jumlah:</strong> <br><span style="color:var(--danger)">-${p.jumlah} ${p.atk ? p.atk.satuan : ''}</span></div>
                    <div><strong>Harga Satuan:</strong> <br>Rp ${parseInt(p.harga_satuan).toLocaleString('id-ID')}</div>
                    <div><strong>Total Beban:</strong> <br><strong style="color:var(--danger)">Rp ${parseInt(p.total_beban_biaya).toLocaleString('id-ID')}</strong></div>
                </div>
                <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <p><strong>Jurnal Pembebanan:</strong> <span style="font-family: monospace; color: var(--accent);">${p.no_jurnal_beban || 'Belum Dibukukan'}</span></p>
                    <p><strong>Petugas Input:</strong> ${p.user ? p.user.name : '-'}</p>
                </div>
                <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px;">
                    <p><strong>Keperluan:</strong><br>${p.keperluan || '-'}</p>
                </div>
            `;
            document.getElementById('modalDetailPemakaian').classList.add('flex');
        } catch (e) { Swal.fire('Error', 'Gagal memuat detail pemakaian.', 'error'); }
    }

    function openJurnalModal(id, currentJurnal) {
        Swal.fire({
            title: 'Update No. Jurnal', text: 'Masukkan nomor jurnal pembebanan akhir bulan:', input: 'text', inputValue: currentJurnal,
            showCancelButton: true, confirmButtonText: 'Simpan Jurnal',
            inputValidator: (val) => { if (!val) return 'Tidak boleh kosong!'; }
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/admin/pemakaian-atk/${id}/jurnal`, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ no_jurnal_beban: result.value, _token: '{{ csrf_token() }}' }) });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) { Swal.fire('Error', 'Terjadi kesalahan.', 'error'); }
            }
        });
    }

    async function handlePemakaianSubmit(e) {
        e.preventDefault();
        const qty = parseInt(document.getElementById('jumlah').value) || 0;
        if (qty > currentAvailableStock) return Swal.fire({ icon: 'error', title: 'Stok Tidak Mencukupi!', text: `Jumlah (${qty}) melebihi stok (${currentAvailableStock}).` });

        const payload = {
            atk_id: document.getElementById('atk_id').value, unit_kerja: document.getElementById('unit_kerja').value,
            tanggal: document.getElementById('tanggal').value, jumlah: qty, no_jurnal_beban: document.getElementById('no_jurnal_beban').value,
            keperluan: document.getElementById('keperluan').value, _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch('/pemakaian-atk', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
            const data = await res.json();
            if (data.success) location.reload();
            else Swal.fire('Gagal', data.message, 'error');
        } catch (err) { Swal.fire('Error', 'Terjadi gangguan.', 'error'); }
    }
</script>
@endsection
