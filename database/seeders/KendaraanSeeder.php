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
                'no_polisi' => 'BA 1234 XY',
                'merk' => 'Toyota Avanza',
                'tahun_pembuatan' => 2019,
                'status' => 'Tersedia',
                'keterangan' => 'Kendaraan Operasional Cabang Padang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no_polisi' => 'BA 5678 ZA',
                'merk' => 'Honda Innova',
                'tahun_pembuatan' => 2021,
                'status' => 'Digunakan',
                'keterangan' => 'Kendaraan Pimpinan',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
