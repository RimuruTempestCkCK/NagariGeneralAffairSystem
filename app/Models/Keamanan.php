<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keamanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lokasi_pengamanan',
        'nama_lokasi',
        'tanggal_laporan',
        'shift',
        'petugas',
        'kondisi_keamanan',
        'uraian_kegiatan',
        'tindakan_lanjutan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
