@extends('layouts.dashboard')

@section('title', 'Perjalanan Kendaraan')

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
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Perjalanan Kendaraan</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Jarak Tempuh / Perjalanan Kendaraan</h1>
        </div>
        
        <div class="sm:flex">
            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('perjalanan-kendaraan.index') }}" class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                <div class="flex pl-0 mt-3 space-x-2 sm:pl-2 sm:mt-0">
                    <select name="kendaraan_id" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Kendaraan</option>
                        @foreach($kendaraans as $k)
                        <option value="{{ $k->id }}" {{ request('kendaraan_id') == $k->id ? 'selected' : '' }}>{{ $k->nomor_kendaraan }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </form>

            <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                <button type="button" onclick="openFormModal()" class="inline-flex items-center justify-center w-1/2 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Catat Perjalanan
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
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Kendaraan</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Tujuan</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jarak Tempuh</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($perjalanans as $p)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ date('d M Y', strtotime($p->tanggal)) }}
                            </td>
                            <td class="p-4 text-sm font-bold text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $p->kendaraan->nomor_kendaraan }}
                            </td>
                            <td class="p-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $p->tujuan }}
                            </td>
                            <td class="p-4 text-sm font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                {{ number_format($p->jarak_tempuh, 0, ',', '.') }} Km
                                <div class="text-xs font-normal text-gray-500">{{ $p->kilometer_awal }} - {{ $p->kilometer_akhir }}</div>
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap">
                                <button type="button" onclick="openFormModal({{ $p->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                    Edit
                                </button>
                                <button type="button" onclick="deleteData({{ $p->id }}, 'perjalanan ke {{ $p->tujuan }}')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data perjalanan.
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
    {{ $perjalanans->links() }}
</div>

<!-- ================= MODAL FORM ================= -->
<div id="modalForm" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-lg md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalFormTitle">Catat Perjalanan</h3>
                <button type="button" onclick="closeFormModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <form id="formData" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="perjalanan_id" name="id">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kendaraan <span class="text-red-500">*</span></label>
                        <select id="kendaraan_id" name="kendaraan_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Pilih Kendaraan...</option>
                            @foreach($kendaraans as $k)
                            <option value="{{ $k->id }}">{{ $k->nomor_kendaraan }} - {{ $k->jenis_kendaraan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Perjalanan <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal" name="tanggal" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kilometer Awal <span class="text-red-500">*</span></label>
                            <input type="number" id="kilometer_awal" name="kilometer_awal" required min="0" oninput="calculateJarak()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kilometer Akhir <span class="text-red-500">*</span></label>
                            <input type="number" id="kilometer_akhir" name="kilometer_akhir" required min="0" oninput="calculateJarak()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jarak Tempuh (Otomatis)</label>
                        <input type="text" id="jarak_tempuh" readonly class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tujuan <span class="text-red-500">*</span></label>
                        <input type="text" id="tujuan" name="tujuan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    </div>
                </div>
                <div class="flex items-center p-4 border-t border-gray-200 rounded-b dark:border-gray-700">
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700">Simpan Perjalanan</button>
                    <button type="button" onclick="closeFormModal()" class="ml-3 text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function calculateJarak() {
        const awal = parseInt(document.getElementById('kilometer_awal').value) || 0;
        const akhir = parseInt(document.getElementById('kilometer_akhir').value) || 0;
        document.getElementById('jarak_tempuh').value = (akhir >= awal ? akhir - awal : 0) + ' Km';
    }

    function openFormModal(id = null) {
        if (!id) {
            document.getElementById('modalFormTitle').textContent = 'Catat Perjalanan';
            document.getElementById('formData').reset();
            document.getElementById('perjalanan_id').value = '';
            document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalForm').classList.add('flex');
        } else {
            document.getElementById('modalFormTitle').textContent = 'Edit Perjalanan';
            fetch(`/perjalanan-kendaraan/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        document.getElementById('perjalanan_id').value = p.id;
                        document.getElementById('kendaraan_id').value = p.kendaraan_id;
                        document.getElementById('tanggal').value = p.tanggal;
                        document.getElementById('kilometer_awal').value = p.kilometer_awal;
                        document.getElementById('kilometer_akhir').value = p.kilometer_akhir;
                        document.getElementById('tujuan').value = p.tujuan;
                        document.getElementById('keterangan').value = p.keterangan || '';
                        calculateJarak();
                        
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

    async function handleFormSubmit(e) {
        e.preventDefault();
        
        const awal = parseInt(document.getElementById('kilometer_awal').value) || 0;
        const akhir = parseInt(document.getElementById('kilometer_akhir').value) || 0;
        
        if (akhir < awal) {
            Swal.fire('Validasi', 'Kilometer akhir tidak boleh lebih kecil dari kilometer awal!', 'warning');
            return;
        }

        const id = document.getElementById('perjalanan_id').value;
        const url = id ? `/perjalanan-kendaraan/${id}` : '/perjalanan-kendaraan';
        const method = id ? 'PUT' : 'POST';

        const payload = {
            kendaraan_id: document.getElementById('kendaraan_id').value,
            tanggal: document.getElementById('tanggal').value,
            kilometer_awal: awal,
            kilometer_akhir: akhir,
            tujuan: document.getElementById('tujuan').value,
            keterangan: document.getElementById('keterangan').value,
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

    function deleteData(id, desc) {
        Swal.fire({
            title: 'Hapus Data?',
            text: `Data ${desc} akan dihapus secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/perjalanan-kendaraan/${id}`, {
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
