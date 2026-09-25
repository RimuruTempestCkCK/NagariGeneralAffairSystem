<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiKeamanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluasiKeamananController extends Controller
{
    public function index(Request $request)
    {
        $query = EvaluasiKeamanan::with('user');

        if ($request->filled('jenis_evaluasi')) {
            $query->where('jenis_evaluasi', $request->jenis_evaluasi);
        }

        if ($request->filled('lokasi_pengamanan')) {
            $query->where('lokasi_pengamanan', $request->lokasi_pengamanan);
        }
        
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if (Auth::user()->role === 'staff') {
            $query->where('user_id', Auth::id());
        }

        $evaluasis = $query->orderBy('tahun', 'desc')->orderBy('periode', 'desc')->paginate(10)->withQueryString();

        return view('evaluasi-keamanan.index', compact('evaluasis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_evaluasi' => 'required|in:Triwulan,Tahunan',
            'lokasi_pengamanan' => 'required|in:Kantor Pusat,Kantor Cabang,Unit Kerja / KCP / Kas',
            'nama_lokasi' => 'required|string|max:255',
            'periode' => 'required|string|max:100',
            'tahun' => 'required|digits:4|integer|min:2000',
            'hasil_evaluasi' => 'required|string',
            'rekomendasi' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $evaluasi = EvaluasiKeamanan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Evaluasi keamanan berhasil dicatat.',
            'data' => $evaluasi,
        ]);
    }

    public function show($id)
    {
        $evaluasi = EvaluasiKeamanan::with('user')->findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $evaluasi->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $evaluasi,
        ]);
    }

    public function update(Request $request, $id)
    {
        $evaluasi = EvaluasiKeamanan::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $evaluasi->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'jenis_evaluasi' => 'required|in:Triwulan,Tahunan',
            'lokasi_pengamanan' => 'required|in:Kantor Pusat,Kantor Cabang,Unit Kerja / KCP / Kas',
            'nama_lokasi' => 'required|string|max:255',
            'periode' => 'required|string|max:100',
            'tahun' => 'required|digits:4|integer|min:2000',
            'hasil_evaluasi' => 'required|string',
            'rekomendasi' => 'nullable|string',
        ]);

        $evaluasi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Evaluasi keamanan berhasil diperbarui.',
            'data' => $evaluasi,
        ]);
    }

    public function destroy($id)
    {
        $evaluasi = EvaluasiKeamanan::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $evaluasi->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $evaluasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evaluasi keamanan berhasil dihapus.',
        ]);
    }
}
