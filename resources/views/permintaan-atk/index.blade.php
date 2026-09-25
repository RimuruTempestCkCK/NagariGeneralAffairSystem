@extends('layouts.dashboard')

@section('title', 'Permintaan ATK / Purchase Order')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Permintaan ATK</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Daftar Permintaan ATK / Purchase Order (PO)</h1>
        </div>

        <div class="sm:flex">
            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('permintaan-atk.index') }}" class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                <div class="relative mt-1 sm:w-64 xl:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Cari No. PO atau Unit...">
                </div>
                <div class="flex pl-0 mt-3 space-x-2 sm:pl-2 sm:mt-0">
                    <select name="status" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                        <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                        <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>APPROVED</option>
                        <option value="REJECTED" {{ request('status') === 'REJECTED' ? 'selected' : '' }}>REJECTED</option>
                    </select>
                </div>
            </form>

            <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                <button type="button" onclick="openCreateModal()" class="inline-flex items-center justify-center w-1/2 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Buat Permintaan ATK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Table Container -->
<div class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Nomor PO</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Pemohon</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Unit Kerja</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Tanggal</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Total Item</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Status</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($permintaans as $p)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $p->nomor_po }}
                            </td>
                            <td class="p-4 text-sm text-gray-600 whitespace-nowrap dark:text-gray-300">
                                {{ $p->user->name ?? '-' }}
                            </td>
                            <td class="p-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $p->unit_kerja }}
                            </td>
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ date('d M Y', strtotime($p->tanggal_permintaan)) }}
                            </td>
                            <td class="p-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                <span class="font-bold">{{ $p->items->count() }}</span> jenis item
                            </td>
                            <td class="p-4 text-sm whitespace-nowrap">
                                @if($p->status === 'DRAFT')
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">DRAFT</span>
                                @elseif($p->status === 'PENDING')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">PENDING APPROVAL</span>
                                @elseif($p->status === 'APPROVED')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">APPROVED</span>
                                @elseif($p->status === 'REJECTED')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">REJECTED</span>
                                @else
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $p->status }}</span>
                                @endif
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap">
                                <!-- Tombol Detail (Semua Role) -->
                                <button type="button" onclick="openDetailModal({{ $p->id }})" class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600">
                                    Detail
                                </button>

                                <!-- Tombol Khusus DRAFT: Submit, Edit & Delete -->
                                @if($p->status === 'DRAFT' && (Auth::user()->role === 'staff' || Auth::user()->role === 'admin'))
                                    <button type="button" onclick="submitPo({{ $p->id }}, '{{ $p->nomor_po }}')" class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300">
                                        Submit
                                    </button>
                                    <button type="button" onclick="openEditModal({{ $p->id }})" class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300">
                                        Edit
                                    </button>
                                    <button type="button" onclick="deletePo({{ $p->id }}, '{{ $p->nomor_po }}')" class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300">
                                        Hapus
                                    </button>
                                @endif

                                <!-- Tombol Khusus Admin untuk Status PENDING: Approve & Reject -->
                                @if(Auth::user()->role === 'admin' && $p->status === 'PENDING')
                                    <button type="button" onclick="approvePo({{ $p->id }}, '{{ $p->nomor_po }}')" class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-300">
                                        Approve
                                    </button>
                                    <button type="button" onclick="rejectPo({{ $p->id }}, '{{ $p->nomor_po }}')" class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300">
                                        Reject
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data permintaan ATK ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="p-4 bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{ $permintaans->links() }}
</div>

<!-- ================= MODAL TAMBAH / EDIT PERMINTAAN ================= -->
<div id="modalFormPermintaan" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalFormTitle">
                    Buat Permintaan ATK Baru
                </h3>
                <button type="button" onclick="closeFormModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            
            <form id="formPermintaan" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="permintaan_id">
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Kerja / Cabang <span class="text-red-500">*</span></label>
                            <input type="text" id="unit_kerja" name="unit_kerja" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Kantor Cabang Padang">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Permintaan <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal_permintaan" name="tanggal_permintaan" value="{{ date('Y-m-d') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Catatan / Keterangan</label>
                        <textarea id="catatan" name="catatan" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Keperluan kebutuhan operasional..."></textarea>
                    </div>

                    <!-- Dynamic ATK Items -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Daftar Item Barang ATK <span class="text-red-500">*</span></label>
                            <button type="button" onclick="addItemRow()" class="px-2.5 py-1 text-xs font-medium text-primary-700 bg-primary-100 rounded-lg hover:bg-primary-200 dark:bg-primary-900 dark:text-primary-300">
                                + Tambah Baris
                            </button>
                        </div>
                        <div class="border rounded-lg p-3 bg-gray-50 dark:bg-gray-700/50 dark:border-gray-600 space-y-2 max-h-60 overflow-y-auto" id="itemsContainer">
                            <!-- Rows injected via JS -->
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-700">
                    <button type="button" onclick="closeFormModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit" name="submit_action" value="draft" class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500">
                        Simpan Draft
                    </button>
                    <button type="submit" name="submit_action" value="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        Submit Langsung
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL PERMINTAAN ================= -->
<div id="modalDetailPermintaan" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Detail Purchase Order ATK
                </h3>
                <button type="button" onclick="closeDetailModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4" id="detailContent">
                <!-- Detail injected via JS -->
            </div>

            <div class="flex items-center justify-end p-4 border-t border-gray-200 rounded-b dark:border-gray-700">
                <button type="button" onclick="closeDetailModal()" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700">
                    Tutup
                </button>
            </div>
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
            optionsHtml += `<option value="${atk.id}" ${isSel} data-satuan="${atk.satuan}" data-stok="${atk.jumlah}">${atk.kode_atk} - ${atk.nama_atk} (Stok: ${atk.jumlah} ${atk.satuan})</option>`;
        });

        const rowHtml = `
            <div class="flex items-center space-x-2 item-row" id="row_${rowId}">
                <select name="atk_id" required class="flex-1 bg-white border border-gray-300 text-gray-900 text-xs rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="updateItemMeta('${rowId}')">
                    ${optionsHtml}
                </select>
                <div class="w-24">
                    <input type="number" name="jumlah_diminta" value="${jumlah}" min="1" required class="w-full bg-white border border-gray-300 text-gray-900 text-xs rounded-lg p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Qty">
                </div>
                <span class="text-xs text-gray-500 w-16" id="satuan_${rowId}">Satuan</span>
                <button type="button" onclick="removeItemRow('${rowId}')" class="p-2 text-red-600 hover:bg-red-100 rounded-lg dark:hover:bg-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHtml);
        updateItemMeta(rowId);
    }

    function removeItemRow(rowId) {
        const row = document.getElementById('row_' + rowId);
        if (document.querySelectorAll('.item-row').length > 1) {
            row.remove();
        } else {
            Swal.fire('Info', 'Minimal harus ada 1 item barang yang diajukan.', 'info');
        }
    }

    function updateItemMeta(rowId) {
        const row = document.getElementById('row_' + rowId);
        const select = row.querySelector('select[name="atk_id"]');
        const selectedOption = select.options[select.selectedIndex];
        const satuanSpan = document.getElementById('satuan_' + rowId);
        if (selectedOption && selectedOption.dataset.satuan) {
            satuanSpan.textContent = selectedOption.dataset.satuan;
        } else {
            satuanSpan.textContent = '-';
        }
    }

    function openCreateModal() {
        document.getElementById('modalFormTitle').textContent = 'Buat Permintaan ATK Baru';
        document.getElementById('permintaan_id').value = '';
        document.getElementById('unit_kerja').value = '';
        document.getElementById('tanggal_permintaan').value = new Date().toISOString().split('T')[0];
        document.getElementById('catatan').value = '';
        document.getElementById('itemsContainer').innerHTML = '';
        addItemRow(); // default 1 baris
        document.getElementById('modalFormPermintaan').classList.remove('hidden');
        document.getElementById('modalFormPermintaan').classList.add('flex');
    }

    function closeFormModal() {
        document.getElementById('modalFormPermintaan').classList.add('hidden');
        document.getElementById('modalFormPermintaan').classList.remove('flex');
    }

    function closeDetailModal() {
        document.getElementById('modalDetailPermintaan').classList.add('hidden');
        document.getElementById('modalDetailPermintaan').classList.remove('flex');
    }

    async function openEditModal(id) {
        try {
            const res = await fetch(`/permintaan-atk/${id}`);
            const data = await res.json();
            if (!data.success) {
                Swal.fire('Error', data.message, 'error');
                return;
            }

            const p = data.data;
            document.getElementById('modalFormTitle').textContent = `Edit Permintaan ATK (${p.nomor_po})`;
            document.getElementById('permintaan_id').value = p.id;
            document.getElementById('unit_kerja').value = p.unit_kerja;
            document.getElementById('tanggal_permintaan').value = p.tanggal_permintaan;
            document.getElementById('catatan').value = p.catatan || '';
            
            document.getElementById('itemsContainer').innerHTML = '';
            p.items.forEach(item => {
                addItemRow(item.atk_id, item.jumlah_diminta);
            });

            document.getElementById('modalFormPermintaan').classList.remove('hidden');
            document.getElementById('modalFormPermintaan').classList.add('flex');
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat data permintaan.', 'error');
        }
    }

    async function openDetailModal(id) {
        try {
            const res = await fetch(`/permintaan-atk/${id}`);
            const data = await res.json();
            if (!data.success) {
                Swal.fire('Error', data.message, 'error');
                return;
            }

            const p = data.data;
            let itemsRows = '';
            p.items.forEach((item, idx) => {
                itemsRows += `
                    <tr class="border-b dark:border-gray-700">
                        <td class="py-2 text-xs font-semibold">${idx + 1}</td>
                        <td class="py-2 text-xs">${item.atk ? item.atk.nama_atk : '-'} (${item.atk ? item.atk.kode_atk : ''})</td>
                        <td class="py-2 text-xs">${item.jumlah_diminta} ${item.atk ? item.atk.satuan : ''}</td>
                        <td class="py-2 text-xs font-bold text-green-600">${item.jumlah_disetujui !== null ? item.jumlah_disetujui : '-'}</td>
                    </tr>
                `;
            });

            document.getElementById('detailContent').innerHTML = `
                <div class="grid grid-cols-2 gap-2 text-sm bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                    <div><span class="text-gray-500">Nomor PO:</span> <strong class="text-gray-900 dark:text-white">${p.nomor_po}</strong></div>
                    <div><span class="text-gray-500">Status:</span> <strong class="text-blue-600">${p.status}</strong></div>
                    <div><span class="text-gray-500">Pemohon:</span> <span class="text-gray-900 dark:text-white">${p.user ? p.user.name : '-'}</span></div>
                    <div><span class="text-gray-500">Unit Kerja:</span> <span class="text-gray-900 dark:text-white">${p.unit_kerja}</span></div>
                    <div><span class="text-gray-500">Tanggal:</span> <span class="text-gray-900 dark:text-white">${p.tanggal_permintaan}</span></div>
                    <div><span class="text-gray-500">Disetujui Oleh:</span> <span class="text-gray-900 dark:text-white">${p.approver ? p.approver.name : '-'}</span></div>
                </div>
                ${p.alasan_reject ? `
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-700">
                        <strong>Alasan Penolakan:</strong> ${p.alasan_reject}
                    </div>
                ` : ''}
                <div>
                    <h4 class="font-semibold text-sm mb-2 text-gray-900 dark:text-white">Rincian Barang yang Diminta:</h4>
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b text-xs text-gray-500">
                                <th class="py-1">#</th>
                                <th class="py-1">Nama Barang</th>
                                <th class="py-1">Diminta</th>
                                <th class="py-1">Disetujui</th>
                            </tr>
                        </thead>
                        <tbody>${itemsRows}</tbody>
                    </table>
                </div>
            `;

            document.getElementById('modalDetailPermintaan').classList.remove('hidden');
            document.getElementById('modalDetailPermintaan').classList.add('flex');
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat detail permintaan.', 'error');
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const actionType = e.submitter ? e.submitter.value : 'draft';
        const id = document.getElementById('permintaan_id').value;
        const url = id ? `/permintaan-atk/${id}` : '/permintaan-atk';
        const method = id ? 'PUT' : 'POST';

        // Ambil data items
        const itemRows = document.querySelectorAll('.item-row');
        const items = [];
        itemRows.forEach(row => {
            const atkId = row.querySelector('select[name="atk_id"]').value;
            const qty = row.querySelector('input[name="jumlah_diminta"]').value;
            if (atkId && qty) {
                items.push({ atk_id: atkId, jumlah_diminta: qty });
            }
        });

        if (items.length === 0) {
            Swal.fire('Perhatian', 'Pilih minimal satu barang ATK.', 'warning');
            return;
        }

        const payload = {
            unit_kerja: document.getElementById('unit_kerja').value,
            tanggal_permintaan: document.getElementById('tanggal_permintaan').value,
            catatan: document.getElementById('catatan').value,
            action: actionType,
            items: items,
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
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Terjadi gangguan jaringan atau server.', 'error');
        }
    }

    function submitPo(id, nomorPo) {
        Swal.fire({
            title: 'Kirim Permintaan?',
            text: `Kirim ${nomorPo} untuk ditinjau oleh Admin? Setelah dikirim status akan menjadi PENDING.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Kirim Sekarang!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/permintaan-atk/${id}/submit`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}' })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Terjadi kesalahan.', 'error');
                }
            }
        });
    }

    function deletePo(id, nomorPo) {
        Swal.fire({
            title: 'Hapus Permintaan?',
            text: `Data ${nomorPo} akan dihapus secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/permintaan-atk/${id}`, {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}' })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Terhapus!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Terjadi kesalahan.', 'error');
                }
            }
        });
    }

    function approvePo(id, nomorPo) {
        Swal.fire({
            title: 'Setujui Permintaan?',
            text: `Apakah Anda yakin ingin menyetujui ${nomorPo}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Setujui (Approve)',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/permintaan-atk/${id}/approve`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ _token: '{{ csrf_token() }}' })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Disetujui!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Terjadi kesalahan.', 'error');
                }
            }
        });
    }

    function rejectPo(id, nomorPo) {
        Swal.fire({
            title: 'Tolak Permintaan?',
            text: `Masukkan alasan penolakan untuk ${nomorPo}:`,
            input: 'textarea',
            inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
            inputValidator: (value) => {
                if (!value || value.trim().length < 3) {
                    return 'Alasan penolakan wajib diisi (minimal 3 karakter)!';
                }
            },
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Tolak Permintaan',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/permintaan-atk/${id}/reject`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ 
                            alasan_reject: result.value,
                            _token: '{{ csrf_token() }}' 
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Ditolak!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Terjadi kesalahan.', 'error');
                }
            }
        });
    }
</script>
@endsection
