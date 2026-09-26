<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PoNotification;
use App\Notifications\DocumentExpiryNotification;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Notify Admins about a new PO
     */
    public static function notifyAdminNewPo($po)
    {
        $admins = User::where('role', 'admin')->get();
        $title = 'Permintaan ATK Baru';
        $message = "PO {$po->nomor_po} diajukan oleh {$po->user->name}.";
        $url = route('permintaan-atk.index'); // Admin views all POs here

        foreach ($admins as $admin) {
            $admin->notify(new PoNotification('PO_NEW', $title, $message, $url, $po->id));
        }
    }

    /**
     * Notify Staff about PO Approval
     */
    public static function notifyStaffPoApproved($po)
    {
        $title = 'Permintaan ATK Disetujui';
        $message = "PO {$po->nomor_po} telah disetujui.";
        $url = route('permintaan-atk.index');

        if ($po->user) {
            $po->user->notify(new PoNotification('PO_APPROVED', $title, $message, $url, $po->id));
        }
    }

    /**
     * Notify Staff about PO Rejection
     */
    public static function notifyStaffPoRejected($po)
    {
        $title = 'Permintaan ATK Ditolak';
        // The reason is already stored in $po->alasan_reject, we use it directly
        // We will escape it in the view, but safe to store as is
        $message = "PO {$po->nomor_po} ditolak. Alasan: {$po->alasan_reject}";
        $url = route('permintaan-atk.index');

        if ($po->user) {
            $po->user->notify(new PoNotification('PO_REJECTED', $title, $message, $url, $po->id));
        }
    }

    /**
     * Notify Admins about Document Expiry (STNK / Sertifikat)
     */
    public static function notifyAdminDocumentExpiry($typeCode, $title, $message, $url, $documentId, $uniqueKey)
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            // Deduplication Check
            $exists = DB::table('notifications')
                ->where('notifiable_id', $admin->id)
                ->where('notifiable_type', User::class)
                ->where('data->unique_key', $uniqueKey)
                ->whereNull('read_at') // Only skip if there's an UNREAD notification of the exact same event
                ->exists();

            if (!$exists) {
                $admin->notify(new DocumentExpiryNotification($typeCode, $title, $message, $url, $documentId, $uniqueKey));
            }
        }
    }
}
