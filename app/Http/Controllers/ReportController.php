<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use App\Models\Kendaraan;
use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function atk(Request $request)
    {
        // Hanya Admin? Prompt: "Admin: dapat melihat seluruh laporan. Staff: hanya melihat data sesuai kewenangan existing... Jangan hanya menyembunyikan menu. Pastikan Staff tidak dapat membuka laporan Admin melalui URL langsung."
        if (Auth::user()->role === 'staff') {
            abort(403, 'Unauthorized. Staff tidak diizinkan melihat laporan rekapan master.');
        }

        $query = Atk::with([
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

        $atks = $query->get()->map(function($atk) {
            $atk->stok_masuk_periode = $atk->stokHistori->where('jenis_transaksi', 'Stok Masuk')->sum('jumlah');
            $atk->pemakaian_periode = $atk->pemakaian->sum('jumlah');
            $atk->beban_biaya_periode = $atk->pemakaian->sum('total_beban_biaya');
            
            // This is dynamic estimation for display (not accounting for time-travel historic stock)
            $atk->nilai_persediaan_saat_ini = ($atk->jumlah ?? 0) * ($atk->harga ?? 0); // Atk model uses 'jumlah' and 'harga'
            $atk->stok = $atk->jumlah ?? 0;
            $atk->harga_satuan = $atk->harga ?? 0;
            
            // Get latest Jurnal info
            $lastStok = $atk->stokHistori->where('no_jurnal', '!=', null)->sortByDesc('created_at')->first();
            $lastPakai = $atk->pemakaian->where('no_jurnal_beban', '!=', null)->sortByDesc('tanggal')->first();
            
            $atk->jurnal_info = [];
            if ($lastStok) $atk->jurnal_info[] = 'Masuk: ' . $lastStok->no_jurnal;
            if ($lastPakai) $atk->jurnal_info[] = 'Beban: ' . $lastPakai->no_jurnal_beban;
            
            return $atk;
        });

        return view('laporan.atk', compact('atks'));
    }

    public function kendaraan(Request $request)
    {
        if (Auth::user()->role === 'staff') {
            abort(403, 'Unauthorized.');
        }

        $query = Kendaraan::with([
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

        $kendaraans = $query->get()->map(function($k) {
            $k->total_jarak = $k->perjalanans->sum('jarak_tempuh');
            $k->total_bbm = $k->bbms->sum('total_biaya');
            
            // Jasa + Onderdil
            $k->total_pemeliharaan = $k->pemeliharaans->sum('harga_onderdil') + $k->pemeliharaans->sum('biaya_jasa');
            
            // Breakdown perbaikan jenis
            $k->breakdown = $k->pemeliharaans->groupBy('jenis_perbaikan')->map(function($items) {
                return $items->sum('harga_onderdil') + $items->sum('biaya_jasa');
            })->toArray();
            
            return $k;
        });

        return view('laporan.kendaraan', compact('kendaraans'));
    }

    public function aset(Request $request)
    {
        if (Auth::user()->role === 'staff') {
            abort(403, 'Unauthorized.');
        }

        $query = Aset::query();

        if ($request->filled('search')) {
            $query->where('nomor_sertifikat', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_cabang', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            // Because status is dynamic attribute, we have to filter in collection or recreate logic in DB.
            // Recreating logic in DB:
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

        $asets = $query->orderBy('kode_cabang')->get();

        return view('laporan.aset', compact('asets'));
    }
}
