<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Atk;
use App\Models\StokAtk;
use App\Models\PermintaanAtk;
use App\Models\Kendaraan;
use App\Models\Keamanan;
use App\Models\Aset;
use Carbon\Carbon;

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
            'harga' => 10000,
            'status' => 'Aktif',
        ]);
        // If it returns 200 JSON, don't check redirect
        $response->assertStatus($response->status() === 302 ? 302 : 200);
        $this->assertDatabaseHas('atks', ['nama_atk' => 'Pena']);
        $atk = Atk::where('nama_atk', 'Pena')->first();

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
            'unit_kerja' => 'Cabang Utama',
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
        $this->assertDatabaseHas('permintaan_atks', ['id' => $poId, 'unit_kerja' => 'Cabang Utama']);

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
        $this->actingAs($this->staff);

        // Valid usage
        $response = $this->post('/staff/pemakaian-atk', [
            'atk_id' => $atk->id,
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
            'jumlah' => 100, // current is 95
            'unit_kerja' => 'KCP',
            'tanggal' => now()->format('Y-m-d'),
            'keterangan' => 'Exceed stock'
        ]);
        // Should fail validation and redirect back with errors or return JSON 422/400
        $this->assertDatabaseHas('atks', ['id' => $atk->id, 'jumlah' => 95]); // no change
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
    
    public function test_empty_database_pages()
    {
        $this->actingAs($this->admin);
        $this->get('/admin/dashboard')->assertStatus(200);
        $this->get('/admin/atk')->assertStatus(200);
        $this->get('/admin/kendaraan')->assertStatus(200);
        $this->get('/admin/aset')->assertStatus(200);
    }
}
