<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Kendaraan::insert([
            [
                'nomor_kendaraan' => 'BA 1234 XY',
                'status_kendaraan' => 'Milik',
                'jenis_kendaraan' => 'Mobil Dinas',
                'tahun_kendaraan' => '2019',
                'nomor_bpkb' => 'BPKB-123456789',
                'nomor_stnk' => 'STNK-123456789',
                'jatuh_tempo_stnk' => now()->addMonths(6)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nomor_kendaraan' => 'BA 5678 ZA',
                'status_kendaraan' => 'Sewa',
                'jenis_kendaraan' => 'Minibus',
                'tahun_kendaraan' => '2021',
                'nomor_bpkb' => 'BPKB-987654321',
                'nomor_stnk' => 'STNK-987654321',
                'jatuh_tempo_stnk' => now()->addMonths(2)->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
