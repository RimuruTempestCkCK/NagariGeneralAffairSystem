@extends('layout.app')

@section('title', 'Master Kendaraan')
@section('active_menu', 'kendaraan')
@section('breadcrumbs', 'Menu Utama | Master Kendaraan')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Master Data</span>
        <h1 class="hero-title">Kendaraan Operasional</h1>
        <p class="hero-sub">Manajemen data kendaraan inventaris perusahaan.</p>
    </div>
    <div class="hero-actions">
        <button type="button" onclick="openFormModal()" class="btn btn-primary">
            + Tambah Kendaraan
        </button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Data Kendaraan</h2>
        </div>
        <form method="GET" action="{{ route(Auth::user()->role . '.kendaraan.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Cari Plat Nomor / Jenis..." style="width: 200px; padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <select name="status_kendaraan" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Status</option>
                <option value="Milik" {{ request('status_kendaraan') === 'Milik' ? 'selected' : '' }}>Milik</option>
                <option value="Sewa" {{ request('status_kendaraan') === 'Sewa' ? 'selected' : '' }}>Sewa</option>
            </select>
            <select name="kondisi" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Kondisi</option>
                <option value="Aktif" {{ request('kondisi') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Servis" {{ request('kondisi') === 'Servis' ? 'selected' : '' }}>Servis</option>
                <option value="Rusak" {{ request('kondisi') === 'Rusak' ? 'selected' : '' }}>Rusak</option>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 5px 15px;">Filter</button>
        </form>
    </div>

    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>Nomor Kendaraan</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Kondisi</th>
                    <th>Jatuh Tempo STNK</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kendaraans as $k)
                @php
                    $diffDays = now()->diffInDays(\Carbon\Carbon::parse($k->jatuh_tempo_stnk), false);
                    $stnkWarning = false;
                    $stnkDanger = false;
                    if ($diffDays < 0) {
                        $stnkDanger = true;
                    } elseif ($diffDays <= 30) {
                        $stnkWarning = true;
                    }
                @endphp
                <tr>
                    <td class="cell-name">{{ $k->nomor_kendaraan }}</td>
                    <td>{{ $k->jenis_kendaraan }} <span style="font-size: 12px; color: var(--t-muted);">({{ $k->tahun_kendaraan }})</span></td>
                    <td>{{ $k->status_kendaraan }}</td>
                    <td>
                        @if($k->kondisi === 'Aktif')
                            <span class="tag t-active">Aktif</span>
                        @elseif($k->kondisi === 'Servis')
                            <span class="tag t-used">Servis</span>
                        @else
                            <span class="tag t-unavail">Rusak</span>
                        @endif
                    </td>
                    <td class="cell-date">
                        @if($stnkDanger)
                            <span class="tag t-unavail">Expired ({{ date('d/m/Y', strtotime($k->jatuh_tempo_stnk)) }})</span>
                        @elseif($stnkWarning)
                            <span class="tag t-used">Segera Habis ({{ date('d/m/Y', strtotime($k->jatuh_tempo_stnk)) }})</span>
                        @else
                            <span>{{ date('d/m/Y', strtotime($k->jatuh_tempo_stnk)) }}</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <div class="data-cell-actions" style="justify-content: flex-end;">
                            <button type="button" onclick="openDetailModal({{ $k->id }})" class="btn--icon" aria-label="View" title="Detail">
                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <button type="button" onclick="openFormModal({{ $k->id }})" class="btn--icon" aria-label="Edit" title="Edit">
                                <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg>
                            </button>
                            <button type="button" onclick="deleteData({{ $k->id }}, '{{ $k->nomor_kendaraan }}')" class="btn--icon" aria-label="Delete" title="Hapus">
                                <svg viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px; color: var(--t-muted);">
                        Tidak ada data kendaraan operasional.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 15px;">
        {{ $kendaraans->links() }}
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
        width: 100%; max-width: 600px;
        border-radius: 8px;
        color: var(--t-base);
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 10px; margin-bottom: 15px; }
    .modal-header h3 { margin: 0; font-size: 18px; }
    .modal-close { cursor: pointer; background: none; border: none; font-size: 20px; color: var(--t-muted); }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-size: 14px; }
    .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid var(--border-soft); border-radius: 4px; background: var(--bg-body); color: var(--t-base); }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid var(--border-soft); padding-top: 15px; margin-top: 15px; }
</style>

<!-- MODAL FORM -->
<div id="modalForm" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalFormTitle">Tambah Kendaraan</h3>
            <button class="modal-close" onclick="closeFormModal()">&times;</button>
        </div>
        <form id="formData" onsubmit="handleFormSubmit(event)">
            <input type="hidden" id="kendaraan_id" name="id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Plat Nomor Kendaraan *</label>
                    <input type="text" id="nomor_kendaraan" required style="text-transform: uppercase;">
                </div>
                <div class="form-group">
                    <label>Jenis Kendaraan *</label>
                    <input type="text" id="jenis_kendaraan" required>
                </div>
                <div class="form-group">
                    <label>Tahun Pembuatan *</label>
                    <input type="number" id="tahun_kendaraan" required min="1900" max="2030">
                </div>
                <div class="form-group">
                    <label>Status Kepemilikan *</label>
                    <select id="status_kendaraan" required>
                        <option value="Milik">Milik (Aset)</option>
                        <option value="Sewa">Sewa (Rental)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nomor BPKB</label>
                    <input type="text" id="nomor_bpkb">
                </div>
                <div class="form-group">
                    <label>Nomor STNK</label>
                    <input type="text" id="nomor_stnk">
                </div>
                <div class="form-group">
                    <label>Tanggal Jatuh Tempo STNK *</label>
                    <input type="date" id="jatuh_tempo_stnk" required>
                </div>
                <div class="form-group">
                    <label>Kondisi Kendaraan *</label>
                    <select id="kondisi" required>
                        <option value="Aktif">Aktif beroperasi</option>
                        <option value="Servis">Sedang diservis</option>
                        <option value="Rusak">Rusak berat / Tidak aktif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeFormModal()" class="btn btn--ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kendaraan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL -->
<div id="modalDetail" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Detail Kendaraan</h3>
            <button class="modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div id="detailContent" style="line-height: 1.6; font-size: 14px;">
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
            document.getElementById('modalFormTitle').textContent = 'Tambah Kendaraan Operasional';
            document.getElementById('formData').reset();
            document.getElementById('kendaraan_id').value = '';
            document.getElementById('modalForm').classList.add('flex');
        } else {
            document.getElementById('modalFormTitle').textContent = 'Edit Kendaraan Operasional';
            fetch(`/${window.GAS_USER_ROLE}/kendaraan/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const k = data.data;
                        document.getElementById('kendaraan_id').value = k.id;
                        document.getElementById('nomor_kendaraan').value = k.nomor_kendaraan;
                        document.getElementById('jenis_kendaraan').value = k.jenis_kendaraan;
                        document.getElementById('tahun_kendaraan').value = k.tahun_kendaraan;
                        document.getElementById('status_kendaraan').value = k.status_kendaraan;
                        document.getElementById('nomor_bpkb').value = k.nomor_bpkb || '';
                        document.getElementById('nomor_stnk').value = k.nomor_stnk || '';
                        document.getElementById('jatuh_tempo_stnk').value = k.jatuh_tempo_stnk;
                        document.getElementById('kondisi').value = k.kondisi;
                        
                        document.getElementById('modalForm').classList.add('flex');
                    }
                });
        }
    }

    function closeFormModal() {
        document.getElementById('modalForm').classList.remove('flex');
    }

    function closeDetailModal() {
        document.getElementById('modalDetail').classList.remove('flex');
    }

    function openDetailModal(id) {
        fetch(`/${window.GAS_USER_ROLE}/kendaraan/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const k = data.data;
                    document.getElementById('detailContent').innerHTML = `
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <p><strong>Nomor Kendaraan:</strong> <span style="text-transform: uppercase;">${k.nomor_kendaraan}</span></p>
                            <p><strong>Status Kepemilikan:</strong> ${k.status_kendaraan}</p>
                            <p><strong>Jenis Kendaraan:</strong> ${k.jenis_kendaraan}</p>
                            <p><strong>Tahun:</strong> ${k.tahun_kendaraan}</p>
                            <p><strong>Kondisi:</strong> ${k.kondisi}</p>
                        </div>
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px;">
                            <p><strong>Warning STNK:</strong> ${k.stnk_status_text}</p>
                            <p><strong>No. STNK:</strong> ${k.nomor_stnk || '-'}</p>
                            <p><strong>Jatuh Tempo STNK:</strong> ${k.jatuh_tempo_stnk}</p>
                            <p><strong>No. BPKB:</strong> ${k.nomor_bpkb || '-'}</p>
                        </div>
                    `;
                    document.getElementById('modalDetail').classList.add('flex');
                }
            });
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('kendaraan_id').value;
        const url = id ? `/${window.GAS_USER_ROLE}/kendaraan/${id}` : `/${window.GAS_USER_ROLE}/kendaraan`;
        const method = id ? 'PUT' : 'POST';

        const payload = {
            nomor_kendaraan: document.getElementById('nomor_kendaraan').value.toUpperCase(),
            jenis_kendaraan: document.getElementById('jenis_kendaraan').value,
            tahun_kendaraan: document.getElementById('tahun_kendaraan').value,
            status_kendaraan: document.getElementById('status_kendaraan').value,
            nomor_bpkb: document.getElementById('nomor_bpkb').value,
            nomor_stnk: document.getElementById('nomor_stnk').value,
            jatuh_tempo_stnk: document.getElementById('jatuh_tempo_stnk').value,
            kondisi: document.getElementById('kondisi').value,
            _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                closeFormModal();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false }).then(() => location.reload());
            } else {
                Swal.fire('Gagal', data.message || 'Periksa kembali isian form.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Terjadi gangguan.', 'error');
        }
    }

    function deleteData(id, nomor_kendaraan) {
        Swal.fire({
            title: 'Hapus Kendaraan?',
            text: `Yakin hapus ${nomor_kendaraan}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Ya, Hapus!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/${window.GAS_USER_ROLE}/kendaraan/${id}`, {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}' })
                    });
                    const data = await res.json();
                    if (data.success) location.reload();
                    else Swal.fire('Gagal', data.message, 'error');
                } catch (e) {
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                }
            }
        });
    }
</script>
@endsection
