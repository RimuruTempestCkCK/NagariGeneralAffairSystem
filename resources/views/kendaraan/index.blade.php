@extends('layouts.adminator')

@section('title', 'Master Kendaraan')

@section('content')
<div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
    <div class="w-full mb-1">
        <div class="mb-4">
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                            <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Master Kendaraan</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Master Data Kendaraan Operasional</h1>
        </div>
        
        <div class="sm:flex">
            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('kendaraan.index') }}" class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                <div class="relative mt-1 sm:w-64 xl:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Cari Plat Nomor / Jenis...">
                </div>
                <div class="flex pl-0 mt-3 space-x-2 sm:pl-2 sm:mt-0">
                    <select name="status_kendaraan" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Status Kepemilikan</option>
                        <option value="Milik" {{ request('status_kendaraan') === 'Milik' ? 'selected' : '' }}>Milik</option>
                        <option value="Sewa" {{ request('status_kendaraan') === 'Sewa' ? 'selected' : '' }}>Sewa</option>
                    </select>
                    <select name="kondisi" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Kondisi</option>
                        <option value="Aktif" {{ request('kondisi') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Servis" {{ request('kondisi') === 'Servis' ? 'selected' : '' }}>Servis</option>
                        <option value="Rusak" {{ request('kondisi') === 'Rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
            </form>

            <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                <button type="button" onclick="openFormModal()" class="inline-flex items-center justify-center w-1/2 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Tambah Kendaraan
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
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Nomor Kendaraan</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jenis</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Status</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Kondisi</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jatuh Tempo STNK</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
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
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-4 text-base font-bold text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $k->nomor_kendaraan }}
                            </td>
                            <td class="p-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $k->jenis_kendaraan }} <span class="text-gray-500 text-xs">({{ $k->tahun_kendaraan }})</span>
                            </td>
                            <td class="p-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $k->status_kendaraan }}
                            </td>
                            <td class="p-4 text-sm whitespace-nowrap">
                                @if($k->kondisi === 'Aktif')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Aktif</span>
                                @elseif($k->kondisi === 'Servis')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">Servis</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Rusak</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm font-medium whitespace-nowrap">
                                @if($stnkDanger)
                                    <span class="inline-flex items-center bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                        Expired ({{ date('d/m/Y', strtotime($k->jatuh_tempo_stnk)) }})
                                    </span>
                                @elseif($stnkWarning)
                                    <span class="inline-flex items-center bg-orange-100 text-orange-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-orange-900 dark:text-orange-300">
                                        Segera Habis ({{ date('d/m/Y', strtotime($k->jatuh_tempo_stnk)) }})
                                    </span>
                                @else
                                    <span class="text-gray-900 dark:text-white">
                                        {{ date('d/m/Y', strtotime($k->jatuh_tempo_stnk)) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap">
                                <button type="button" onclick="openDetailModal({{ $k->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600">
                                    Detail
                                </button>
                                <button type="button" onclick="openFormModal({{ $k->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                    Edit
                                </button>
                                <button type="button" onclick="deleteData({{ $k->id }}, '{{ $k->nomor_kendaraan }}')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data kendaraan operasional.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="p-4 bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    {{ $kendaraans->links() }}
</div>

<!-- ================= MODAL FORM ================= -->
<div id="modalForm" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalFormTitle">Tambah Kendaraan</h3>
                <button type="button" onclick="closeFormModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <form id="formData" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="kendaraan_id" name="id">
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Plat Nomor Kendaraan <span class="text-red-500">*</span></label>
                            <input type="text" id="nomor_kendaraan" name="nomor_kendaraan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white uppercase" placeholder="Contoh: BA 1234 XY">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kendaraan <span class="text-red-500">*</span></label>
                            <input type="text" id="jenis_kendaraan" name="jenis_kendaraan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: Minibus Avanza">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun Pembuatan <span class="text-red-500">*</span></label>
                            <input type="number" id="tahun_kendaraan" name="tahun_kendaraan" required min="1900" max="2030" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status Kepemilikan <span class="text-red-500">*</span></label>
                            <select id="status_kendaraan" name="status_kendaraan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Milik">Milik (Aset)</option>
                                <option value="Sewa">Sewa (Rental)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor BPKB</label>
                            <input type="text" id="nomor_bpkb" name="nomor_bpkb" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor STNK</label>
                            <input type="text" id="nomor_stnk" name="nomor_stnk" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Jatuh Tempo STNK <span class="text-red-500">*</span></label>
                            <input type="date" id="jatuh_tempo_stnk" name="jatuh_tempo_stnk" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kondisi Kendaraan <span class="text-red-500">*</span></label>
                            <select id="kondisi" name="kondisi" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Aktif">Aktif beroperasi</option>
                                <option value="Servis">Sedang diservis</option>
                                <option value="Rusak">Rusak berat / Tidak aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex items-center p-4 border-t border-gray-200 rounded-b dark:border-gray-700">
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700">Simpan Kendaraan</button>
                    <button type="button" onclick="closeFormModal()" class="ml-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL ================= -->
<div id="modalDetail" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-lg md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Kendaraan</h3>
                <button type="button" onclick="closeDetailModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-3" id="detailContent">
                <!-- Injected via JS -->
            </div>

            <div class="flex items-center p-4 border-t border-gray-200 rounded-b dark:border-gray-700">
                <button type="button" onclick="closeDetailModal()" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openFormModal(id = null) {
        if (!id) {
            document.getElementById('modalFormTitle').textContent = 'Tambah Kendaraan Operasional';
            document.getElementById('formData').reset();
            document.getElementById('kendaraan_id').value = '';
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalForm').classList.add('flex');
        } else {
            document.getElementById('modalFormTitle').textContent = 'Edit Kendaraan Operasional';
            fetch(`/kendaraan/${id}`)
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
                        
                        document.getElementById('modalForm').classList.remove('hidden');
                        document.getElementById('modalForm').classList.add('flex');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Gagal memuat data.', 'error'));
        }
    }

    function closeFormModal() {
        document.getElementById('modalForm').classList.add('hidden');
        document.getElementById('modalForm').classList.remove('flex');
    }

    function closeDetailModal() {
        document.getElementById('modalDetail').classList.add('hidden');
        document.getElementById('modalDetail').classList.remove('flex');
    }

    function openDetailModal(id) {
        fetch(`/kendaraan/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const k = data.data;
                    document.getElementById('detailContent').innerHTML = `
                        <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div><span class="text-gray-500 block mb-1">Nomor Kendaraan:</span> <strong class="text-lg text-gray-900 dark:text-white uppercase">${k.nomor_kendaraan}</strong></div>
                            <div><span class="text-gray-500 block mb-1">Status Kepemilikan:</span> <span class="bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 rounded dark:bg-gray-600 dark:text-gray-300">${k.status_kendaraan}</span></div>
                            
                            <div><span class="text-gray-500 block mb-1">Jenis Kendaraan:</span> <span class="text-gray-900 dark:text-white">${k.jenis_kendaraan}</span></div>
                            <div><span class="text-gray-500 block mb-1">Tahun:</span> <span class="text-gray-900 dark:text-white">${k.tahun_kendaraan}</span></div>
                            
                            <div><span class="text-gray-500 block mb-1">Kondisi:</span> <span class="text-gray-900 dark:text-white">${k.kondisi}</span></div>
                            <div><span class="text-gray-500 block mb-1">Warning STNK:</span> <strong class="${k.stnk_status_text.includes('Jatuh Tempo') ? 'text-red-600' : 'text-green-600'}">${k.stnk_status_text}</strong></div>
                            
                            <div class="col-span-2 border-t pt-2 mt-2 border-gray-200 dark:border-gray-600"></div>
                            
                            <div><span class="text-gray-500 block mb-1">No. STNK:</span> <span class="text-gray-900 dark:text-white font-mono">${k.nomor_stnk || '-'}</span></div>
                            <div><span class="text-gray-500 block mb-1">Jatuh Tempo STNK:</span> <span class="text-gray-900 dark:text-white">${k.jatuh_tempo_stnk}</span></div>
                            
                            <div class="col-span-2"><span class="text-gray-500 block mb-1">No. BPKB:</span> <span class="text-gray-900 dark:text-white font-mono">${k.nomor_bpkb || '-'}</span></div>
                        </div>
                    `;
                    document.getElementById('modalDetail').classList.remove('hidden');
                    document.getElementById('modalDetail').classList.add('flex');
                }
            })
            .catch(err => Swal.fire('Error', 'Gagal memuat detail kendaraan.', 'error'));
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('kendaraan_id').value;
        const url = id ? `/kendaraan/${id}` : '/kendaraan';
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
            Swal.fire('Error', 'Terjadi gangguan jaringan atau server.', 'error');
        }
    }

    function deleteData(id, nomor_kendaraan) {
        Swal.fire({
            title: 'Hapus Kendaraan?',
            text: `Data kendaraan ${nomor_kendaraan} akan dihapus secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/kendaraan/${id}`, {
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
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                }
            }
        });
    }
</script>
@endsection
