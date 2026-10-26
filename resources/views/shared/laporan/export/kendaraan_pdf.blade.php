@extends('shared.laporan.export.layout_pdf')

@section('title', 'Laporan Kendaraan - PDF')

@section('report_title', 'Laporan Operasional Kendaraan')

@section('filter_info')
    <strong>Periode:</strong> {{ $request->start_date ?? '-' }} s/d {{ $request->end_date ?? '-' }} <br>
    <strong>Pencarian:</strong> {{ $request->search ?? '-' }}
@endsection

@section('content')
    <style>
        .journey-proof { margin-bottom: 8px; page-break-inside: avoid; }
        .journey-proof-date { display: block; margin-bottom: 3px; font-size: 9px; color: #555; }
        .journey-proof-photo { display: inline-block; margin: 0 5px 3px 0; text-align: center; font-size: 9px; }
        .journey-proof-photo img { display: block; width: 72px; height: 54px; object-fit: cover; border: 1px solid #bbb; }
    </style>
    <table>
        <thead>
            <tr>
                <th>Plat Nomor</th>
                <th>Jenis Kendaraan</th>
                <th>Jarak Tempuh</th>
                <th>Bukti Kilometer</th>
                <th>Biaya BBM</th>
                <th>Biaya Pemeliharaan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kendaraans as $k)
            <tr>
                <td>{{ $k->nomor_kendaraan }}</td>
                <td>{{ $k->jenis_kendaraan }}</td>
                <td class="text-right">{{ number_format($k->total_jarak, 0, ',', '.') }} Km</td>
                <td>
                    @foreach($k->perjalanans as $perjalanan)
                        @if($perjalanan->foto_km_awal || $perjalanan->foto_km_akhir)
                        <div class="journey-proof">
                            <span class="journey-proof-date">{{ date('d/m/Y', strtotime($perjalanan->tanggal)) }}</span>
                            @if($perjalanan->foto_km_awal)
                            <a class="journey-proof-photo" href="{{ asset('storage/' . $perjalanan->foto_km_awal) }}">
                                <img src="{{ asset('storage/' . $perjalanan->foto_km_awal) }}" alt="Foto kilometer berangkat {{ $k->nomor_kendaraan }}">
                                Berangkat
                            </a>
                            @endif
                            @if($perjalanan->foto_km_akhir)
                            <a class="journey-proof-photo" href="{{ asset('storage/' . $perjalanan->foto_km_akhir) }}">
                                <img src="{{ asset('storage/' . $perjalanan->foto_km_akhir) }}" alt="Foto kilometer sampai {{ $k->nomor_kendaraan }}">
                                Sampai
                            </a>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </td>
                <td class="text-right">Rp {{ number_format($k->total_bbm, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($k->total_pemeliharaan, 0, ',', '.') }}</td>
                <td>{{ $k->trashed() ? 'Dihapus (Histori)' : 'Aktif' }}</td>
            </tr>
            @endforeach
            @if($kendaraans->isEmpty())
            <tr>
                <td colspan="7" class="text-center">Tidak ada data.</td>
            </tr>
            @endif
        </tbody>
    </table>
@endsection
