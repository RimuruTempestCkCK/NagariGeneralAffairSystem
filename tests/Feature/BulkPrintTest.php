<?php

namespace Tests\Feature;

use App\Models\Atk;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BulkPrintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
    }

    public function test_admin_can_bulk_print_single_and_multiple()
    {
        $atk1 = Atk::create(['kode_atk'=>'ATK-01','nama_atk'=>'Barang 1','jenis_atk'=>'A','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);
        $atk2 = Atk::create(['kode_atk'=>'ATK-02','nama_atk'=>'Barang 2','jenis_atk'=>'B','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);
        
        $response = $this->actingAs($this->admin)->get(route('atk.bulk-print', ['ids' => [$atk1->id, $atk2->id]]));
        $response->assertStatus(200);
        $response->assertViewIs('atk.bulk-print');
        $response->assertSee($atk1->kode_atk);
        $response->assertSee($atk2->kode_atk);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'BULK_PRINT',
            'module' => 'ATK',
        ]);
    }

    public function test_staff_cannot_bulk_print()
    {
        $atk1 = Atk::create(['kode_atk'=>'ATK-01','nama_atk'=>'Barang 1','jenis_atk'=>'A','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);
        $response = $this->actingAs($this->staff)->get(route('atk.bulk-print', ['ids' => [$atk1->id]]));
        $response->assertStatus(403);
    }

    public function test_duplicate_ids_are_normalized()
    {
        $atk1 = Atk::create(['kode_atk'=>'ATK-01','nama_atk'=>'Barang 1','jenis_atk'=>'A','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);
        $response = $this->actingAs($this->admin)->get(route('atk.bulk-print', ['ids' => [$atk1->id, $atk1->id, $atk1->id]]));
        $response->assertStatus(200);
        
        $atks = $response->viewData('atks');
        $this->assertCount(1, $atks);
    }

    public function test_soft_deleted_and_invalid_ids_are_rejected()
    {
        $atk1 = Atk::create(['kode_atk'=>'ATK-01','nama_atk'=>'Barang 1','jenis_atk'=>'A','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);
        $atk1->delete(); // Soft delete

        // Send soft deleted ID and invalid ID
        $response = $this->actingAs($this->admin)->get(route('atk.bulk-print', ['ids' => [$atk1->id, 999]]));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Silakan pilih minimal satu ATK aktif.');
    }

    public function test_empty_selection_is_rejected()
    {
        $response = $this->actingAs($this->admin)->get(route('atk.bulk-print'));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Silakan pilih minimal satu ATK aktif.');
    }

    public function test_existing_individual_print_works()
    {
        $atk1 = Atk::create(['kode_atk'=>'ATK-01','nama_atk'=>'Barang 1','jenis_atk'=>'A','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);
        $response = $this->actingAs($this->admin)->get(route('atk.print-qr', $atk1->id));
        $response->assertStatus(200);
        $response->assertViewIs('atk.print-qr');
    }

    public function test_existing_scanner_works()
    {
        $atk1 = Atk::create(['kode_atk'=>'ATK-SCAN','nama_atk'=>'Barang 1','jenis_atk'=>'A','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);
        $response = $this->actingAs($this->admin)->get(route('atk.scan.lookup', 'ATK-SCAN'));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}
