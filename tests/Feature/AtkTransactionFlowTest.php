<?php

namespace Tests\Feature;

use App\Models\Atk;
use App\Models\PermintaanAtk;
use App\Models\StokAtk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AtkTransactionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_atk_transaction_and_approval_flow()
    {
        $admin = User::create([
            'name' => 'Admin GAS',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $staff = User::create([
            'name' => 'Staff Operasional',
            'email' => 'staff@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        // 1. Admin membuat Master ATK
        $atk = Atk::create([
            'kode_atk' => 'ATK-' . rand(10000, 99999),
            'nama_atk' => 'Buku Kas Besar',
            'jenis_atk' => 'Buku & Form',
            'satuan' => 'Buku',
            'jumlah' => 20,
            'harga' => 35000,
            'status' => 'Aktif',
            'qr_code' => 'ATK-TEST',
        ]);

        $this->assertDatabaseHas('atks', ['id' => $atk->id, 'jumlah' => 20]);

        // 2. Admin menambah Stok Masuk dengan jurnal BYD
        $stokResponse = $this->actingAs($admin)->postJson(route('stok-atk.store'), [
            'atk_id' => $atk->id,
            'jenis_transaksi' => 'Stok Masuk',
            'tanggal' => now()->toDateString(),
            'jumlah' => 30,
            'harga_satuan' => 40000,
            'no_jurnal' => 'JRN-BYD-2026-TEST',
            'keterangan' => 'Penerimaan Buku Kas Baru',
        ]);

        $stokResponse->assertStatus(200);
        $this->assertEquals(50, $atk->fresh()->jumlah);
        $this->assertEquals(40000, $atk->fresh()->harga);

        // 3. Staff membuat Permintaan ATK (PO)
        $poResponse = $this->actingAs($staff)->postJson(route('permintaan-atk.store'), [
            'unit_kerja' => 'Kantor Cabang Bukittinggi',
            'tanggal_permintaan' => now()->toDateString(),
            'catatan' => 'Kebutuhan Buku Kas Bulanan',
            'action' => 'submit',
            'items' => [
                [
                    'atk_id' => $atk->id,
                    'jumlah_diminta' => 5,
                ]
            ]
        ]);

        $poResponse->assertStatus(200);
        $permintaanId = $poResponse->json('data.id');
        $po = PermintaanAtk::find($permintaanId);
        $this->assertEquals('PENDING', $po->status);

        // 4. Staff biasa tidak boleh approve
        $unauthApprove = $this->actingAs($staff)->postJson(route('permintaan-atk.approve', $po->id));
        $unauthApprove->assertStatus(403);

        // 5. Admin menyetujui (Approve) PO
        $adminApprove = $this->actingAs($admin)->postJson(route('permintaan-atk.approve', $po->id));
        $adminApprove->assertStatus(200);
        $this->assertEquals('APPROVED', $po->fresh()->status);

        // 6. Pemakaian ATK dengan pemotongan stok otomatis
        $pemakaianResponse = $this->actingAs($staff)->postJson(route('pemakaian-atk.store'), [
            'atk_id' => $atk->id,
            'unit_kerja' => 'Kantor Cabang Bukittinggi',
            'tanggal' => now()->toDateString(),
            'jumlah' => 10,
            'keperluan' => 'Operasional Customer Service',
        ]);

        $pemakaianResponse->assertStatus(200);
        $this->assertEquals(40, $atk->fresh()->jumlah); // 50 - 10 = 40

        // 7. Validasi stok tidak cukup
        $failPemakaian = $this->actingAs($staff)->postJson(route('pemakaian-atk.store'), [
            'atk_id' => $atk->id,
            'unit_kerja' => 'Kantor Cabang Bukittinggi',
            'tanggal' => now()->toDateString(),
            'jumlah' => 9999, // melebihi sisa stok 40
            'keperluan' => 'Uji batas stok',
        ]);

        $failPemakaian->assertStatus(422);
        $this->assertEquals(40, $atk->fresh()->jumlah); // stok tetap tidak minus
    }
}
