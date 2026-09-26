<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\PerjalananKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerjalananKendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PerjalananKendaraan::with(['kendaraan', 'user']);

        if ($request->filled('kendaraan_id')) {
            $query->where('kendaraan_id', $request->kendaraan_id);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }
        
        // Staff only see their own journey (if it follows PO logic, but since it's vehicle, let's keep it visible or specific to staff logic - per BRD usually staff inputs it, maybe we just show all or staff specific)
        if (Auth::user()->role === 'staff') {
            // Assuming staff only see their own input
            $query->where('user_id', Auth::id());
        }

        $perjalanans = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();
        $kendaraans = Kendaraan::where('kondisi', '!=', 'Rusak')->orderBy('nomor_kendaraan', 'asc')->get();

        return view('shared.perjalanan-kendaraan.index', compact('perjalanans', 'kendaraans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal' => 'required|date',
            'kilometer_awal' => 'required|integer|min:0',
            'kilometer_akhir' => 'required|integer|gte:kilometer_awal',
            'tujuan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $validated['jarak_tempuh'] = $validated['kilometer_akhir'] - $validated['kilometer_awal'];
        $validated['user_id'] = Auth::id();

        $perjalanan = PerjalananKendaraan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Perjalanan berhasil ditambahkan.',
            'data' => $perjalanan,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $perjalanan = PerjalananKendaraan::with(['kendaraan', 'user'])->findOrFail($id);
        
        // Security check for staff
        if (Auth::user()->role === 'staff' && $perjalanan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $perjalanan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $perjalanan = PerjalananKendaraan::findOrFail($id);
        
        // Security check for staff
        if (Auth::user()->role === 'staff' && $perjalanan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal' => 'required|date',
            'kilometer_awal' => 'required|integer|min:0',
            'kilometer_akhir' => 'required|integer|gte:kilometer_awal',
            'tujuan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $validated['jarak_tempuh'] = $validated['kilometer_akhir'] - $validated['kilometer_awal'];

        $perjalanan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Perjalanan berhasil diperbarui.',
            'data' => $perjalanan,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $perjalanan = PerjalananKendaraan::findOrFail($id);
        
        // Security check for staff
        if (Auth::user()->role === 'staff' && $perjalanan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $perjalanan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Perjalanan berhasil dihapus.',
        ]);
    }
}

