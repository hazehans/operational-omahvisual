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

## 🎯 5. Rencana Kerja Selanjutnya (Action Items untuk Chat Baru)

Berikut adalah urutan tugas yang siap dikerjakan pada sesi chat berikutnya:

### Tahap 1: Autentikasi & Tampilan Admin Lengkap (Prioritas Segera)
- [ ] **Modul Autentikasi Penuh (`/login`, `/logout`):**
  - Halaman login bernuansa modern dark/light dengan logo Omah Visual LED.
  - Kartu tombol *Quick-Login Demo* (sekali klik langsung login sebagai Owner / Warehouse / Admin untuk kemudahan testing).
  - Proteksi middleware auth (halaman web tidak bisa diakses tanpa login).
- [ ] **Master Layout Admin Dashboard (`resources/views/layouts/app.blade.php`):**
  - Sidebar navigasi interaktif dengan badge indikator.
  - Topbar dengan nama user aktif, role, dan tombol logout.
  - Notifikasi toast / alert feedback.

### Tahap 2: Antarmuka Modul Inventori (CRUD & Operasi)
- [ ] **Halaman Master Barang & Kategori (`/products`):**
  - Tabel interaktif dengan filter kategori, pencarian real-time, status aktif/nonaktif.
  - Form tambah/edit barang dengan pilihan mode individual vs kuantitas, serta input spesifikasi luas kabinet.
- [ ] **Halaman Aset Terlacak / Serialized (`/assets`):**
  - Menampilkan 25 unit processor Novastar (`OVL-PROC-001` s/d `025`), TV, dan Toolbox.
  - Tampilan kartu detail unit, status (Di Gudang / Dipakai / Servis), dan riwayat pergerakan aset.
- [ ] **Halaman Lokasi Gudang (`/locations`):**
  - Visualisasi isi barang per lokasi: Lemari 1, Lemari 2, Lemari 3, Area Staging LED, dan Area Workshop.
- [ ] **Halaman Mutasi Stok (`/movements`):**
  - Form pencatatan mutasi barang masuk (*Inbound*), barang keluar (*Outbound*), dan transfer antar lemari.
- [ ] **Halaman Penyesuaian Stok & Approval Owner (`/adjustments`):**
  - Warehouse mengajukan koreksi selisih stok fisik vs sistem (+ lampiran alasan/foto).
  - Khusus Owner: Tampilan review dan tombol **Setujui (Approve)** atau **Tolak (Reject)**. Saldo otomatis diperbarui hanya jika disetujui.
- [ ] **Halaman Audit Trail (`/audit-logs`):**
  - Khusus Owner & Superuser untuk memantau siapa mengubah apa dan kapan.

### Tahap 3: Proyeksi Lanjutan (Modul Operasional & Event)
- [ ] Modul Booking & Alokasi Event (SPK, Surat Jalan Manifest barang berangkat & pulang).
- [ ] Rekonsiliasi barang pasca-event (pencatatan kekurangan/kehilangan di venue).
- [ ] Modul Servis & Perbaikan.

