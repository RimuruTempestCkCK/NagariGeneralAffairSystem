<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\PemeliharaanKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemeliharaanKendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PemeliharaanKendaraan::with(['kendaraan', 'user']);

        if ($request->filled('kendaraan_id')) {
            $query->where('kendaraan_id', $request->kendaraan_id);
        }

        if ($request->filled('jenis_perbaikan')) {
            $query->where('jenis_perbaikan', $request->jenis_perbaikan);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }
        
        if (Auth::user()->role === 'staff') {
            $query->where('user_id', Auth::id());
        }

        $pemeliharaans = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();
        $kendaraans = Kendaraan::orderBy('nomor_kendaraan', 'asc')->get();

        return view('shared.pemeliharaan-kendaraan.index', compact('pemeliharaans', 'kendaraans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal' => 'required|date',
            'jenis_perbaikan' => 'required|string|max:100',
            'onderdil' => 'nullable|string',
            'harga_onderdil' => 'required|numeric|min:0',
            'biaya_jasa' => 'required|numeric|min:0',
            'bengkel' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        $validated['total_biaya'] = $validated['harga_onderdil'] + $validated['biaya_jasa'];
        $validated['user_id'] = Auth::id();

        $pemeliharaan = PemeliharaanKendaraan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Pemeliharaan berhasil dicatat.',
            'data' => $pemeliharaan,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pemeliharaan = PemeliharaanKendaraan::with(['kendaraan', 'user'])->findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $pemeliharaan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $pemeliharaan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pemeliharaan = PemeliharaanKendaraan::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $pemeliharaan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal' => 'required|date',
            'jenis_perbaikan' => 'required|string|max:100',
            'onderdil' => 'nullable|string',
            'harga_onderdil' => 'required|numeric|min:0',
            'biaya_jasa' => 'required|numeric|min:0',
            'bengkel' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        $validated['total_biaya'] = $validated['harga_onderdil'] + $validated['biaya_jasa'];

        $pemeliharaan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Pemeliharaan berhasil diperbarui.',
            'data' => $pemeliharaan,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pemeliharaan = PemeliharaanKendaraan::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $pemeliharaan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $pemeliharaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Pemeliharaan berhasil dihapus.',
        ]);
    }
}

