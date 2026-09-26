<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use HasFactory, \App\Traits\Auditable, SoftDeletes;

    protected $fillable = [
        'nomor_kendaraan',
        'status_kendaraan',
        'jenis_kendaraan',
        'tahun_kendaraan',
        'nomor_bpkb',
        'nomor_stnk',
        'jatuh_tempo_stnk',
        'kondisi',
    ];

    public function perjalanans()
    {
        return $this->hasMany(PerjalananKendaraan::class, 'kendaraan_id');
    }

    public function bbms()
    {
        return $this->hasMany(BbmKendaraan::class, 'kendaraan_id');
    }

    public function pemeliharaans()
    {
        return $this->hasMany(PemeliharaanKendaraan::class, 'kendaraan_id');
    }
}
