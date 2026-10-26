<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use App\Models\Kendaraan;
use App\Models\Aset;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    // --- QUERY BUILDERS ---
    
    public function buildAtkQuery(Request $request)
    {
        $branches = Branch::options();
        $query = Atk::withTrashed()->with([
            'stokHistori' => function ($q) use ($request, $branches) {
                if ($request->filled('start_date')) $q->whereDate('tanggal', '>=', $request->start_date);
                if ($request->filled('end_date')) $q->whereDate('tanggal', '<=', $request->end_date);
                if (array_key_exists($request->query('kode_cabang'), $branches)) {
                    $q->where('kode_cabang', $request->query('kode_cabang'));
                }
            },
            'pemakaian' => function ($q) use ($request, $branches) {
                if ($request->filled('start_date')) $q->whereDate('tanggal', '>=', $request->start_date);
                if ($request->filled('end_date')) $q->whereDate('tanggal', '<=', $request->end_date);
                if (array_key_exists($request->query('kode_cabang'), $branches)) {
                    $q->where('kode_cabang', $request->query('kode_cabang'));
                }
            },
            'cabangStoks' => function ($q) use ($request, $branches) {
                if (array_key_exists($request->query('kode_cabang'), $branches)) {
                    $q->where('kode_cabang', $request->query('kode_cabang'));
                }
            },
        ]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_atk', 'like', '%' . $request->search . '%')
                    ->orWhere('kode_atk', 'like', '%' . $request->search . '%');
            });
        }

        return $query;
    }

    public function mapAtkRow($atk, bool $branchFilter = false)
    {
        if ($atk->relationLoaded('cabangStoks') && $branchFilter) {
            $atk->jumlah = $atk->cabangStoks->sum('jumlah');
        }

        $atk->stok_masuk_periode = $atk->stokHistori->where('jenis_transaksi', 'Stok Masuk')->sum('jumlah');
        $atk->pemakaian_periode = $atk->pemakaian->sum('jumlah');
        $atk->beban_biaya_periode = $atk->pemakaian->sum('total_beban_biaya');
        
        $atk->nilai_persediaan_saat_ini = ($atk->jumlah ?? 0) * ($atk->harga ?? 0);
        $atk->stok = $atk->jumlah ?? 0;
        $atk->harga_satuan = $atk->harga ?? 0;
        
        $lastStok = $atk->stokHistori->where('no_jurnal', '!=', null)->sortByDesc('created_at')->first();
        $lastPakai = $atk->pemakaian->where('no_jurnal_beban', '!=', null)->sortByDesc('tanggal')->first();
        
        // 'jurnal_info' bukan kolom DB dan tidak punya accessor/cast, sehingga
        // $atk->jurnal_info[] = ... menulis pada array hasil __get() yang
        // dikembalikan by-value ("Indirect modification ... has no effect").
        // Susun pada array lokal lalu assign utuh sekali.
        $jurnalInfo = [];
        if ($lastStok) $jurnalInfo[] = 'Masuk: ' . $lastStok->no_jurnal;
        if ($lastPakai) $jurnalInfo[] = 'Beban: ' . $lastPakai->no_jurnal_beban;
        $atk->jurnal_info = $jurnalInfo;

        return $atk;
    }

    public function buildKendaraanQuery(Request $request)
    {
        $query = Kendaraan::withTrashed()->with([
            'perjalanans' => function($q) use ($request) {
                if ($request->filled('start_date')) $q->whereDate('tanggal', '>=', $request->start_date);
                if ($request->filled('end_date')) $q->whereDate('tanggal', '<=', $request->end_date);
            },
            'bbms' => function($q) use ($request) {
                if ($request->filled('start_date')) $q->whereDate('tanggal', '>=', $request->start_date);
                if ($request->filled('end_date')) $q->whereDate('tanggal', '<=', $request->end_date);
            },
            'pemeliharaans' => function($q) use ($request) {
                if ($request->filled('start_date')) $q->whereDate('tanggal', '>=', $request->start_date);
                if ($request->filled('end_date')) $q->whereDate('tanggal', '<=', $request->end_date);
            }
        ]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_kendaraan', 'like', '%' . $request->search . '%')
                    ->orWhere('jenis_kendaraan', 'like', '%' . $request->search . '%');
            });
        }

        if (array_key_exists($request->query('kode_cabang'), Branch::options())) {
            $query->where('kode_cabang', $request->query('kode_cabang'));
        }

        return $query;
    }

    public function mapKendaraanRow($k)
    {
        $k->total_jarak = $k->perjalanans->sum('jarak_tempuh');
        $k->total_bbm = $k->bbms->sum('total_biaya');
        $k->total_pemeliharaan = $k->pemeliharaans->sum('harga_onderdil') + $k->pemeliharaans->sum('biaya_jasa');
        
        $k->breakdown = $k->pemeliharaans->groupBy('jenis_perbaikan')->map(function($items) {
            return $items->sum('harga_onderdil') + $items->sum('biaya_jasa');
        })->toArray();
        
        return $k;
    }

    public function buildAsetQuery(Request $request)
    {
        $query = Aset::withTrashed();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_sertifikat', 'like', '%' . $request->search . '%')
                    ->orWhere('lokasi', 'like', '%' . $request->search . '%')
                    ->orWhere('kode_cabang', 'like', '%' . $request->search . '%');
            });
        }

        if (array_key_exists($request->query('kode_cabang'), Branch::options())) {
            $query->where('kode_cabang', $request->query('kode_cabang'));
        }
        
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'Aman') {
                $query->where(function($q) {
                    $q->whereNull('jatuh_tempo_sertifikat')
                      ->orWhereDate('jatuh_tempo_sertifikat', '>', \Carbon\Carbon::now()->addDays(90));
                });
            } elseif ($status === 'Akan Jatuh Tempo') {
                $query->whereDate('jatuh_tempo_sertifikat', '<=', \Carbon\Carbon::now()->addDays(90))
                      ->whereDate('jatuh_tempo_sertifikat', '>=', \Carbon\Carbon::now());
            } elseif ($status === 'Sudah Jatuh Tempo') {
                $query->whereDate('jatuh_tempo_sertifikat', '<', \Carbon\Carbon::now());
            }
        }

        return $query->orderBy('kode_cabang');
    }

    // --- HTML VIEWS ---

    public function atk(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');

        $branchFilter = array_key_exists($request->query('kode_cabang'), Branch::options());
        $atks = $this->buildAtkQuery($request)->get()->map(function($atk) use ($branchFilter) {
            return $this->mapAtkRow($atk, $branchFilter);
        });

        return view('shared.laporan.atk', [
            'atks' => $atks,
            'branches' => Branch::options(),
        ]);
    }

    public function kendaraan(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');

        $kendaraans = $this->buildKendaraanQuery($request)->get()->map(function($k) {
            return $this->mapKendaraanRow($k);
        });

        return view('shared.laporan.kendaraan', [
            'kendaraans' => $kendaraans,
            'branches' => Branch::options(),
        ]);
    }

    public function aset(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');

        $asets = $this->buildAsetQuery($request)->get();

        return view('shared.laporan.aset', [
            'asets' => $asets,
            'branches' => Branch::options(),
        ]);
    }
    // --- EXPORT LOGIC ---

    public function exportAtk(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');
        
        $format = $request->query('format', 'excel');
        $branchFilter = array_key_exists($request->query('kode_cabang'), Branch::options());
        $atks = $this->buildAtkQuery($request)->get()->map(function($atk) use ($branchFilter) {
            return $this->mapAtkRow($atk, $branchFilter);
        });

        if ($format === 'pdf') {
            AuditLogService::log('EXPORT', 'ATK_REPORT', 'User exported Laporan ATK to PDF', Auth::user());
            return view('shared.laporan.export.atk_pdf', compact('atks', 'request'));
        }

        AuditLogService::log('EXPORT', 'ATK_REPORT', 'User exported Laporan ATK to Excel (CSV)', Auth::user());
        $filename = 'laporan_atk_' . ($request->start_date ?? 'all') . '_' . ($request->end_date ?? 'all') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        $callback = function() use($atks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode ATK', 'Nama ATK', 'Jenis ATK', 'Satuan', 'Stok Masuk', 'Pemakaian', 'Beban Biaya (Rp)', 'Sisa Stok', 'Harga Satuan (Rp)', 'Nilai Persediaan (Rp)', 'Status']);
            foreach ($atks as $atk) {
                fputcsv($file, [
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
                    $atk->trashed() ? 'Dihapus (Histori)' : 'Aktif'
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportKendaraan(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');

        $format = $request->query('format', 'excel');
        $kendaraans = $this->buildKendaraanQuery($request)->get()->map(function($k) {
            return $this->mapKendaraanRow($k);
        });

        if ($format === 'pdf') {
            AuditLogService::log('EXPORT', 'KENDARAAN_REPORT', 'User exported Laporan Kendaraan to PDF', Auth::user());
            return view('shared.laporan.export.kendaraan_pdf', compact('kendaraans', 'request'));
        }

        AuditLogService::log('EXPORT', 'KENDARAAN_REPORT', 'User exported Laporan Kendaraan to XLSX', Auth::user());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kendaraan');
        $sheet->fromArray([
            'Plat Nomor',
            'Jenis Kendaraan',
            'Total Jarak (KM)',
            'Biaya BBM (Rp)',
            'Biaya Pemeliharaan (Rp)',
            'Status',
            'Tanggal Perjalanan',
            'Kilometer (Awal - Akhir)',
            'Foto KM Berangkat',
            'Foto KM Sampai',
        ], null, 'A1');

        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('2C3E50');
        foreach (['A' => 18, 'B' => 20, 'C' => 18, 'D' => 20, 'E' => 24, 'F' => 16, 'G' => 20, 'H' => 24, 'I' => 22, 'J' => 22] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $row = 2;
        foreach ($kendaraans as $kendaraan) {
            $perjalanans = $kendaraan->perjalanans->values();
            $rows = $perjalanans->isEmpty() ? [null] : $perjalanans;

            foreach ($rows as $index => $perjalanan) {
                if ($index === 0) {
                    $sheet->fromArray([
                        $kendaraan->nomor_kendaraan,
                        $kendaraan->jenis_kendaraan,
                        $kendaraan->total_jarak,
                        $kendaraan->total_bbm,
                        $kendaraan->total_pemeliharaan,
                        $kendaraan->trashed() ? 'Dihapus (Histori)' : 'Aktif',
                    ], null, "A{$row}");
                }

                if ($perjalanan) {
                    $sheet->setCellValue("G{$row}", $perjalanan->tanggal);
                    $sheet->setCellValue("H{$row}", "{$perjalanan->kilometer_awal} - {$perjalanan->kilometer_akhir}");

                    foreach ([
                        'I' => $perjalanan->foto_km_awal,
                        'J' => $perjalanan->foto_km_akhir,
                    ] as $column => $photoPath) {
                        if (!$photoPath || !Storage::disk('public')->exists($photoPath)) {
                            continue;
                        }

                        $drawing = new Drawing();
                        $drawing->setName($column === 'I' ? 'Kilometer Berangkat' : 'Kilometer Sampai');
                        $drawing->setDescription("Foto kilometer {$kendaraan->nomor_kendaraan} tanggal {$perjalanan->tanggal}");
                        $drawing->setPath(Storage::disk('public')->path($photoPath));
                        $drawing->setHeight(56);
                        $drawing->setCoordinates("{$column}{$row}");
                        $drawing->setOffsetX(6);
                        $drawing->setOffsetY(4);
                        $drawing->setWorksheet($sheet);
                    }
                }

                $sheet->getRowDimension($row)->setRowHeight(66);
                $row++;
            }
        }

        $sheet->freezePane('A2');
        $sheet->getAutoFilter()->setRange('A1:J' . max(1, $row - 1));
        $filename = 'laporan_kendaraan_' . ($request->start_date ?? 'all') . '_' . ($request->end_date ?? 'all') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportAset(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');

        $format = $request->query('format', 'excel');
        $asets = $this->buildAsetQuery($request)->get();

        if ($format === 'pdf') {
            AuditLogService::log('EXPORT', 'ASET_REPORT', 'User exported Laporan Aset to PDF', Auth::user());
            $branches = Branch::options();
            return view('shared.laporan.export.aset_pdf', compact('asets', 'request', 'branches'));
        }

        AuditLogService::log('EXPORT', 'ASET_REPORT', 'User exported Laporan Aset to Excel (CSV)', Auth::user());
        $filename = 'laporan_aset_' . ($request->status ?? 'all') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        $branches = Branch::options();
        $callback = function() use($asets, $branches) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Cabang', 'Nama Cabang', 'Nomor Sertifikat', 'Nama Pemilik', 'Lokasi', 'Luas Tanah (m2)', 'Jatuh Tempo', 'Status Sertifikat', 'Status Data']);
            foreach ($asets as $aset) {
                fputcsv($file, [
                    $aset->kode_cabang,
                    $branches[$aset->kode_cabang] ?? 'Belum ditentukan',
                    $aset->nomor_sertifikat,
                    $aset->nama_pemilik,
                    $aset->lokasi,
                    $aset->luas_tanah,
                    $aset->jatuh_tempo_sertifikat,
                    $aset->status_sertifikat,
                    $aset->trashed() ? 'Dihapus (Histori)' : 'Aktif'
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
