<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AtkExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $query;
    protected $controller;

    public function __construct($query, $controller)
    {
        $this->query = $query;
        $this->controller = $controller;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Kode ATK',
            'Nama ATK',
            'Jenis ATK',
            'Satuan',
            'Stok Masuk',
            'Pemakaian',
            'Beban Biaya (Rp)',
            'Sisa Stok',
            'Harga Satuan (Rp)',
            'Nilai Persediaan (Rp)',
            'Jurnal Info',
            'Status Data'
        ];
    }

    public function map($row): array
    {
        $atk = $this->controller->mapAtkRow($row);
        
        return [
            $atk->kode_atk,
            $atk->nama_atk,
            $atk->jenis_atk,
            $atk->satuan,
            $atk->stok_masuk_periode,
            $atk->pemakaian_periode,
            $atk->beban_biaya_periode,
            $atk->stok,
            $atk->harga_satuan,
            $atk->nilai_persediaan_saat_ini,
            implode(', ', $atk->jurnal_info),
            $atk->trashed() ? 'Dihapus (History)' : 'Aktif'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']]],
        ];
    }
}
