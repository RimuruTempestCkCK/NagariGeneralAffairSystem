<?php

namespace App\Http\Controllers;

use App\Models\BbmKendaraan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BbmKendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BbmKendaraan::with(['kendaraan', 'user']);

        if ($request->filled('kendaraan_id')) {
            $query->where('kendaraan_id', $request->kendaraan_id);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }
        
        if (Auth::user()->role === 'staff') {
            $query->where('user_id', Auth::id());
        }

        $bbms = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();
        $kendaraans = Kendaraan::where('kondisi', '!=', 'Rusak')->orderBy('nomor_kendaraan', 'asc')->get();

        return view('shared.bbm-kendaraan.index', compact('bbms', 'kendaraans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal' => 'required|date',
            'liter' => 'required|numeric|min:0.1',
            'harga_per_liter' => 'required|numeric|min:0',
            'jenis_bbm' => 'required|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $validated['total_biaya'] = $validated['liter'] * $validated['harga_per_liter'];
        $validated['user_id'] = Auth::id();

        $bbm = BbmKendaraan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data BBM berhasil dicatat.',
            'data' => $bbm,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bbm = BbmKendaraan::with(['kendaraan', 'user'])->findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $bbm->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $bbm,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bbm = BbmKendaraan::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $bbm->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal' => 'required|date',
            'liter' => 'required|numeric|min:0.1',
            'harga_per_liter' => 'required|numeric|min:0',
            'jenis_bbm' => 'required|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        $validated['total_biaya'] = $validated['liter'] * $validated['harga_per_liter'];

        $bbm->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data BBM berhasil diperbarui.',
            'data' => $bbm,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bbm = BbmKendaraan::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $bbm->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $bbm->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data BBM berhasil dihapus.',
        ]);
    }
}

