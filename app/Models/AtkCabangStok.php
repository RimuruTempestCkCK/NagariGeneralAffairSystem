<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtkCabangStok extends Model
{
    protected $fillable = [
        'atk_id',
        'kode_cabang',
        'jumlah',
    ];

    public function atk()
    {
        return $this->belongsTo(Atk::class);
    }
}
