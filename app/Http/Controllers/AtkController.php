<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AtkController extends Controller
{
    public function index(Request $request)
    {
        $query = Atk::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_atk', 'like', "%{$search}%")
                  ->orWhere('nama_atk', 'like', "%{$search}%")
                  ->orWhere('jenis_atk', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_atk')) {
            $query->where('jenis_atk', $request->jenis_atk);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $atks = $query->orderBy('kode_atk', 'asc')->paginate(10)->withQueryString();
        $jenisList = Atk::select('jenis_atk')->distinct()->pluck('jenis_atk');

        return view('admin.atk.index', compact('atks', 'jenisList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_atk' => 'required|string|max:50|unique:atks,kode_atk',
            'nama_atk' => 'required|string|max:255',
            'jenis_atk' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'jumlah' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'rekening_penampungan' => 'nullable|string|max:100',
            'rekening_biaya' => 'nullable|string|max:100',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $validated['qr_code'] = $validated['kode_atk'];

        $atk = Atk::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data ATK berhasil ditambahkan.',
            'data' => $atk,
        ]);
    }

    public function show($id)
    {
        $atk = Atk::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $atk,
        ]);
    }

    public function update(Request $request, $id)
    {
        $atk = Atk::findOrFail($id);

        $validated = $request->validate([
            'nama_atk' => 'required|string|max:255',
            'jenis_atk' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'jumlah' => 'required|integer|min:0',
            'harga' => 'required|numeric|min:0',
            'rekening_penampungan' => 'nullable|string|max:100',
            'rekening_biaya' => 'nullable|string|max:100',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $atk->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data ATK berhasil diperbarui.',
            'data' => $atk,
        ]);
    }

    public function destroy($id)
    {
        $atk = Atk::findOrFail($id);
        $atk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data ATK berhasil dihapus.',
        ]);
    }

    public function generateQr($id)
    {
        $atk = Atk::findOrFail($id);
        $atk->update(['qr_code' => $atk->kode_atk]);

        return response()->json([
            'success' => true,
            'message' => 'QR Code berhasil di-generate.',
            'qr_code' => $atk->qr_code,
        ]);
    }

    public function bulkPrint(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids)) {
            $ids = [];
        }

        $ids = array_unique($ids);
        
        $atks = Atk::whereIn('id', $ids)->where('status', 'Aktif')->get();
        
        if ($atks->isEmpty()) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu ATK aktif.');
        }
        
        if ($atks->count() > 100) {
            return redirect()->back()->with('error', 'Maksimal 100 QR Code dapat dicetak sekaligus.');
        }

        \App\Services\AuditLogService::log(
            'BULK_PRINT', 
            'ATK', 
            'Admin mencetak ' . $atks->count() . ' QR Code ATK', 
            null, null, 
            ['ids' => $atks->pluck('id')->toArray()]
        );

        return view('admin.atk.bulk-print', compact('atks'));
    }

    public function printQr($id)
    {
        $atk = Atk::findOrFail($id);
        $qrImage = base64_encode(QrCode::format('svg')->size(180)->generate($atk->kode_atk));

        return view('admin.atk.print-qr', compact('atk', 'qrImage'));
    }

    public function scanView()
    {
        return view('admin.atk.scan');
    }

    public function scanLookup($kode)
    {
        $atk = Atk::where('kode_atk', $kode)->first();

        if (!$atk) {
            return response()->json([
                'success' => false,
                'message' => 'Data ATK tidak ditemukan untuk kode: ' . $kode,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $atk,
        ]);
    }
}

