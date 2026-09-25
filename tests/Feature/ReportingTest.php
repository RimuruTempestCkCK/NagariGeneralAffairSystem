<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Atk;
use App\Models\Kendaraan;
use App\Models\Aset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
    }

    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }
    
    public function test_staff_can_access_dashboard()
    {
        $response = $this->actingAs($this->staff)->get('/staff/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_laporan_atk()
    {
        $response = $this->actingAs($this->admin)->get('/laporan/atk');
        $response->assertStatus(200);
    }
    
    public function test_admin_can_access_laporan_kendaraan()
    {
        $response = $this->actingAs($this->admin)->get('/laporan/kendaraan');
        $response->assertStatus(200);
    }
    
    public function test_admin_can_access_laporan_aset()
    {
        $response = $this->actingAs($this->admin)->get('/laporan/aset');
        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_laporan_atk()
    {
        $response = $this->actingAs($this->staff)->get('/laporan/atk');
        $response->assertStatus(403);
    }
    
    public function test_staff_cannot_access_laporan_kendaraan()
    {
        $response = $this->actingAs($this->staff)->get('/laporan/kendaraan');
        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_laporan_aset()
    {
        $response = $this->actingAs($this->staff)->get('/laporan/aset');
        $response->assertStatus(403);
    }
    
    public function test_laporan_atk_filters()
    {
        Atk::create([
            'jenis_atk' => 'Kertas',
            'kode_atk' => 'ATK-001',
            'nama_atk' => 'Kertas A4',
            'satuan' => 'Rim'
        ]);
        
        $response = $this->actingAs($this->admin)->get('/laporan/atk?search=Kertas');
        $response->assertStatus(200);
        $response->assertSee('Kertas A4');
        
        $response2 = $this->actingAs($this->admin)->get('/laporan/atk?search=Tinta');
        $response2->assertStatus(200);
        $response2->assertDontSee('Kertas A4');
    }
    
    public function test_laporan_aset_filters()
    {
        Aset::create([
            'kode_cabang' => 'CAB-001',
            'nomor_sertifikat' => 'SHM-ASET-1',
            'nama_pemilik' => 'Bank Nagari',
            'lokasi' => 'Padang',
            'luas_tanah' => 100,
            'jatuh_tempo_sertifikat' => now()->addDays(10)->toDateString() // Akan Jatuh Tempo
        ]);
        
        $response = $this->actingAs($this->admin)->get('/laporan/aset?status=Akan Jatuh Tempo');
        $response->assertStatus(200);
        $response->assertSee('SHM-ASET-1');
        
        $response2 = $this->actingAs($this->admin)->get('/laporan/aset?status=Aman');
        $response2->assertStatus(200);
        $response2->assertDontSee('SHM-ASET-1');
    }
}
