<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluasi_keamanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('jenis_evaluasi', ['Triwulan', 'Tahunan']);
            $table->enum('lokasi_pengamanan', ['Kantor Pusat', 'Kantor Cabang', 'Unit Kerja / KCP / Kas']);
            $table->string('nama_lokasi');
            $table->string('periode'); // e.g. "Q1", "Q2", "Tahunan"
            $table->year('tahun');
            $table->text('hasil_evaluasi');
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi_keamanans');
    }
};
