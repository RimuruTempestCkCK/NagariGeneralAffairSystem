<?php

namespace App\Http\Controllers;

use App\Models\Keamanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeamananController extends Controller
{
    public function index(Request $request)
    {
        $query = Keamanan::with('user');

        if ($request->filled('lokasi_pengamanan')) {
            $query->where('lokasi_pengamanan', $request->lokasi_pengamanan);
        }

        if ($request->filled('kondisi_keamanan')) {
            $query->where('kondisi_keamanan', $request->kondisi_keamanan);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal_laporan', [$request->tanggal_mulai, $request->tanggal_akhir]);
        }
        
        // Staff see their own input or based on their role
        if (Auth::user()->role === 'staff') {
            $query->where('user_id', Auth::id());
        }

        $laporans = $query->orderBy('tanggal_laporan', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('shared.keamanan.index', compact('laporans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lokasi_pengamanan' => 'required|in:Kantor Pusat,Kantor Cabang,Unit Kerja / KCP / Kas',
            'nama_lokasi' => 'required|string|max:255',
            'tanggal_laporan' => 'required|date',
            'shift' => 'required|in:Pagi,Siang,Malam',
            'petugas' => 'required|string|max:255',
            'kondisi_keamanan' => 'required|in:Aman Kondusif,Insiden / Masalah',
            'uraian_kegiatan' => 'required|string',
            'tindakan_lanjutan' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $laporan = Keamanan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Laporan keamanan berhasil dicatat.',
            'data' => $laporan,
        ]);
    }

    public function show($id)
    {
        $laporan = Keamanan::with('user')->findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $laporan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $laporan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $laporan = Keamanan::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $laporan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'lokasi_pengamanan' => 'required|in:Kantor Pusat,Kantor Cabang,Unit Kerja / KCP / Kas',
            'nama_lokasi' => 'required|string|max:255',
            'tanggal_laporan' => 'required|date',
            'shift' => 'required|in:Pagi,Siang,Malam',
            'petugas' => 'required|string|max:255',
            'kondisi_keamanan' => 'required|in:Aman Kondusif,Insiden / Masalah',
            'uraian_kegiatan' => 'required|string',
            'tindakan_lanjutan' => 'nullable|string',
        ]);

        $laporan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Laporan keamanan berhasil diperbarui.',
            'data' => $laporan,
        ]);
    }

    public function destroy($id)
    {
        $laporan = Keamanan::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $laporan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $laporan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Laporan keamanan berhasil dihapus.',
        ]);
    }
}

