<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanAtk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_po',
        'user_id',
        'unit_kerja',
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
}
