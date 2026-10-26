<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->string('kode_cabang', 3)->nullable()->index();
        });

        Schema::table('stok_atks', function (Blueprint $table) {
            $table->string('kode_cabang', 3)->nullable()->index();
        });

        Schema::table('pemakaian_atks', function (Blueprint $table) {
            $table->string('kode_cabang', 3)->nullable()->index();
        });

        Schema::table('perjalanan_kendaraans', function (Blueprint $table) {
            $table->string('foto_km_awal')->nullable();
            $table->string('foto_km_akhir')->nullable();
        });

        Schema::table('bbm_kendaraans', function (Blueprint $table) {
            $table->string('foto_struk')->nullable();
        });

        Schema::table('pemeliharaan_kendaraans', function (Blueprint $table) {
            $table->string('foto_nota')->nullable();
        });

        Schema::table('asets', function (Blueprint $table) {
            $table->json('lampiran_files')->nullable();
        });

        Schema::create('atk_cabang_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_id')->constrained('atks')->cascadeOnDelete();
            $table->string('kode_cabang', 3);
            $table->unsignedInteger('jumlah')->default(0);
            $table->timestamps();
            $table->unique(['atk_id', 'kode_cabang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk_cabang_stoks');

        Schema::table('asets', function (Blueprint $table) {
            $table->dropColumn('lampiran_files');
        });

        Schema::table('pemeliharaan_kendaraans', function (Blueprint $table) {
            $table->dropColumn('foto_nota');
        });

        Schema::table('bbm_kendaraans', function (Blueprint $table) {
            $table->dropColumn('foto_struk');
        });

        Schema::table('perjalanan_kendaraans', function (Blueprint $table) {
            $table->dropColumn(['foto_km_awal', 'foto_km_akhir']);
        });

        Schema::table('stok_atks', function (Blueprint $table) {
            $table->dropIndex(['kode_cabang']);
            $table->dropColumn('kode_cabang');
        });

        Schema::table('pemakaian_atks', function (Blueprint $table) {
            $table->dropIndex(['kode_cabang']);
            $table->dropColumn('kode_cabang');
        });

        Schema::table('kendaraans', function (Blueprint $table) {
            $table->dropIndex(['kode_cabang']);
            $table->dropColumn('kode_cabang');
        });
    }
};
