<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 3)->unique();
            $table->string('nama', 100)->unique();
            $table->timestamps();
        });

        foreach (config('branches', []) as $kode => $nama) {
            DB::table('branches')->insert([
                'kode' => $kode,
                'nama' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('permintaan_atks', function (Blueprint $table) {
            $table->string('kode_cabang', 3)->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_atks', function (Blueprint $table) {
            $table->dropIndex(['kode_cabang']);
            $table->dropColumn('kode_cabang');
        });

        Schema::dropIfExists('branches');
    }
};
