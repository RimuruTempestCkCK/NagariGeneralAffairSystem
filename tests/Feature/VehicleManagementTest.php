<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
    }

    public function test_admin_can_access_vehicle_master()
    {
        $response = $this->actingAs($this->admin)->get('/kendaraan');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_vehicle()
    {
        $data = [
            'nomor_kendaraan' => 'BA 1234 XY',
            'status_kendaraan' => 'Milik',
            'jenis_kendaraan' => 'Minibus',
            'tahun_kendaraan' => 2020,
            'jatuh_tempo_stnk' => now()->addDays(30)->toDateString(),
            'kondisi' => 'Aktif',
        ];

        $response = $this->actingAs($this->admin)->postJson('/kendaraan', $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('kendaraans', ['nomor_kendaraan' => 'BA 1234 XY']);
    }

    public function test_journey_validation_kilometer_akhir()
    {
        $kendaraan = Kendaraan::create([
            'nomor_kendaraan' => 'BA 9999 XX',
            'status_kendaraan' => 'Milik',
            'jenis_kendaraan' => 'Motor',
            'tahun_kendaraan' => 2021,
            'jatuh_tempo_stnk' => now()->addDays(30)->toDateString(),
            'kondisi' => 'Aktif',
        ]);

        $data = [
            'kendaraan_id' => $kendaraan->id,
            'tanggal' => now()->toDateString(),
            'kilometer_awal' => 1000,
            'kilometer_akhir' => 900, // Invalid: less than awal
            'tujuan' => 'Kantor Pusat',
        ];

        $response = $this->actingAs($this->staff)->postJson('/perjalanan-kendaraan', $data);
        $response->assertStatus(422); // Validation error
    }

    public function test_journey_calculation()
    {
        $kendaraan = Kendaraan::create([
            'nomor_kendaraan' => 'BA 8888 YY',
            'status_kendaraan' => 'Milik',
            'jenis_kendaraan' => 'Mobil Dinas',
            'tahun_kendaraan' => 2022,
            'jatuh_tempo_stnk' => now()->addDays(30)->toDateString(),
            'kondisi' => 'Aktif',
        ]);

        $data = [
            'kendaraan_id' => $kendaraan->id,
            'tanggal' => now()->toDateString(),
            'kilometer_awal' => 1000,
            'kilometer_akhir' => 1050,
            'tujuan' => 'Cabang Payakumbuh',
        ];

        $response = $this->actingAs($this->staff)->postJson('/perjalanan-kendaraan', $data);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('perjalanan_kendaraans', [
            'jarak_tempuh' => 50
        ]);
    }

    public function test_bbm_calculation()
    {
        $kendaraan = Kendaraan::create([
            'nomor_kendaraan' => 'BA 7777 ZZ',
            'status_kendaraan' => 'Milik',
            'jenis_kendaraan' => 'Mobil Dinas',
            'tahun_kendaraan' => 2022,
            'jatuh_tempo_stnk' => now()->addDays(30)->toDateString(),
            'kondisi' => 'Aktif',
        ]);

        $data = [
            'kendaraan_id' => $kendaraan->id,
            'tanggal' => now()->toDateString(),
            'jenis_bbm' => 'Pertamax',
            'liter' => 10,
            'harga_per_liter' => 13500,
        ];

        $response = $this->actingAs($this->staff)->postJson('/bbm-kendaraan', $data);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('bbm_kendaraans', [
            'total_biaya' => 135000
        ]);
    }
    
    public function test_pemeliharaan_calculation()
    {
        $kendaraan = Kendaraan::create([
            'nomor_kendaraan' => 'BA 6666 ZZ',
            'status_kendaraan' => 'Milik',
            'jenis_kendaraan' => 'Mobil Dinas',
            'tahun_kendaraan' => 2022,
            'jatuh_tempo_stnk' => now()->addDays(30)->toDateString(),
            'kondisi' => 'Aktif',
        ]);

        $data = [
            'kendaraan_id' => $kendaraan->id,
            'tanggal' => now()->toDateString(),
            'jenis_perbaikan' => 'Service',
            'harga_onderdil' => 500000,
            'biaya_jasa' => 200000,
        ];

        $response = $this->actingAs($this->admin)->postJson('/pemeliharaan-kendaraan', $data);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('pemeliharaan_kendaraans', [
            'total_biaya' => 700000
        ]);
    }
}
