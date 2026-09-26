<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use App\Models\PermintaanAtk;
use App\Models\PermintaanAtkItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermintaanAtkController extends Controller
{
    /**
     * Display a listing of the resource (Single Page CRUD).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PermintaanAtk::with(['user', 'approver', 'items.atk']);

        // Jika role staff, hanya lihat permintaan miliknya
        if ($user->role === 'staff') {
            $query->where('user_id', $user->id);
        }

        // Filter search (nomor PO atau unit kerja)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_po', 'like', "%{$search}%")
                  ->orWhere('unit_kerja', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $permintaans = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $atks = Atk::where('status', 'Aktif')->orderBy('nama_atk')->get();

        return view('permintaan-atk.index', compact('permintaans', 'atks'));
    }

    /**
     * Store a newly created Permintaan ATK / PO in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'unit_kerja' => 'required|string|max:255',
            'tanggal_permintaan' => 'required|date',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.atk_id' => 'required|exists:atks,id',
            'items.*.jumlah_diminta' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Generate nomor PO unik: PO-YYYYMMDD-XXXX
            $datePrefix = date('Ymd', strtotime($request->tanggal_permintaan));
            $lastPo = PermintaanAtk::where('nomor_po', 'like', "PO-{$datePrefix}-%")
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $seq = 1;
            if ($lastPo) {
                $parts = explode('-', $lastPo->nomor_po);
                if (count($parts) === 3) {
                    $seq = intval($parts[2]) + 1;
                }
            }
            $nomorPo = sprintf("PO-%s-%04d", $datePrefix, $seq);

            $status = $request->input('action') === 'draft' ? 'DRAFT' : 'PENDING';

            $permintaan = PermintaanAtk::create([
                'nomor_po' => $nomorPo,
                'user_id' => Auth::id(),
                'unit_kerja' => $request->unit_kerja,
                'status' => $status,
                'catatan' => $request->catatan,
                'tanggal_permintaan' => $request->tanggal_permintaan,
            ]);

            foreach ($request->items as $itemData) {
                PermintaanAtkItem::create([
                    'permintaan_atk_id' => $permintaan->id,
                    'atk_id' => $itemData['atk_id'],
                    'jumlah_diminta' => $itemData['jumlah_diminta'],
                ]);
            }

            DB::commit();

            \App\Services\NotificationService::notifyAdminNewPo($permintaan);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan ATK ' . $nomorPo . ' berhasil dibuat dengan status ' . $status . '.',
                'data' => $permintaan,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat permintaan ATK: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $permintaan = PermintaanAtk::with(['user', 'approver', 'items.atk'])->findOrFail($id);

        // Security check
        if (Auth::user()->role === 'staff' && $permintaan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $permintaan,
        ]);
    }

    /**
     * Update the specified resource (Hanya bisa diedit jika status masih DRAFT).
     */
    public function update(Request $request, $id)
    {
        $permintaan = PermintaanAtk::findOrFail($id);

        // Authorization check
        if (Auth::user()->role === 'staff' && $permintaan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($permintaan->status !== 'DRAFT') {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan tidak dapat diubah karena status sudah ' . $permintaan->status . '.',
            ], 422);
        }

        $request->validate([
            'unit_kerja' => 'required|string|max:255',
            'tanggal_permintaan' => 'required|date',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.atk_id' => 'required|exists:atks,id',
            'items.*.jumlah_diminta' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $status = $request->input('action') === 'submit' ? 'PENDING' : 'DRAFT';

            $permintaan->update([
                'unit_kerja' => $request->unit_kerja,
                'tanggal_permintaan' => $request->tanggal_permintaan,
                'catatan' => $request->catatan,
                'status' => $status,
            ]);

            // Hapus items lama dan masukkan yang baru
            PermintaanAtkItem::where('permintaan_atk_id', $permintaan->id)->delete();
            foreach ($request->items as $itemData) {
                PermintaanAtkItem::create([
                    'permintaan_atk_id' => $permintaan->id,
                    'atk_id' => $itemData['atk_id'],
                    'jumlah_diminta' => $itemData['jumlah_diminta'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan ATK berhasil diperbarui.',
                'data' => $permintaan,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui permintaan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit Permintaan dari DRAFT menjadi PENDING.
     */
    public function submit($id)
    {
        $permintaan = PermintaanAtk::findOrFail($id);

        if (Auth::user()->role === 'staff' && $permintaan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($permintaan->status !== 'DRAFT') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permintaan berstatus DRAFT yang dapat disubmit.',
            ], 422);
        }

        $permintaan->update(['status' => 'PENDING']);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan ' . $permintaan->nomor_po . ' berhasil disubmit dan menunggu approval.',
        ]);
    }

    /**
     * Remove the specified resource (Hanya bisa dihapus jika status DRAFT).
     */
    public function destroy($id)
    {
        $permintaan = PermintaanAtk::findOrFail($id);

        if (Auth::user()->role === 'staff' && $permintaan->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        if ($permintaan->status !== 'DRAFT') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permintaan berstatus DRAFT yang dapat dihapus.',
            ], 422);
        }

        $permintaan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permintaan ATK berhasil dihapus.',
        ]);
    }

    /**
     * Approve Permintaan ATK (Admin Only).
     */
    public function approve(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak: Hanya Admin yang dapat melakukan approval.'], 403);
        }

        $permintaan = PermintaanAtk::with('items')->findOrFail($id);

        if ($permintaan->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permintaan dengan status PENDING yang dapat disetujui.',
            ], 422);
        }

        // Set jumlah_disetujui sama dengan jumlah_diminta secara default
        foreach ($permintaan->items as $item) {
            $item->update(['jumlah_disetujui' => $item->jumlah_diminta]);
        }

        $permintaan->update([
            'status' => 'APPROVED',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'alasan_reject' => null,
        ]);

        \App\Services\AuditLogService::log('APPROVE', 'PO', 'Admin menyetujui PO ' . $permintaan->nomor_po, $permintaan, clone $permintaan, null);

        \App\Services\NotificationService::notifyStaffPoApproved($permintaan);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan ATK ' . $permintaan->nomor_po . ' berhasil disetujui (APPROVED).',
        ]);
    }

    /**
     * Reject Permintaan ATK (Admin Only).
     */
    public function reject(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak: Hanya Admin yang dapat menolak permintaan.'], 403);
        }

        $request->validate([
            'alasan_reject' => 'required|string|min:3|max:1000',
        ]);

        $permintaan = PermintaanAtk::findOrFail($id);

        if ($permintaan->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permintaan dengan status PENDING yang dapat ditolak.',
            ], 422);
        }

        $permintaan->update([
            'status' => 'REJECTED',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'alasan_reject' => $request->alasan_reject,
        ]);

        \App\Services\AuditLogService::log('REJECT', 'PO', 'Admin menolak PO ' . $permintaan->nomor_po . ' alasan: ' . $request->alasan_reject, $permintaan, clone $permintaan, null);

        \App\Services\NotificationService::notifyStaffPoRejected($permintaan);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan ATK ' . $permintaan->nomor_po . ' berhasil ditolak.',
        ]);
    }
}
