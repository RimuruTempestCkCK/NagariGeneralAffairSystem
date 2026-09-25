<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_cabang',
        'nomor_sertifikat',
        'nama_pemilik',
        'lokasi',
        'luas_tanah',
        'jatuh_tempo_sertifikat',
        'lampiran_bukti',
        'keterangan',
    ];
}
