<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class BasicWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_staff_can_access_permintaan_atk(): void
    {
        $staff = User::where('role', 'staff')->first();
        
        $response = $this->actingAs($staff)->get('/staff/permintaan-atk');
        $response->assertStatus(200);
    }

    public function test_staff_can_access_kendaraan(): void
    {
        $staff = User::where('role', 'staff')->first();
        
        $response = $this->actingAs($staff)->get('/staff/kendaraan');
        $response->assertStatus(200);
    }

    public function test_staff_can_access_dashboard(): void
    {
        $staff = User::where('role', 'staff')->first();
        
        $response = $this->actingAs($staff)->get('/staff/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}
