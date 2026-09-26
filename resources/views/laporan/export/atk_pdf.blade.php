@extends('laporan.export.layout_pdf')

@section('title', 'Laporan ATK - PDF')

@section('report_title', 'Laporan Persediaan & Pemakaian ATK')

@section('filter_info')
    <strong>Periode:</strong> {{ $request->start_date ?? '-' }} s/d {{ $request->end_date ?? '-' }} <br>
    <strong>Pencarian:</strong> {{ $request->search ?? '-' }}
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama ATK</th>
                <th>Jenis</th>
                <th>Stok Masuk</th>
                <th>Pemakaian</th>
                <th>Sisa Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atks as $atk)
            <tr>
                <td>{{ $atk->kode_atk }}</td>
                <td>{{ $atk->nama_atk }}</td>
                <td>{{ $atk->jenis_atk }}</td>
                <td class="text-center">{{ $atk->stok_masuk_periode }}</td>
                <td class="text-center">{{ $atk->pemakaian_periode }}</td>
                <td class="text-center">{{ $atk->stok }}</td>
                <td>{{ $atk->trashed() ? 'Dihapus (Histori)' : 'Aktif' }}</td>
            </tr>
            @endforeach
            @if($atks->isEmpty())
            <tr>
                <td colspan="7" class="text-center">Tidak ada data.</td>
            </tr>
            @endif
        </tbody>
    </table>
@endsection
