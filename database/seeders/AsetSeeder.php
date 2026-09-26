<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Aset::insert([
            [
                'kode_cabang' => 'CBG-001',
                'nomor_sertifikat' => 'SRT-001/Milik/2026',
                'nama_pemilik' => 'Bank Nagari',
                'lokasi' => 'Jl. Pemuda No. 21 Padang',
                'luas_tanah' => 1250.50,
                'jatuh_tempo_sertifikat' => now()->addYears(10)->toDateString(),
                'lampiran_bukti' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_cabang' => 'CBG-002',
                'nomor_sertifikat' => 'SRT-002/Milik/2026',
                'nama_pemilik' => 'Bank Nagari',
                'lokasi' => 'Jl. Sudirman No. 10 Bukittinggi',
                'luas_tanah' => 850.00,
                'jatuh_tempo_sertifikat' => now()->addYears(5)->toDateString(),
                'lampiran_bukti' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
