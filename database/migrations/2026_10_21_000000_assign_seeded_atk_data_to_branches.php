<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $seededAtkBranches = [
        'ATK-001' => ['nama_atk' => 'Kertas HVS A4 80gr', 'kode_cabang' => '001'],
        'ATK-002' => ['nama_atk' => 'Tinta Printer Epson Black 664', 'kode_cabang' => '002'],
        'ATK-003' => ['nama_atk' => 'Buku Tulis Eksekutif 100 Lembar', 'kode_cabang' => '003'],
        'ATK-004' => ['nama_atk' => 'Pena Ballpoint Hitam', 'kode_cabang' => '004'],
    ];

    public function up(): void
    {
        foreach ($this->seededAtkBranches as $kodeAtk => $mapping) {
            $atk = DB::table('atks')
                ->where('kode_atk', $kodeAtk)
                ->where('nama_atk', $mapping['nama_atk'])
                ->first(['id', 'jumlah']);

            if (!$atk) {
                continue;
            }

            DB::table('stok_atks')
                ->where('atk_id', $atk->id)
                ->whereNull('kode_cabang')
                ->update(['kode_cabang' => $mapping['kode_cabang']]);

            DB::table('pemakaian_atks')
                ->where('atk_id', $atk->id)
                ->whereNull('kode_cabang')
                ->update(['kode_cabang' => $mapping['kode_cabang']]);

            $branchStockExists = DB::table('atk_cabang_stoks')
                ->where('atk_id', $atk->id)
                ->where('kode_cabang', $mapping['kode_cabang'])
                ->exists();

            if (!$branchStockExists) {
                DB::table('atk_cabang_stoks')->insert([
                    'atk_id' => $atk->id,
                    'kode_cabang' => $mapping['kode_cabang'],
                    'jumlah' => $atk->jumlah,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
    }
};
