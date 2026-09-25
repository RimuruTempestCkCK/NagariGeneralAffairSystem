@extends('layouts.adminator')

@section('title', 'Pemakaian ATK & Pembebanan')

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
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Pemakaian ATK</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Catatan Pemakaian ATK & Jurnal Pembebanan Biaya</h1>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700/50 dark:border-gray-600">
                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Total Pemakaian Bulan Ini</span>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalItemDipakaiBulanIni) }} Unit</h3>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700/50 dark:border-gray-600">
                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Total Beban Biaya Bulan Ini</span>
                <h3 class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">Rp {{ number_format($totalBebanBulanIni, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="sm:flex">
            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('pemakaian-atk.index') }}" class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                <div class="relative mt-1 sm:w-64 xl:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Cari Unit / No. Jurnal / ATK...">
                </div>
            </form>

            <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                <button type="button" onclick="openCreateModal()" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    + Catat Pemakaian ATK
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
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Tanggal</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Unit Kerja</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Barang ATK</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jumlah</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Harga Satuan</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Total Beban</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jurnal Pembebanan</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($pemakaians as $pem)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ date('d M Y', strtotime($pem->tanggal)) }}
                            </td>
                            <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $pem->unit_kerja }}
                            </td>
                            <td class="p-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $pem->atk->nama_atk ?? '-' }}
                                <span class="block text-xs font-normal text-gray-500">{{ $pem->atk->kode_atk ?? '' }}</span>
                            </td>
                            <td class="p-4 text-sm font-bold text-red-600 whitespace-nowrap dark:text-red-400">
                                -{{ number_format($pem->jumlah) }} {{ $pem->atk->satuan ?? 'PCS' }}
                            </td>
                            <td class="p-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                Rp {{ number_format($pem->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-sm font-bold text-gray-900 whitespace-nowrap dark:text-white">
                                Rp {{ number_format($pem->total_beban_biaya, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400 font-mono">
                                @if($pem->no_jurnal_beban)
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                        {{ $pem->no_jurnal_beban }}
                                    </span>
                                @else
                                    <span class="text-xs text-yellow-600 dark:text-yellow-400 italic">Belum dibukukan</span>
                                @endif
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap">
                                <button type="button" onclick="openDetailModal({{ $pem->id }})" class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                                    Detail
                                </button>
                                @if(Auth::user()->role === 'admin')
                                    <button type="button" onclick="openJurnalModal({{ $pem->id }}, '{{ $pem->no_jurnal_beban ?? '' }}')" class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-300">
                                        No. Jurnal
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                Belum ada riwayat pemakaian ATK.
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
    {{ $pemakaians->links() }}
</div>

<!-- ================= MODAL TAMBAH PEMAKAIAN ATK ================= -->
<div id="modalFormPemakaian" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-lg md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Catat Pemakaian ATK
                </h3>
                <button type="button" onclick="closeFormModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            
            <form id="formPemakaian" onsubmit="handlePemakaianSubmit(event)">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Barang ATK <span class="text-red-500">*</span></label>
                        <select id="atk_id" name="atk_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="autoFillPriceAndStock()">
                            <option value="">-- Pilih Barang ATK --</option>
                            @foreach($atks as $atk)
                                <option value="{{ $atk->id }}" data-harga="{{ $atk->harga }}" data-satuan="{{ $atk->satuan }}" data-stok="{{ $atk->jumlah }}">
                                    {{ $atk->kode_atk }} - {{ $atk->nama_atk }} (Tersedia: {{ $atk->jumlah }} {{ $atk->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Kerja / Cabang <span class="text-red-500">*</span></label>
                            <input type="text" id="unit_kerja" name="unit_kerja" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Operasional Pusat">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pemakaian <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Pemakaian <span class="text-red-500">*</span></label>
                            <input type="number" id="jumlah" name="jumlah" min="1" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: 5" oninput="calculateTotal()">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga Satuan Berlaku</label>
                            <input type="text" id="harga_satuan_label" readonly class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300" value="Rp 0">
                        </div>
                    </div>

                    <div class="p-3 bg-red-50 dark:bg-gray-700/50 border border-red-200 dark:border-gray-600 rounded-lg flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">Estimasi Beban Biaya:</span>
                        <strong class="text-sm font-bold text-red-600 dark:text-red-400" id="previewBeban">Rp 0</strong>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. Jurnal Pembebanan Akhir Bulan</label>
                        <input type="text" id="no_jurnal_beban" name="no_jurnal_beban" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Opsional (dapat diisi akhir bulan oleh Akuntansi / Admin)">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keperluan Pemakaian</label>
                        <textarea id="keperluan" name="keperluan" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Keperluan operasional divisi / rapat kerja..."></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-700">
                    <button type="button" onclick="closeFormModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        Simpan & Potong Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL PEMAKAIAN ================= -->
<div id="modalDetailPemakaian" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-md md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Detail Catatan Pemakaian ATK
                </h3>
                <button type="button" onclick="closeDetailModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-3" id="detailPemakaianContent">
                <!-- Injected via JS -->
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
        document.getElementById('modalFormPemakaian').classList.remove('hidden');
        document.getElementById('modalFormPemakaian').classList.add('flex');
    }

    function closeFormModal() {
        document.getElementById('modalFormPemakaian').classList.add('hidden');
        document.getElementById('modalFormPemakaian').classList.remove('flex');
    }

    function closeDetailModal() {
        document.getElementById('modalDetailPemakaian').classList.add('hidden');
        document.getElementById('modalDetailPemakaian').classList.remove('flex');
    }

    async function openDetailModal(id) {
        try {
            const res = await fetch(`/pemakaian-atk/${id}`);
            const data = await res.json();
            if (!data.success) {
                Swal.fire('Error', data.message, 'error');
                return;
            }

            const p = data.data;
            document.getElementById('detailPemakaianContent').innerHTML = `
                <div class="space-y-2 text-sm bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div><span class="text-gray-500">Unit Kerja:</span> <strong class="text-gray-900 dark:text-white">${p.unit_kerja}</strong></div>
                    <div><span class="text-gray-500">Barang ATK:</span> <strong class="text-gray-900 dark:text-white">${p.atk ? p.atk.nama_atk : '-'} (${p.atk ? p.atk.kode_atk : ''})</strong></div>
                    <div><span class="text-gray-500">Jumlah Pemakaian:</span> <strong class="text-red-600">-${p.jumlah} ${p.atk ? p.atk.satuan : ''}</strong></div>
                    <div><span class="text-gray-500">Harga Satuan:</span> <span class="text-gray-900 dark:text-white">Rp ${parseInt(p.harga_satuan).toLocaleString('id-ID')}</span></div>
                    <div><span class="text-gray-500">Total Beban Biaya:</span> <strong class="text-red-600">Rp ${parseInt(p.total_beban_biaya).toLocaleString('id-ID')}</strong></div>
                    <div><span class="text-gray-500">Jurnal Pembebanan:</span> <strong class="font-mono text-blue-600">${p.no_jurnal_beban || 'Belum Dibukukan'}</strong></div>
                    <div><span class="text-gray-500">Tanggal:</span> <span class="text-gray-900 dark:text-white">${p.tanggal}</span></div>
                    <div><span class="text-gray-500">Petugas Input:</span> <span class="text-gray-900 dark:text-white">${p.user ? p.user.name : '-'}</span></div>
                    <div><span class="text-gray-500">Keperluan:</span> <p class="text-gray-800 dark:text-gray-200 mt-1">${p.keperluan || '-'}</p></div>
                </div>
            `;
            document.getElementById('modalDetailPemakaian').classList.remove('hidden');
            document.getElementById('modalDetailPemakaian').classList.add('flex');
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat detail pemakaian.', 'error');
        }
    }

    function openJurnalModal(id, currentJurnal) {
        Swal.fire({
            title: 'Update No. Jurnal Pembebanan',
            text: 'Masukkan nomor jurnal pembebanan akhir bulan:',
            input: 'text',
            inputValue: currentJurnal,
            inputPlaceholder: 'Contoh: JRN-BBN-2026-09-001',
            inputValidator: (val) => {
                if (!val || val.trim().length === 0) {
                    return 'Nomor jurnal tidak boleh kosong!';
                }
            },
            showCancelButton: true,
            confirmButtonText: 'Simpan Jurnal',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/pemakaian-atk/${id}/jurnal`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ 
                            no_jurnal_beban: result.value,
                            _token: '{{ csrf_token() }}' 
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                }
            }
        });
    }

    async function handlePemakaianSubmit(e) {
        e.preventDefault();
        const qty = parseInt(document.getElementById('jumlah').value) || 0;

        if (qty > currentAvailableStock) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Tidak Mencukupi!',
                text: `Jumlah pemakaian (${qty}) melebihi stok yang tersedia (${currentAvailableStock}).`
            });
            return;
        }

        const payload = {
            atk_id: document.getElementById('atk_id').value,
            unit_kerja: document.getElementById('unit_kerja').value,
            tanggal: document.getElementById('tanggal').value,
            jumlah: qty,
            no_jurnal_beban: document.getElementById('no_jurnal_beban').value,
            keperluan: document.getElementById('keperluan').value,
            _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch('/pemakaian-atk', {
                method: 'POST',
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
</script>
@endsection
