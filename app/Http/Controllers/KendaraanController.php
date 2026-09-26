<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kendaraan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kendaraan', 'like', "%{$search}%")
                  ->orWhere('jenis_kendaraan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_kendaraan')) {
            $query->where('status_kendaraan', $request->status_kendaraan);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $kendaraans = $query->orderBy('nomor_kendaraan', 'asc')->paginate(10)->withQueryString();

        return view('shared.kendaraan.index', compact('kendaraans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role === 'staff') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nomor_kendaraan' => 'required|string|max:50|unique:kendaraans,nomor_kendaraan',
            'status_kendaraan' => 'required|in:Milik,Sewa',
            'jenis_kendaraan' => 'required|string|max:100',
            'tahun_kendaraan' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'nomor_bpkb' => 'nullable|string|max:100',
            'nomor_stnk' => 'nullable|string|max:100',
            'jatuh_tempo_stnk' => 'required|date',
            'kondisi' => 'required|in:Aktif,Servis,Rusak',
        ]);

        $kendaraan = Kendaraan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Kendaraan berhasil ditambahkan.',
            'data' => $kendaraan,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        
        // Calculate STNK status string
        $today = now();
        $jatuhTempo = \Carbon\Carbon::parse($kendaraan->jatuh_tempo_stnk);
        $diffDays = $today->diffInDays($jatuhTempo, false);
        
        $stnk_status = 'Aman';
        if ($diffDays < 0) {
            $stnk_status = 'Sudah Jatuh Tempo';
        } elseif ($diffDays <= 30) {
            $stnk_status = 'Akan Jatuh Tempo (' . intval($diffDays) . ' hari lagi)';
        }
        
        // Append dynamic status
        $kendaraan->stnk_status_text = $stnk_status;

        return response()->json([
            'success' => true,
            'data' => $kendaraan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role === 'staff') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $kendaraan = Kendaraan::findOrFail($id);

        $validated = $request->validate([
            'nomor_kendaraan' => 'required|string|max:50|unique:kendaraans,nomor_kendaraan,' . $id,
            'status_kendaraan' => 'required|in:Milik,Sewa',
            'jenis_kendaraan' => 'required|string|max:100',
            'tahun_kendaraan' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'nomor_bpkb' => 'nullable|string|max:100',
            'nomor_stnk' => 'nullable|string|max:100',
            'jatuh_tempo_stnk' => 'required|date',
            'kondisi' => 'required|in:Aktif,Servis,Rusak',
        ]);

        $kendaraan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Kendaraan berhasil diperbarui.',
            'data' => $kendaraan,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role === 'staff') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $kendaraan = Kendaraan::findOrFail($id);
        $kendaraan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Kendaraan berhasil dihapus.',
        ]);
    }
}

