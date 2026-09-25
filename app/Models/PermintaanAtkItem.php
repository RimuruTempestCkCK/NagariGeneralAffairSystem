<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanAtkItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'permintaan_atk_id',
        'atk_id',
        'jumlah_diminta',
        'jumlah_disetujui',
    ];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanAtk::class, 'permintaan_atk_id');
    }

    public function atk()
    {
        return $this->belongsTo(Atk::class, 'atk_id');
    }
}
