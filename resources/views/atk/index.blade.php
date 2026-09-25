@extends('layouts.dashboard')

@section('title', 'Master ATK')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Master ATK</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Master Data Alat Tulis Kantor (ATK)</h1>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="items-center justify-between block sm:flex md:divide-x md:divide-gray-100 dark:divide-gray-700">
            <form action="{{ route('atk.index') }}" method="GET" class="flex flex-wrap items-center gap-3 sm:mb-0">
                <div class="relative w-48 sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Cari kode/nama ATK...">
                </div>
                <div>
                    <select name="jenis_atk" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisList as $j)
                            <option value="{{ $j }}" {{ request('jenis_atk') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700">Filter</button>
                @if(request()->anyFilled(['search', 'jenis_atk', 'status']))
                    <a href="{{ route('atk.index') }}" class="text-gray-500 hover:text-gray-900 text-sm font-medium py-2.5 dark:text-gray-400 dark:hover:text-white">Reset</a>
                @endif
            </form>

            <div class="flex items-center pl-0 sm:pl-4 space-x-2 mt-3 sm:mt-0">
                <a href="{{ route('atk.scan') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-primary-700 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Scan QR
                </a>
                @if(Auth::user()->role === 'admin')
                    <button type="button" onclick="openCreateModal()" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none">
                        + Tambah ATK
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Table Data ATK -->
<div class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Kode ATK</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Nama Barang</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jenis</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Stok</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Harga Satuan</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Rek. Penampungan / Biaya</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Status</th>
                            <th scope="col" class="p-4 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($atks as $item)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-4 text-sm font-semibold text-primary-600 dark:text-primary-400 whitespace-nowrap">
                                {{ $item->kode_atk }}
                            </td>
                            <td class="p-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $item->nama_atk }}
                            </td>
                            <td class="p-4 text-sm font-normal text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ $item->jenis_atk }}
                            </td>
                            <td class="p-4 text-sm font-bold {{ $item->jumlah < 10 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} whitespace-nowrap">
                                {{ number_format($item->jumlah) }} {{ $item->satuan }}
                            </td>
                            <td class="p-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                <div>BYD: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item->rekening_penampungan ?? '-' }}</span></div>
                                <div>Beban: <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item->rekening_biaya ?? '-' }}</span></div>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded {{ $item->status === 'Aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="p-4 text-center space-x-1 whitespace-nowrap">
                                <button type="button" onclick="showDetailModal({{ $item->id }})" title="Lihat Detail" class="p-2 text-sm text-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                                </button>
                                
                                <a href="{{ route('atk.print-qr', $item->id) }}" target="_blank" title="Cetak QR Code" class="inline-block p-2 text-sm text-purple-600 rounded-lg hover:bg-purple-50 dark:hover:bg-gray-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </a>

                                @if(Auth::user()->role === 'admin')
                                    <button type="button" onclick="openEditModal({{ $item->id }})" title="Edit ATK" class="p-2 text-sm text-primary-600 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                                    </button>
                                    <button type="button" onclick="confirmDelete({{ $item->id }}, '{{ $item->nama_atk }}')" title="Hapus ATK" class="p-2 text-sm text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-gray-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-base font-semibold mb-1">Belum ada data ATK.</p>
                                <p class="text-sm mb-4">Tambahkan item alat tulis kantor pertama Anda untuk memulai manajemen inventaris.</p>
                                @if(Auth::user()->role === 'admin')
                                    <button type="button" onclick="openCreateModal()" class="text-white bg-primary-700 hover:bg-primary-800 font-medium rounded-lg text-sm px-4 py-2">
                                        + Tambah Data
                                    </button>
                                @endif
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
<div class="p-4 bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700 flex items-center justify-between">
    {{ $atks->links() }}
</div>

<!-- MODAL TAMBAH / EDIT ATK -->
<div id="atk-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50">
    <div class="relative w-full h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-xl shadow dark:bg-gray-800">
            <!-- Modal header -->
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modal-title">
                    Tambah Master ATK
                </h3>
                <button type="button" onclick="closeModal('atk-modal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                </button>
            </div>
            <!-- Modal body -->
            <form id="atk-form" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="atk-id">
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="form-kode" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kode Jenis Barang (Kode ATK) *</label>
                            <input type="text" id="form-kode" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: ATK-00001" required>
                        </div>
                        <div>
                            <label for="form-nama" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Barang ATK *</label>
                            <input type="text" id="form-nama" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Kertas HVS A4 80gr" required>
                        </div>
                        <div>
                            <label for="form-jenis" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Barang ATK *</label>
                            <input type="text" id="form-jenis" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Kertas, Pulpen, Tinta, dll." required>
                        </div>
                        <div>
                            <label for="form-satuan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Satuan *</label>
                            <input type="text" id="form-satuan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Rim, PCS, Box, Dus" required>
                        </div>
                        <div>
                            <label for="form-jumlah" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah / Stok *</label>
                            <input type="number" min="0" id="form-jumlah" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label for="form-harga" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga Barang (Rp) *</label>
                            <input type="number" min="0" step="100" id="form-harga" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                        <div>
                            <label for="form-rekening-penampungan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rekening Penampungan (BYD)</label>
                            <input type="text" id="form-rekening-penampungan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: 1001.02.0001">
                        </div>
                        <div>
                            <label for="form-rekening-biaya" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rekening Biaya</label>
                            <input type="text" id="form-rekening-biaya" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: 5001.01.0024">
                        </div>
                    </div>
                    <div>
                        <label for="form-status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select id="form-status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center justify-end p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-700">
                    <button type="button" onclick="closeModal('atk-modal')" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Batal</button>
                    <button type="submit" id="btn-save" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DETAIL ATK -->
<div id="detail-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50">
    <div class="relative w-full h-full max-w-lg md:h-auto">
        <div class="relative bg-white rounded-xl shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Detail Master ATK
                </h3>
                <button type="button" onclick="closeModal('detail-modal')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>  
                </button>
            </div>
            <div class="p-6 space-y-3" id="detail-content">
                <!-- Diisi via JavaScript -->
            </div>
            <div class="flex items-center justify-end p-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeModal('detail-modal')" class="text-white bg-primary-700 hover:bg-primary-800 font-medium rounded-lg text-sm px-4 py-2">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
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
            })
            .catch(err => {
                Swal.fire('Error', 'Gagal mengambil data ATK.', 'error');
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
            if (!res.ok) {
                let msg = data.message || 'Gagal menyimpan data.';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('<br>');
                }
                throw new Error(msg);
            }
            return data;
        })
        .then(res => {
            closeModal('atk-modal');
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            Swal.fire('Gagal', err.message, 'error');
        });
    }

    function showDetailModal(id) {
        fetch(`/atk/${id}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    const html = `
                        <div class="border-b pb-2 dark:border-gray-700">
                            <span class="text-xs text-gray-500">Kode ATK</span>
                            <p class="text-base font-bold text-primary-600 dark:text-primary-400">${d.kode_atk}</p>
                        </div>
                        <div class="border-b pb-2 dark:border-gray-700">
                            <span class="text-xs text-gray-500">Nama Barang</span>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">${d.nama_atk}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 border-b pb-2 dark:border-gray-700">
                            <div>
                                <span class="text-xs text-gray-500">Jenis Barang</span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${d.jenis_atk}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Stok Saat Ini</span>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">${d.jumlah} ${d.satuan}</p>
                            </div>
                        </div>
                        <div class="border-b pb-2 dark:border-gray-700">
                            <span class="text-xs text-gray-500">Harga Satuan</span>
                            <p class="text-base font-bold text-gray-900 dark:text-white">Rp ${new Intl.NumberFormat('id-ID').format(d.harga)}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 border-b pb-2 dark:border-gray-700">
                            <div>
                                <span class="text-xs text-gray-500">Rekening Penampungan (BYD)</span>
                                <p class="text-sm text-gray-800 dark:text-gray-200">${d.rekening_penampungan || '-'}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Rekening Biaya</span>
                                <p class="text-sm text-gray-800 dark:text-gray-200">${d.rekening_biaya || '-'}</p>
                            </div>
                        </div>
                        <div class="pt-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500">Status</span>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded ${d.status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${d.status}</span>
                        </div>
                    `;
                    document.getElementById('detail-content').innerHTML = html;
                    openModal('detail-modal');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Gagal memuat detail data.', 'error');
            });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Master ATK?',
            text: `Apakah Anda yakin ingin menghapus "${name}"? Data yang dihapus tidak dapat dikembalikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/atk/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Gagal', 'Terjadi kesalahan sistem saat menghapus data.', 'error');
                });
            }
        });
    }
</script>
@endsection
