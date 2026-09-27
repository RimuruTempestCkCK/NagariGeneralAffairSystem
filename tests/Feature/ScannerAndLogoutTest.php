<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Atk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScannerAndLogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed needed data for testing
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_scan@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->staff = User::create([
            'name' => 'Staff Test',
            'email' => 'staff_scan@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff'
        ]);

        $this->atk = Atk::create([
            'kode_atk' => 'ATK-SCAN-001',
            'nama_atk' => 'Spidol Scan',
            'jenis_atk' => 'Alat Tulis',
            'jumlah' => 10,
            'satuan' => 'Pcs',
            'harga' => 5000,
            'status' => 'Aktif'
        ]);
    }

    public function test_scanner_lookup_returns_valid_data_for_admin()
    {
        $response = $this->actingAs($this->admin)
                         ->getJson('/admin/atk/scan/lookup/ATK-SCAN-001');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'kode_atk' => 'ATK-SCAN-001',
                         'nama_atk' => 'Spidol Scan'
                     ]
                 ]);
    }

    public function test_scanner_lookup_returns_valid_data_for_staff()
    {
        $response = $this->actingAs($this->staff)
                         ->getJson('/staff/atk/scan/lookup/ATK-SCAN-001');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);
    }

    public function test_scanner_lookup_handles_invalid_code()
    {
        $response = $this->actingAs($this->admin)
                         ->getJson('/admin/atk/scan/lookup/INVALID-999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    public function test_scanner_lookup_unauthorized_for_guest()
    {
        $response = $this->getJson('/admin/atk/scan/lookup/ATK-SCAN-001');
        $response->assertStatus(401);
    }
    
    public function test_scanner_lookup_staff_cannot_access_admin_route()
    {
        $response = $this->actingAs($this->staff)
                         ->getJson('/admin/atk/scan/lookup/ATK-SCAN-001');
        $response->assertStatus(403);
    }

    public function test_logout_invalidates_session_and_redirects()
    {
        $this->actingAs($this->admin);
        
        $response = $this->post('/logout');
        
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/'); // or login page depending on middleware redirect

        $response2 = $this->get('/staff/dashboard');
        $response2->assertRedirect('/');
    }
}
