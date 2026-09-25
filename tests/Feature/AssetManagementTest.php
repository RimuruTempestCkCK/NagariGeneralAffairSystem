<?php

namespace Tests\Feature;

use App\Models\Aset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
        Storage::fake('public');
    }

    public function test_admin_can_access_aset()
    {
        $response = $this->actingAs($this->admin)->get('/aset');
        $response->assertStatus(200);
    }
    
    public function test_staff_can_access_aset_view_only()
    {
        $response = $this->actingAs($this->staff)->get('/aset');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_aset_with_file()
    {
        $file = UploadedFile::fake()->create('sertifikat.pdf', 1024); // 1MB PDF

        $data = [
            'kode_cabang' => 'CAB-001',
            'nomor_sertifikat' => 'SHM-123456',
            'nama_pemilik' => 'PT Bank Nagari',
            'lokasi' => 'Jl. Pemuda No 21, Padang',
            'luas_tanah' => 500.25,
            'jatuh_tempo_sertifikat' => now()->addYears(5)->toDateString(),
            'lampiran_bukti' => $file,
            'keterangan' => 'Sertifikat Hak Milik'
        ];

        $response = $this->actingAs($this->admin)->postJson('/aset', $data);
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('asets', [
            'nomor_sertifikat' => 'SHM-123456',
        ]);
        
        $aset = Aset::first();
        $this->assertNotNull($aset->lampiran_bukti);
        Storage::disk('public')->assertExists($aset->lampiran_bukti);
    }
    
    public function test_file_upload_validation()
    {
        $file = UploadedFile::fake()->create('virus.exe', 1024);

        $data = [
            'kode_cabang' => 'CAB-001',
            'nomor_sertifikat' => 'SHM-INVALID',
            'nama_pemilik' => 'PT Bank Nagari',
            'lokasi' => 'Jl. Pemuda',
            'luas_tanah' => 500,
            'lampiran_bukti' => $file,
        ];

        $response = $this->actingAs($this->admin)->postJson('/aset', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lampiran_bukti']);
    }

    public function test_staff_cannot_create_aset()
    {
        $data = [
            'kode_cabang' => 'CAB-001',
            'nomor_sertifikat' => 'SHM-999',
            'nama_pemilik' => 'PT Bank Nagari',
            'lokasi' => 'Jl. Pemuda',
            'luas_tanah' => 500,
        ];

        $response = $this->actingAs($this->staff)->postJson('/aset', $data);
        $response->assertStatus(403);
    }
    
    public function test_warning_sertifikat_expired()
    {
        $aset = Aset::create([
            'kode_cabang' => 'CAB-001',
            'nomor_sertifikat' => 'SHM-EXPIRED',
            'nama_pemilik' => 'PT Bank Nagari',
            'lokasi' => 'Padang',
            'luas_tanah' => 100,
            'jatuh_tempo_sertifikat' => now()->subDays(5)->toDateString(),
        ]);
        
        $this->assertEquals('Sudah Jatuh Tempo', $aset->status_sertifikat);
    }
    
    public function test_warning_sertifikat_akan_jatuh_tempo()
    {
        $aset = Aset::create([
            'kode_cabang' => 'CAB-001',
            'nomor_sertifikat' => 'SHM-WARNING',
            'nama_pemilik' => 'PT Bank Nagari',
            'lokasi' => 'Padang',
            'luas_tanah' => 100,
            'jatuh_tempo_sertifikat' => now()->addDays(30)->toDateString(),
        ]);
        
        $this->assertEquals('Akan Jatuh Tempo', $aset->status_sertifikat);
    }

    public function test_admin_can_delete_aset()
    {
        $aset = Aset::create([
            'kode_cabang' => 'CAB-001',
            'nomor_sertifikat' => 'SHM-DELETE',
            'nama_pemilik' => 'PT Bank Nagari',
            'lokasi' => 'Padang',
            'luas_tanah' => 100,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson('/aset/' . $aset->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('asets', ['id' => $aset->id]);
    }
}
