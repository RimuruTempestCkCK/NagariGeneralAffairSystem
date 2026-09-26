@extends('layout.app')

@section('title', 'Master ATK')
@section('active_menu', 'atk')
@section('breadcrumbs', 'Menu Utama | Master ATK')

@section('content')
<section class="hero">
    <div class="hero-text">
        <span class="eyebrow">Master Data</span>
        <h1 class="hero-title">Alat Tulis Kantor (ATK)</h1>
        <p class="hero-sub">Manajemen inventaris alat tulis kantor.</p>
    </div>
    <div class="hero-actions">
        <a href="{{ route('admin.atk.scan') }}" class="btn btn--ghost">
            Scan QR
        </a>
        @if(Auth::user()->role === 'admin')
            <button type="button" onclick="openCreateModal()" class="btn btn-primary">
                + Tambah ATK
            </button>
        @endif
        <button type="button" onclick="submitBulkPrint()" class="btn btn--ghost">
            Bulk Print QR
        </button>
    </div>
</section>

<section class="card col-12">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar</span>
            <h2 class="card-title">Data ATK</h2>
        </div>
        <form action="{{ route('atk.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Cari kode/nama ATK..." style="width: 200px; padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
            <select name="jenis_atk" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Jenis</option>
                @foreach($jenisList as $j)
                    <option value="{{ $j }}" {{ request('jenis_atk') == $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()" class="input" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-soft);">
                <option value="">Semua Status</option>
                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 5px 15px;">Filter</button>
            @if(request()->anyFilled(['search', 'jenis_atk', 'status']))
                <a href="{{ route('atk.index') }}" style="color: var(--t-muted); font-size: 14px;">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-scroll">
        <form id="bulkPrintForm" action="{{ route('atk.bulk-print') }}" method="GET" target="_blank">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                        <th>Kode ATK</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Stok</th>
                        <th>Harga Satuan</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($atks as $item)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $item->id }}" class="row-checkbox"></td>
                        <td class="cell-name">{{ $item->kode_atk }}</td>
                        <td>{{ $item->nama_atk }}</td>
                        <td>{{ $item->jenis_atk }}</td>
                        <td style="font-weight: bold; color: {{ $item->jumlah < 10 ? 'var(--danger)' : 'inherit' }}">{{ number_format($item->jumlah) }} {{ $item->satuan }}</td>
                        <td class="cell-price">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td>
                            <span class="tag {{ $item->status === 'Aktif' ? 't-active' : 't-unavail' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="data-cell-actions" style="justify-content: flex-end;">
                                <button type="button" onclick="showDetailModal({{ $item->id }})" class="btn--icon" aria-label="View" title="Lihat Detail">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                <a href="{{ route('admin.atk.print-qr', $item->id) }}" target="_blank" class="btn--icon" title="Cetak QR Code">
                                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </a>
                                @if(Auth::user()->role === 'admin')
                                    <button type="button" onclick="openEditModal({{ $item->id }})" class="btn--icon" aria-label="Edit" title="Edit ATK">
                                        <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg>
                                    </button>
                                    <button type="button" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama_atk }}')" class="btn--icon" aria-label="Delete" title="Hapus ATK">
                                        <svg viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px; color: var(--t-muted);">
                            <p style="font-weight: bold; margin-bottom: 5px;">Belum ada data ATK.</p>
                            <p>Tambahkan item alat tulis kantor pertama Anda untuk memulai manajemen inventaris.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>
    <div style="padding: 15px;">
        {{ $atks->links() }}
    </div>
</section>

<!-- Simple Modal Styles (since adminator might not have built-in modals or I don't know the classes) -->
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

<!-- MODAL TAMBAH / EDIT ATK -->
<div id="atk-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Tambah Master ATK</h3>
            <button class="modal-close" onclick="closeModal('atk-modal')">&times;</button>
        </div>
        <form id="atk-form" onsubmit="handleFormSubmit(event)">
            <input type="hidden" id="atk-id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Kode Jenis Barang (Kode ATK) *</label>
                    <input type="text" id="form-kode" required>
                </div>
                <div class="form-group">
                    <label>Nama Barang ATK *</label>
                    <input type="text" id="form-nama" required>
                </div>
                <div class="form-group">
                    <label>Jenis Barang ATK *</label>
                    <input type="text" id="form-jenis" required>
                </div>
                <div class="form-group">
                    <label>Satuan *</label>
                    <input type="text" id="form-satuan" required>
                </div>
                <div class="form-group">
                    <label>Jumlah / Stok *</label>
                    <input type="number" id="form-jumlah" required>
                </div>
                <div class="form-group">
                    <label>Harga Barang (Rp) *</label>
                    <input type="number" id="form-harga" required>
                </div>
                <div class="form-group">
                    <label>Rekening Penampungan (BYD)</label>
                    <input type="text" id="form-rekening-penampungan">
                </div>
                <div class="form-group">
                    <label>Rekening Biaya</label>
                    <input type="text" id="form-rekening-biaya">
                </div>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select id="form-status">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('atk-modal')" class="btn btn--ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL ATK -->
<div id="detail-modal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3>Detail Master ATK</h3>
            <button class="modal-close" onclick="closeModal('detail-modal')">&times;</button>
        </div>
        <div id="detail-content" style="line-height: 1.6;">
            <!-- Diisi via JavaScript -->
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('detail-modal')" class="btn btn-primary">Tutup</button>
        </div>
    </div>
</div>

<script>
    function toggleSelectAll(source) {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        for(let i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function submitBulkPrint() {
        let checked = document.querySelectorAll('.row-checkbox:checked');
        if (checked.length === 0) {
            Swal.fire('Pilih ATK', 'Silakan centang minimal satu ATK untuk dicetak QR-nya.', 'warning');
            return;
        }
        document.getElementById('bulkPrintForm').submit();
    }

    function openModal(id) {
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('flex');
    }

    function openCreateModal() {
        document.getElementById('modal-title').innerText = 'Tambah Master ATK';
        document.getElementById('atk-form').reset();
        document.getElementById('atk-id').value = '';
        document.getElementById('form-kode').removeAttribute('readonly');
        openModal('atk-modal');
    }

    function openEditModal(id) {
        fetch(`/atk/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    document.getElementById('modal-title').innerText = 'Edit Master ATK: ' + d.kode_atk;
                    document.getElementById('atk-id').value = d.id;
                    document.getElementById('form-kode').value = d.kode_atk;
                    document.getElementById('form-kode').setAttribute('readonly', true);
                    document.getElementById('form-nama').value = d.nama_atk;
                    document.getElementById('form-jenis').value = d.jenis_atk;
                    document.getElementById('form-satuan').value = d.satuan;
                    document.getElementById('form-jumlah').value = d.jumlah;
                    document.getElementById('form-harga').value = d.harga;
                    document.getElementById('form-rekening-penampungan').value = d.rekening_penampungan || '';
                    document.getElementById('form-rekening-biaya').value = d.rekening_biaya || '';
                    document.getElementById('form-status').value = d.status;
                    openModal('atk-modal');
                }
            });
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('atk-id').value;
        const isEdit = !!id;
        const url = isEdit ? `/atk/${id}` : '/atk';
        const method = isEdit ? 'PUT' : 'POST';

        const payload = {
            kode_atk: document.getElementById('form-kode').value,
            nama_atk: document.getElementById('form-nama').value,
            jenis_atk: document.getElementById('form-jenis').value,
            satuan: document.getElementById('form-satuan').value,
            jumlah: document.getElementById('form-jumlah').value,
            harga: document.getElementById('form-harga').value,
            rekening_penampungan: document.getElementById('form-rekening-penampungan').value,
            rekening_biaya: document.getElementById('form-rekening-biaya').value,
            status: document.getElementById('form-status').value,
        };

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Gagal menyimpan data.');
            return data;
        })
        .then(res => {
            closeModal('atk-modal');
            Swal.fire({
                icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false
            }).then(() => location.reload());
        })
        .catch(err => Swal.fire('Gagal', err.message, 'error'));
    }

    function showDetailModal(id) {
        fetch(`/atk/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    document.getElementById('detail-content').innerHTML = `
                        <p><strong>Kode ATK:</strong> ${d.kode_atk}</p>
                        <p><strong>Nama Barang:</strong> ${d.nama_atk}</p>
                        <p><strong>Jenis:</strong> ${d.jenis_atk}</p>
                        <p><strong>Stok:</strong> ${d.jumlah} ${d.satuan}</p>
                        <p><strong>Harga:</strong> Rp ${new Intl.NumberFormat('id-ID').format(d.harga)}</p>
                        <p><strong>BYD:</strong> ${d.rekening_penampungan || '-'}</p>
                        <p><strong>Biaya:</strong> ${d.rekening_biaya || '-'}</p>
                        <p><strong>Status:</strong> ${d.status}</p>
                    `;
                    openModal('detail-modal');
                }
            });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Master ATK?',
            text: `Yakin hapus "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/atk/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(res => res.json())
                .then(res => {
                    if (res.success) location.reload();
                    else Swal.fire('Gagal', res.message, 'error');
                });
            }
        });
    }
</script>
@endsection
