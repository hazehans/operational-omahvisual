# 🎬 Omah Visual LED — Operational & Inventory System
> **Sistem Manajemen Operasional, Rental Videotron, dan Inventori Terpadu**  
> Dikembangkan untuk **CV Han Media Tekindo (Omah Visual LED)**

---

## 📌 1. Gambaran Umum Sistem

Sistem ini merupakan aplikasi web operasional terintegrasi (*Modular Monolith*) yang dirancang khusus untuk menangani proses bisnis rental LED videotron, peralatan broadcast, armada visual, perlengkapan panggung, hingga pelacakan inventori gudang secara presisi.

Aplikasi ini mengintegrasikan data fisik riil dari spreadsheet inventori lapangan ke dalam database terstruktur dengan kalkulasi otomatis luasan meter persegi ($m^2$), status kesiapan sewa, mutasi perpindahan barang, approval selisih stok, serta audit trail keamanan menyeluruh.

---

## ✨ 2. Fitur Utama & Modul Sistem

### 🎯 A. Manajemen Inventori & Gudang
1. **Dual Tracking Mode (Individual vs Kuantitas):**
   * **Unit Serial Individual (`assets`):** Melacak unit bernilai tinggi dengan kode unik, nomor seri pabrik, dan lokasi fisik:
     * **25 Unit Video Processor Novastar** bernomor serial urut (`VX20M`, `VX10`, `VX8M`, `VX6 QL`, `VX4E`, `VX4M`, `VX4s`, `VX2S`, `VX2M`).
     * **Armada Layar TV (43", 50", 55", 60", 65")** lengkap dengan nomor flightcase.
     * **10 Unit Toolbox Teknisi (`TB-01` s/d `TB-10`)** masing-masing berisi 13 perkakas standar.
   * **Kuantitas Stok (`stock_balances`):** Melacak material massal seperti kabel jumper LAN, kabel power Neutrik, kabel roll FOH, baut rigging (800+ pcs), katrol takel, clam C, dan span skrup.
2. **Kalkulasi Otomatis Luasan LED ($m^2$):**
   * Sistem otomatis menghitung total luasan meter persegi kabinet videotron:
     * Kabinet 100x50 cm = $0.50\,m^2$/unit (misal: 72 unit = $36\,m^2$)
     * Kabinet 50x50 cm = $0.25\,m^2$/unit (misal: 36 unit = $9\,m^2$)
3. **Pemisahan Kondisi Fisik Riil:**
   * Setiap stok dipisahkan statusnya: `available` (siap sewa), `damaged` (rusak), dan `in_service` (dalam servis).
   * Barang rusak atau sedang diperbaiki otomatis dikeluarkan dari hitungan stok siap sewa.
4. **Visualisasi Titik Lokasi Penyimpanan (Gedung Utama):**
   * **Lemari 1 (`GU-ALM-1`):** Sparepart, PSU modul, Receiving Card, Powerbox.
   * **Lemari 2 (`GU-ALM-2`):** Kabel display, HDMI/SDI converter, capture card, tools.
   * **Lemari 3 (`GU-ALM-3`):** Smart TV, STB, HDMI Extender TX/RX, Splitter.
   * **Area Staging LED (`GU-STG-LED`):** Kabinet LED, Hanging Bar, Rigging H.
   * **Area Workshop (`GU-WS-TB`):** Toolbox teknisi TB-01 s/d TB-10.
5. **Upload & Manajemen Foto Produk:**
   * Master barang mendukung upload foto/gambar perangkat dengan live preview.
   * Tampilan thumbnail foto pada katalog dan profil detail produk.
   * Pembersihan otomatis file lama di storage publik saat diganti atau dihapus.

### 🔄 B. Transaksi & Alur Kontrol Operasional
1. **Pengesahan Saldo Stok Awal (`/initial-counts`):**
   * Sesi verifikasi fisik stok awal Excel oleh Tim Warehouse.
   * Tombol **"Sahkan Saldo Stok Awal"** mengunci data menjadi saldo resmi berjalan dan memposting histori mutasi.
2. **Mutasi Stok Atomik (`/movements`):**
   * Transaksi Inbound (Masuk), Outbound (Keluar ke Event), dan Transfer Antar-Lemari.
   * Transaksi database atomik (ACID) dengan pencatatan otomatis ke kartu stok.
3. **Penyesuaian Stok & Approval Owner (`/adjustments`):**
   * Form pengajuan koreksi fisik oleh Tim Warehouse dilengkapi alasan dan upload bukti foto/dokumen.
   * Gate approval berjenjang khusus Owner: saldo stok resmi hanya berubah setelah disetujui (*Approved*).
4. **Audit Trail Komprehensif (`/audit-logs`):**
   * Seluruh aktivitas pengguna (siapa, waktu, aksi, perubahan nilai lama vs baru, alamat IP) dicatat secara kekal (*immutable*).

### 🛡️ C. Superuser Raw Database Manager (`/superuser/database`)
* Panel penjelajah dan pengelola database tingkat rendah (*raw CRUD explorer*) setara Django Admin khusus role `Superuser`.
* Memuat katalog 16 tabel PostgreSQL dengan penghitung baris data (*live count*).
* Pencarian teks/ID di semua kolom, pengurutan kolom dinamis, dan paginasi data.
* Fitur *Raw Create*, *Raw Edit*, dan *Raw Delete* dengan dialog konfirmasi keamanan serta pencatatan audit log otomatis.
* Keamanan password pada tabel `users` otomatis menggunakan hashing Bcrypt.

### 🛠️ E. Modul Servis, Workshop & Manajemen Pengguna Multi-Role
* **Tiket Servis Modul LED (`/service-tickets`):** Alur antrean perbaikan, investigasi diagnosa teknisi, pengambilan sparepart lemari bengkel, dan uji QC.
* **Manajemen Pengguna Multi-Role (`/users`):** Dukungan penetapan banyak peran per pengguna (misal Warehouse merangkap Teknisi) serta proteksi Superuser.
* **Aksi Cepat Inventori & Aset Serial (`/products` & `/assets`):** Shortcut tambah stok masuk, registrasi unit serial baru, dan pelaporan selisih/kehilangan barang.

### 💰 F. Modul Invoicing, Penagihan Klien & Kuitansi Resmi (`/invoices`)
* **Penerbitan Invoice Cepat:** Dibuat otomatis dari pesanan event dengan mengekstrak spesifikasi layar LED ($m^2$, pitch, seri), unit TV, dan aksesoris.
* **Termin Tagihan Fleksibel:** Pilihan Lunas (100%), Down Payment (DP), atau Pelunasan Sisa Tagihan.
* **Ledger Pembayaran Masuk:** Pencatatan bertahap (multi-payment) dengan nomor kuitansi unik `PAY-YYYYMM-XXXX`, metode transfer bank BCA resmi Omah Visual, dan upload bukti transfer.
* **Dokumen Cetak Standar A4:** Lembar Faktur Tagihan resmi A4 (kop surat, Terbilang Rupiah, instruksi rekening bank, tanda tangan finance, stempel watermark *LUNAS* / *DP DITERIMA*) dan Kuitansi Penerimaan Pembayaran resmi.
* **1-Klik Bagikan ke WhatsApp Customer:** Format pesan tagihan WhatsApp resmi dengan rincian total, DP, sisa piutang, dan nomor rekening transfer.

### 🌐 G. Input Nomor Telepon Internasional Dinamis
* Komponen interaktif `components/phone-input.blade.php` dengan katalog 33 negara (Asia Tenggara, Asia Pasifik, Timur Tengah, Eropa, Amerika).
* Filter pencarian live bendera, nama negara, dan dial code, auto-detect prefix, auto-trim angka 0 di depan nomor, dan integrasi WhatsApp `wa.me` E.164.


---

## 💻 3. Tech Stack & Spesifikasi Sistem

* **Backend Framework:** Laravel 13 (PHP 8.5)
* **Database Engine:** PostgreSQL 18
* **Frontend:** Laravel Blade, Tailwind CSS v4, Vite
* **Autentikasi & RBAC:** Session Guard + `spatie/laravel-permission` v8.3
* **Code Formatter:** Laravel Pint
* **Automated Testing:** PHPUnit (107 feature tests, 693 assertions, 100% Green)

---

## 🚀 4. Panduan Menjalankan Aplikasi

### Metode 1: Menggunakan Launcher Batch (Paling Praktis)
Cukup klik dua kali (*double-click*) file berikut di direktori utama:
```bat
nyalakan-inventory.bat
```
*Script ini otomatis memastikan ekstensi PHP aktif, menjalankan server di background, dan membuka browser ke `http://127.0.0.1:8000`.*

### Metode 2: Menggunakan Terminal Manual
1. Pastikan PostgreSQL berjalan dan database `inventory_db` tersedia.
2. Jalankan migrasi dan seeder data riil (jika instalasi baru):
   ```bash
   php artisan migrate --seed
   ```
3. Hubungkan symlink storage publik:
   ```bash
   php artisan storage:link
   ```
4. Build aset frontend (opsional jika sudah terbuild):
   ```bash
   npm run build
   ```
5. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
6. Buka browser pada alamat: **`http://127.0.0.1:8000`**

---

## 👥 5. Akun Pengguna Demo

Aplikasi telah disiapkan dengan beberapa akun pengujian sesuai peran (*roles*) operasional:

| Role / Peran | Email Login | Password Default | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Owner** | `owner@omahvisual.com` | `password123` | Akses penuh dashboard, approval penyesuaian stok, audit trail |
| **Warehouse** | `warehouse@omahvisual.com` | `password123` | Input produk, mutasi barang, pengesahan stok awal, pengajuan koreksi |
| **Admin LED** | `admin@omahvisual.com` | `password123` | Monitoring ketersediaan stok kabinet videotron, katalog produk |
| **Superuser** | `super@omahvisual.com` | `password123` | Akses teknis penuh, kontrol Database Manager (Raw CRUD Explorer) |

---

## 🧪 6. Menjalankan Pengujian Otomatis

Untuk memastikan seluruh alur bisnis, proteksi hak akses, dan integritas database tetap valid:

```bash
# Jalankan seluruh test suite
php artisan test --compact

# Jalankan pengujian database manager & gambar
php artisan test --compact --filter=SuperuserDatabaseAndProductImageTest
```

---

## 📂 7. Struktur Direktori Utama

```
operational-omahvisual/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php               # Login & Logout session
│   │   ├── DashboardController.php          # Metrik utama kabinet LED & aset
│   │   ├── ProductController.php            # Master SKU & upload foto
│   │   ├── AssetController.php              # Unit serial individual (25 Processor)
│   │   ├── LocationController.php           # Titik lemari 1-3 & area gudang
│   │   ├── StockMovementController.php      # Mutasi Inbound, Outbound, Transfer
│   │   ├── InitialCountController.php       # Verifikasi stok awal Excel
│   │   ├── StockAdjustmentController.php    # Koreksi selisih stok & approval
│   │   ├── SuperuserDatabaseController.php  # Raw Database CRUD Manager
│   │   └── AuditLogController.php           # Log immutable audit trail
│   └── Models/                              # Eloquent Models & Relasi
├── database/
│   ├── migrations/                          # Skema tabel PostgreSQL
│   └── seeders/                             # Seeder data riil Excel (Processor, TV, dll)
├── resources/
│   ├── css/app.css                          # Konfigurasi Tailwind CSS v4 (@custom-variant dark)
│   └── views/                               # Template antarmuka Blade
│       ├── auth/                            # Form login LED broadcast
│       ├── layouts/                         # Master shell & dark/light theme
│       ├── products/                        # Form katalog & preview foto
│       ├── assets/                          # Kartu aset serial & status
│       ├── locations/                       # Visualisasi lemari gudang
│       ├── movements/                       # Form mutasi stok
│       ├── initial_counts/                  # Berita acara stok awal
│       ├── adjustments/                     # Form koreksi & panel approval
│       └── superuser/database/              # Raw data grid, editor, & catalog
├── routes/
│   └── web.php                              # Definisi rute & middleware RBAC
├── storage/                                 # Storage publik upload foto produk
└── nyalakan-inventory.bat                   # Launcher satu-klik untuk Windows
```

---

## 🔮 8. Roadmap Pengembangan Berikutnya

Sesuai dengan **PRD Global Operasional**, status implementasi fitur utama:
* [x] **Modul Customer & Order:** Pembuatan SPK sewa, denah panggung 2D/3D, dan kontak customer WhatsApp terintegrasi.
* [x] **Modul SPK & Cargo List / Surat Jalan:** Pembuatan daftar muat barang dinamis berdasarkan spesifikasi luas $m^2$ LED yang dipesan (Dual tracking kuantitas & serial flightcase).
* [x] **Gate Kesiapan Keberangkatan & Multi-Armada:** Pengecekan kesiapan 4 pilar sebelum armada jalan (Barang, Kru Teknisi, Armada Multi-Kendaraan Logistik, Dana Operasional BBM/Tol).
* [x] **Modul Penggajian & Rekapitulasi Honor Kru Freelance (`/crew-payroll`):** Rekapitulasi honor kru per event dan bulanan, cetak slip gaji A4, pelunasan massal, dan 1-klik bagikan slip via WhatsApp.
* [x] **Work Order Perbaikan Internal (Workshop):** Manajemen tiket servis modul LED, karantina unit rusak, konsumsi sparepart, dan pemulihan saldo siap sewa.
* [x] **Dropdown Kode Negara Internasional Dinamis (Kru & Customer):** Dukungan multi-negara selain Indonesia (+62) pada form kontak dengan format E.164 resmi.
* [x] **Modul Invoicing & Penagihan Client Resmi (`/invoices`):** Faktur tagihan A4, pembayaran bertahap (DP & pelunasan), kuitansi pembayaran resmi, dan 1-klik kirim tagihan via WhatsApp.
* [ ] **Dashboard Analisis Manajer Operasional:** Grafik utilisasi armada TRAGA 1/2/3, tren ukuran layar LED, performa crew, dan analisa laba bersih event.
* [ ] **Portal Pelacakan Mandiri oleh Customer (Tracking Status LED Screen via Order Token):** Pelacakan live persiapan LED tanpa login untuk EO/klien.
* [ ] **Demobilisasi & Rekonsiliasi Pasca-Event Otomatis:** Deteksi barang tertinggal atau modul rusak pasca-event ke tiket servis.
* [ ] **Integrasi Barcode Scanner Fisik:** Penerapan scanner barcode/QR code fisik pasca alur SPK dan cargo list matang.

---

*Hak Cipta © CV Han Media Tekindo — Omah Visual LED. Seluruh hak cipta dilindungi.*
