<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Atk;
use App\Models\AtkCabangStok;
use App\Models\StokAtk;
use App\Models\PermintaanAtk;
use App\Models\Kendaraan;
use App\Models\Keamanan;
use App\Models\Aset;
use App\Models\BbmKendaraan;
use App\Models\PemeliharaanKendaraan;
use App\Models\PerjalananKendaraan;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BrdFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up dummy roles
        $this->admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);
        $this->staff = User::factory()->create(['role' => 'staff', 'password' => bcrypt('password')]);
    }

    public function test_auth_login_logout()
    {
        // Password default dari factory
        $response = $this->post('/login', [
            'email' => $this->admin->email,
            'password' => 'password'
        ]);
        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        
        $this->post('/login', ['email' => $this->admin->email, 'password' => 'wrong'])->assertSessionHasErrors();
    }

    public function test_role_authorization_admin_routes()
    {
        $adminRoutes = [
            ['method' => 'get', 'uri' => '/admin/atk'],
            ['method' => 'get', 'uri' => '/admin/stok-atk'],
            ['method' => 'get', 'uri' => '/admin/branches'],
            ['method' => 'get', 'uri' => '/admin/pemeliharaan-kendaraan'],
            ['method' => 'get', 'uri' => '/admin/evaluasi-keamanan'],
            ['method' => 'get', 'uri' => '/admin/aset'],
            ['method' => 'get', 'uri' => '/admin/laporan/atk'],
        ];

        // Guest check
        foreach ($adminRoutes as $route) {
            $this->{$route['method']}($route['uri'])->assertRedirect('/');
        }

        // Staff check
        $this->actingAs($this->staff);
        foreach ($adminRoutes as $route) {
            $this->{$route['method']}($route['uri'])->assertStatus(403);
        }

        // Admin check
        $this->actingAs($this->admin);
        foreach ($adminRoutes as $route) {
            $this->{$route['method']}($route['uri'])->assertStatus(200);
        }
    }

    public function test_admin_can_manage_branches_and_new_branches_feed_request_dropdown()
    {
        $this->actingAs($this->admin);
        $this->get('/admin/branches')
            ->assertOk()
            ->assertSee('Tambah Cabang')
            ->assertSee('Batas maksimal 5 cabang sudah tercapai.')
            ->assertSee('disabled', false);

        $branch = \App\Models\Branch::where('kode', '005')->firstOrFail();

        $this->delete('/admin/branches/' . $branch->id)->assertRedirect();
        $this->post('/admin/branches', [
            'kode' => '006',
            'nama' => 'Cabang Enam',
        ])->assertRedirect();
        $this->put('/admin/branches/' . \App\Models\Branch::where('kode', '001')->value('id'), [
            'nama' => 'Kantor Pasar Raya',
        ])->assertRedirect();

        $this->assertDatabaseHas('branches', ['kode' => '006', 'nama' => 'Cabang Enam']);
        $this->assertDatabaseHas('branches', ['kode' => '001', 'nama' => 'Kantor Pasar Raya']);
        $this->actingAs($this->staff);
        $this->get('/staff/permintaan-atk')
            ->assertOk()
            ->assertSee('001 - Kantor Pasar Raya')
            ->assertSee('006 - Cabang Enam');
    }

    public function test_purchase_request_requires_a_managed_branch_code()
    {
        $atk = Atk::create([
            'kode_atk' => 'ATK-BRANCH-REQUIRED',
            'nama_atk' => 'Kertas',
            'jenis_atk' => 'Kertas',
            'satuan' => 'Rim',
            'jumlah' => 10,
            'harga' => 1000,
            'status' => 'Aktif',
        ]);

        $this->actingAs($this->staff)
            ->postJson('/staff/permintaan-atk', [
                'unit_kerja' => 'Cabang Manual',
                'tanggal_permintaan' => now()->toDateString(),
                'items' => [['atk_id' => $atk->id, 'jumlah_diminta' => 1]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('kode_cabang');

        $this->assertDatabaseMissing('permintaan_atks', ['unit_kerja' => 'Cabang Manual']);
    }

    public function test_branch_in_use_cannot_be_deleted()
    {
        $this->actingAs($this->admin);
        $branch = \App\Models\Branch::where('kode', '001')->firstOrFail();
        Kendaraan::create([
            'nomor_kendaraan' => 'BA 9876 XX',
            'kode_cabang' => $branch->kode,
            'status_kendaraan' => 'Milik',
            'jenis_kendaraan' => 'Mobil',
            'tahun_kendaraan' => 2021,
            'jatuh_tempo_stnk' => now()->addYear()->toDateString(),
            'kondisi' => 'Aktif',
        ]);

        $this->delete('/admin/branches/' . $branch->id)
            ->assertSessionHasErrors('branch');
        $this->assertDatabaseHas('branches', ['kode' => '001']);
    }

    public function test_atk_crud()
    {
        $this->actingAs($this->admin);
        // Create
        $response = $this->post('/admin/atk', [
            'kode_atk' => 'ATK-TEST1',
            'nama_atk' => 'Pena',
            'jenis_atk' => 'Alat Tulis',
            'satuan' => 'PCS',
            'jumlah' => 0,
            'kode_cabang' => '001',
            'harga' => 10000,
            'status' => 'Aktif',
        ]);
        // If it returns 200 JSON, don't check redirect
        $response->assertStatus($response->status() === 302 ? 302 : 200);
        $this->assertDatabaseHas('atks', ['nama_atk' => 'Pena']);
        $atk = Atk::where('nama_atk', 'Pena')->first();
        $this->assertDatabaseHas('atk_cabang_stoks', ['atk_id' => $atk->id, 'kode_cabang' => '001', 'jumlah' => 0]);

        // Update
        $this->put('/admin/atk/' . $atk->id, [
            'kode_atk' => 'ATK-TEST1',
            'nama_atk' => 'Pena Hitam',
            'jenis_atk' => 'Alat Tulis',
            'satuan' => 'PCS',
            'jumlah' => 0,
            'harga' => 12000,
            'status' => 'Aktif',
        ])->assertStatus($response->status() === 302 ? 302 : 200);
        $this->assertDatabaseHas('atks', ['nama_atk' => 'Pena Hitam', 'harga' => 12000]);

        // Generate barcode
        $this->post('/admin/atk/' . $atk->id . '/generate-qr')->assertStatus(200);
        $this->assertNotNull($atk->fresh()->qr_code);

        // Delete
        $this->delete('/admin/atk/' . $atk->id)->assertStatus($response->status() === 302 ? 302 : 200);
        $this->assertSoftDeleted('atks', ['id' => $atk->id]);
    }

    public function test_purchase_order_flow()
    {
        $atk = Atk::create([
            'kode_atk' => 'ATK-PO',
            'nama_atk' => 'Buku',
            'jenis_atk' => 'Kertas',
            'satuan' => 'PCS',
            'jumlah' => 100,
            'harga' => 5000,
            'status' => 'Aktif'
        ]);

        $this->actingAs($this->staff);
        // Create PO
        $poData = [
            'kode_cabang' => '001',
            'items' => [
                ['atk_id' => $atk->id, 'jumlah_diminta' => 10]
            ],
            'tanggal_permintaan' => now()->format('Y-m-d')
        ];
        $res = $this->post('/staff/permintaan-atk', $poData);
        if ($res->status() === 500) { $res->dump(); }
        $res->assertStatus($res->status() === 302 ? 302 : 200);
        $poId = PermintaanAtk::first()->id;

        // Validasi database
        $this->assertDatabaseHas('permintaan_atks', [
            'id' => $poId,
            'unit_kerja' => 'Pasar Raya',
            'kode_cabang' => '001',
        ]);

        // Admin approve
        $this->actingAs($this->admin);
        $resApprove = $this->post('/admin/permintaan-atk/' . $poId . '/approve');
        $this->assertTrue(in_array($resApprove->status(), [200, 302]));
        $this->assertDatabaseHas('permintaan_atks', ['id' => $poId, 'status' => 'APPROVED']);
        
        // Admin reject
        $poId2 = PermintaanAtk::create([
            'nomor_po' => 'PO-TEST-002',
            'user_id' => $this->staff->id,
            'unit_kerja' => 'Cabang Pembantu',
            'kode_cabang' => '002',
            'status' => 'PENDING',
            'tanggal_permintaan' => now()->format('Y-m-d')
        ])->id;
        
        $resReject = $this->post('/admin/permintaan-atk/' . $poId2 . '/reject', ['alasan_reject' => 'Ditolak test']);
        $this->assertDatabaseHas('permintaan_atks', ['id' => $poId2, 'status' => 'REJECTED']);
    }
    
    public function test_stock_usage_and_limit()
    {
        $atk = Atk::create([
            'kode_atk' => 'ATK-USG',
            'nama_atk' => 'Spidol',
            'jenis_atk' => 'Alat Tulis',
            'satuan' => 'PCS',
            'jumlah' => 100,
            'harga' => 10000,
            'status' => 'Aktif'
        ]);
        AtkCabangStok::create([
            'atk_id' => $atk->id,
            'kode_cabang' => '001',
            'jumlah' => 100,
        ]);
        $this->actingAs($this->staff);

        // Valid usage
        $response = $this->post('/staff/pemakaian-atk', [
            'atk_id' => $atk->id,
            'kode_cabang' => '001',
            'jumlah' => 5,
            'unit_kerja' => 'KCP',
            'tanggal' => now()->format('Y-m-d'),
            'keterangan' => 'Untuk rapat'
        ]);
        $response->assertStatus($response->status() === 302 ? 302 : 200);
        $this->assertDatabaseHas('atks', ['id' => $atk->id, 'jumlah' => 95]);

        // Exceeding stock limit
        $response = $this->post('/staff/pemakaian-atk', [
            'atk_id' => $atk->id,
            'kode_cabang' => '001',
            'jumlah' => 100, // current is 95
            'unit_kerja' => 'KCP',
            'tanggal' => now()->format('Y-m-d'),
            'keterangan' => 'Exceed stock'
        ]);
        $response->assertStatus(422);
        $this->assertDatabaseHas('atks', ['id' => $atk->id, 'jumlah' => 95]); // no change
        $this->assertDatabaseHas('atk_cabang_stoks', ['atk_id' => $atk->id, 'kode_cabang' => '001', 'jumlah' => 95]);
    }

    public function test_stock_page_filters_branch_transactions_and_balance()
    {
        $branchAtk = Atk::create([
            'kode_atk' => 'ATK-BRANCH-001',
            'nama_atk' => 'Kertas Cabang Satu',
            'jenis_atk' => 'Kertas',
            'satuan' => 'Rim',
            'jumlah' => 17,
            'harga' => 50000,
            'status' => 'Aktif',
        ]);
        $otherBranchAtk = Atk::create([
            'kode_atk' => 'ATK-BRANCH-002',
            'nama_atk' => 'Tinta Cabang Dua',
            'jenis_atk' => 'Tinta',
            'satuan' => 'Botol',
            'jumlah' => 9,
            'harga' => 75000,
            'status' => 'Aktif',
        ]);

        AtkCabangStok::create([
            'atk_id' => $branchAtk->id,
            'kode_cabang' => '001',
            'jumlah' => 17,
        ]);
        AtkCabangStok::create([
            'atk_id' => $otherBranchAtk->id,
            'kode_cabang' => '002',
            'jumlah' => 9,
        ]);
        StokAtk::create([
            'atk_id' => $branchAtk->id,
            'user_id' => $this->admin->id,
            'jenis_transaksi' => 'Stok Awal',
            'jumlah' => 17,
            'harga_satuan' => 50000,
            'total_harga' => 850000,
            'tanggal' => now()->toDateString(),
            'kode_cabang' => '001',
            'no_jurnal' => 'JRN-BRANCH-001',
        ]);
        StokAtk::create([
            'atk_id' => $otherBranchAtk->id,
            'user_id' => $this->admin->id,
            'jenis_transaksi' => 'Stok Awal',
            'jumlah' => 9,
            'harga_satuan' => 75000,
            'total_harga' => 675000,
            'tanggal' => now()->toDateString(),
            'kode_cabang' => '002',
            'no_jurnal' => 'JRN-BRANCH-002',
        ]);

        $this->actingAs($this->admin)
            ->get('/admin/stok-atk?kode_cabang=001')
            ->assertOk()
            ->assertSee('JRN-BRANCH-001')
            ->assertDontSee('JRN-BRANCH-002')
            ->assertSee('17 Unit');
    }
    
    public function test_barcode_scan_integration()
    {
        $atk = Atk::create([
            'kode_atk' => 'ATK-SCAN',
            'nama_atk' => 'Tinta',
            'jenis_atk' => 'Alat Tulis',
            'satuan' => 'PCS',
            'jumlah' => 100,
            'harga' => 10000,
            'status' => 'Aktif',
            'qr_code' => 'QR-12345'
        ]);
        $this->actingAs($this->admin);
        
        // Scan lookup using kode_atk
        $response = $this->get('/admin/atk/scan/lookup/' . $atk->kode_atk);
        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $atk->id);

        $this->get('/admin/atk/scan/lookup/INVALID')->assertStatus(404);
    }
    
    public function test_vehicle_flow()
    {
        $this->actingAs($this->admin);
        $kendaraan = Kendaraan::create([
            'nomor_kendaraan' => 'BA 1234 XY',
            'jenis_kendaraan' => 'Mobil',
            'merk' => 'Toyota',
            'tahun_kendaraan' => 2020,
            'status' => 'Tersedia',
            'jatuh_tempo_stnk' => now()->addYear()->format('Y-m-d')
        ]);
        
        $this->actingAs($this->admin);
        $response = $this->post('/admin/perjalanan-kendaraan', [
            'kendaraan_id' => $kendaraan->id,
            'tujuan' => 'Dinas Luar',
            'kilometer_awal' => 1000,
            'kilometer_akhir' => 1050,
            'keperluan' => 'Meeting',
            'tanggal' => now()->format('Y-m-d')
        ]);
        $response->assertStatus($response->status() === 302 ? 302 : 200);
        $this->assertDatabaseHas('perjalanan_kendaraans', ['kendaraan_id' => $kendaraan->id, 'tujuan' => 'Dinas Luar']);
    }

    public function test_vehicle_evidence_and_multiple_asset_documents_are_saved()
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $kendaraan = Kendaraan::create([
            'nomor_kendaraan' => 'BA 4321 ZZ',
            'kode_cabang' => '001',
            'status_kendaraan' => 'Milik',
            'jenis_kendaraan' => 'Mobil',
            'tahun_kendaraan' => 2022,
            'jatuh_tempo_stnk' => now()->addYear()->toDateString(),
            'kondisi' => 'Aktif',
        ]);

        $this->post('/admin/perjalanan-kendaraan', [
            'kendaraan_id' => $kendaraan->id,
            'tanggal' => now()->toDateString(),
            'kilometer_awal' => 100,
            'kilometer_akhir' => 150,
            'tujuan' => 'Pengujian unggah foto',
            'foto_km_awal' => UploadedFile::fake()->image('awal.jpg'),
            'foto_km_akhir' => UploadedFile::fake()->image('akhir.jpg'),
        ])->assertOk();
        $perjalanan = PerjalananKendaraan::firstOrFail();
        Storage::disk('public')->assertExists($perjalanan->foto_km_awal);
        Storage::disk('public')->assertExists($perjalanan->foto_km_akhir);

        $this->post('/admin/bbm-kendaraan', [
            'kendaraan_id' => $kendaraan->id,
            'tanggal' => now()->toDateString(),
            'liter' => 10,
            'harga_per_liter' => 10000,
            'jenis_bbm' => 'Pertamax',
            'foto_struk' => UploadedFile::fake()->image('struk.jpg'),
        ])->assertOk();
        Storage::disk('public')->assertExists(BbmKendaraan::firstOrFail()->foto_struk);

        $this->post('/admin/pemeliharaan-kendaraan', [
            'kendaraan_id' => $kendaraan->id,
            'tanggal' => now()->toDateString(),
            'jenis_perbaikan' => 'Service',
            'harga_onderdil' => 0,
            'biaya_jasa' => 50000,
            'foto_nota' => UploadedFile::fake()->image('nota.jpg'),
        ])->assertOk();
        Storage::disk('public')->assertExists(PemeliharaanKendaraan::firstOrFail()->foto_nota);

        $this->post('/admin/aset', [
            'kode_cabang' => '001',
            'nomor_sertifikat' => 'CERT-MULTI-001',
            'nama_pemilik' => 'Bank Nagari',
            'lokasi' => 'Pasar Raya',
            'luas_tanah' => 100,
            'lampiran_files' => [
                UploadedFile::fake()->create('sertifikat.pdf', 10, 'application/pdf'),
                UploadedFile::fake()->create('denah.pdf', 10, 'application/pdf'),
                UploadedFile::fake()->create('bukti.pdf', 10, 'application/pdf'),
            ],
        ])->assertOk();

        $aset = Aset::where('nomor_sertifikat', 'CERT-MULTI-001')->firstOrFail();
        $this->assertCount(3, $aset->lampiran_files);
        foreach ($aset->lampiran_files as $file) {
            Storage::disk('public')->assertExists($file);
        }
    }

    public function test_journey_photos_are_visible_in_history_and_vehicle_report()
    {
        Storage::fake('public');
        $this->actingAs($this->admin);

        $kendaraan = Kendaraan::create([
            'nomor_kendaraan' => 'BA 2345 PHOTO',
            'kode_cabang' => '001',
            'status_kendaraan' => 'Milik',
            'jenis_kendaraan' => 'Mobil',
            'tahun_kendaraan' => 2023,
            'jatuh_tempo_stnk' => now()->addYear()->toDateString(),
            'kondisi' => 'Aktif',
        ]);

        $this->post('/admin/perjalanan-kendaraan', [
            'kendaraan_id' => $kendaraan->id,
            'tanggal' => now()->toDateString(),
            'kilometer_awal' => 200,
            'kilometer_akhir' => 250,
            'tujuan' => 'Pengujian bukti foto',
            'foto_km_awal' => UploadedFile::fake()->image('kilometer-awal.jpg'),
            'foto_km_akhir' => UploadedFile::fake()->image('kilometer-akhir.jpg'),
        ])->assertOk();

        $perjalanan = PerjalananKendaraan::where('kendaraan_id', $kendaraan->id)->firstOrFail();

        $this->get('/admin/perjalanan-kendaraan')
            ->assertOk()
            ->assertSee('alt="Foto kilometer berangkat BA 2345 PHOTO"', false)
            ->assertSee($perjalanan->foto_km_awal)
            ->assertSee('alt="Foto kilometer sampai BA 2345 PHOTO"', false)
            ->assertSee($perjalanan->foto_km_akhir);

        $this->get('/admin/laporan/kendaraan?kode_cabang=001')
            ->assertOk()
            ->assertSee('Bukti Kilometer')
            ->assertSee('alt="Kilometer berangkat BA 2345 PHOTO"', false)
            ->assertSee($perjalanan->foto_km_awal)
            ->assertSee('alt="Kilometer sampai BA 2345 PHOTO"', false)
            ->assertSee($perjalanan->foto_km_akhir);

        $this->get('/admin/laporan/kendaraan/export?format=pdf&kode_cabang=001')
            ->assertOk()
            ->assertSee('alt="Foto kilometer berangkat BA 2345 PHOTO"', false)
            ->assertSee('storage/' . $perjalanan->foto_km_awal)
            ->assertSee('alt="Foto kilometer sampai BA 2345 PHOTO"', false)
            ->assertSee('storage/' . $perjalanan->foto_km_akhir);

        $excelResponse = $this->get('/admin/laporan/kendaraan/export?format=excel&kode_cabang=001');
        $excelResponse->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringStartsWith('PK', $excelResponse->streamedContent());

        $xlsxPath = tempnam(sys_get_temp_dir(), 'ngas-kendaraan-');
        try {
            file_put_contents($xlsxPath, $excelResponse->streamedContent());
            $workbook = \PhpOffice\PhpSpreadsheet\IOFactory::load($xlsxPath);
            $sheet = $workbook->getActiveSheet();
            $this->assertSame(2, count($sheet->getDrawingCollection()));
            $this->assertSame('BA 2345 PHOTO', $sheet->getCell('A2')->getValue());
            $workbook->disconnectWorksheets();
        } finally {
            unlink($xlsxPath);
        }
    }
    
    public function test_empty_database_pages()
    {
        $this->actingAs($this->admin);
        $this->get('/admin/dashboard')->assertStatus(200);
        $this->get('/admin/atk')->assertStatus(200);
        $this->get('/admin/kendaraan')->assertStatus(200);
        $this->get('/admin/aset')->assertStatus(200);
    }
}
