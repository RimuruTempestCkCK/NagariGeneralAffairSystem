<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AtkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Atk::insert([
            [
                'kode_atk' => 'ATK-001',
                'nama_atk' => 'Kertas HVS A4 80gr',
                'jenis_atk' => 'Kertas',
                'satuan' => 'Rim',
                'harga' => 55000,
                'jumlah' => 150,
                'rekening_penampungan' => '100.111.001',
                'rekening_biaya' => '500.111.001',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_atk' => 'ATK-002',
                'nama_atk' => 'Tinta Printer Epson Black 664',
                'jenis_atk' => 'Tinta',
                'satuan' => 'Botol',
                'harga' => 85000,
                'jumlah' => 45,
                'rekening_penampungan' => '100.111.002',
                'rekening_biaya' => '500.111.002',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_atk' => 'ATK-003',
                'nama_atk' => 'Buku Tulis Eksekutif 100 Lembar',
                'jenis_atk' => 'Buku',
                'satuan' => 'Pcs',
                'harga' => 25000,
                'jumlah' => 80,
                'rekening_penampungan' => '100.111.003',
                'rekening_biaya' => '500.111.003',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_atk' => 'ATK-004',
                'nama_atk' => 'Pena Ballpoint Hitam',
                'jenis_atk' => 'Alat Tulis',
                'satuan' => 'Kotak',
                'harga' => 35000,
                'jumlah' => 30,
                'rekening_penampungan' => '100.111.004',
                'rekening_biaya' => '500.111.004',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
