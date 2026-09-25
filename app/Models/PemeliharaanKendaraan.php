<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeliharaanKendaraan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kendaraan_id',
        'user_id',
        'tanggal',
        'jenis_perbaikan',
        'onderdil',
        'harga_onderdil',
        'biaya_jasa',
        'total_biaya',
        'bengkel',
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
