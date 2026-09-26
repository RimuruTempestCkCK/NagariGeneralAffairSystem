@extends('laporan.export.layout_pdf')

@section('title', 'Laporan Kendaraan - PDF')

@section('report_title', 'Laporan Operasional Kendaraan')

@section('filter_info')
    <strong>Periode:</strong> {{ $request->start_date ?? '-' }} s/d {{ $request->end_date ?? '-' }} <br>
    <strong>Pencarian:</strong> {{ $request->search ?? '-' }}
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Plat Nomor</th>
                <th>Jenis Kendaraan</th>
                <th>Jarak Tempuh</th>
                <th>Biaya BBM</th>
                <th>Biaya Pemeliharaan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kendaraans as $k)
            <tr>
                <td>{{ $k->plat_nomor }}</td>
                <td>{{ $k->jenis_kendaraan }}</td>
                <td class="text-right">{{ number_format($k->total_jarak, 0, ',', '.') }} Km</td>
                <td class="text-right">Rp {{ number_format($k->total_bbm, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($k->total_pemeliharaan, 0, ',', '.') }}</td>
                <td>{{ $k->trashed() ? 'Dihapus (Histori)' : 'Aktif' }}</td>
            </tr>
            @endforeach
            @if($kendaraans->isEmpty())
            <tr>
                <td colspan="6" class="text-center">Tidak ada data.</td>
            </tr>
            @endif
        </tbody>
    </table>
@endsection
