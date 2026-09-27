<?php

namespace Database\Seeders;

use App\Models\Atk;
use App\Models\PemakaianAtk;
use App\Models\StokAtk;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransaksiAtkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $staff = User::where('role', 'staff')->first();

        if (!$admin || !$staff) return;

        $stokMasuk = [
            'ATK-001' => [25, 15],
            'ATK-002' => [10, 8],
            'ATK-003' => [20, 12],
            'ATK-004' => [5, 3],
        ];

        foreach ($stokMasuk as $kodeAtk => $jumlahMasuk) {
            $atk = Atk::where('kode_atk', $kodeAtk)->first();
            if (!$atk) continue;

            StokAtk::updateOrCreate(
                [
                    'atk_id' => $atk->id,
                    'jenis_transaksi' => 'Stok Awal',
                    'no_jurnal' => "STOK/AWAL/{$atk->kode_atk}",
                ],
                [
                    'user_id' => $admin->id,
                    'jumlah' => $atk->jumlah,
                    'harga_satuan' => $atk->harga,
                    'total_harga' => $atk->jumlah * $atk->harga,
                    'harga_sebelumnya' => null,
                    'keterangan' => 'Stok awal periode berjalan',
                    'tanggal' => $this->tanggalDalamBulan(20),
                ]
            );

            foreach ($jumlahMasuk as $index => $jumlah) {
                $urutan = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

                StokAtk::updateOrCreate(
                    [
                        'atk_id' => $atk->id,
                        'jenis_transaksi' => 'Stok Masuk',
                        'no_jurnal' => "STOK/MASUK/{$atk->kode_atk}/{$urutan}",
                    ],
                    [
                        'user_id' => $admin->id,
                        'jumlah' => $jumlah,
                        'harga_satuan' => $atk->harga,
                        'total_harga' => $jumlah * $atk->harga,
                        'harga_sebelumnya' => $atk->harga,
                        'keterangan' => $index === 0
                            ? 'Pengadaan melalui e-Katalog'
                            : 'Replenishment dari gudang regional',
                        'tanggal' => $this->tanggalDalamBulan(12 - ($index * 7)),
                    ]
                );
            }
        }

        $pemakaianRows = [
            [
                'kode' => 'ATK-001', 'jumlah' => 10, 'role' => 'admin', 'unit' => 'Kantor Pusat',
                'jurnal' => 'BEBAN/ATK-001/01', 'keperluan' => 'Cetak laporan keuangan dan proposal kredit',
                'hari' => 9,
            ],
            [
                'kode' => 'ATK-001', 'jumlah' => 5, 'role' => 'staff', 'unit' => 'Kantor Cabang Padang',
                'jurnal' => null, 'keperluan' => 'Cetak formulir dan surat unit kantor cabang',
                'hari' => 3,
            ],
            [
                'kode' => 'ATK-002', 'jumlah' => 2, 'role' => 'admin', 'unit' => 'Kantor Pusat',
                'jurnal' => 'BEBAN/ATK-002/01', 'keperluan' => 'Pengisian tinta printer unit teller',
                'hari' => 8,
            ],
            [
                'kode' => 'ATK-002', 'jumlah' => 1, 'role' => 'staff', 'unit' => 'Kantor Cabang Padang',
                'jurnal' => null, 'keperluan' => 'Penggantian tinta printer front office',
                'hari' => 2,
            ],
            [
                'kode' => 'ATK-003', 'jumlah' => 8, 'role' => 'admin', 'unit' => 'Kantor Pusat',
                'jurnal' => 'BEBAN/ATK-003/01', 'keperluan' => 'Persediaan buku catatan untuk rapat',
                'hari' => 7,
            ],
            [
                'kode' => 'ATK-003', 'jumlah' => 4, 'role' => 'staff', 'unit' => 'Kantor Cabang Bukittinggi',
                'jurnal' => null, 'keperluan' => 'Persediaan buku agenda unit operasional',
                'hari' => 1,
            ],
            [
                'kode' => 'ATK-004', 'jumlah' => 2, 'role' => 'admin', 'unit' => 'Kantor Pusat',
                'jurnal' => 'BEBAN/ATK-004/01', 'keperluan' => 'Persediaan alat tulis proposal',
                'hari' => 6,
            ],
            [
                'kode' => 'ATK-004', 'jumlah' => 1, 'role' => 'staff', 'unit' => 'Kantor Cabang Padang',
                'jurnal' => null, 'keperluan' => 'Penggantian pena meja resepsionis',
                'hari' => 1,
            ],
        ];

        foreach ($pemakaianRows as $row) {
            $atk = Atk::where('kode_atk', $row['kode'])->first();
            if (!$atk) continue;

            $user = $row['role'] === 'admin' ? $admin : $staff;
            $totalBeban = $row['jumlah'] * $atk->harga;

            PemakaianAtk::updateOrCreate(
                [
                    'atk_id' => $atk->id,
                    'user_id' => $user->id,
                    'keperluan' => $row['keperluan'],
                ],
                [
                    'unit_kerja' => $row['unit'],
                    'jumlah' => $row['jumlah'],
                    'harga_satuan' => $atk->harga,
                    'total_beban_biaya' => $totalBeban,
                    'no_jurnal_beban' => $row['jurnal'],
                    'tanggal' => $this->tanggalDalamBulan($row['hari']),
                ]
            );
        }
    }

    /**
     * Tanggal berada di bulan berjalan tanpa pernah melebihi hari ini.
     */
    private function tanggalDalamBulan(int $hariLalu): string
    {
        $awalBulan = Carbon::now()->startOfMonth();
        $tanggal = Carbon::now()->subDays($hariLalu);

        if ($tanggal->lt($awalBulan)) {
            $tanggal = $awalBulan->copy();
        }

        return $tanggal->toDateString();
    }
}
