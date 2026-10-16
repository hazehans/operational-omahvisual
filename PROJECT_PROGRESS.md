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

### Tahap 3.5: Visualisasi Foto Titik Lokasi / Lemari & Simulasi Stok Awal (SELESAI ✅)
- [x] **Manajemen & Unggah Foto Titik Lokasi / Lemari (`locations.image_path`):**
  - Migrasi penambahan kolom `image_path` pada tabel `locations`.
  - Implementasi CRUD penuh pada [`LocationController.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/app/Http/Controllers/LocationController.php) (index, create, store, show, edit, update, destroy).
  - Formulir unggah foto titik lokasi (`create.blade.php` dan `edit.blade.php`) dengan *live preview* gambar JavaScript dan pembersihan file dari storage saat dihapus/diganti.
  - Tampilan kartu lokasi visual di [`locations/index.blade.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/locations/index.blade.php) dengan foto fisik lemari dan *fallback visual placeholder* bergaya LED broadcast Omah Visual sesuai tipe zona (`cabinet`, `staging_area`, `locker`, `building`).
  - Header detail lokasi di [`locations/show.blade.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/locations/show.blade.php) dengan banner foto fisik dan tombol quick edit foto.
- [x] **Simulasi & Dinamisasi Notifikasi Sahkan Stok Awal:**
  - Indikator menu sidebar dinamis: Dot amber berkedip (*animate-ping*) aktif saat ada sesi yang belum diverifikasi, dan otomatis berubah menjadi centang hijau tenang (`✓`) ketika disahkan resmi.
  - Tombol simulasi reset (`resetSimulation`) untuk memfasilitasi pengujian alur verifikasi berkali-kali tanpa merusak basis data.
- [x] **Pengujian Otomatis:** Menambahkan [`LocationPhotoManagementTest.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/tests/Feature/LocationPhotoManagementTest.php) (6 tests baru).

### Tahap 3.6: Pemotong Gambar Interaktif (Cropper.js) & Halaman Profil Pengguna (SELESAI ✅)
- [x] **Pustaka Pemotong Gambar (Cropper.js):**
  - Komponen modal global [`image-cropper-modal.blade.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/components/image-cropper-modal.blade.php) terpasang di layout utama.
  - Mendukung rotasi 90°, zoom in/out, reset, dan rasio pemotongan presisi:
    - 16:9 untuk Titik Lokasi & Lemari Gudang
    - 4:3 untuk Master Barang & Komponen Videotron
    - 1:1 untuk Avatar Profil Pengguna
  - Terintegrasi penuh pada formulir tambah & edit lokasi, tambah & edit produk, dan profil pengguna.
- [x] **Modul Profil Pengguna & Keamanan Kata Sandi (`/profile`):**
  - Migrasi `add_phone_and_avatar_path_to_users_table`.
  - Akses kontroler [`ProfileController.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/app/Http/Controllers/ProfileController.php) di `/profile`.
  - Formulir pembaruan nama, nomor WhatsApp/telepon, dan foto avatar ber-crop 1:1 dengan tombol hapus foto.
  - Formulir pembaruan kata sandi mandiri dengan verifikasi `current_password` dan konfirmasi sandi baru.
  - Penampil wewenang akun (*Roles & Permissions*) yang transparan bagi setiap staf.
  - Penambahan tautan profil di Topbar navigasi lengkap dengan foto avatar/inisial dan role aktif.
- [x] **Pengujian Otomatis:** Menambahkan suite [`UserProfileAndAvatarTest.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/tests/Feature/UserProfileAndAvatarTest.php) (6 tests baru). Total suite pengujian proyek kini mencapai: **29 Tests, 179 Assertions, 100% Lulus (Green)**.

### Tahap 4: Modul Pemesanan Rental LED & SPK Lapangan (SELESAI ✅)
- [x] **Kalkulator Panel LED & Order Persewaan (`/orders`):**
  - Kalkulator kebutuhan kabinet LED otomatis berdasarkan dimensi panjang $\times$ tinggi target panggung.
  - Alokasi stok kabinet dan aksesoris pendukung (hanging bar, kabel, processor) saat order dibuat.
- [x] **Surat Perintah Kerja (SPK) & Cargo Checklist Lapangan (`/spk`):**
  - Cetak dokumen SPK siap cetak (Halaman 1 Ringkasan, Halaman 2 Peta Panggung, Halaman 3 Cargo Checklist, Halaman 4 Berita Acara & Checklist Gate).
  - Verifikasi Keberangkatan dan Kepulangan Gate dengan aturan PIC kepulangan wajib berbeda dari keberangkatan.
  - Tautan Live View kru panggung tanpa login via token publik unik (`spk/live/{token}`).

### Tahap 5: Modul Servis & Workshop Maintenance (SELESAI ✅)
- [x] **Manajemen Tiket Servis (`/service-tickets`):**
  - Siklus tiket servis perbaikan modul/hardware LED: *Antrean Servis*, *Diagnosa & Investigasi*, *Pengerjaan Perbaikan*, *Uji Coba & QC*, *Selesai Siap Pakai*.
  - Pemisahan wewenang operasional: Warehouse membuka tiket saat inspeksi barang kembali, Teknisi mengeksekusi pengerjaan di workshop.
  - Role baru `Teknisi Servis` dengan hak akses fokus bengkel (`manage service tickets`, `view inventory`).
  - Fitur ambil dan catat penggunaan suku cadang/sparepart dari lemari dengan mutasi otomatis ke stok bengkel.
  - Perbaikan bug UI dark mode, penataan modal pop-up sparepart berbasis stacking context `@stack('modals')`, dan type casting Intelephense pada controller.

### Tahap 6: Manajemen Pengguna Multi-Role & Quick Action Inventori (SELESAI ✅)
- [x] **Manajemen Pengguna & Dukungan Multi-Role (`/users`):**
  - Pembaruan formulir pengguna (`users/create.blade.php`, `users/edit.blade.php`) menggunakan kartu checkbox multi-role interaktif.
  - Staf dapat memegang lebih dari satu peran (misal: *Warehouse* merangkap *Teknisi Servis*).
  - Sinkronisasi peran otomatis via Spatie Permission `syncRoles()` dan proteksi akun Superuser terakhir.
  - Tampilan multi-badge warna tematik per role pada tabel daftar pengguna (`users/index.blade.php`).
- [x] **Aksi Cepat Inventori & Pelacakan Kehilangan/Stok Masuk (`/products/{id}` & `/assets`):**
  - Penambahan tombol pintas aksi cepat (*Quick Actions*) di halaman detail barang:
    - `+ Tambah Stok (Beli/Masuk)` untuk barang kuantitas.
    - `+ Tambah Unit Serial` untuk barang individual (Processor, TV, dll).
    - `Lapor Selisih / Hilang` langsung ke form penyesuaian stok (*Stock Adjustment*).
  - Pembuatan modul form pendaftaran unit serial baru ([`assets/create.blade.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/assets/create.blade.php)) dengan auto-generate kode aset (`OVL-AST-YYYYMM-XXXX`), input serial pabrik, barcode, lokasi lemari, dan status unit.
  - Preseleksi otomatis parameter query URL (`product_id`, `type`, `location_id`) pada form mutasi dan adjustment.
  - Koreksi kalkulasi status stok fisik (Siap Sewa, Rusak, Servis) pada perangkat serial di `ProductController::show()`.
- [x] **Pengujian Otomatis Menyeluruh:**
  - Total test suite saat ini mencapai **71 Tests, 423 Assertions, 100% Lulus (Green)** tanpa kegagalan.

### Tahap 7: Rantai Operasional Terintegrasi — Penugasan Kru, QC Venue & BAST, serta Return Gate Otomatis (SELESAI ✅)
- [x] **Penugasan Kru & Kesiapan Armada Kendaraan Dinamis (`/spk/{id}`):**
  - Pembaruan modal interaktif untuk mengedit roster kru panggung (Crew 1 s/d 7 dan PIC Event) dengan auto-suggest daftar staf aktif.
  - Pemeriksaan kelengkapan kendaraan (E-Toll, Kunci Roda/Dongkrak, STNK Asli, Kartu KIR, Barcode Subsidi MyPertamina) dengan indikator status visual (Siap vs Belum).
  - Sinkronisasi armada kendaraan loading-in/out otomatis ke data pesanan acara.
- [x] **Uji Kelayakan Venue & BAST Instalasi Panggung (`spk/live/{token}` & `/spk/{id}`):**
  - Penambahan form pelaporan uji kelayakan lapangan di mobile live view kru panggung:
    - Checklist proteksi Grounding listrik aman (< 2V AC pencegah induksi panggung).
    - Uji pola warna RGB (Red, Green, Blue, White bersih bebas dead pixel).
    - Uji sinyal video & kestabilan refresh rate 3840Hz bebas flicker kamera.
    - Status kelayakan *Lolos Siap Show* atau *Ditemukan Kendala Teknis*.
    - Upload foto dokumentasi hasil instalasi panggung menyala (BAST visual) langsung via kamera smartphone kru.
  - Kartu pemantauan QC panggung di SPK kantor lengkap dengan pratinjau foto dan opsi verifikasi manual dari desktop.
  - Penyertaan blok ringkasan uji kelayakan QC venue pada lembar cetak fisik SPK A4 (`spk/print.blade.php`).
- [x] **Integrasi Pintu Kepulangan (Return Gate) $\rightarrow$ Servis & Penyesuaian Stok Cepat:**
  - Deteksi otomatis barang yang keluar namun belum dicentang kembali saat verifikasi kepulangan gudang, lengkap dengan banner peringatan.
  - Tombol aksi cepat pasca-event pada setiap baris item checklist (Halaman 2 & Halaman 3):
    - `🛠️ Servis`: Membuka tiket servis karantina di workshop dengan prefill otomatis nama barang, kuantitas, dan riwayat event.
    - `⚠️ Hilang`: Membuka formulir pengajuan penyesuaian stok (*Stock Adjustment*) ke Owner untuk barang tertinggal atau selisih fisik.
    - `📝 Note`: Pengisian catatan kondisi temuan lapangan secara instan berbasis AJAX.
- [x] **Pembaruan Pengujian Fitur:**
  - Penambahan 4 pengujian fitur baru di `EventOrderAndSpkManagementTest.php` untuk penugasan kru, submit QC token publik, verifikasi QC desktop, dan update catatan kondisi checklist.

### Tahap 8: Master Data Management Terdedikasi, Crop & Deletable Images, Integrasi Kompas & Perbaikan Operasional (SELESAI ✅)
- [x] **Pembaruan Input Pesanan Event & Formasi Layar 3D (`/orders/create`):**
  - Bidang input acara diperkaya: *Provinsi Acara*, *Kabupaten/Kota Acara*, *Status Wilayah* (Dalam Kota vs Luar Kota sebagai matriks tarif & operasional), *Jam Menyala* (time), *Waktu Gladi* (datetime), *Kelistrikan* (Genset / Disediakan Client / Lainnya) bersyarat keterangan, serta tipe data *time* presisi pada Jam Loading dan Jam Acara.
  - Dukungan fleksibilitas panggung non-lapangan (outdoor maupun indoor/tanpa panggung fisik) pada modul Stage Designer 3D.
  - Penambahan kompas arah mata angin (Utara & Selatan) pada visual denah 2D Tampak Atas kanvas dan cetak lembar ke-4 SPK.
  - Perbaikan panel detail modular LED di mana tombol hapus panel dan kontrol dimensi tidak lagi terpotong/tenggelam.
- [x] **Master Data Terdedikasi di Luar Database Manager RAW:**
  - **Master Pelanggan (`/customers`):** Pengelolaan 15 atribut lengkap (ID readable `CUST-XXXX`, Nama, Nama Lengkap, Tanggal Lahir, Email, Telepon, Kategori dinamis, Vendor/Instansi, Alamat, Dropdown Provinsi & Kab/Kota Indonesia, Kontak PIC Cadangan, Hubungan, Tanggal Registrasi).
  - **Master Kru Lapangan (`/crews`):** Mendukung multi-posisi sekaligus (PIC, Crew, Supir, Operator), klasifikasi status kerja (*Karyawan Tetap*, *Freelance Outsourcing*, *Magang*), Nomor Darurat, serta upload Foto Profil, Foto KTP, dan Foto Rate Card.
  - **Master Armada Kendaraan (`/vehicles`):** Monitoring status ketersediaan dinamis (*Tersedia, Dipakai, Maintenance, Standby*), kondisi khusus fisik armada, riwayat kelengkapan dokumen, dan upload Foto Mobil, STNK, E-Toll, serta Barcode BBM Subsidi MyPertamina.
- [x] **Integrasi Pemotong Gambar Interaktif (Cropper.js) & Penghapusan Gambar Dinamis:**
  - Modal crop interaktif global dengan 5 mode rasio (1:1 Avatar/Barcode BBM, 3:2 KTP/STNK/E-Toll, 4:3 Hardware, 16:9 Mobil/Panggung, Freeform Rate Card).
  - Fitur 1-klik `🗑️ Hapus` gambar dengan tampilan seketika beralih ke ilustrasi placeholder grafis default dan tombol `↩️ Batal (Undo)` sebelum form disimpan.
  - Backend controller otomatis menghapus berkas fisik lama dari disk storage dan mengatur kolom menjadi `NULL`.
  - Penanganan bug modal crop yang sempat muncul otomatis, diselesaikan tuntas dengan `style="display: none;"`, penutupan dengan Escape key, dan klik backdrop luar.
- [x] **Optimasi Aset Vendor Lokal & Uji Menyeluruh:**
  - Pustaka `Alpine.js` dan `Cropper.js` kini diunduh dan tersimpan lokal di `public/vendor/` untuk operasi yang 100% offline-ready dan stabil.
  - Pencegahan duplikasi nomor kode otomatis (*auto-code generation*) pada model Pelanggan, Kru, dan Armada menggunakan mekanisme loop anti-kolisi.
  - Total pengujian otomatis saat ini mencapai **76 Tests, 465 Assertions, 100% Lulus (Green)** tanpa kegagalan.

---

## 🔮 8. Roadmap Jangka Panjang (Operasional Lanjutan Sesuai PRD Global)
- [ ] Integrasi Barcode / QR Code Fisik Scanner (Ditunda sementara sesuai arahan user agar fokus pematangan operasional)
- [ ] Modul Invoicing & Verifikasi Pembayaran (Finance)
- [ ] Fitur Ekspor Laporan Bulanan (Excel/PDF) untuk Rekapitulasi Barang Hilang & Biaya Servis
- [ ] Notifikasi Discrepancy Otomatis via WhatsApp / Email saat ada barang kurang pasca-event


