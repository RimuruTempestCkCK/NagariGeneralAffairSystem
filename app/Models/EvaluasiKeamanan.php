<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiKeamanan extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $fillable = [
        'user_id',
        'jenis_evaluasi',
        'lokasi_pengamanan',
        'nama_lokasi',
        'periode',
        'tahun',
        'hasil_evaluasi',
        'rekomendasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
