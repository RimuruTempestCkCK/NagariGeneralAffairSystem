<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aset extends Model
{
    use HasFactory, \App\Traits\Auditable, SoftDeletes;

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
    
    protected $appends = ['status_sertifikat'];

    public function getStatusSertifikatAttribute()
    {
        if (!$this->jatuh_tempo_sertifikat) {
            return 'Tidak Ada Jatuh Tempo';
        }

        $jatuhTempo = \Carbon\Carbon::parse($this->jatuh_tempo_sertifikat);
        $sekarang = \Carbon\Carbon::now();

        if ($jatuhTempo->isPast()) {
            return 'Sudah Jatuh Tempo';
        } elseif ($jatuhTempo->diffInDays($sekarang) <= 90) { // threshold 90 days
            return 'Akan Jatuh Tempo';
        }

        return 'Aman';
    }
}
