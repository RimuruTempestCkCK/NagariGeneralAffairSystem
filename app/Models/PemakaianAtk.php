<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemakaianAtk extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $fillable = [
        'atk_id',
        'user_id',
        'unit_kerja',
        'jumlah',
        'harga_satuan',
        'total_beban_biaya',
        'no_jurnal_beban',
        'keperluan',
        'tanggal',
    ];

    public function atk()
    {
        return $this->belongsTo(Atk::class, 'atk_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
