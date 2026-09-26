<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    /**
     * Log an activity to the audit trail.
     *
     * @param string $action (e.g. CREATE, UPDATE, DELETE, LOGIN, APPROVE, REJECT)
     * @param string $module
     * @param string $description
     * @param mixed $subject
     * @param array|null $oldValues
     * @param array|null $newValues
     * @return void
     */
    public static function log($action, $module, $description, $subject = null, $oldValues = null, $newValues = null)
    {
        try {
            $oldValues = self::sanitize($oldValues);
            $newValues = self::sanitize($newValues);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject ? $subject->id : null,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Jika audit log gagal, jangan batalkan transaksi bisnis utama, cukup log ke Laravel Log
            Log::error('Gagal mencatat audit log: ' . $e->getMessage());
        }
    }

    /**
     * Remove sensitive data from payload
     */
    private static function sanitize($payload)
    {
        if (empty($payload) || !is_array($payload) && !is_object($payload)) {
            return $payload;
        }

        if (is_object($payload) && method_exists($payload, 'toArray')) {
            $payload = $payload->toArray();
        } elseif (is_object($payload)) {
            $payload = (array) $payload;
        }

        $sensitiveKeys = ['password', 'password_confirmation', 'remember_token', '_token', 'api_key', 'secret'];

        $sanitized = [];
        foreach ($payload as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $sanitized[$key] = '[HIDDEN]';
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
