@extends('layout.app')

@section('title', 'Manajemen Aset')
@section('active_menu', 'aset')
@section('breadcrumbs', 'Modul Manajemen | Manajemen Aset')

@section('content')
<section class="card" style="min-height: 500px;">
    <div class="card-head">
        <div class="card-title-wrap">
            <span class="eyebrow">Daftar Aset</span>
            <h2 class="card-title">Manajemen Aset Perusahaan</h2>
        </div>
        <div class="card-action">
            <form action="{{ route(Auth::user()->role . '.aset.index') }}" method="GET" class="filter-bar">
                <input type="text" name="search" value="{{ request('search') }}" class="input" style="width: 250px;" placeholder="Cari Sertifikat / Cabang...">
                <button type="submit" class="btn btn--primary btn--filter">Cari</button>
            </form>

            @if(Auth::user()->role === 'admin')
            <button class="btn btn--primary" onclick="openFormModal()">
                <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Tambah Aset
            </button>
            @endif
        </div>
    </div>
    
    <div class="table-scroll">
    <table class="table">
        <thead>
            <tr>
                <th>No. Sertifikat</th>
                <th>Cabang / Lokasi</th>
                <th>Pemilik</th>
                <th>Masa Berlaku</th>
                <th>Status</th>
                <th style="text-align:right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asets as $aset)
            <tr>
                <td class="cell-name">{{ $aset->nomor_sertifikat }}</td>
                <td>
                    <strong>{{ $aset->kode_cabang }}</strong><br>
                    <span style="font-size: 12px; color: var(--t-muted);">{{ Str::limit($aset->lokasi, 30) }}</span>
                </td>
                <td>{{ $aset->nama_pemilik }}</td>
                <td class="cell-date">
                    {{ $aset->jatuh_tempo_sertifikat ? date('d M Y', strtotime($aset->jatuh_tempo_sertifikat)) : '-' }}
                </td>
                <td>
                    @php
                        $statusClass = '';
                        if($aset->status_sertifikat === 'Aman' || $aset->status_sertifikat === 'Tidak Ada Jatuh Tempo') $statusClass = 't-new'; // green
                        elseif($aset->status_sertifikat === 'Akan Jatuh Tempo') $statusClass = 't-used'; // yellow
                        elseif($aset->status_sertifikat === 'Sudah Jatuh Tempo') $statusClass = 't-unavail'; // red
                    @endphp
                    <span class="tag {{ $statusClass }}">{{ $aset->status_sertifikat }}</span>
                </td>
                <td style="text-align:right; white-space: nowrap;">
                    <div class="data-cell-actions" style="justify-content: flex-end;">
                        <button type="button" onclick="openDetailModal({{ $aset->id }})" class="btn--icon act--view" aria-label="Detail" title="Detail">
                            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                        @if(Auth::user()->role === 'admin')
                        <button type="button" onclick="openFormModal({{ $aset->id }})" class="btn--icon act--edit" aria-label="Edit" title="Edit">
                            <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg>
                        </button>
                        <button type="button" onclick="deleteData({{ $aset->id }}, '{{ $aset->nomor_sertifikat }}')" class="btn--icon act--delete" aria-label="Hapus" title="Hapus">
                            <svg viewBox="0 0 24 24"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding: 20px;">Tidak ada data aset.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="padding: 15px;">
        {{ $asets->links() }}
    </div>
</section>

<!-- ================= MODAL FORM ================= -->
<div id="modalForm" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalFormTitle">Tambah Aset</h3>
            <button type="button" class="modal-close" onclick="closeFormModal()" aria-label="Tutup modal">&times;</button>
        </div>
        <form id="formData" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data">
            <input type="hidden" id="aset_id" name="id">
            
            <div style="display: flex; gap: 15px;">
                <div style="flex:1;">
                    <label class="form-label">Kode Cabang <span style="color:red">*</span></label>
                    <input type="text" id="kode_cabang" name="kode_cabang" class="input" required placeholder="Contoh: CAB-001">
                </div>
                <div style="flex:1;">
                    <label class="form-label">Nomor Sertifikat <span style="color:red">*</span></label>
                    <input type="text" id="nomor_sertifikat" name="nomor_sertifikat" class="input" required>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div style="flex:1;">
                    <label class="form-label">Nama Pemilik <span style="color:red">*</span></label>
                    <input type="text" id="nama_pemilik" name="nama_pemilik" class="input" required>
                </div>
                <div style="flex:1;">
                    <label class="form-label">Luas Tanah (m&sup2;) <span style="color:red">*</span></label>
                    <input type="number" step="0.01" id="luas_tanah" name="luas_tanah" class="input" required>
                </div>
            </div>

            <label class="form-label">Lokasi <span style="color:red">*</span></label>
            <textarea id="lokasi" name="lokasi" class="input" rows="2" required></textarea>

            <div style="display: flex; gap: 15px;">
                <div style="flex:1;">
                    <label class="form-label">Tanggal Jatuh Tempo Sertifikat</label>
                    <input type="date" id="jatuh_tempo_sertifikat" name="jatuh_tempo_sertifikat" class="input">
                </div>
                <div style="flex:1;">
                    <label class="form-label">Lampiran Bukti (PDF/JPG/PNG, Max 5MB)</label>
                    <input type="file" id="lampiran_bukti" name="lampiran_bukti" class="input" accept=".pdf,.jpg,.jpeg,.png">
                    <div id="current_lampiran" style="font-size: 12px; margin-top: -5px; margin-bottom: 10px;"></div>
                </div>
            </div>

            <label class="form-label">Keterangan Tambahan</label>
            <textarea id="keterangan" name="keterangan" class="input" rows="2"></textarea>

            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn btn--ghost" onclick="closeFormModal()">Batal</button>
                <button type="submit" class="btn btn--primary">Simpan Aset</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div id="modalDetail" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Detail Aset</h3>
            <button type="button" class="modal-close" onclick="closeDetailModal()" aria-label="Tutup modal">&times;</button>
        </div>
        
        <div id="detailContent" style="font-size: 14px; line-height: 1.6;">
            <!-- Injected via JS -->
        </div>

        <div style="text-align: right; margin-top: 20px;">
            <button type="button" class="btn btn--ghost" onclick="closeDetailModal()">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openFormModal(id = null) {
        document.getElementById('formData').reset();
        document.getElementById('current_lampiran').innerHTML = '';
        
        if (!id) {
            document.getElementById('modalFormTitle').textContent = 'Tambah Aset';
            document.getElementById('aset_id').value = '';
            document.getElementById('modalForm').classList.add('flex');
        } else {
            document.getElementById('modalFormTitle').textContent = 'Edit Aset';
            fetch(`/${window.NGAS_USER_ROLE}/aset/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        document.getElementById('aset_id').value = p.id;
                        document.getElementById('kode_cabang').value = p.kode_cabang;
                        document.getElementById('nomor_sertifikat').value = p.nomor_sertifikat;
                        document.getElementById('nama_pemilik').value = p.nama_pemilik;
                        document.getElementById('luas_tanah').value = p.luas_tanah;
                        document.getElementById('lokasi').value = p.lokasi;
                        document.getElementById('jatuh_tempo_sertifikat').value = p.jatuh_tempo_sertifikat || '';
                        document.getElementById('keterangan').value = p.keterangan || '';
                        
                        if (p.lampiran_url) {
                            document.getElementById('current_lampiran').innerHTML = `File saat ini: <a href="${p.lampiran_url}" target="_blank" style="color:var(--primary); text-decoration:underline;">Lihat File</a> (Pilih file baru untuk mengganti)`;
                        }
                        
                        document.getElementById('modalForm').classList.add('flex');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Gagal memuat data.', 'error'));
        }
    }

    function closeFormModal() {
        document.getElementById('modalForm').classList.remove('flex');
    }

    function closeDetailModal() {
        document.getElementById('modalDetail').classList.remove('flex');
    }

    function openDetailModal(id) {
        fetch(`/${window.NGAS_USER_ROLE}/aset/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const p = data.data;
                    
                    let statusColor = '#34495e';
                    if (p.status_sertifikat === 'Aman' || p.status_sertifikat === 'Tidak Ada Jatuh Tempo') statusColor = '#27ae60';
                    else if (p.status_sertifikat === 'Akan Jatuh Tempo') statusColor = '#f39c12';
                    else if (p.status_sertifikat === 'Sudah Jatuh Tempo') statusColor = '#e74c3c';

                    let fileLink = p.lampiran_url 
                        ? `<a href="${p.lampiran_url}" target="_blank" style="color:var(--primary); font-weight:bold; text-decoration:underline;">Tampilkan Dokumen</a>`
                        : '<span style="color:var(--t-muted)">Tidak ada lampiran</span>';

                    document.getElementById('detailContent').innerHTML = `
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <div style="margin-bottom: 8px;"><strong style="display:inline-block; width: 140px;">No. Sertifikat</strong>: ${p.nomor_sertifikat}</div>
                            <div style="margin-bottom: 8px;"><strong style="display:inline-block; width: 140px;">Kode Cabang</strong>: ${p.kode_cabang}</div>
                            <div style="margin-bottom: 8px;"><strong style="display:inline-block; width: 140px;">Nama Pemilik</strong>: ${p.nama_pemilik}</div>
                            <div style="margin-bottom: 8px;"><strong style="display:inline-block; width: 140px;">Luas Tanah</strong>: ${p.luas_tanah} m&sup2;</div>
                            <div style="margin-bottom: 8px;"><strong style="display:inline-block; width: 140px;">Lokasi</strong>: ${p.lokasi}</div>
                        </div>
                        <div style="background:var(--bg-muted); padding: 15px; border-radius: 8px;">
                            <div style="margin-bottom: 8px;"><strong style="display:inline-block; width: 140px;">Masa Berlaku</strong>: ${p.jatuh_tempo_sertifikat || '-'}</div>
                            <div style="margin-bottom: 8px;"><strong style="display:inline-block; width: 140px;">Status</strong>: <span style="color:${statusColor}; font-weight:bold;">${p.status_sertifikat}</span></div>
                            <div style="margin-bottom: 8px;"><strong style="display:inline-block; width: 140px;">Lampiran</strong>: ${fileLink}</div>
                            <div style="margin-top: 10px;"><strong>Keterangan:</strong><br>${p.keterangan || '-'}</div>
                        </div>
                    `;
                    document.getElementById('modalDetail').classList.add('flex');
                }
            })
            .catch(err => Swal.fire('Error', 'Gagal memuat detail aset.', 'error'));
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        
        const id = document.getElementById('aset_id').value;
        const url = id ? `/${window.NGAS_USER_ROLE}/aset/${id}` : `/${window.NGAS_USER_ROLE}/aset`;
        
        // Use FormData for file upload
        const formElement = document.getElementById('formData');
        const formData = new FormData(formElement);
        
        if (id) {
            formData.append('_method', 'PUT');
        }
        
        // Add CSRF
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        try {
            const res = await fetch(url, {
                method: 'POST', // always POST when sending FormData, override with _method for PUT
                headers: { 'Accept': 'application/json' },
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                closeFormModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Gagal', data.message || 'Periksa kembali isian form.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Terjadi kesalahan sistem atau file terlalu besar.', 'error');
        }
    }

    function deleteData(id, desc) {
        Swal.fire({
            title: 'Hapus Aset?',
            text: `Data aset nomor ${desc} akan dihapus secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/${window.NGAS_USER_ROLE}/aset/${id}`, {
                        method: 'DELETE',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Terhapus!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                }
            }
        });
    }
</script>
@endpush
