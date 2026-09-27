<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = \App\Models\User::where('role', 'admin')->first();
        $staff = \App\Models\User::where('role', 'staff')->first();
        
        $atk1 = \App\Models\Atk::where('kode_atk', 'ATK-001')->first();
        $atk2 = \App\Models\Atk::where('kode_atk', 'ATK-002')->first();
        $kendaraan = \App\Models\Kendaraan::where('nomor_kendaraan', 'BA 1234 XY')->first();

        if (!$staff || !$admin || !$atk1 || !$kendaraan) return;

        // 1. Permintaan ATK (Workflow: Draft, Pending, Approved, Rejected)
        
        // A. DRAFT
        $draft = \App\Models\PermintaanAtk::updateOrCreate(
            ['nomor_po' => 'PO-TEST-001'],
            [
                'user_id' => $staff->id,
                'unit_kerja' => 'Kantor Cabang Padang',
                'status' => 'DRAFT',
                'tanggal_permintaan' => now()->toDateString(),
            ]
        );
        \App\Models\PermintaanAtkItem::updateOrCreate(
            ['permintaan_atk_id' => $draft->id, 'atk_id' => $atk1->id],
            ['jumlah_diminta' => 5]
        );

        // B. PENDING (Menunggu Approval)
        $pending = \App\Models\PermintaanAtk::updateOrCreate(
            ['nomor_po' => 'PO-TEST-002'],
            [
                'user_id' => $staff->id,
                'unit_kerja' => 'Kantor Cabang Padang',
                'status' => 'PENDING',
                'tanggal_permintaan' => now()->toDateString(),
            ]
        );
        \App\Models\PermintaanAtkItem::updateOrCreate(
            ['permintaan_atk_id' => $pending->id, 'atk_id' => $atk2->id],
            ['jumlah_diminta' => 10]
        );

        // C. APPROVED
        $approved = \App\Models\PermintaanAtk::updateOrCreate(
            ['nomor_po' => 'PO-TEST-003'],
            [
                'user_id' => $staff->id,
                'unit_kerja' => 'Kantor Pusat',
                'status' => 'APPROVED',
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'tanggal_permintaan' => now()->subDays(2)->toDateString(),
            ]
        );
        \App\Models\PermintaanAtkItem::updateOrCreate(
            ['permintaan_atk_id' => $approved->id, 'atk_id' => $atk1->id],
            ['jumlah_diminta' => 20, 'jumlah_disetujui' => 20]
        );

        // D. REJECTED
        $rejected = \App\Models\PermintaanAtk::updateOrCreate(
            ['nomor_po' => 'PO-TEST-004'],
            [
                'user_id' => $staff->id,
                'unit_kerja' => 'Kantor Cabang Bukittinggi',
                'status' => 'REJECTED',
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'alasan_reject' => 'Stok sedang kosong, silakan ajukan bulan depan.',
                'tanggal_permintaan' => now()->subDays(5)->toDateString(),
            ]
        );
        \App\Models\PermintaanAtkItem::updateOrCreate(
            ['permintaan_atk_id' => $rejected->id, 'atk_id' => $atk2->id],
            ['jumlah_diminta' => 50]
        );

        // 2. Transaksi Kendaraan
        \App\Models\PerjalananKendaraan::updateOrCreate(
            [
                'kendaraan_id' => $kendaraan->id,
                'user_id' => $staff->id,
                'tanggal' => now()->subDays(1)->toDateString(),
                'tujuan' => 'Kunjungan Nasabah Area Padang'
            ],
            [
                'kilometer_awal' => 15000,
                'kilometer_akhir' => 15050,
                'jarak_tempuh' => 50,
                'keterangan' => 'Rutin',
            ]
        );

        \App\Models\BbmKendaraan::updateOrCreate(
            [
                'kendaraan_id' => $kendaraan->id,
                'user_id' => $staff->id,
                'tanggal' => now()->subDays(1)->toDateString(),
                'jenis_bbm' => 'Pertamax'
            ],
            [
                'liter' => 20,
                'harga_per_liter' => 13500,
                'total_biaya' => 20 * 13500,
            ]
        );

        \App\Models\PemeliharaanKendaraan::updateOrCreate(
            [
                'kendaraan_id' => $kendaraan->id,
                'user_id' => $staff->id,
                'tanggal' => now()->subDays(10)->toDateString(),
                'jenis_perbaikan' => 'Ganti Ban'
            ],
            [
                'onderdil' => 'Ban Dalam',
                'harga_onderdil' => 500000,
                'biaya_jasa' => 50000,
                'total_biaya' => 550000,
                'bengkel' => 'Bengkel Resmi Toyota'
            ]
        );

        // 3. Keamanan & Evaluasi
        \App\Models\Keamanan::updateOrCreate(
            [
                'user_id' => $staff->id,
                'tanggal_laporan' => now()->toDateString(),
                'shift' => 'Pagi'
            ],
            [
                'lokasi_pengamanan' => 'Kantor Cabang',
                'nama_lokasi' => 'KC Padang',
                'petugas' => 'Satpam Budi',
                'kondisi_keamanan' => 'Aman Kondusif',
                'uraian_kegiatan' => 'Patroli rutin aman terkendali'
            ]
        );

        \App\Models\EvaluasiKeamanan::updateOrCreate(
            [
                'user_id' => $admin->id,
                'jenis_evaluasi' => 'Triwulan',
                'periode' => 'Q3',
                'tahun' => date('Y'),
                'lokasi_pengamanan' => 'Kantor Pusat'
            ],
            [
                'nama_lokasi' => 'Gedung Pusat',
                'hasil_evaluasi' => 'Sistem CCTV perlu peremajaan di lantai 2.',
                'rekomendasi' => 'Anggarkan pengadaan CCTV baru.'
            ]
        );
    }
}
