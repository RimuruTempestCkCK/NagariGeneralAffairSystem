USE CASE GENERAL AFFAIR SYSTEM (GAS)
VERSI IMPLEMENTASI DENGAN 2 ROLE: ADMIN DAN STAFF
DENGAN FITUR GENERATE DAN SCAN BARCODE ATK

============================================================

1. AKTOR SISTEM
   ============================================================

Aktor utama dalam aplikasi GAS yang diimplementasikan terdiri dari 2 role:

1. ADMIN
2. STAFF

Pemetaan aktor berdasarkan BRD:

* Staff merupakan role aplikasi yang mewakili Unit Kerja sebagai pengguna
  operasional yang melakukan permintaan, pencatatan pemakaian, perjalanan
  kendaraan, BBM, dan laporan pengamanan.

* Admin merupakan role aplikasi yang mewakili Divisi Umum/pengelola yang
  melakukan pengelolaan data, stok, kendaraan, aset, keamanan, laporan,
  serta proses approval yang diberikan kepada pihak yang berwenang.

Catatan:
BRD asli tidak secara eksplisit menyebut nama role teknis "Admin" dan
"Staff". Admin dan Staff merupakan pemetaan role aplikasi berdasarkan
fungsi bisnis yang dijelaskan dalam BRD.

============================================================
2. DAFTAR USE CASE
==================

UC-01  Login
UC-02  Melihat Dashboard

MODUL ATK
UC-ATK-01  Mengelola Master Data ATK
UC-ATK-02  Membuat Permintaan / Purchase Order ATK
UC-ATK-03  Approval Purchase Order
UC-ATK-04  Mengelola Stok Awal / Stok Masuk
UC-ATK-05  Mencatat Pemakaian ATK
UC-ATK-06  Menghasilkan Stok Akhir
UC-ATK-07  Melihat Status Purchase Order
UC-ATK-08  Generate Barcode ATK
UC-ATK-09  Scan Barcode ATK

MODUL KENDARAAN
UC-KND-01  Mengelola Master Kendaraan
UC-KND-02  Mencatat Jarak Tempuh Kendaraan
UC-KND-03  Mencatat Pemakaian BBM
UC-KND-04  Mengelola Perbaikan dan Pemeliharaan Kendaraan
UC-KND-05  Menghasilkan Report Kendaraan Bulanan

MODUL KEAMANAN
UC-SEC-01  Input Laporan Pengamanan
UC-SEC-02  Evaluasi Pengamanan

MODUL ASET
UC-AST-01  Mengelola Data Kepemilikan Aset

MODUL REPORT
UC-RPT-01  Menarik Laporan ATK
UC-RPT-02  Menarik Laporan Eksploitasi Kendaraan
UC-RPT-03  Menarik Daftar Kepemilikan Aset

============================================================
3. USE CASE ADMIN
=================

3.1 UC-01 — Login

Aktor:

* Admin
* Staff

Tujuan:
Memungkinkan pengguna masuk ke dalam aplikasi sesuai dengan role dan
hak aksesnya.

Alur:

1. Pengguna membuka halaman login.
2. Pengguna memasukkan username.
3. Pengguna memasukkan password.
4. Sistem melakukan validasi kredensial.
5. Sistem mengidentifikasi role pengguna.
6. Sistem membuat sesi pengguna.
7. Sistem mengarahkan pengguna ke halaman dashboard.

Hasil:
Pengguna berhasil masuk ke sistem sesuai role.

---

3.2 UC-02 — Melihat Dashboard

Aktor:

* Admin
* Staff

Tujuan:
Menampilkan informasi umum sesuai dengan hak akses pengguna.

Alur:

1. Pengguna berhasil login.
2. Sistem menampilkan dashboard.
3. Sistem menampilkan informasi yang dapat diakses oleh pengguna.
4. Pengguna dapat memilih modul sesuai dengan hak aksesnya.

Catatan:
Detail dashboard dan jenis chart tidak ditentukan secara eksplisit oleh
BRD sehingga implementasinya merupakan kebutuhan aplikasi.

============================================================
4. USE CASE MODUL ATK
=====================

4.1 UC-ATK-01 — Mengelola Master Data ATK

Aktor:

* Admin

Tujuan:
Mengelola data dasar barang ATK yang digunakan dalam transaksi sekaligus
menjadi sumber data untuk pembuatan identitas barcode ATK.

Data:

* Jenis barang ATK
* Kode jenis barang
* Nama barang
* Jumlah barang
* Harga barang
* Rekening penampungan
* Rekening biaya
* Identitas barcode

Alur:

1. Admin membuka menu Master Data ATK.
2. Sistem menampilkan data ATK.
3. Admin dapat menambahkan data ATK.
4. Admin dapat mengubah data ATK.
5. Admin dapat menghapus data sesuai kewenangan.
6. Admin menyimpan perubahan.
7. Sistem menyimpan data ATK.
8. Sistem menyediakan identitas barang yang dapat digunakan untuk
   pembuatan barcode.

Hasil:
Data master ATK tersedia untuk digunakan dalam transaksi dan proses
identifikasi menggunakan barcode.

---

4.2 UC-ATK-02 — Membuat Permintaan / Purchase Order ATK

Aktor:

* Staff

Tujuan:
Mengajukan kebutuhan atau pemesanan barang ATK.

Alur:

1. Staff membuka modul ATK.
2. Staff memilih barang yang dibutuhkan.
3. Staff memasukkan jumlah kebutuhan.
4. Staff mengisi data permintaan/PO.
5. Staff mengirim permintaan.
6. Sistem menyimpan permintaan.
7. Sistem memberikan status menunggu approval.

Hasil:
Purchase Order/permintaan ATK tercatat dan menunggu proses approval.

---

4.3 UC-ATK-03 — Approval Purchase Order

Aktor:

* Admin

Tujuan:
Melakukan persetujuan terhadap permintaan/PO yang diajukan oleh Staff.

Alur:

1. Admin membuka daftar Purchase Order.
2. Sistem menampilkan PO yang membutuhkan approval.
3. Admin memilih PO.
4. Sistem menampilkan detail PO.
5. Admin melakukan pemeriksaan.
6. Admin memilih:
   a. Approve, atau
   b. Reject.
7. Sistem menyimpan keputusan.
8. Status PO diperbarui.

Jika disetujui:
PO berstatus disetujui dan dapat dilanjutkan ke proses pengadaan/
pemenuhan barang.

Jika ditolak:
PO berstatus ditolak.

Catatan:
BRD secara eksplisit menyebut adanya approval dari unit kerja yang
berwenang. Penamaan role "Admin" merupakan pemetaan implementasi.

---

4.4 UC-ATK-04 — Mengelola Stok Awal / Stok Masuk

Aktor:

* Admin

Tujuan:
Mencatat stok awal dan barang ATK yang masuk.

Data:

* Jenis barang
* Jumlah unit
* Harga barang awal
* Perubahan harga
* Jurnal pembukuan stok masuk

Alur:

1. Admin membuka modul stok.
2. Admin memilih jenis ATK.
3. Admin memasukkan jumlah barang.
4. Admin memasukkan harga.
5. Sistem mencatat stok masuk.
6. Sistem memperbarui saldo stok.
7. Sistem mencatat informasi pembukuan stok.

Hasil:
Stok ATK bertambah sesuai transaksi.

---

4.5 UC-ATK-05 — Mencatat Pemakaian ATK

Aktor:

* Staff

Tujuan:
Mencatat penggunaan barang ATK oleh unit kerja menggunakan pemilihan
barang atau hasil scan barcode.

Data:

* Barcode ATK
* Jenis barang
* Jumlah barang yang dipakai
* Harga barang yang dipakai
* Informasi pembebanan pemakaian

Alur:

1. Staff membuka menu Pemakaian ATK.
2. Staff memilih metode identifikasi barang.
3. Staff dapat memilih barang secara manual atau menggunakan scanner.
4. Jika menggunakan scanner, Staff melakukan scan barcode ATK.
5. Sistem membaca barcode.
6. Sistem mencari data ATK berdasarkan barcode.
7. Sistem menampilkan informasi barang yang sesuai.
8. Staff memasukkan jumlah pemakaian.
9. Sistem mengambil informasi harga sesuai data yang tersedia.
10. Sistem melakukan validasi ketersediaan stok.
11. Sistem mencatat transaksi pemakaian.
12. Sistem mengurangi jumlah stok sesuai proses bisnis.
13. Data pemakaian tersimpan.

Hasil:
Pemakaian ATK tercatat dan stok diperbarui sesuai transaksi.

---

4.6 UC-ATK-06 — Menghasilkan Stok Akhir

Aktor:

* Admin

Tujuan:
Mengetahui kondisi stok ATK pada akhir periode.

Perhitungan konseptual:

Stok Awal
+
Stok Masuk
----------

# Pemakaian

Stok Akhir

Informasi:

* Jenis barang
* Sisa unit
* Total pemakaian per bulan
* Total unit pemakaian
* Total beban biaya
* Sisa barang berdasarkan nilai
* Saldo rekening penampungan
* Jumlah stok akhir

Alur:

1. Sistem mengambil stok awal.
2. Sistem mengambil transaksi stok masuk.
3. Sistem mengambil transaksi pemakaian.
4. Sistem menghitung kondisi stok.
5. Sistem menampilkan stok akhir.
6. Admin dapat melihat hasil stok akhir.

---

4.7 UC-ATK-07 — Melihat Status Purchase Order

Aktor:

* Staff

Tujuan:
Mengetahui perkembangan PO yang telah diajukan.

Alur:

1. Staff membuka menu Purchase Order.
2. Sistem menampilkan PO milik Staff.
3. Staff memilih PO.
4. Sistem menampilkan status PO.

Contoh status implementasi:

* Menunggu Approval
* Disetujui
* Ditolak

Catatan:
Status tersebut merupakan elaborasi implementasi karena BRD tidak
menentukan daftar status PO secara teknis.

---

4.8 UC-ATK-08 — Generate Barcode ATK

Aktor:

* Admin

Tujuan:
Membuat barcode secara otomatis berdasarkan data ATK yang tersimpan
dalam sistem sehingga setiap barang/jenis ATK memiliki identitas yang
dapat digunakan untuk proses identifikasi.

Data yang digunakan:

* Kode ATK
* Nama/Jenis ATK
* Identitas unik ATK
* Data master ATK

Alur:

1. Admin membuka menu Master Data ATK atau menu Barcode ATK.
2. Sistem menampilkan data ATK.
3. Admin memilih data ATK yang akan dibuatkan barcode.
4. Admin menjalankan proses Generate Barcode.
5. Sistem membuat identitas barcode berdasarkan data/ID unik ATK.
6. Sistem menyimpan hubungan antara barcode dengan data ATK.
7. Sistem menampilkan barcode.
8. Admin dapat mencetak atau menggunakan barcode tersebut pada barang.

Hasil:
Setiap data ATK yang memiliki barcode dapat diidentifikasi melalui
proses scanning.

Catatan:
Generate barcode merupakan fitur implementasi aplikasi dan tidak
dijelaskan secara eksplisit dalam BRD.

---

4.9 UC-ATK-09 — Scan Barcode ATK

Aktor:

* Staff

Tujuan:
Mengidentifikasi barang ATK secara cepat menggunakan barcode yang telah
dibuat oleh Admin.

Alur:

1. Staff membuka menu Pemakaian ATK.
2. Staff memilih fitur Scan Barcode.
3. Sistem mengaktifkan scanner/kamera perangkat.
4. Staff mengarahkan scanner ke barcode ATK.
5. Sistem membaca barcode.
6. Sistem mencocokkan barcode dengan data ATK.
7. Sistem menampilkan data ATK yang sesuai.
8. Staff memeriksa data barang.
9. Staff memasukkan jumlah pemakaian.
10. Sistem memvalidasi ketersediaan stok.
11. Sistem melanjutkan proses pencatatan pemakaian ATK.
12. Sistem menyimpan transaksi.
13. Sistem memperbarui stok.

Hasil:
Barang ATK berhasil diidentifikasi melalui barcode dan dapat digunakan
dalam transaksi pemakaian.

Catatan:
Scan barcode merupakan fitur implementasi tambahan untuk mendukung
proses operasional Staff.

============================================================
5. USE CASE MODUL KENDARAAN
===========================

5.1 UC-KND-01 — Mengelola Master Kendaraan

Aktor:

* Admin

Tujuan:
Mengelola data kendaraan yang digunakan dalam operasional.

Data:

* Nomor kendaraan
* Status kendaraan: sewa/milik
* Jenis kendaraan
* Tahun kendaraan
* Nomor BPKB
* Nomor STNK
* Jatuh tempo STNK

Alur:

1. Admin membuka Master Kendaraan.
2. Admin menambahkan data kendaraan.
3. Admin mengisi data kendaraan.
4. Admin menyimpan data.
5. Sistem menyimpan data kendaraan.
6. Sistem dapat memberikan informasi/warning terkait jatuh tempo STNK.

Hasil:
Data kendaraan tersedia untuk transaksi operasional.

---

5.2 UC-KND-02 — Mencatat Jarak Tempuh Kendaraan

Aktor:

* Staff

Tujuan:
Mencatat histori perjalanan kendaraan.

Data:

* Nomor kendaraan
* Tanggal perjalanan
* Kilometer awal
* Kilometer akhir
* Jarak tempuh

Perhitungan:

KM Akhir - KM Awal = Jarak Tempuh

Alur:

1. Staff memilih kendaraan.
2. Staff memasukkan tanggal perjalanan.
3. Staff memasukkan KM awal.
4. Staff memasukkan KM akhir.
5. Sistem menghitung jarak tempuh.
6. Sistem menyimpan histori perjalanan.

Hasil:
Data jarak tempuh kendaraan tercatat.

---

5.3 UC-KND-03 — Mencatat Pemakaian BBM

Aktor:

* Staff

Tujuan:
Mencatat transaksi pembelian/pemakaian BBM kendaraan.

Data:

* Nomor kendaraan
* Tanggal pembelian
* Liter pembelian
* Harga BBM per liter
* Harga total pembelian

Alur:

1. Staff memilih kendaraan.
2. Staff memasukkan tanggal pembelian.
3. Staff memasukkan jumlah liter.
4. Staff memasukkan harga per liter.
5. Sistem menghitung total pembelian.
6. Sistem menyimpan transaksi BBM.

Hasil:
Data pemakaian/pembelian BBM tercatat.

---

5.4 UC-KND-04 — Mengelola Perbaikan dan Pemeliharaan Kendaraan

Aktor:

* Admin

Tujuan:
Mencatat histori perbaikan dan pemeliharaan kendaraan.

Data:

* Nomor kendaraan
* Jenis perbaikan
* Onderdil
* Ban
* Suku cadang
* Service
* Harga onderdil
* Biaya service
* Jasa perbaikan
* Biaya pemeliharaan

Alur:

1. Admin memilih kendaraan.
2. Admin memilih/mengisi jenis perbaikan.
3. Admin memasukkan komponen perbaikan/service.
4. Admin memasukkan biaya.
5. Admin menyimpan data.
6. Sistem mencatat histori pemeliharaan.

Hasil:
Histori perbaikan dan pemeliharaan kendaraan tersimpan.

---

5.5 UC-KND-05 — Menghasilkan Report Kendaraan Bulanan

Aktor:

* Admin

Tujuan:
Menghasilkan laporan eksploitasi kendaraan pada akhir periode.

Informasi:

* Nomor kendaraan
* Jenis kendaraan
* Total KM per bulan
* Total biaya BBM
* Total biaya pemeliharaan/perbaikan
* Biaya berdasarkan jenis perbaikan

Alur:

1. Admin memilih periode laporan.
2. Sistem mengambil data kendaraan.
3. Sistem mengambil data jarak tempuh.
4. Sistem mengambil data BBM.
5. Sistem mengambil data pemeliharaan.
6. Sistem mengolah data.
7. Sistem menghasilkan laporan kendaraan bulanan.

============================================================
6. USE CASE MODUL KEAMANAN
==========================

6.1 UC-SEC-01 — Input Laporan Pengamanan

Aktor:

* Staff

Tujuan:
Mencatat laporan pengamanan pada lingkungan kerja.

Lingkup:

* Kantor Pusat
* Kantor Cabang
* Unit kerja di bawah Kantor Cabang

Alur:

1. Staff membuka menu Pengamanan.
2. Staff memilih lokasi/unit kerja.
3. Staff mengisi laporan pengamanan.
4. Staff menyimpan laporan.
5. Sistem menyimpan data laporan.
6. Data dapat digunakan untuk proses evaluasi.

Catatan:
BRD tidak menjelaskan field detail laporan pengamanan.

---

6.2 UC-SEC-02 — Evaluasi Pengamanan

Aktor:

* Admin

Tujuan:
Melakukan pengelolaan/evaluasi laporan pengamanan.

Periode:

* Triwulan
* Tahunan

Alur:

1. Admin membuka laporan pengamanan.
2. Sistem mengambil data laporan pengamanan.
3. Admin memilih periode evaluasi.
4. Sistem menampilkan data berdasarkan periode.
5. Admin melakukan proses evaluasi sesuai ketentuan bisnis.
6. Sistem menyimpan/menampilkan hasil evaluasi.

Catatan:
Metode dan formula evaluasi tidak ditentukan secara rinci dalam BRD.

============================================================
7. USE CASE MODUL ASET
======================

7.1 UC-AST-01 — Mengelola Data Kepemilikan Aset

Aktor:

* Admin

Tujuan:
Mengelola data kepemilikan aset beserta dokumen/bukti kepemilikannya.

Data:

* Kode cabang
* Nomor sertifikat
* Lokasi
* Luas tanah
* Nama pemilik
* Jatuh tempo sertifikat
* Lampiran bukti kepemilikan

Alur:

1. Admin membuka menu Kepemilikan Aset.
2. Admin menambahkan data aset.
3. Admin memasukkan kode cabang.
4. Admin memasukkan nomor sertifikat.
5. Admin memasukkan lokasi.
6. Admin memasukkan luas tanah.
7. Admin memasukkan nama pemilik.
8. Admin memasukkan jatuh tempo sertifikat.
9. Admin mengunggah bukti kepemilikan.
10. Admin menyimpan data.
11. Sistem menyimpan data aset dan dokumen.

Hasil:
Data kepemilikan aset tersimpan dan dapat digunakan dalam laporan.

============================================================
8. USE CASE REPORT
==================

8.1 UC-RPT-01 — Menarik Laporan ATK

Aktor:

* Admin

Tujuan:
Menghasilkan laporan pengelolaan ATK.

Data laporan dapat meliputi:

* Stok
* Pemakaian
* Nilai
* Beban
* Rekening
* Kondisi stok akhir

Alur:

1. Admin membuka menu Laporan.
2. Admin memilih Laporan ATK.
3. Admin menentukan periode/filter.
4. Sistem mengambil data ATK.
5. Sistem mengolah data.
6. Sistem menampilkan laporan.

---

8.2 UC-RPT-02 — Menarik Laporan Eksploitasi Kendaraan

Aktor:

* Admin

Tujuan:
Menghasilkan laporan eksploitasi kendaraan.

Data:

* Kendaraan
* Jenis kendaraan
* Jarak tempuh
* Biaya BBM
* Biaya pemeliharaan
* Biaya berdasarkan jenis perbaikan

Alur:

1. Admin membuka menu Laporan.
2. Admin memilih Laporan Eksploitasi Kendaraan.
3. Admin menentukan periode.
4. Sistem mengambil data kendaraan.
5. Sistem mengambil data perjalanan.
6. Sistem mengambil data BBM.
7. Sistem mengambil data pemeliharaan.
8. Sistem menghasilkan laporan.

---

8.3 UC-RPT-03 — Menarik Daftar Kepemilikan Aset

Aktor:

* Admin

Tujuan:
Menghasilkan daftar kepemilikan aset.

Data:

* Kode cabang
* Nomor sertifikat
* Lokasi
* Luas tanah
* Nama pemilik
* Jatuh tempo sertifikat
* Bukti kepemilikan

Alur:

1. Admin membuka menu Laporan.
2. Admin memilih Daftar Kepemilikan Aset.
3. Sistem mengambil data aset.
4. Sistem menampilkan daftar kepemilikan aset.

============================================================
9. MATRIKS ROLE DAN USE CASE
============================

USE CASE                                      ADMIN       STAFF

UC-01 Login                                    ✓           ✓
UC-02 Melihat Dashboard                        ✓           ✓

UC-ATK-01 Mengelola Master ATK                 ✓
UC-ATK-02 Membuat Permintaan/PO                            ✓
UC-ATK-03 Approval PO                           ✓
UC-ATK-04 Mengelola Stok Awal/Stok Masuk       ✓
UC-ATK-05 Mencatat Pemakaian ATK                            ✓
UC-ATK-06 Menghasilkan Stok Akhir              ✓
UC-ATK-07 Melihat Status PO                                 ✓
UC-ATK-08 Generate Barcode ATK                 ✓
UC-ATK-09 Scan Barcode ATK                                  ✓

UC-KND-01 Mengelola Master Kendaraan            ✓
UC-KND-02 Mencatat Jarak Tempuh                            ✓
UC-KND-03 Mencatat Pemakaian BBM                            ✓
UC-KND-04 Mengelola Pemeliharaan Kendaraan      ✓
UC-KND-05 Report Kendaraan Bulanan               ✓

UC-SEC-01 Input Laporan Pengamanan                           ✓
UC-SEC-02 Evaluasi Pengamanan                   ✓

UC-AST-01 Mengelola Kepemilikan Aset            ✓

UC-RPT-01 Laporan ATK                            ✓
UC-RPT-02 Laporan Eksploitasi Kendaraan         ✓
UC-RPT-03 Daftar Kepemilikan Aset               ✓

============================================================
10. ALUR BISNIS UTAMA STAFF
===========================

STAFF
│
├── Login
│
├── Melihat Dashboard
│
├── Membuat Permintaan / PO ATK
│       │
│       ▼
│   Menunggu Approval
│       │
│       ▼
│     ADMIN
│
├── Melihat Status PO
│
├── Mencatat Pemakaian ATK
│       │
│       ├── Pilih ATK Manual
│       │
│       └── Scan Barcode ATK
│               │
│               ▼
│          Sistem Identifikasi ATK
│               │
│               ▼
│          Input Jumlah Pemakaian
│               │
│               ▼
│          Stok Berkurang
│
├── Mencatat Jarak Tempuh Kendaraan
│
├── Mencatat Pemakaian BBM
│
└── Input Laporan Pengamanan

============================================================
11. ALUR BISNIS UTAMA ADMIN
===========================

ADMIN
│
├── Login
│
├── Melihat Dashboard
│
├── Mengelola Master ATK
│       │
│       ▼
│   Generate Barcode ATK
│       │
│       ▼
│   Barcode dikaitkan dengan Data ATK
│
├── Approval PO
│
├── Mengelola Stok Awal/Stok Masuk
│
├── Melihat Stok Akhir
│
├── Mengelola Master Kendaraan
│
├── Mengelola Pemeliharaan Kendaraan
│
├── Menghasilkan Report Kendaraan
│
├── Melakukan Evaluasi Pengamanan
│
├── Mengelola Kepemilikan Aset
│
├── Menarik Laporan ATK
│
├── Menarik Laporan Eksploitasi Kendaraan
│
└── Menarik Daftar Kepemilikan Aset

============================================================
12. ALUR BARCODE ATK
====================

```
                DATA MASTER ATK
                       │
                       ▼
                ADMIN
                       │
                       ▼
              GENERATE BARCODE
                       │
                       ▼
          BARCODE TERHUBUNG DENGAN
                DATA ATK
                       │
                       ▼
             BARCODE DICETAK/
             DITEMPELKAN PADA ATK
                       │
                       ▼
                    STAFF
                       │
                       ▼
                SCAN BARCODE
                       │
                       ▼
              SISTEM MEMBACA ID
                       │
                       ▼
              CARI DATA ATK
                       │
                       ▼
            DATA ATK DITEMUKAN
                       │
                       ▼
         STAFF INPUT JUMLAH PAKAI
                       │
                       ▼
             VALIDASI STOK
                       │
                       ▼
          SIMPAN PEMAKAIAN ATK
                       │
                       ▼
                STOK BERKURANG
                       │
                       ▼
               STOK TERBARU
```

============================================================
13. ALUR BISNIS KESELURUHAN GAS
===============================

```
                GENERAL AFFAIR SYSTEM
                          │
            ┌─────────────┴─────────────┐
            │                           │
          STAFF                       ADMIN
            │                           │
   ┌────────┼────────┐          ┌───────┼──────────┐
   │        │        │          │       │          │
   ▼        ▼        ▼          ▼       ▼          ▼
  PO       ATK     Kendaraan   Master  Approval   Reporting
   │      Pakai      & BBM     Data      PO
   │        │
   │        ├── Scan Barcode
   │        │        │
   │        │        ▼
   │        │   Identifikasi ATK
   │        │        │
   │        │        ▼
   │        │   Pemakaian ATK
   │        │
   ▼        ▼
```

Menunggu  Transaksi
Approval  Pemakaian
│        │
▼        ▼
ADMIN   Stok Berkurang
│
▼
Approval
│
┌───┴────┐
▼        ▼
Approve   Reject
│
▼
Pengadaan/
Pemenuhan
│
▼
Stok Masuk
│
▼
Pemakaian
│
▼
Stok Akhir
│
▼
Reporting

============================================================
14. HUBUNGAN USE CASE DENGAN BRD
================================

Requirement yang secara langsung didukung:

ATK:

* Master Data ATK
* Purchase Order
* Approval PO
* Stok Awal
* Stok Masuk
* Perubahan Harga
* Pemakaian ATK
* Stok Akhir
* Laporan ATK

Kendaraan:

* Master Kendaraan
* Status Sewa/Milik
* BPKB
* STNK
* Jatuh Tempo STNK
* Jarak Tempuh
* Pemakaian BBM
* Perbaikan/Pemeliharaan
* Laporan Kendaraan

Keamanan:

* Laporan Pengamanan Kantor Pusat
* Laporan Pengamanan Kantor Cabang
* Laporan Pengamanan Unit Kerja
* Evaluasi Triwulan
* Evaluasi Tahunan

Aset:

* Kode Cabang
* Sertifikat
* Lokasi
* Luas Tanah
* Nama Pemilik
* Jatuh Tempo Sertifikat
* Bukti Kepemilikan

Reporting:

* Laporan ATK
* Laporan Eksploitasi Kendaraan
* Daftar Kepemilikan Aset

Fitur implementasi tambahan:

* Generate Barcode ATK oleh Admin
* Scan Barcode ATK oleh Staff
* Identifikasi data ATK berdasarkan barcode
* Pencatatan pemakaian berdasarkan hasil scan

============================================================
15. CATATAN IMPLEMENTASI
========================

ROLE ADMIN

* Memiliki hak pengelolaan data.
* Mengelola master data.
* Mengelola stok.
* Melakukan approval PO.
* Mengelola kendaraan.
* Mengelola pemeliharaan.
* Melakukan evaluasi keamanan.
* Mengelola aset.
* Mengakses laporan.
* Membuat/generate barcode ATK.
* Menghubungkan barcode dengan data ATK.

ROLE STAFF

* Melakukan aktivitas operasional.
* Mengajukan PO/permintaan ATK.
* Melihat status PO.
* Mencatat pemakaian ATK.
* Melakukan scan barcode ATK.
* Menggunakan hasil scan untuk mengidentifikasi barang ATK.
* Mencatat perjalanan kendaraan.
* Mencatat pemakaian BBM.
* Menginput laporan pengamanan.

MEKANISME BARCODE

1. Admin membuat atau mengelola data ATK.
2. Sistem memiliki identitas unik untuk setiap data ATK.
3. Admin melakukan Generate Barcode.
4. Sistem menghasilkan barcode berdasarkan identitas tersebut.
5. Barcode disimpan/terhubung dengan data ATK.
6. Barcode dapat dicetak dan ditempelkan pada barang ATK.
7. Staff melakukan scan barcode ketika melakukan pemakaian ATK.
8. Sistem membaca barcode.
9. Sistem mencari data ATK yang terkait.
10. Sistem menampilkan informasi ATK.
11. Staff memasukkan jumlah pemakaian.
12. Sistem melakukan validasi stok.
13. Sistem menyimpan transaksi pemakaian.
14. Sistem mengurangi stok.

Catatan penting:

Barcode harus memiliki identitas unik yang terhubung dengan data ATK
di database. Barcode tidak sebaiknya hanya menyimpan nama barang atau
informasi yang dapat berubah.

BRD tidak secara eksplisit menyebut fitur barcode/QR Code. Oleh karena
itu, Generate Barcode dan Scan Barcode merupakan fitur tambahan dalam
implementasi aplikasi untuk mendukung proses operasional ATK.

============================================================
16. KESIMPULAN
==============

Untuk implementasi aplikasi GAS, Use Case menggunakan 2 role utama:

1. ADMIN
2. STAFF

Staff berfokus pada aktivitas operasional dan pengajuan/pencatatan.

Admin berfokus pada pengelolaan data, approval, monitoring, evaluasi,
reporting, serta pembuatan barcode ATK.

Alur barcode:

ADMIN
↓
Mengelola Data ATK
↓
Generate Barcode
↓
Barcode Terhubung dengan Data ATK
↓
Barcode Dicetak/Ditempel
↓
STAFF
↓
Scan Barcode
↓
Sistem Identifikasi ATK
↓
Input Pemakaian
↓
Validasi Stok
↓
Transaksi Pemakaian
↓
Stok Berkurang

Dengan demikian, barcode bukan fitur yang berdiri sendiri, tetapi
menjadi bagian dari alur pengelolaan dan pemakaian ATK.

Catatan akhir:
Generate Barcode dan Scan Barcode merupakan elaborasi implementasi
aplikasi karena tidak disebutkan secara eksplisit dalam BRD.
