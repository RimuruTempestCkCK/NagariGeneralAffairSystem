@extends('shared.laporan.export.layout_pdf')

@section('title', 'Laporan Aset - PDF')

@section('report_title', 'Laporan Data Aset & Sertifikat')

@section('filter_info')
    <strong>Status:</strong> {{ $request->status ?? 'Semua' }} <br>
    <strong>Pencarian:</strong> {{ $request->search ?? '-' }}
@endsection

@section('content')
    <table>
        <thead>
            <tr>
                <th>Kode Cabang</th>
                <th>Sertifikat</th>
                <th>Pemilik</th>
                <th>Luas Tanah</th>
                <th>Jatuh Tempo</th>
                <th>Status Sertifikat</th>
                <th>Status Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asets as $aset)
            <tr>
                <td>{{ $aset->kode_cabang }}</td>
                <td>{{ $aset->nomor_sertifikat }}</td>
                <td>{{ $aset->nama_pemilik }}</td>
                <td class="text-right">{{ number_format($aset->luas_tanah, 0, ',', '.') }} mÂ²</td>
                <td>{{ $aset->jatuh_tempo_sertifikat ? \Carbon\Carbon::parse($aset->jatuh_tempo_sertifikat)->format('d/m/Y') : '-' }}</td>
                <td>{{ $aset->status_sertifikat }}</td>
                <td>{{ $aset->trashed() ? 'Dihapus (Histori)' : 'Aktif' }}</td>
            </tr>
            @endforeach
            @if($asets->isEmpty())
            <tr>
                <td colspan="7" class="text-center">Tidak ada data.</td>
            </tr>
            @endif
        </tbody>
    </table>
@endsection

