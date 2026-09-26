<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use App\Models\PemakaianAtk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemakaianAtkController extends Controller
{
    /**
     * Display a listing of Pemakaian ATK (Single Page CRUD).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PemakaianAtk::with(['atk', 'user']);

        // Jika staff, tampilkan data yang diinputnya
        if ($user->role === 'staff') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('unit_kerja', 'like', "%{$search}%")
                  ->orWhere('no_jurnal_beban', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%")
                  ->orWhereHas('atk', function ($sub) use ($search) {
                      $sub->where('nama_atk', 'like', "%{$search}%")
                          ->orWhere('kode_atk', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('unit_kerja')) {
            $query->where('unit_kerja', $request->unit_kerja);
        }

        $pemakaians = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $atks = Atk::where('status', 'Aktif')->where('jumlah', '>', 0)->orderBy('nama_atk')->get();

        // Agregasi pemakaian bulan ini
        $totalBebanBulanIni = PemakaianAtk::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_beban_biaya');
        $totalItemDipakaiBulanIni = PemakaianAtk::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');

        return view('shared.pemakaian-atk.index', compact('pemakaians', 'atks', 'totalBebanBulanIni', 'totalItemDipakaiBulanIni'));
    }

    /**
     * Store a newly created Pemakaian ATK with automatic stock deduction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'atk_id' => 'required|exists:atks,id',
            'unit_kerja' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'no_jurnal_beban' => 'nullable|string|max:100',
            'keperluan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $atk = Atk::lockForUpdate()->findOrFail($validated['atk_id']);

            // Validasi kecukupan stok
            if ($atk->jumlah < $validated['jumlah']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi! Stok saat ini untuk {$atk->nama_atk} hanya tersisa {$atk->jumlah} {$atk->satuan}.",
                ], 422);
            }

            $hargaSatuan = $atk->harga;
            $totalBeban = $validated['jumlah'] * $hargaSatuan;

            $pemakaian = PemakaianAtk::create([
                'atk_id' => $atk->id,
                'user_id' => Auth::id(),
                'unit_kerja' => $validated['unit_kerja'],
                'jumlah' => $validated['jumlah'],
                'harga_satuan' => $hargaSatuan,
                'total_beban_biaya' => $totalBeban,
                'no_jurnal_beban' => $validated['no_jurnal_beban'] ?? null,
                'keperluan' => $validated['keperluan'] ?? null,
                'tanggal' => $validated['tanggal'],
            ]);

            // Pemotongan stok otomatis secara atomic
            $atk->jumlah -= $validated['jumlah'];
            $atk->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Pemakaian ATK berhasil dicatat. Sisa stok {$atk->nama_atk} sekarang {$atk->jumlah} {$atk->satuan}.",
                'data' => $pemakaian,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat pemakaian ATK: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show detail of specific Pemakaian ATK.
     */
    public function show($id)
    {
        $pemakaian = PemakaianAtk::with(['atk', 'user'])->findOrFail($id);

        if (Auth::user()->role === 'staff' && $pemakaian->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $pemakaian,
        ]);
    }

    /**
     * Update nomor jurnal beban (khususnya pembukuan pembebanan akhir bulan oleh Admin).
     */
    public function updateJurnal(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Hanya Admin yang dapat memperbarui jurnal beban.'], 403);
        }

        $request->validate([
            'no_jurnal_beban' => 'required|string|max:100',
        ]);

        $pemakaian = PemakaianAtk::findOrFail($id);
        $pemakaian->update([
            'no_jurnal_beban' => $request->no_jurnal_beban,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nomor jurnal beban berhasil diperbarui.',
            'data' => $pemakaian,
        ]);
    }
}

