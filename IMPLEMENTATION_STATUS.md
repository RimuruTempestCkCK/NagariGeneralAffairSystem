# Laporan Status Implementasi General Affair System (GAS)

Dokumen ini berisi pencatatan mengenai apa saja yang telah diimplementasikan dan apa yang belum/perlu diperbaiki berdasarkan requirement utama (BRD) dan instruksi awal.

## ✅ FITUR YANG SUDAH DIIMPLEMENTASIKAN (SELESAI)

### 1. Stack & Arsitektur Dasar
- **Framework & DB:** Menggunakan Laravel + MySQL + Blade.
- **Role & Access Control:** Diimplementasikan 2 role utama (`admin` dan `staff`). Admin memiliki akses ke seluruh sistem, Staff hanya memiliki akses ke pengajuan/input miliknya sendiri. Route telah dilindungi menggunakan Middleware dan pengecekan di dalam Controller.
- **Single UI System (Adminator):** Template UI telah menggunakan Adminator 100%. Template usang (`templete`) telah dihapus secara bersih dari tracking Git.
- **Single Page CRUD & Modal:** Semua operasi Tambah dan Edit tidak me-redirect ke halaman baru, melainkan menggunakan sistem Modal/Popup pada halaman yang sama.
- **SweetAlert2:** Seluruh notifikasi sukses, gagal, konfirmasi hapus, dan otorisasi menggunakan SweetAlert2. Tidak ada penggunaan `alert()` bawaan browser.

### 2. Modul ATK & QR Code
- **Master ATK:** CRUD lengkap (Kode, Nama, Jenis, Satuan, Harga, dll).
- **QR Code:** Identifier unik otomatis tergenerate. Tombol khusus disediakan untuk mencetak label QR satu-per-satu (menggunakan styling isolasi cetak).
- **QR Scanner:** Tersedia halaman khusus bagi Staff dan Admin untuk memindai QR Code dan menampilkan rincian ATK.
- **Transaksi (Permintaan / PO):** Staff dapat mengajukan PO, Admin dapat melakukan *Approve/Reject* dengan alasan.
- **Manajemen Stok:** Modul Stok Masuk (dengan histori & jurnal), dan Modul Pemakaian (dengan histori pembebanan per akhir bulan).

### 3. Modul Kendaraan
- **Master Kendaraan:** Pencatatan kendaraan sewa/milik, jenis, tahun, nomor mesin, dsb. Termasuk peringatan status STNK jatuh tempo dinamis.
- **Jarak Tempuh:** Pencatatan kilometer perjalanan operasional. Terdapat validasi *Kilometer Akhir >= Kilometer Awal*.
- **Pemakaian BBM:** Pencatatan pembelian BBM dan perhitungan total biaya otomatis.
- **Pemeliharaan:** Pencatatan biaya perbaikan (onderdil + jasa).

### 4. Modul Keamanan
- **Laporan Pengamanan:** Staff (Satpam/Unit Kerja) dapat menginput laporan harian pengamanan.
- **Evaluasi:** Admin dapat menginput hasil evaluasi kinerja keamanan triwulan dan tahunan.

### 5. Modul Aset
- **Master Aset:** Manajemen data sertifikat, luas tanah, lokasi, dan pemilik.
- **File Upload:** Upload lampiran dokumen kepemilikan aset dengan validasi format.
- **Warning Sertifikat:** Sistem dapat menandai (Aman, Akan Jatuh Tempo, Expired) secara _real-time_.

### 6. Dashboard & Reporting
- **Dashboard:** Dinamis berdasarkan _query database_ (tidak *hardcode*). Dashboard Admin memuat agregat _count/sum_, status STNK, dan status Aset. Dashboard Staff menampilkan _summary_ input mandiri.
- **Reporting:** Tersedia Laporan ATK (mencakup akumulasi stok masuk, pemakaian, persediaan), Laporan Kendaraan (total jarak, BBM, perbaikan), dan Laporan Aset.
- **Filter & Print:** Laporan mendukung filter tanggal, pencarian nama, dan dapat langsung di-cetak (*Print*) dengan tampilan bersih via eksekusi `@media print`.

### 7. Pengujian & Stabilitas
- Seluruh endpoint CRUD dan kalkulasi relasi telah di-*test* menggunakan `php artisan test` (33 Passed / 64 Assertions).
- Sistem memproteksi *direct URL access* dari Staff yang iseng mengetikkan rute Admin (selalu me-return 403 Unauthorized).

---

## ⚠️ HAL YANG BELUM / PERLU DIPERBAIKI (PENDING / LIMITASI)

Berdasarkan *scope* dan pedoman tahap awal, fitur berikut ini ditunda atau belum ada:

1. **Fitur Ekspor File (Excel/PDF):** 
   - Laporan saat ini hanya bisa dicetak melalui mekanisme bawaan Browser (Print/Save as PDF). Belum menggunakan _library_ khusus ekspor seperti DomPDF atau Maatwebsite Excel (karena instruksi mensyaratkan tidak menambah library berat jika tidak perlu di tahap 1).
2. **Bulk Printing QR Code:** 
   - Print QR Code saat ini dilakukan per-item di halaman Master ATK. Pencetakan massal berlembar-lembar sekaligus belum dibuat (disesuaikan dengan pedoman awal).
3. **Audit Trail Tersentralisasi:** 
   - Meskipun aksi approval, penambahan stok, dan pembuatan data memiliki relasi `user_id`, belum ada satu tabel khusus (Activity Log) yang melacak "_Siapa mengklik tombol apa pada pukul berapa_" untuk keseluruhan aplikasi.
4. **Email / Push Notifications:** 
   - Notifikasi PO baru atau pemberitahuan STNK kedaluwarsa hanya tampil aktif pada antarmuka *Dashboard*. Belum ada integrasi ke SMTP Email ataupun WhatsApp bagi Admin maupun Staff.
5. **Soft Deletes:** 
   - Saat ini proses *Delete* secara permanen menghapus *row* di Database. Jika dirasa data histori sangat krusial, implementasi Laravel `SoftDeletes` mungkin dibutuhkan ke depannya.
