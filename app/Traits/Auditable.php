<?php

namespace App\Traits;

use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            if (Auth::check()) {
                AuditLogService::log(
                    'CREATE',
                    class_basename($model),
                    'Membuat data ' . class_basename($model),
                    $model,
                    null,
                    $model->toArray()
                );
            }
        });

        static::updated(function ($model) {
            if (Auth::check()) {
                // Jangan log update jika ini adalah soft delete
                if (method_exists($model, 'isDirty') && $model->isDirty('deleted_at') && $model->deleted_at !== null) {
                    return;
                }

                AuditLogService::log(
                    'UPDATE',
                    class_basename($model),
                    'Memperbarui data ' . class_basename($model),
                    $model,
                    array_intersect_key($model->getOriginal(), $model->getDirty()),
                    $model->getDirty()
                );
            }
        });

        static::deleted(function ($model) {
            if (Auth::check()) {
                AuditLogService::log(
                    'DELETE',
                    class_basename($model),
                    'Menghapus data ' . class_basename($model),
                    $model,
                    $model->toArray(),
                    null
                );
            }
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                if (Auth::check()) {
                    AuditLogService::log(
                        'RESTORE',
                        class_basename($model),
                        'Restore data ' . class_basename($model),
                        $model,
                        null,
                        $model->toArray()
                    );
                }
            });
        }
    }
}
