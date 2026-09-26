<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Atk extends Model
{
    use HasFactory, \App\Traits\Auditable, SoftDeletes;

    protected $fillable = [
        'kode_atk',
        'nama_atk',
        'jenis_atk',
        'satuan',
        'jumlah',
        'harga',
        'rekening_penampungan',
        'rekening_biaya',
        'qr_code',
        'status',
    ];

    public function stokHistori()
    {
        return $this->hasMany(StokAtk::class, 'atk_id');
    }

    public function pemakaian()
    {
        return $this->hasMany(PemakaianAtk::class, 'atk_id');
    }
}
