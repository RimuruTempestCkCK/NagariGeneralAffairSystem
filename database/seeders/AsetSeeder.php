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
                'kode_aset' => 'AST-001',
                'nama_aset' => 'Laptop Lenovo ThinkPad',
                'kondisi' => 'Baik',
                'lokasi' => 'Ruang IT',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_aset' => 'AST-002',
                'nama_aset' => 'Printer HP LaserJet',
                'kondisi' => 'Baik',
                'lokasi' => 'Ruang Administrasi',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_aset' => 'AST-003',
                'nama_aset' => 'Proyektor Epson',
                'kondisi' => 'Rusak Ringan',
                'lokasi' => 'Ruang Rapat Utama',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
