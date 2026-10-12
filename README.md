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

### 🎨 D. Antarmuka Pengguna Modern & Aksesibilitas
* Tema antarmuka bertema *Broadcast LED Event* (slate gelap elegan dengan aksen indigo/cyan Omah Visual).
* Mendukung **Mode Gelap (Dark Mode)** dan **Mode Terang (Light Mode)** dengan penyimpanan preferensi di `localStorage`.
* Tipografi yang proporsional, nyaman dibaca, dan kontras warna yang ramah pengguna.

---

## 💻 3. Tech Stack & Spesifikasi Sistem

* **Backend Framework:** Laravel 13 (PHP 8.5)
* **Database Engine:** PostgreSQL 18
* **Frontend:** Laravel Blade, Tailwind CSS v4, Vite
* **Autentikasi & RBAC:** Session Guard + `spatie/laravel-permission` v8.3
* **Code Formatter:** Laravel Pint
* **Automated Testing:** PHPUnit (17 feature tests, 131 assertions, 100% Green)

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

Sesuai dengan **PRD Global Operasional**, pengembangan modul berikutnya akan mencakup:
* [ ] **Modul Customer & Order:** Pembuatan SPK sewa, Quotation, dan pencatatan order via WhatsApp.
* [ ] **Modul SPK & Cargo List / Surat Jalan:** Pembuatan daftar muat barang otomatis berdasarkan luas $m^2$ LED yang dipesan.
* [ ] **Gate Kesiapan Keberangkatan:** Pengecekan kesiapan 4 pilar sebelum truk jalan (Barang, Kru Teknisi, Armada Truk, Dana Operasional).
* [ ] **Demobilisasi & Rekonsiliasi Pasca-Event:** Deteksi otomatis barang hilang, tertinggal di venue, atau modul LED rusak sehabis acara.
* [ ] **Work Order Perbaikan Internal:** Manajemen servis modul LED dan penugasan teknisi internal.
* [ ] **Integrasi Barcode Scanner Fisik:** Penerapan scanner barcode/QR code setelah alur SPK dan cargo list rampung.

---

*Hak Cipta © CV Han Media Tekindo — Omah Visual LED. Seluruh hak cipta dilindungi.*
