<?php

namespace Tests\Feature;

use App\Models\Atk;
use App\Models\PermintaanAtk;
use App\Models\PermintaanAtkItem;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
    }

    public function test_admin_can_soft_delete_atk()
    {
        $atk = Atk::create(['kode_atk'=>'ATK-SD3','nama_atk'=>'Barang SD 3','jenis_atk'=>'Kertas','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);
        
        $response = $this->actingAs($this->admin)->deleteJson(route('atk.destroy', $atk->id));
        $response->assertStatus(200);

        $this->assertSoftDeleted($atk);

        // Record not in normal query
        $this->assertNull(Atk::find($atk->id));

        // Record in withTrashed
        $this->assertNotNull(Atk::withTrashed()->find($atk->id));
        
        // Check Audit Log generated for soft delete
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'DELETE', // from our Auditable trait
            'module' => 'Atk',
            'subject_id' => $atk->id,
        ]);
    }

    public function test_staff_cannot_soft_delete_master()
    {
        $atk = Atk::create(['kode_atk'=>'ATK-SD4','nama_atk'=>'Barang SD 4','jenis_atk'=>'Kertas','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);
        
        $response = $this->actingAs($this->staff)->deleteJson(route('atk.destroy', $atk->id));
        $response->assertStatus(403);
        
        $this->assertNotSoftDeleted($atk);
    }

    public function test_soft_deleted_data_not_in_dropdown_but_history_maintained()
    {
        $atk = Atk::create(['kode_atk'=>'ATK-SD5','nama_atk'=>'Barang SD 5','jenis_atk'=>'Kertas','satuan'=>'Pcs','status'=>'Aktif','jumlah'=>10,'harga'=>1000]);

        
        // Create history
        $po = PermintaanAtk::create([
            'nomor_po' => 'PO-TEST',
            'user_id' => $this->staff->id,
            'unit_kerja' => 'IT',
            'status' => 'DRAFT',
            'tanggal_permintaan' => now(),
        ]);

        PermintaanAtkItem::create([
            'permintaan_atk_id' => $po->id,
            'atk_id' => $atk->id,
            'jumlah_diminta' => 5,
        ]);

        // Soft delete
        $atk->delete();

        // 1. Check dropdown / create page query (Admin & Staff)
        $response = $this->actingAs($this->staff)->get(route('permintaan-atk.index'));
        $response->assertStatus(200);
        // It shouldn't contain the deleted ATK in the options
        $response->assertDontSee($atk->nama_atk); // Assumes nama_atk is rendered in dropdown

        // 2. But the history should still load it
        $this->assertNotNull(PermintaanAtkItem::first()->atk);
        $this->assertEquals($atk->id, PermintaanAtkItem::first()->atk->id);
    }
}
