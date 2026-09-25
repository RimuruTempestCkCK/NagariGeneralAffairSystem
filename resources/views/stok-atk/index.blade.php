@extends('layouts.adminator')

@section('title', 'Stok ATK & Jurnal BYD')

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
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Stok ATK</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Pengelolaan Stok Awal & Stok Masuk ATK</h1>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700/50 dark:border-gray-600">
                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Total Jenis ATK</span>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalItemAtk) }} Item</h3>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700/50 dark:border-gray-600">
                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Total Fisik Stok Tersedia</span>
                <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ number_format($totalStokFisik) }} Unit</h3>
            </div>
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-700/50 dark:border-gray-600">
                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Total Nilai Persediaan (BYD)</span>
                <h3 class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($totalNilaiPersediaan, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="sm:flex">
            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('stok-atk.index') }}" class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                <div class="relative mt-1 sm:w-64 xl:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Cari No. Jurnal / Keterangan / ATK...">
                </div>
                <div class="flex pl-0 mt-3 space-x-2 sm:pl-2 sm:mt-0">
                    <select name="jenis_transaksi" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        <option value="">Semua Transaksi</option>
                        <option value="Stok Awal" {{ request('jenis_transaksi') === 'Stok Awal' ? 'selected' : '' }}>Stok Awal</option>
                        <option value="Stok Masuk" {{ request('jenis_transaksi') === 'Stok Masuk' ? 'selected' : '' }}>Stok Masuk</option>
                    </select>
                </div>
            </form>

            <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                <button type="button" onclick="openCreateModal('Stok Awal')" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-center text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-200 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    + Catat Stok Awal
                </button>
                <button type="button" onclick="openCreateModal('Stok Masuk')" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    + Tambah Stok Masuk
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Table Transaksi Riwayat Stok -->
<div class="flex flex-col">
    <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Tanggal</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jenis Transaksi</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Barang ATK</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jumlah</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Harga Satuan</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Total Harga</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">No. Jurnal (BYD)</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($transaksiStok as $stok)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                {{ date('d M Y', strtotime($stok->tanggal)) }}
                            </td>
                            <td class="p-4 text-sm whitespace-nowrap">
                                @if($stok->jenis_transaksi === 'Stok Awal')
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">Stok Awal</span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-emerald-900 dark:text-emerald-300">Stok Masuk</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $stok->atk->nama_atk ?? '-' }}
                                <span class="block text-xs font-normal text-gray-500">{{ $stok->atk->kode_atk ?? '' }}</span>
                            </td>
                            <td class="p-4 text-sm font-bold text-gray-900 whitespace-nowrap dark:text-white">
                                +{{ number_format($stok->jumlah) }} {{ $stok->atk->satuan ?? 'PCS' }}
                            </td>
                            <td class="p-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                Rp {{ number_format($stok->harga_satuan, 0, ',', '.') }}
                                @if($stok->harga_sebelumnya && $stok->harga_sebelumnya != $stok->harga_satuan)
                                    <span class="block text-[11px] text-yellow-600 dark:text-yellow-400">
                                        (Lama: Rp {{ number_format($stok->harga_sebelumnya, 0, ',', '.') }})
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-sm font-bold text-gray-900 whitespace-nowrap dark:text-white">
                                Rp {{ number_format($stok->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400 font-mono">
                                {{ $stok->no_jurnal ?? '-' }}
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap">
                                <button type="button" onclick="openDetailModal({{ $stok->id }})" class="inline-flex items-center px-3 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                Belum ada riwayat transaksi stok ATK.
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
    {{ $transaksiStok->links() }}
</div>

<!-- ================= MODAL TAMBAH STOK (AWAL / MASUK) ================= -->
<div id="modalFormStok" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-lg md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalStokTitle">
                    Tambah Stok Masuk ATK
                </h3>
                <button type="button" onclick="closeFormModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            
            <form id="formStok" onsubmit="handleStokSubmit(event)">
                <input type="hidden" id="jenis_transaksi" name="jenis_transaksi" value="Stok Masuk">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Barang ATK <span class="text-red-500">*</span></label>
                        <select id="atk_id" name="atk_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" onchange="autoFillAtkPrice()">
                            <option value="">-- Pilih Barang ATK --</option>
                            @foreach($atks as $atk)
                                <option value="{{ $atk->id }}" data-harga="{{ $atk->harga }}" data-satuan="{{ $atk->satuan }}" data-stok="{{ $atk->jumlah }}">
                                    {{ $atk->kode_atk }} - {{ $atk->nama_atk }} (Stok saat ini: {{ $atk->jumlah }} {{ $atk->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jumlah Masuk <span class="text-red-500">*</span></label>
                            <input type="number" id="jumlah" name="jumlah" min="1" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: 50" oninput="calculateTotal()">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" id="harga_satuan" name="harga_satuan" min="0" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0" oninput="calculateTotal()">
                        </div>
                    </div>

                    <div class="p-3 bg-blue-50 dark:bg-gray-700/50 border border-blue-200 dark:border-gray-600 rounded-lg flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">Estimasi Total Pembukuan:</span>
                        <strong class="text-sm font-bold text-primary-700 dark:text-primary-400" id="previewTotal">Rp 0</strong>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Transaksi <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No. Jurnal Pembukuan BYD</label>
                            <input type="text" id="no_jurnal" name="no_jurnal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: JRN-BYD-2026-001">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan / Faktur</label>
                        <textarea id="keterangan" name="keterangan" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Catatan pembelian / penerimaan dari supplier..."></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-700">
                    <button type="button" onclick="closeFormModal()" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                        Simpan Transaksi Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL RIWAYAT STOK ================= -->
<div id="modalDetailStok" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-md md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Detail Riwayat Transaksi Stok
                </h3>
                <button type="button" onclick="closeDetailModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-3" id="detailStokContent">
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
        const total = qty * harga;
        document.getElementById('previewTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
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
        document.getElementById('modalFormStok').classList.remove('hidden');
        document.getElementById('modalFormStok').classList.add('flex');
    }

    function closeFormModal() {
        document.getElementById('modalFormStok').classList.add('hidden');
        document.getElementById('modalFormStok').classList.remove('flex');
    }

    function closeDetailModal() {
        document.getElementById('modalDetailStok').classList.add('hidden');
        document.getElementById('modalDetailStok').classList.remove('flex');
    }

    async function openDetailModal(id) {
        try {
            const res = await fetch(`/stok-atk/${id}`);
            const data = await res.json();
            if (!data.success) {
                Swal.fire('Error', data.message, 'error');
                return;
            }

            const s = data.data;
            document.getElementById('detailStokContent').innerHTML = `
                <div class="space-y-2 text-sm bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div><span class="text-gray-500">Jenis:</span> <strong class="text-primary-600">${s.jenis_transaksi}</strong></div>
                    <div><span class="text-gray-500">Barang:</span> <strong class="text-gray-900 dark:text-white">${s.atk ? s.atk.nama_atk : '-'} (${s.atk ? s.atk.kode_atk : ''})</strong></div>
                    <div><span class="text-gray-500">Jumlah Masuk:</span> <strong class="text-green-600">+${s.jumlah} ${s.atk ? s.atk.satuan : ''}</strong></div>
                    <div><span class="text-gray-500">Harga Satuan:</span> <span class="text-gray-900 dark:text-white">Rp ${parseInt(s.harga_satuan).toLocaleString('id-ID')}</span></div>
                    <div><span class="text-gray-500">Harga Sebelum Transaksi:</span> <span class="text-gray-900 dark:text-white">${s.harga_sebelumnya ? 'Rp ' + parseInt(s.harga_sebelumnya).toLocaleString('id-ID') : '-'}</span></div>
                    <div><span class="text-gray-500">Total Nilai Transaksi:</span> <strong class="text-emerald-600">Rp ${parseInt(s.total_harga).toLocaleString('id-ID')}</strong></div>
                    <div><span class="text-gray-500">No. Jurnal Rekening BYD:</span> <strong class="font-mono text-gray-900 dark:text-white">${s.no_jurnal || '-'}</strong></div>
                    <div><span class="text-gray-500">Tanggal:</span> <span class="text-gray-900 dark:text-white">${s.tanggal}</span></div>
                    <div><span class="text-gray-500">Petugas Input:</span> <span class="text-gray-900 dark:text-white">${s.user ? s.user.name : '-'}</span></div>
                    <div><span class="text-gray-500">Keterangan:</span> <p class="text-gray-800 dark:text-gray-200 mt-1">${s.keterangan || '-'}</p></div>
                </div>
            `;
            document.getElementById('modalDetailStok').classList.remove('hidden');
            document.getElementById('modalDetailStok').classList.add('flex');
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat detail transaksi.', 'error');
        }
    }

    async function handleStokSubmit(e) {
        e.preventDefault();
        const payload = {
            atk_id: document.getElementById('atk_id').value,
            jenis_transaksi: document.getElementById('jenis_transaksi').value,
            tanggal: document.getElementById('tanggal').value,
            jumlah: document.getElementById('jumlah').value,
            harga_satuan: document.getElementById('harga_satuan').value,
            no_jurnal: document.getElementById('no_jurnal').value,
            keterangan: document.getElementById('keterangan').value,
            _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch('/stok-atk', {
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
