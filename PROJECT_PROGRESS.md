# 📋 Omah Visual LED — Operational & Inventory System
**Project Progress & Roadmap Reference Document**  
*Dokumen ini disusun sebagai rangkuman status pengembangan, arsitektur sistem, dan panduan kerja untuk sesi percakapan berikutnya.*

---

## 🏢 1. Profil & Konteks Proyek
* **Nama Sistem:** Web Operational & Inventory Management System — Omah Visual LED
* **Pemilik Kebutuhan:** CV Han Media Tekindo / Omah Visual LED
* **Lokasi Operasional Utama:** Gedung Utama Omah Visual LED
* **Keputusan Arsitektur:** **Single Repository / Modular Monolith** (Menyatukan seluruh proses operasional rental, event, SPK, dan inventori dalam satu aplikasi dan satu database terpadu).
* **Repository GitHub:** [`https://github.com/hazehans/operational-omahvisual.git`](https://github.com/hazehans/operational-omahvisual.git)
* **Direktori Lokal:** `D:\Job\ID_CV Han Media Tekindo\operational-omahvisual`

---

## 🛠️ 2. Tech Stack & Environment
* **Backend:** PHP 8.5 (CLI x64) terpasang di `C:\Users\user\php` (sudah ada di User PATH)
* **Package Manager:** Composer v2.11 (terpasang di `C:\Users\user\php`)
* **Framework:** Laravel 11 / 12
* **Frontend:** Laravel Blade + Tailwind CSS (Interaktivitas modern)
* **Database:** PostgreSQL 18 Lokal
  - **Nama DB:** `inventory_db`
  - **Host / Port:** `127.0.0.1:5432`
  - **Username / Password:** `postgres` / `hans`
* **Paket RBAC:** `spatie/laravel-permission` v8.3
* **File Launcher Cepat:** `nyalakan-inventory.bat` (cukup double-click untuk ON, otomatis buka `http://127.0.0.1:8000`)

---

## 🔍 3. Rangkuman Temuan Data Riil (Excel Analysis)
Berdasarkan analisis 2 file Excel (`DATA TOOLS BOX .2` dan `DEV_STOCK OPNAME by Risma` / 27 sheet tab) di folder `raw_data`:

1. **Dual Tracking Mode:**
   - **Individual (`assets`):** Untuk barang bernilai tinggi atau yang sudah memiliki nomor unik:
     - **25 Unit Video Processor Novastar** bernomor 1 s/d 25 (`VX20M`, `VX10`, `VX8M`, `VX6 QL`, `VX 4E QL`, `VX4M`, `VX4s`, `VX2S`, `VX2M`).
     - **Layar Smart TV (43", 50", 55", 60", 65")** + nomor flightcase.
     - **Toolbox Teknisi (TB-01 s/d TB-10)** masing-masing berisi 13 tools standar.
   - **Kuantitas (`stock_balances`):** Kabel jumper LAN/Power (ratusan pcs), kabel FOH roll, baut (800+ pcs), kupu-kupu rigging, clam C, takel katrol, webbing sling.
2. **Kalkulasi Luasan LED ($m^2$):**
   - Satuan dasar kabinet adalah **`Unit`**, namun sistem otomatis menghitung luasan meter persegi:
     - Kabinet 100x50 cm = $0.50\,m^2$/unit (misal 72 unit = $36\,m^2$)
     - Kabinet 50x50 cm = $0.25\,m^2$/unit (misal 36 unit = $9\,m^2$)
3. **Titik Lokasi Riil di Gedung Utama:**
   - **Lemari 1 (`GU-ALM-1`):** Sparepart, PSU, Powerbox, Modul, Receiving Card
   - **Lemari 2 (`GU-ALM-2`):** Kabel display, konverter, capture card, spare tools
   - **Lemari 3 (`GU-ALM-3`):** Perangkat TV, STB, HDMI Extender TX/RX, Splitter
   - **Area Staging LED (`GU-STG-LED`):** Kabinet LED, Hanging Bar, Rigging Besi H
   - **Area Workshop (`GU-WS-TB`):** Toolbox TB-01 s/d TB-10
4. **Pemisahan Kondisi Fisik:**
   - Status barang dipisahkan: `available` (tersedia), `damaged` (rusak), dan `in_service` (dalam perbaikan).
   - Barang rusak (misal: PSU Lite rusak 1) tidak dihitung dalam stok yang siap disewakan.

---

## ✅ 4. Pencapaian yang Sudah Selesai (Completed Milestones)

- [x] **Setup Environment:** PHP 8.5, Composer, ekstensi database (`pdo_pgsql`, `pgsql`, `pdo_sqlite`), dan User PATH.
- [x] **Inisialisasi Project Laravel:** Kerangka project Laravel lengkap terpasang di root direktori.
- [x] **Konfigurasi Database `.env`:** Terkoneksi sempurna ke PostgreSQL `inventory_db`.
- [x] **Instalasi RBAC:** `spatie/laravel-permission` terpasang dan termigrasi.
- [x] **Perancangan Skema Database (Migrations):**
  - `categories` (Kategori hierarkis)
  - `units` (Satuan barang)
  - `locations` (Hierarki lokasi penyimpanan Gedung Utama)
  - `products` (Master barang + `tracking_type` + `sqm_per_unit`)
  - `assets` (Unit individual + auto-generator kode `OVL-AST-YYYYMM-XXXX`)
  - `stock_balances` (Saldo kuantitas + kepemilikan + kondisi fisik + alokasi)
  - `stock_movements` & `stock_movement_items` (Audit trail mutasi atomik)
  - `stock_adjustments` & `stock_adjustment_items` (Alur pengajuan & approval)
  - `inventory_counts` & `inventory_count_items` (Sesi stock opname)
  - `audit_logs` (Pencatatan aktivitas sistem immutable)
- [x] **Eloquent Models:** Model lengkap dengan relasi, helper kalkulasi ketersediaan stok, dan generate kode.
- [x] **Database Seeder (`InventoryDatabaseSeeder`):**
  - **Roles:** `Owner`, `Branch Manager`, `Warehouse`, `Admin LED`, `Superuser`
  - **Akun Uji Coba:**
    - `owner@omahvisual.com` (pass: `password123`)
    - `warehouse@omahvisual.com` (pass: `password123`)
    - `admin@omahvisual.com` (pass: `password123`)
    - `super@omahvisual.com` (pass: `password123`)
  - **Data Riil Excel Tersemai:** 25 Processor Novastar bernomor, Kabinet P3.9 & P2.6 dengan luas $m^2$, TV 50" Samsung, kabel jumper, lemari 1-3, dan toolbox 1-10.
- [x] **Automated Testing:** Pengujian PHPUnit lulus 100% (Green).
- [x] **Git Remote Sync:** Tersambung ke repo GitHub `operational-omahvisual`.
- [x] **Launcher Script:** `nyalakan-inventory.bat` berfungsi normal.

---

## ✅ 5. Pencapaian Terbaru (Completed Milestones)

### Tahap 1: Autentikasi & Antarmuka Lengkap (SELESAI ✅)
- [x] **Modul Autentikasi Penuh (`/login`, `/logout`):**
  - Form login email + password standar sesuai preferensi pengguna.
  - Nuansa modern minimalist dark mode dengan ambient gradient LED broadcast Omah Visual (`slate-950`, `indigo-950`, `cyan-400`).
  - Proteksi middleware `auth` dan `guest` pada seluruh rute di [`routes/web.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/routes/web.php).
- [x] **Master Layout Shell Dashboard ([`layouts/app.blade.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/layouts/app.blade.php)):**
  - Toggle Theme (Dark / Light) dengan persistensi `localStorage`.
  - Sidebar navigasi lengkap dengan badge indikator role pengguna aktif (`Owner`, `Branch Manager`, `Warehouse`, `Admin LED`, `Superuser`).
  - Topbar dengan nama user, indikator lokasi Gedung Utama, dan tombol logout.
  - Alert container global (Success, Warning, Error, Info).

### Tahap 2: Antarmuka & Logika Modul Inventori (SELESAI ✅)
- [x] **Halaman Master Barang & Kategori ([`/products`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/products/index.blade.php)):**
  - CRUD lengkap dengan kalkulasi luas meter persegi ($m^2$) kabinet LED.
  - Filter kategori, mode pelacakan (individual/kuantitas), dan pencarian real-time.
- [x] **Halaman Aset Serialized ([`/assets`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/assets/index.blade.php)):**
  - Menampilkan 25 unit processor Novastar (`OVL-PROC-001` s/d `025`), TV Samsung, dan Toolbox.
  - Form update cepat kondisi fisik (Bagus/Rusak/Servis) dan status penempatan.
- [x] **Halaman Lokasi Gudang ([`/locations`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/locations/index.blade.php)):**
  - Visualisasi 5 area penyimpanan: Lemari 1, Lemari 2, Lemari 3, Area Staging LED, dan Workshop.
- [x] **Halaman Pengesahan Stok Awal ([`/initial-counts`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/initial_counts/index.blade.php)):**
  - Sesi verifikasi stok awal Excel dengan tombol **"Sahkan Saldo Stok Awal (Verifikasi Warehouse)"**.
  - Mengubah status ke verified, membuat transaksi mutasi awal, dan mencatat audit trail.
- [x] **Halaman Mutasi Stok ([`/movements`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/movements/index.blade.php)):**
  - Form pencatatan Inbound, Outbound, dan Transfer antar lemari dengan transaksi database atomik.
- [x] **Halaman Penyesuaian Stok & Approval Owner ([`/adjustments`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/adjustments/index.blade.php)):**
  - Form pengajuan koreksi fisik oleh Warehouse (+ alasan & lampiran bukti).
  - Tab Menunggu Persetujuan Owner, Disetujui, dan Ditolak.
  - Tombol **Setujui (Approve)** dan **Tolak (Reject)** khusus Owner/Branch Manager dengan posting saldo atomik.
- [x] **Halaman Audit Trail ([`/audit-logs`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/audit_logs/index.blade.php)):**
  - Log aktivitas pengguna immutable untuk Owner & Superuser.
- [x] **Automated Tests:** 17 test PHPUnit lulus 100% (131 assertions).
- [x] **Code Style:** Difilter & diformat sesuai standar Laravel Pint.

### Tahap 3: Fitur Foto Produk, Raw DB Superuser Manager, & Tipografi Nyaman (SELESAI ✅)
- [x] **Upload & Manajemen Foto Produk (`image_path`):**
  - Migrasi `add_image_path_to_products_table` & storage symlink aktif (`/storage/products/`).
  - Form input upload foto di `products/create.blade.php` & `products/edit.blade.php` dengan live JavaScript preview file.
  - Opsi hapus foto saat edit dan pembersihan file otomatis dari storage disk public saat diganti/dihapus.
  - Tampilan thumbnail foto pada tabel katalog dan kartu profil rincian produk (`show.blade.php`).
- [x] **Superuser Raw Database Manager (Opsi 1 / Setara Django Admin CRUD):**
  - Kontroler [`SuperuserDatabaseController.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/app/Http/Controllers/SuperuserDatabaseController.php) di `/superuser/database`.
  - Akses terbatas secara ketat untuk peran `Superuser` (role lain mendapatkan `403 Forbidden`).
  - Whitelist 16 tabel inti database PostgreSQL dengan rekap baris data per kategori (Master Data, Inventaris, Transaksi, Sistem & Keamanan).
  - Data grid dinamis dengan pencarian teks/ID, pengurutan kolom, paginasi, aksi view/edit/delete, dan pencatatan audit log otomatis.
  - **Bugfix Navigasi & Static Analysis:** Memperbaiki collection grouping link tabel agar selalu mengarah ke nama tabel riil (`$item['table']`), membersihkan seluruh warning deprecated `$request->get()`, dan memperbaiki type-hinting `abort_if()`.
- [x] **Peningkatan Tipografi & Skalasi Font (Typography Scaling):**
  - Mengubah ukuran teks yang sebelumnya terlalu kecil (`text-[10px]`, `text-[11px]`, `text-xs`) menjadi lebih nyaman dibaca (`text-xs`, `text-sm`, `text-base`, `text-lg`).
  - Optimalisasi hierarki visual pada kartu dashboard, tabel aset, tabel master barang, dan form input.
- [x] **Dokumentasi Proyek Komprehensif:** Menyusun [`README.md`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/README.md) resmi yang merinci profil proyek, arsitektur, cara kerja, akun demo, dan roadmap lanjutan.
- [x] **Catatan Barcode Scanner:** Ditunda sementara atas persetujuan pengguna agar alur operasional utama matang terlebih dahulu sebelum integrasi fisik scanner.

---

## 🔮 6. Roadmap Jangka Panjang (Operasional Lanjutan Sesuai PRD Global)
- [ ] Integrasi Barcode / QR Code Fisik Scanner (Ditunda sementara sesuai arahan user agar fokus pematangan operasional)
- [ ] Modul Customer & Order (Input order Admin via WhatsApp, Quotation & Revisi)
- [ ] Modul SPK (Surat Perintah Kerja) & Cargo List / Surat Jalan
- [ ] Gate Kesiapan Keberangkatan Event (Barang, Kru, Armada, Dana Operasional)
- [ ] Demobilisasi & Rekonsiliasi Pasca-Event (Deteksi barang hilang/tertinggal di venue)
- [ ] Modul Work Order Servis Internal & Pemeliharaan Modul LED
- [ ] Modul Invoicing & Verifikasi Pembayaran (Finance)


