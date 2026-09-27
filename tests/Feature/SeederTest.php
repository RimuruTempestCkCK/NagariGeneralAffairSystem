<?php

namespace Tests\Feature;

use App\Models\Atk;
use App\Models\PemakaianAtk;
use App\Models\StokAtk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_seed_populates_atk_transaction_tables(): void
    {
        $this->assertGreaterThan(0, User::count());
        $this->assertGreaterThan(0, Atk::count());
        $this->assertGreaterThan(0, StokAtk::count());
        $this->assertGreaterThan(0, PemakaianAtk::count());
    }

    public function test_seed_is_idempotent(): void
    {
        $before = [
            User::count(),
            Atk::count(),
            StokAtk::count(),
            PemakaianAtk::count(),
        ];

        $this->artisan('db:seed');
        $this->artisan('db:seed');

        $after = [
            User::count(),
            Atk::count(),
            StokAtk::count(),
            PemakaianAtk::count(),
        ];

        $this->assertSame($before, $after);
    }

    public function test_user_seeder_creates_exactly_two_hashed_users(): void
    {
        $this->assertSame(2, User::count());
        $this->assertSame(1, User::where('role', 'admin')->count());
        $this->assertSame(1, User::where('role', 'staff')->count());

        foreach (User::all() as $user) {
            $this->assertTrue(Hash::isHashed($user->password), 'Password harus tersimpan sebagai hash.');
        }
    }

    public function test_stok_atk_totals_and_journal_are_consistent(): void
    {
        $stok = StokAtk::all();

        $this->assertSame(
            $stok->count(),
            $stok->whereNotNull('no_jurnal')->count(),
            'Setiap transaksi stok wajib memiliki nomor jurnal.'
        );

        foreach ($stok as $row) {
            $this->assertEqualsWithDelta(
                (float) $row->jumlah * (float) $row->harga_satuan,
                (float) $row->total_harga,
                0.01,
                "total_harga tidak konsisten pada jurnal {$row->no_jurnal}."
            );
        }
    }

    public function test_pemakaian_atk_totals_and_stock_are_consistent(): void
    {
        foreach (PemakaianAtk::all() as $row) {
            $this->assertEqualsWithDelta(
                (float) $row->jumlah * (float) $row->harga_satuan,
                (float) $row->total_beban_biaya,
                0.01,
                "total_beban_biaya tidak konsisten pada pemakaian ATK-{$row->atk_id}."
            );

            $this->assertLessThanOrEqual(
                (int) $row->atk->jumlah,
                (int) $row->jumlah,
                'Jumlah pemakaian tidak boleh melebihi stok yang tersedia.'
            );
        }
    }

    public function test_pemakaian_tanggal_falls_in_current_period(): void
    {
        $this->assertGreaterThan(0, PemakaianAtk::whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)->count());
    }
}
