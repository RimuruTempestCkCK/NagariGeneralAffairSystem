<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokAtk extends Model
{
    use HasFactory, \App\Traits\Auditable;

    protected $fillable = [
        'atk_id',
        'user_id',
        'jenis_transaksi',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'harga_sebelumnya',
        'no_jurnal',
        'keterangan',
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
