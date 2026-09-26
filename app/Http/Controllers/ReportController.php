<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use App\Models\Kendaraan;
use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogService;

class ReportController extends Controller
{
    // --- QUERY BUILDERS ---
    
    public function buildAtkQuery(Request $request)
    {
        $query = Atk::withTrashed()->with([
            'stokHistori' => function($q) use ($request) {
                if ($request->filled('start_date')) $q->whereDate('created_at', '>=', $request->start_date);
                if ($request->filled('end_date')) $q->whereDate('created_at', '<=', $request->end_date);
            },
            'pemakaian' => function($q) use ($request) {
                if ($request->filled('start_date')) $q->whereDate('tanggal', '>=', $request->start_date);
                if ($request->filled('end_date')) $q->whereDate('tanggal', '<=', $request->end_date);
            }
        ]);

        if ($request->filled('search')) {
            $query->where('nama_atk', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_atk', 'like', '%' . $request->search . '%');
        }

        return $query;
    }

    public function mapAtkRow($atk)
    {
        $atk->stok_masuk_periode = $atk->stokHistori->where('jenis_transaksi', 'Stok Masuk')->sum('jumlah');
        $atk->pemakaian_periode = $atk->pemakaian->sum('jumlah');
        $atk->beban_biaya_periode = $atk->pemakaian->sum('total_beban_biaya');
        
        $atk->nilai_persediaan_saat_ini = ($atk->jumlah ?? 0) * ($atk->harga ?? 0);
        $atk->stok = $atk->jumlah ?? 0;
        $atk->harga_satuan = $atk->harga ?? 0;
        
        $lastStok = $atk->stokHistori->where('no_jurnal', '!=', null)->sortByDesc('created_at')->first();
        $lastPakai = $atk->pemakaian->where('no_jurnal_beban', '!=', null)->sortByDesc('tanggal')->first();
        
        $atk->jurnal_info = [];
        if ($lastStok) $atk->jurnal_info[] = 'Masuk: ' . $lastStok->no_jurnal;
        if ($lastPakai) $atk->jurnal_info[] = 'Beban: ' . $lastPakai->no_jurnal_beban;
        
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
            $query->where('plat_nomor', 'like', '%' . $request->search . '%')
                  ->orWhere('jenis_kendaraan', 'like', '%' . $request->search . '%');
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
            $query->where('nomor_sertifikat', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_cabang', 'like', '%' . $request->search . '%');
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

        $atks = $this->buildAtkQuery($request)->get()->map(function($atk) {
            return $this->mapAtkRow($atk);
        });

        return view('laporan.atk', compact('atks'));
    }

    public function kendaraan(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');

        $kendaraans = $this->buildKendaraanQuery($request)->get()->map(function($k) {
            return $this->mapKendaraanRow($k);
        });

        return view('laporan.kendaraan', compact('kendaraans'));
    }

    public function aset(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');

        $asets = $this->buildAsetQuery($request)->get();

        return view('laporan.aset', compact('asets'));
    }
    // --- EXPORT LOGIC ---

    public function exportAtk(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');
        
        $format = $request->query('format', 'excel');
        $atks = $this->buildAtkQuery($request)->get()->map(function($atk) {
            return $this->mapAtkRow($atk);
        });

        if ($format === 'pdf') {
            AuditLogService::log('EXPORT', 'ATK_REPORT', 'User exported Laporan ATK to PDF', Auth::user());
            return view('laporan.export.atk_pdf', compact('atks', 'request'));
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
            return view('laporan.export.kendaraan_pdf', compact('kendaraans', 'request'));
        }

        AuditLogService::log('EXPORT', 'KENDARAAN_REPORT', 'User exported Laporan Kendaraan to Excel (CSV)', Auth::user());
        $filename = 'laporan_kendaraan_' . ($request->start_date ?? 'all') . '_' . ($request->end_date ?? 'all') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        $callback = function() use($kendaraans) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Plat Nomor', 'Jenis Kendaraan', 'Jarak Tempuh', 'Biaya BBM (Rp)', 'Biaya Pemeliharaan (Rp)', 'Status']);
            foreach ($kendaraans as $k) {
                fputcsv($file, [
                    $k->plat_nomor,
                    $k->jenis_kendaraan,
                    $k->total_jarak,
                    $k->total_bbm,
                    $k->total_pemeliharaan,
                    $k->trashed() ? 'Dihapus (Histori)' : 'Aktif'
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportAset(Request $request)
    {
        if (Auth::user()->role === 'staff') abort(403, 'Unauthorized.');

        $format = $request->query('format', 'excel');
        $asets = $this->buildAsetQuery($request)->get();

        if ($format === 'pdf') {
            AuditLogService::log('EXPORT', 'ASET_REPORT', 'User exported Laporan Aset to PDF', Auth::user());
            return view('laporan.export.aset_pdf', compact('asets', 'request'));
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
        $callback = function() use($asets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Cabang', 'Nomor Sertifikat', 'Nama Pemilik', 'Lokasi', 'Luas Tanah (m2)', 'Jatuh Tempo', 'Status Sertifikat', 'Status Data']);
            foreach ($asets as $aset) {
                fputcsv($file, [
                    $aset->kode_cabang,
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
