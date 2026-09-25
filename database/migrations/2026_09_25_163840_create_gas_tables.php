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
        // 1. MASTER ATK
        Schema::create('atks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_atk')->unique(); // contoh ATK-00001
            $table->string('nama_atk');
            $table->string('jenis_atk');
            $table->string('satuan')->default('PCS');
            $table->integer('jumlah')->default(0); // stok saat ini
            $table->decimal('harga', 15, 2)->default(0);
            $table->string('rekening_penampungan')->nullable(); // rekening BYD
            $table->string('rekening_biaya')->nullable();       // rekening beban biaya
            $table->string('qr_code')->nullable();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();
        });

        // 2. STOK AWAL & STOK MASUK ATK (Termasuk Jurnal Stok Masuk & Perubahan Harga)
        Schema::create('stok_atks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_id')->constrained('atks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('jenis_transaksi', ['Stok Awal', 'Stok Masuk']);
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->decimal('harga_sebelumnya', 15, 2)->nullable();
            $table->string('no_jurnal')->nullable(); // jurnal pembukuan stok masuk ke Rekening BYD
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });

        // 3. PERMINTAAN / PURCHASE ORDER ATK
        Schema::create('permintaan_atks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_po')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // pemohon (staff)
            $table->string('unit_kerja'); // Kantor Pusat / Kantor Cabang / KCP / KAS
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'REJECTED', 'COMPLETED'])->default('PENDING');
            $table->text('catatan')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('alasan_reject')->nullable();
            $table->date('tanggal_permintaan');
            $table->timestamps();
        });

        Schema::create('permintaan_atk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_atk_id')->constrained('permintaan_atks')->onDelete('cascade');
            $table->foreignId('atk_id')->constrained('atks')->onDelete('cascade');
            $table->integer('jumlah_diminta');
            $table->integer('jumlah_disetujui')->nullable();
            $table->timestamps();
        });

        // 4. PEMAKAIAN ATK
        Schema::create('pemakaian_atks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_id')->constrained('atks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('unit_kerja');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_beban_biaya', 15, 2); // jumlah * harga_satuan
            $table->string('no_jurnal_beban')->nullable(); // jurnal pembukuan pembebanan pemakaian akhir bulan
            $table->text('keperluan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });

        // 5. MASTER KENDARAAN
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kendaraan')->unique(); // Plat No (e.g., BA 1234 XY)
            $table->enum('status_kendaraan', ['Milik', 'Sewa'])->default('Milik');
            $table->string('jenis_kendaraan'); // Mobil Dinas, Minibus, Motor, dll.
            $table->year('tahun_kendaraan');
            $table->string('nomor_bpkb')->nullable();
            $table->string('nomor_stnk')->nullable();
            $table->date('jatuh_tempo_stnk'); // Untuk sistem peringatan warning pajak
            $table->enum('kondisi', ['Aktif', 'Servis', 'Rusak'])->default('Aktif');
            $table->timestamps();
        });

        // 6. PERJALANAN / JARAK TEMPUH KENDARAAN
        Schema::create('perjalanan_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('kilometer_awal');
            $table->integer('kilometer_akhir');
            $table->integer('jarak_tempuh'); // kilometer_akhir - kilometer_awal
            $table->string('tujuan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 7. PEMAKAIAN BBM
        Schema::create('bbm_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('liter', 8, 2);
            $table->decimal('harga_per_liter', 15, 2);
            $table->decimal('total_biaya', 15, 2); // liter * harga_per_liter
            $table->string('jenis_bbm')->default('Pertamax');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 8. PERBAIKAN DAN PEMELIHARAAN KENDARAAN
        Schema::create('pemeliharaan_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('jenis_perbaikan'); // Ban, Suku Cadang, Service Rutin, dll.
            $table->text('onderdil')->nullable();
            $table->decimal('harga_onderdil', 15, 2)->default(0);
            $table->decimal('biaya_jasa', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2); // harga_onderdil + biaya_jasa
            $table->string('bengkel')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 9. PENGELOLAAN KEAMANAN
        Schema::create('keamanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('lokasi_pengamanan', ['Kantor Pusat', 'Kantor Cabang', 'Unit Kerja / KCP / Kas']);
            $table->string('nama_lokasi'); // e.g., Kantor Cabang Padang
            $table->date('tanggal_laporan');
            $table->enum('shift', ['Pagi', 'Siang', 'Malam'])->default('Pagi');
            $table->string('petugas');
            $table->enum('kondisi_keamanan', ['Aman Kondusif', 'Insiden / Masalah'])->default('Aman Kondusif');
            $table->text('uraian_kegiatan');
            $table->text('tindakan_lanjutan')->nullable();
            $table->timestamps();
        });

        // 10. DAFTAR KEPEMILIKAN ASET
        Schema::create('asets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cabang');
            $table->string('nomor_sertifikat')->unique();
            $table->string('nama_pemilik');
            $table->text('lokasi');
            $table->decimal('luas_tanah', 12, 2); // m2
            $table->date('jatuh_tempo_sertifikat')->nullable();
            $table->string('lampiran_bukti')->nullable(); // path upload
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asets');
        Schema::dropIfExists('keamanans');
        Schema::dropIfExists('pemeliharaan_kendaraans');
        Schema::dropIfExists('bbm_kendaraans');
        Schema::dropIfExists('perjalanan_kendaraans');
        Schema::dropIfExists('kendaraans');
        Schema::dropIfExists('pemakaian_atks');
        Schema::dropIfExists('permintaan_atk_items');
        Schema::dropIfExists('permintaan_atks');
        Schema::dropIfExists('stok_atks');
        Schema::dropIfExists('atks');
    }
};
