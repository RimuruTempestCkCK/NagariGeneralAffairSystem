<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class SecurityAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_staff_cannot_access_admin_aset_module(): void
    {
        $staff = User::where('role', 'staff')->first();
        
        $response = $this->actingAs($staff)->get('/admin/aset');
        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_admin_evaluasi_module(): void
    {
        $staff = User::where('role', 'staff')->first();
        
        $response = $this->actingAs($staff)->get('/admin/evaluasi-keamanan');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_aset_module(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        $response = $this->actingAs($admin)->get('/admin/aset');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_evaluasi_module(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        $response = $this->actingAs($admin)->get('/admin/evaluasi-keamanan');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/admin/aset');
        $response->assertRedirect('/');
    }
}
