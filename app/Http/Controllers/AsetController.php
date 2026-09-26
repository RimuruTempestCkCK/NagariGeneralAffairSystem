<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AsetController extends Controller
{
    public function index(Request $request)
    {
        $query = Aset::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nomor_sertifikat', 'like', "%{$search}%")
                  ->orWhere('kode_cabang', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('nama_pemilik', 'like', "%{$search}%");
        }
        
        if (Auth::user()->role === 'staff') {
            // As per existing logic or lack thereof, Aset might be fully visible or managed by admin only.
            // Requirement: Staff: "mengikuti pola authorization existing."
            // But assets usually don't have user_id. For now, staff can only view? Or if they can create, there's no user_id tracking. 
            // The BRD doesn't strictly say staff can't view assets.
            // Let's allow view, but mutations are restricted in store/update/destroy methods, or we can just allow it if there's no user_id mapping.
            // I'll add user_id column if needed, or simply assume they can view.
        }

        $asets = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('shared.aset.index', compact('asets'));
    }

    public function store(Request $request)
    {
        // Admin only can create? The prompt says "Admin: dapat mengelola seluruh data aset... Staff: mengikuti pola authorization existing."
        if (Auth::user()->role === 'staff') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Hanya Admin yang dapat menambah aset.'], 403);
        }

        $validated = $request->validate([
            'kode_cabang' => 'required|string|max:255',
            'nomor_sertifikat' => 'required|string|max:255|unique:asets',
            'nama_pemilik' => 'required|string|max:255',
            'lokasi' => 'required|string',
            'luas_tanah' => 'required|numeric|min:0',
            'jatuh_tempo_sertifikat' => 'nullable|date',
            'lampiran_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('lampiran_bukti')) {
            $file = $request->file('lampiran_bukti');
            $filename = Str::slug($validated['nomor_sertifikat']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('lampiran_aset', $filename, 'public');
            $validated['lampiran_bukti'] = $path;
        }

        $aset = Aset::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data aset berhasil ditambahkan.',
            'data' => $aset,
        ]);
    }

    public function show($id)
    {
        $aset = Aset::findOrFail($id);
        
        $aset->lampiran_url = $aset->lampiran_bukti ? asset('storage/' . $aset->lampiran_bukti) : null;
        
        return response()->json([
            'success' => true,
            'data' => $aset,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role === 'staff') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Hanya Admin yang dapat mengubah aset.'], 403);
        }

        $aset = Aset::findOrFail($id);

        $validated = $request->validate([
            'kode_cabang' => 'required|string|max:255',
            'nomor_sertifikat' => 'required|string|max:255|unique:asets,nomor_sertifikat,' . $id,
            'nama_pemilik' => 'required|string|max:255',
            'lokasi' => 'required|string',
            'luas_tanah' => 'required|numeric|min:0',
            'jatuh_tempo_sertifikat' => 'nullable|date',
            'lampiran_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('lampiran_bukti')) {
            // Delete old file
            if ($aset->lampiran_bukti && Storage::disk('public')->exists($aset->lampiran_bukti)) {
                Storage::disk('public')->delete($aset->lampiran_bukti);
            }
            
            $file = $request->file('lampiran_bukti');
            $filename = Str::slug($validated['nomor_sertifikat']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('lampiran_aset', $filename, 'public');
            $validated['lampiran_bukti'] = $path;
        }

        $aset->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data aset berhasil diperbarui.',
            'data' => $aset,
        ]);
    }

    public function destroy($id)
    {
        if (Auth::user()->role === 'staff') {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Hanya Admin yang dapat menghapus aset.'], 403);
        }

        $aset = Aset::findOrFail($id);

        if ($aset->lampiran_bukti && Storage::disk('public')->exists($aset->lampiran_bukti)) {
            Storage::disk('public')->delete($aset->lampiran_bukti);
        }

        $aset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data aset berhasil dihapus.',
        ]);
    }
}

