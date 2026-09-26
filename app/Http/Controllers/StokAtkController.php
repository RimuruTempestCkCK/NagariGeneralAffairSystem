<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use App\Models\StokAtk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokAtkController extends Controller
{
    /**
     * Display a listing of Stok ATK & histori transaksi (Single Page).
     */
    public function index(Request $request)
    {
        $query = StokAtk::with(['atk', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_jurnal', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('atk', function ($sub) use ($search) {
                      $sub->where('nama_atk', 'like', "%{$search}%")
                          ->orWhere('kode_atk', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('jenis_transaksi')) {
            $query->where('jenis_transaksi', $request->jenis_transaksi);
        }

        if ($request->filled('atk_id')) {
            $query->where('atk_id', $request->atk_id);
        }

        $transaksiStok = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $atks = Atk::where('status', 'Aktif')->orderBy('nama_atk')->get();

        // Ringkasan stok ATK keseluruhan
        $totalNilaiPersediaan = Atk::selectRaw('SUM(jumlah * harga) as total')->value('total') ?? 0;
        $totalItemAtk = Atk::count();
        $totalStokFisik = Atk::sum('jumlah');

        return view('admin.stok-atk.index', compact('transaksiStok', 'atks', 'totalNilaiPersediaan', 'totalItemAtk', 'totalStokFisik'));
    }

    /**
     * Store Stok Awal or Stok Masuk (with BYD journal & price history).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'atk_id' => 'required|exists:atks,id',
            'jenis_transaksi' => 'required|in:Stok Awal,Stok Masuk',
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'no_jurnal' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $atk = Atk::lockForUpdate()->findOrFail($validated['atk_id']);
            $hargaSebelumnya = $atk->harga;
            $totalHarga = $validated['jumlah'] * $validated['harga_satuan'];

            // Simpan riwayat transaksi stok
            $stok = StokAtk::create([
                'atk_id' => $atk->id,
                'user_id' => Auth::id(),
                'jenis_transaksi' => $validated['jenis_transaksi'],
                'jumlah' => $validated['jumlah'],
                'harga_satuan' => $validated['harga_satuan'],
                'total_harga' => $totalHarga,
                'harga_sebelumnya' => $hargaSebelumnya,
                'no_jurnal' => $validated['no_jurnal'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'tanggal' => $validated['tanggal'],
            ]);

            // Update master ATK: tambah jumlah dan update harga terkini
            $atk->jumlah += $validated['jumlah'];
            $atk->harga = $validated['harga_satuan']; // update harga terkini sesuai transaksi masuk
            $atk->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Transaksi {$validated['jenis_transaksi']} berhasil disimpan. Stok {$atk->nama_atk} bertambah {$validated['jumlah']}.",
                'data' => $stok,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat transaksi stok: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show detail of specific stok transaction.
     */
    public function show($id)
    {
        $stok = StokAtk::with(['atk', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $stok,
        ]);
    }
}

