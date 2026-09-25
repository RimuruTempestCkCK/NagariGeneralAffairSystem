<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BbmKendaraan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kendaraan_id',
        'user_id',
        'tanggal',
        'liter',
        'harga_per_liter',
        'total_biaya',
        'jenis_bbm',
        'catatan',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
