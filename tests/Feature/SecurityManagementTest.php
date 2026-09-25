<?php

namespace Tests\Feature;

use App\Models\Keamanan;
use App\Models\EvaluasiKeamanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
    }

    public function test_admin_can_access_laporan_keamanan()
    {
        $response = $this->actingAs($this->admin)->get('/keamanan');
        $response->assertStatus(200);
    }
    
    public function test_staff_can_access_laporan_keamanan()
    {
        $response = $this->actingAs($this->staff)->get('/keamanan');
        $response->assertStatus(200);
    }

    public function test_staff_can_create_laporan_keamanan()
    {
        $data = [
            'lokasi_pengamanan' => 'Kantor Cabang',
            'nama_lokasi' => 'Cabang Padang',
            'tanggal_laporan' => now()->toDateString(),
            'shift' => 'Pagi',
            'petugas' => 'Budi Satpam',
            'kondisi_keamanan' => 'Aman Kondusif',
            'uraian_kegiatan' => 'Patroli keliling gedung',
        ];

        $response = $this->actingAs($this->staff)->postJson('/keamanan', $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('keamanans', [
            'nama_lokasi' => 'Cabang Padang',
            'user_id' => $this->staff->id
        ]);
    }

    public function test_staff_cannot_edit_other_staff_laporan()
    {
        $laporan = Keamanan::create([
            'user_id' => $this->admin->id,
            'lokasi_pengamanan' => 'Kantor Pusat',
            'nama_lokasi' => 'Pusat',
            'tanggal_laporan' => now()->toDateString(),
            'shift' => 'Pagi',
            'petugas' => 'Andi',
            'kondisi_keamanan' => 'Aman Kondusif',
            'uraian_kegiatan' => 'Jaga lobby',
        ]);

        $data = [
            'lokasi_pengamanan' => 'Kantor Pusat',
            'nama_lokasi' => 'Pusat Diubah',
            'tanggal_laporan' => now()->toDateString(),
            'shift' => 'Pagi',
            'petugas' => 'Andi',
            'kondisi_keamanan' => 'Aman Kondusif',
            'uraian_kegiatan' => 'Jaga lobby',
        ];

        $response = $this->actingAs($this->staff)->putJson('/keamanan/' . $laporan->id, $data);
        $response->assertStatus(403);
    }

    public function test_admin_can_create_evaluasi_triwulan()
    {
        $data = [
            'jenis_evaluasi' => 'Triwulan',
            'lokasi_pengamanan' => 'Kantor Pusat',
            'nama_lokasi' => 'Pusat',
            'periode' => 'Q1',
            'tahun' => 2026,
            'hasil_evaluasi' => 'Kondisi aman terkendali, sistem CCTV berfungsi baik.',
            'rekomendasi' => 'Penambahan APAR di lantai 2',
        ];

        $response = $this->actingAs($this->admin)->postJson('/evaluasi-keamanan', $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('evaluasi_keamanans', [
            'jenis_evaluasi' => 'Triwulan',
            'periode' => 'Q1',
            'tahun' => 2026
        ]);
    }

    public function test_admin_can_create_evaluasi_tahunan()
    {
        $data = [
            'jenis_evaluasi' => 'Tahunan',
            'lokasi_pengamanan' => 'Kantor Cabang',
            'nama_lokasi' => 'Cabang Bukittinggi',
            'periode' => 'Tahunan',
            'tahun' => 2026,
            'hasil_evaluasi' => 'Tingkat keamanan 98%.',
            'rekomendasi' => 'Lanjutkan kinerja',
        ];

        $response = $this->actingAs($this->admin)->postJson('/evaluasi-keamanan', $data);
        $response->assertStatus(200);
        $this->assertDatabaseHas('evaluasi_keamanans', [
            'jenis_evaluasi' => 'Tahunan',
            'lokasi_pengamanan' => 'Kantor Cabang',
            'tahun' => 2026
        ]);
    }
}
