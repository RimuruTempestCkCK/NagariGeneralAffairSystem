<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PermintaanAtk;
use App\Models\Atk;
use App\Models\Kendaraan;
use App\Models\Aset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->staff = User::factory()->create(['role' => 'staff']);
        $this->staff2 = User::factory()->create(['role' => 'staff']);
        
        $this->atk = Atk::create([
            'kode_atk' => 'ATK-001',
            'nama_atk' => 'Kertas A4',
            'jenis_atk' => 'Kertas',
            'satuan' => 'Rim',
            'status' => 'Aktif',
            'jumlah' => 100,
            'harga' => 50000,
        ]);
    }

    public function test_staff_create_po_sends_notification_to_admin()
    {
        $response = $this->actingAs($this->staff)->postJson(route('permintaan-atk.store'), [
            'unit_kerja' => 'IT',
            'tanggal_permintaan' => now()->toDateString(),
            'items' => [
                ['atk_id' => $this->atk->id, 'jumlah_diminta' => 5]
            ]
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->admin->id,
            'notifiable_type' => User::class,
        ]);
        
        $notification = DB::table('notifications')->where('notifiable_id', $this->admin->id)->first();
        $this->assertStringContainsString('PO_NEW', $notification->data);
    }

    public function test_admin_approve_po_sends_notification_to_staff()
    {
        $po = PermintaanAtk::create([
            'user_id' => $this->staff->id,
            'nomor_po' => 'PO-TEST',
            'unit_kerja' => 'IT',
            'tanggal_permintaan' => now(),
            'status' => 'PENDING',
        ]);
        $po->items()->create(['atk_id' => $this->atk->id, 'jumlah_diminta' => 2]);

        $response = $this->actingAs($this->admin)->postJson(route('permintaan-atk.approve', $po->id));
        $response->assertStatus(200);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->staff->id,
        ]);
        $notification = DB::table('notifications')->where('notifiable_id', $this->staff->id)->first();
        $this->assertStringContainsString('PO_APPROVED', $notification->data);
    }

    public function test_admin_reject_po_sends_notification_to_staff_with_reason()
    {
        $po = PermintaanAtk::create([
            'user_id' => $this->staff->id,
            'nomor_po' => 'PO-TEST',
            'unit_kerja' => 'IT',
            'tanggal_permintaan' => now(),
            'status' => 'PENDING',
        ]);
        $po->items()->create(['atk_id' => $this->atk->id, 'jumlah_diminta' => 2]);

        $response = $this->actingAs($this->admin)->postJson(route('permintaan-atk.reject', $po->id), [
            'alasan_reject' => 'Stok habis',
        ]);
        $response->assertStatus(200);

        $notification = DB::table('notifications')->where('notifiable_id', $this->staff->id)->first();
        $this->assertStringContainsString('PO_REJECTED', $notification->data);
        $this->assertStringContainsString('Stok habis', $notification->data);
    }

    public function test_user_can_view_own_notifications()
    {
        \App\Services\NotificationService::notifyStaffPoApproved((object)['nomor_po'=>'TEST','id'=>1,'user'=>$this->staff]);
        
        $response = $this->actingAs($this->staff)->get(route('notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('TEST');
    }

    public function test_stnk_expiry_creates_notification_without_duplicate()
    {
        $k = Kendaraan::create([
            'nomor_kendaraan' => 'K-001',
            'plat_nomor' => 'BA 1234 CD',
            'jenis_kendaraan' => 'Motor',
            'tahun_kendaraan' => 2020,
            'jatuh_tempo_stnk' => now()->addDays(5)->toDateString(),
            'status' => 'Aktif',
        ]);

        $this->artisan('gas:check-expiry')->assertExitCode(0);

        // Assert notification created
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->admin->id,
        ]);
        
        $count1 = DB::table('notifications')->count();

        // Run again, should deduplicate
        $this->artisan('gas:check-expiry')->assertExitCode(0);
        
        $count2 = DB::table('notifications')->count();
        $this->assertEquals($count1, $count2); // No duplicate
    }
}
