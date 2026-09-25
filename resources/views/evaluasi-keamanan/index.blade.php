@extends('layouts.adminator')

@section('title', 'Evaluasi Keamanan')

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
                            <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Evaluasi Keamanan</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">Evaluasi Keamanan</h1>
        </div>
        
        <div class="sm:flex">
            <!-- Filter Form -->
            <form method="GET" action="{{ route('evaluasi-keamanan.index') }}" class="items-center hidden mb-3 sm:flex sm:divide-x sm:divide-gray-100 sm:mb-0 dark:divide-gray-700">
                <div class="flex pl-0 mt-3 space-x-2 sm:pl-2 sm:mt-0">
                    <select name="jenis_evaluasi" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Jenis</option>
                        <option value="Triwulan" {{ request('jenis_evaluasi') === 'Triwulan' ? 'selected' : '' }}>Triwulan</option>
                        <option value="Tahunan" {{ request('jenis_evaluasi') === 'Tahunan' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                    <select name="lokasi_pengamanan" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Semua Lokasi</option>
                        <option value="Kantor Pusat" {{ request('lokasi_pengamanan') === 'Kantor Pusat' ? 'selected' : '' }}>Kantor Pusat</option>
                        <option value="Kantor Cabang" {{ request('lokasi_pengamanan') === 'Kantor Cabang' ? 'selected' : '' }}>Kantor Cabang</option>
                        <option value="Unit Kerja / KCP / Kas" {{ request('lokasi_pengamanan') === 'Unit Kerja / KCP / Kas' ? 'selected' : '' }}>Unit Kerja / KCP / Kas</option>
                    </select>
                    <input type="number" name="tahun" value="{{ request('tahun') }}" placeholder="Tahun" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block p-2.5 w-24 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <button type="submit" class="p-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Cari
                    </button>
                </div>
            </form>

            <div class="flex items-center ml-auto space-x-2 sm:space-x-3">
                <button type="button" onclick="openFormModal()" class="inline-flex items-center justify-center w-1/2 px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Catat Evaluasi
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
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Periode / Tahun</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Jenis</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Lokasi</th>
                            <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($evaluasis as $p)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $p->periode }} - {{ $p->tahun }}
                            </td>
                            <td class="p-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300">
                                    {{ $p->jenis_evaluasi }}
                                </span>
                            </td>
                            <td class="p-4 text-sm font-bold text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $p->nama_lokasi }}
                                <div class="text-xs text-gray-500 font-normal mt-1">{{ $p->lokasi_pengamanan }}</div>
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap">
                                <button type="button" onclick="openDetailModal({{ $p->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600">
                                    Detail
                                </button>
                                <button type="button" onclick="openFormModal({{ $p->id }})" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                    Edit
                                </button>
                                <button type="button" onclick="deleteData({{ $p->id }}, 'evaluasi {{ $p->periode }} {{ $p->tahun }} di {{ $p->nama_lokasi }}')" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada data evaluasi keamanan.
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
    {{ $evaluasis->links() }}
</div>

<!-- ================= MODAL FORM ================= -->
<div id="modalForm" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 items-center justify-center hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-gray-900/50 backdrop-blur-sm">
    <div class="relative w-full h-full max-w-2xl md:h-auto">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-800">
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalFormTitle">Catat Evaluasi Keamanan</h3>
                <button type="button" onclick="closeFormModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <form id="formData" onsubmit="handleFormSubmit(event)">
                <input type="hidden" id="evaluasi_id" name="id">
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Evaluasi <span class="text-red-500">*</span></label>
                            <select id="jenis_evaluasi" name="jenis_evaluasi" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Triwulan">Triwulan</option>
                                <option value="Tahunan">Tahunan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tahun <span class="text-red-500">*</span></label>
                            <input type="number" id="tahun" name="tahun" required min="2000" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Lokasi <span class="text-red-500">*</span></label>
                            <select id="lokasi_pengamanan" name="lokasi_pengamanan" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Kantor Pusat">Kantor Pusat</option>
                                <option value="Kantor Cabang">Kantor Cabang</option>
                                <option value="Unit Kerja / KCP / Kas">Unit Kerja / KCP / Kas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lokasi <span class="text-red-500">*</span></label>
                            <input type="text" id="nama_lokasi" name="nama_lokasi" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Cth: Cabang Padang">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Periode (Keterangan Triwulan/Tahunan) <span class="text-red-500">*</span></label>
                        <input type="text" id="periode" name="periode" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Cth: Q1, Q2, Tahunan">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hasil Evaluasi <span class="text-red-500">*</span></label>
                        <textarea id="hasil_evaluasi" name="hasil_evaluasi" required rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rekomendasi / Tindak Lanjut</label>
                        <textarea id="rekomendasi" name="rekomendasi" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    </div>
                </div>
                <div class="flex items-center p-4 border-t border-gray-200 rounded-b dark:border-gray-700">
                    <button type="submit" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700">Simpan Evaluasi</button>
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
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Evaluasi Keamanan</h3>
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
            document.getElementById('modalFormTitle').textContent = 'Catat Evaluasi Keamanan';
            document.getElementById('formData').reset();
            document.getElementById('evaluasi_id').value = '';
            document.getElementById('tahun').value = new Date().getFullYear();
            document.getElementById('modalForm').classList.remove('hidden');
            document.getElementById('modalForm').classList.add('flex');
        } else {
            document.getElementById('modalFormTitle').textContent = 'Edit Evaluasi Keamanan';
            fetch(`/evaluasi-keamanan/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        document.getElementById('evaluasi_id').value = p.id;
                        document.getElementById('jenis_evaluasi').value = p.jenis_evaluasi;
                        document.getElementById('tahun').value = p.tahun;
                        document.getElementById('lokasi_pengamanan').value = p.lokasi_pengamanan;
                        document.getElementById('nama_lokasi').value = p.nama_lokasi;
                        document.getElementById('periode').value = p.periode;
                        document.getElementById('hasil_evaluasi').value = p.hasil_evaluasi;
                        document.getElementById('rekomendasi').value = p.rekomendasi || '';
                        
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
        fetch(`/evaluasi-keamanan/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const p = data.data;
                    document.getElementById('detailContent').innerHTML = `
                        <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="col-span-2 border-b pb-2 mb-2 border-gray-200 dark:border-gray-600">
                                <span class="text-gray-500 block mb-1">Lokasi:</span> 
                                <strong class="text-lg text-gray-900 dark:text-white uppercase">${p.nama_lokasi}</strong>
                                <div class="text-xs text-gray-500">${p.lokasi_pengamanan}</div>
                            </div>
                            
                            <div><span class="text-gray-500 block mb-1">Jenis Evaluasi:</span> <span class="text-gray-900 dark:text-white">${p.jenis_evaluasi}</span></div>
                            <div><span class="text-gray-500 block mb-1">Periode/Tahun:</span> <span class="text-gray-900 dark:text-white">${p.periode} - ${p.tahun}</span></div>
                            
                            <div class="col-span-2 border-t pt-2 mt-2 border-gray-200 dark:border-gray-600">
                                <span class="text-gray-500 block mb-1">Hasil Evaluasi:</span> 
                                <div class="text-gray-900 dark:text-white whitespace-pre-wrap">${p.hasil_evaluasi}</div>
                            </div>
                            
                            <div class="col-span-2 border-t pt-2 mt-2 border-gray-200 dark:border-gray-600">
                                <span class="text-gray-500 block mb-1">Rekomendasi:</span> 
                                <div class="text-gray-900 dark:text-white whitespace-pre-wrap">${p.rekomendasi || '-'}</div>
                            </div>
                        </div>
                    `;
                    document.getElementById('modalDetail').classList.remove('hidden');
                    document.getElementById('modalDetail').classList.add('flex');
                }
            })
            .catch(err => Swal.fire('Error', 'Gagal memuat detail evaluasi.', 'error'));
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        
        const id = document.getElementById('evaluasi_id').value;
        const url = id ? `/evaluasi-keamanan/${id}` : '/evaluasi-keamanan';
        const method = id ? 'PUT' : 'POST';

        const payload = {
            jenis_evaluasi: document.getElementById('jenis_evaluasi').value,
            tahun: document.getElementById('tahun').value,
            lokasi_pengamanan: document.getElementById('lokasi_pengamanan').value,
            nama_lokasi: document.getElementById('nama_lokasi').value,
            periode: document.getElementById('periode').value,
            hasil_evaluasi: document.getElementById('hasil_evaluasi').value,
            rekomendasi: document.getElementById('rekomendasi').value,
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
            title: 'Hapus Evaluasi?',
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
                    const res = await fetch(`/evaluasi-keamanan/${id}`, {
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
