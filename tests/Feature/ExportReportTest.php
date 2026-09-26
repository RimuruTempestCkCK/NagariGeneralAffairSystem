<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
    }

    public function test_admin_can_export_atk_excel()
    {
        $response = $this->actingAs($this->admin)->get(route('laporan.atk.export', ['format' => 'excel']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'EXPORT',
            'module' => 'ATK_REPORT',
        ]);
    }

    public function test_admin_can_export_atk_pdf()
    {
        $response = $this->actingAs($this->admin)->get(route('laporan.atk.export', ['format' => 'pdf']));
        $response->assertStatus(200);
        $response->assertViewIs('shared.laporan.export.atk_pdf');
        
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'EXPORT',
            'module' => 'ATK_REPORT',
        ]);
    }

    public function test_staff_cannot_export_master_reports()
    {
        $response = $this->actingAs($this->staff)->get(route('laporan.atk.export'));
        $response->assertStatus(403);
        
        $response = $this->actingAs($this->staff)->get(route('laporan.kendaraan.export'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->staff)->get(route('laporan.aset.export'));
        $response->assertStatus(403);
    }
    
    public function test_filters_are_applied_in_export()
    {
        $response = $this->actingAs($this->admin)->get(route('laporan.kendaraan.export', ['start_date' => '2026-09-01', 'end_date' => '2026-09-30', 'format' => 'excel']));
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=laporan_kendaraan_2026-09-01_2026-09-30.csv');
    }

    public function test_invalid_user_cannot_export()
    {
        $response = $this->get(route('laporan.aset.export'));
        $response->assertRedirect(route('login'));
    }
}
