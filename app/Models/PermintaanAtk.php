<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanAtk extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $fillable = [
        'nomor_po',
        'user_id',
        'unit_kerja',
        'kode_cabang',
        'status',
        'catatan',
        'approved_by',
        'approved_at',
        'alasan_reject',
        'tanggal_permintaan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(PermintaanAtkItem::class, 'permintaan_atk_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'kode_cabang', 'kode');
    }
}
