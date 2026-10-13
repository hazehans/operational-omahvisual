# 📌 TODO LIST — OMAH VISUAL LED OPERATIONAL & INVENTORY SYSTEM

## 🚀 Status Implementasi: Autentikasi & Antarmuka Lengkap (SELESAI ✅)

### 1. Autentikasi & Keamanan (Priority 1) — ✅ Selesai
- [x] Buat `AuthController` (metode `showLoginForm`, `login`, `logout`)
- [x] Buat view `resources/views/auth/login.blade.php` (tampilan elegan modern dark mode dengan gradient broadcast ambient, email + password reguler)
- [x] Pasang middleware `auth` dan `guest` pada seluruh rute di `routes/web.php`
- [x] Konfigurasi proteksi RBAC Spatie pada menu sidebar (`@can`, `@role`)

### 2. Layout & Shell Dashboard (Priority 1) — ✅ Selesai
- [x] Buat master layout `resources/views/layouts/app.blade.php`
  - [x] Sidebar navigasi (Dashboard, Master Barang, Aset Serial, Lokasi Lemari, Mutasi, Sahkan Stok Awal, Koreksi & Approval, Audit Trail)
  - [x] Header / Topbar dengan indikator lokasi gedung utama, avatar user, role badge, toggle mode gelap/terang, dan tombol logout
  - [x] Flash message container (Success, Warning, Error alerts)
  - [x] Toggle Theme (Dark / Light) dengan persistensi `localStorage`

### 3. Modul Master Data (`/products`) — ✅ Selesai
- [x] Buat `ProductController` (index, show, create, store, edit, update)
- [x] Halaman daftar produk (`resources/views/products/index.blade.php`) dengan filter kategori, mode pelacakan, dan pencarian real-time
- [x] Form input produk (`create.blade.php` & `edit.blade.php`) dengan penanganan:
  - [x] Mode individual vs kuantitas
  - [x] Luas meter persegi per unit ($m^2$) khusus kabinet LED
  - [x] Satuan barang dan kategori hierarkis
- [x] Halaman detail produk (`show.blade.php`) dengan rincian unit terdaftar atau sebaran saldo per lokasi

### 4. Modul Unit Aset Individual (`/assets`) — ✅ Selesai
- [x] Buat `AssetController` (index, show, updateStatus)
- [x] Tampilkan 25 unit processor Novastar (`OVL-PROC-001` s/d `025`), unit TV, dan Toolbox
- [x] Kartu rincian aset (`show.blade.php`): status lokasi saat ini, kondisi, histori mutasi, dan form ganti kondisi (Bagus/Rusak/Servis)

### 5. Modul Titik Lokasi Lemari (`/locations`) — ✅ Selesai
- [x] Buat `LocationController` (index, show)
- [x] Tampilan visualisasi isi tiap lemari (`index.blade.php` & `show.blade.php`): Lemari 1, Lemari 2, Lemari 3, Staging LED, Workshop

### 6. Modul Mutasi Stok (`/movements`) — ✅ Selesai
- [x] Buat `StockMovementController` (index, create, store)
- [x] Form transaksi mutasi (`create.blade.php`): Inbound, Outbound, Transfer antar lemari
- [x] Eksekusi transaksi database atomik & pencatatan audit log mutasi

### 7. Modul Pengesahan Stok Awal (`/initial-counts`) — ✅ Selesai (Simulasi Keputusan User #2)
- [x] Buat `InitialCountController` (index, verify)
- [x] Halaman sesi pengesahan saldo awal fisik (`initial_counts/index.blade.php`)
- [x] Tombol verifikasi resmi oleh Tim Warehouse yang otomatis memposting saldo awal berjalan dan mencatat audit trail

### 8. Modul Penyesuaian Stok & Approval Owner (`/adjustments`) — ✅ Selesai
- [x] Buat `StockAdjustmentController` (index, create, store, approve, reject)
- [x] Form pengajuan koreksi stok fisik oleh tim Warehouse (+ alasan & upload bukti)
- [x] Tab status: Menunggu Approval Owner, Disetujui, Ditolak
- [x] Tombol **Setujui (Approve)** & **Tolak (Reject)** khusus Owner (posting saldo resmi hanya setelah disetujui)

### 9. Modul Audit Trail (`/audit-logs`) — ✅ Selesai
- [x] Buat `AuditLogController` (index)
- [x] Tabel log aktivitas pengguna (siapa, aksi apa, entitas apa, waktu, IP) khusus Owner & Superuser

### 10. Fitur Upload Gambar Barang (`image_path`) — ✅ Selesai
- [x] Migrasi `add_image_path_to_products_table` dan `php artisan storage:link`
- [x] Model `Product`: atribut `image_path` & accessor `getImageUrlAttribute`
- [x] `ProductController`: validasi gambar, upload disk `public/products`, ganti file lama, dan hapus gambar
- [x] Antarmuka upload foto pada `products/create.blade.php` & `products/edit.blade.php` dengan live JavaScript preview
- [x] Tampilan foto produk pada thumbnail tabel katalog (`products/index.blade.php`), kartu profil detail (`products/show.blade.php`)

### 11. Superuser Raw Database Manager (Opsi 1 / Django Admin CRUD) — ✅ Selesai
- [x] Buat `SuperuserDatabaseController` dengan proteksi ketat role `Superuser` (abort 403 untuk role lain)
- [x] Whitelist 16 tabel inti database PostgreSQL (Master Data, Inventaris, Transaksi, Sistem & Keamanan)
- [x] Halaman katalog tabel (`/superuser/database`): ringkasan baris data per tabel dan kategori
- [x] Halaman data grid mentah (`/superuser/database/{table}`): filter pencarian teks/ID, sorting kolom dinamis, paginasi
- [x] Form raw insert baris baru (`create.blade.php`) & form raw edit baris (`edit.blade.php`) dengan tipe kolom otomatis (text, boolean, json, date)
- [x] Hapus baris aman (`destroy`) dan pencatatan seluruh mutasi database mentah ke `AuditLog`
- [x] Menu navigasi khusus "Database Manager (RAW)" di sidebar khusus user `Superuser`

### 12. Peningkatan Tipografi & Keterbacaan UI (Typography Scaling) — ✅ Selesai
- [x] Skalasi ukuran font dari sebelumnya terlalu kecil (`text-[10px]`, `text-[11px]`, `text-xs`) menjadi lebih nyaman dan proporsional (`text-xs`, `text-sm`, `text-base`, `text-lg`)
- [x] Perbaikan hierarki visual pada kartu metrik, tabel data, label input form, dan status badges di seluruh view

### 13. Bugfix & Diagnostik Superuser Database Controller — ✅ Selesai
- [x] Perbaikan routing navigasi tabel: Memperbaiki collection grouping di `index.blade.php` agar selalu mengarah ke nama tabel riil (`$item['table']`), bukan indeks numerik `0, 1, 2`
- [x] Perbaikan static analysis VS Code pada `abort_unless()`: Menggunakan boolean check ketat (`abort_if(! $rawRecord, 404)`) untuk menghindari error tipe `stdClass|null` dan `array`
- [x] Migrasi fungsi deprecated: Mengganti seluruh pemanggilan `$request->get()` menjadi `$request->input()`
- [x] Penambahan test suite untuk memastikan ke-16 link tabel di katalog Database Manager valid dan menghasilkan HTTP 200 (131 assertions lulus 100%)
- [x] Dokumentasi lengkap proyek di `README.md`, `TODO.md`, dan `PROJECT_PROGRESS.md`

### 14. Visualisasi & Manajemen Foto Titik Lokasi / Lemari Gudang — ✅ Selesai
- [x] Migrasi database: Kolom `image_path` pada tabel `locations`
- [x] Model `Location`: Penambahan `$fillable` dan accessor `image_url`
- [x] `LocationController`: Implementasi penuh CRUD (create, store, edit, update, destroy) dengan otorisasi hak akses `manage locations`
- [x] Fitur upload berkas gambar lokasi (JPG, PNG, WebP) dengan live preview JavaScript dan opsi penghapusan foto
- [x] Tampilan kartu lokasi visual di `locations/index.blade.php` dengan fallback visual placeholder bertema LED sesuai zona (`cabinet`, `staging_area`, `locker`, `building`)
- [x] Header kartu detail lokasi di `locations/show.blade.php` lengkap dengan foto visual dan tombol quick edit/ganti foto
- [x] Pengujian otomatis komprehensif di `tests/Feature/LocationPhotoManagementTest.php` (6 tests baru lulus 100%)

### 15. Simulasi Interaktif & Dinamisasi Notifikasi Sahkan Stok Awal — ✅ Selesai
- [x] Dinamisasi indikator menu sidebar: Dot kuning berkedip (*animate-ping*) aktif hanya ketika ada sesi stok awal `pending_verification`, dan otomatis berubah menjadi centang hijau tenang (`✓`) saat stok telah disahkan resmi
- [x] Tombol simulasi reset (`resetSimulation`): Memungkinkan pengguna menguji ulang alur pengesahan stok awal kapan saja untuk keperluan simulasi/demo
- [x] Pengujian otomatis pengesahan dan reset simulasi di `InventoryFeatureTest.php`

### 16. Integrasi Pemotong Gambar Interaktif (Cropper.js) — ✅ Selesai
- [x] Integrasi pustaka client-side Cropper.js dengan modal global (`components/image-cropper-modal.blade.php`)
- [x] Fitur pemotongan foto interaktif dengan kontrol putar (rotate), perbesar (zoom), reset, dan crop rasio spesifik:
  - Rasio 16:9 untuk Titik Lokasi & Lemari Gudang
  - Rasio 4:3 untuk Master Barang & Modul LED
  - Rasio 1:1 (persegi) untuk Foto Avatar Pengguna
- [x] Mekanisme injeksi berkas hasil crop via HTML5 `DataTransfer` sehingga controller backend tetap menerima objek `UploadedFile` standar tanpa modifikasi controller

### 17. Modul Profil Pengguna & Keamanan Kata Sandi (`/profile`) — ✅ Selesai
- [x] Migrasi database: Penambahan kolom `phone` dan `avatar_path` pada tabel `users`
- [x] Model `User`: Konfigurasi `$fillable`, accessor `avatar_url`, dan helper `initials`
- [x] Kontroler [`ProfileController.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/app/Http/Controllers/ProfileController.php) dengan metode `edit`, `update`, dan `updatePassword`
- [x] Tampilan formulir profil di [`profile/edit.blade.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/profile/edit.blade.php) lengkap dengan kartu identitas, upload avatar dengan crop 1:1, dan info hak akses role Spatie
- [x] Formulir mandiri pembaruan kata sandi akun dengan validasi password saat ini dan konfirmasi
- [x] Tautan cepat profil di Topbar navigasi [`layouts/app.blade.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/layouts/app.blade.php) menampilkan avatar/inisial dan role aktif
- [x] Pengujian otomatis komprehensif di `tests/Feature/UserProfileAndAvatarTest.php` (6 tests baru, 29 tests total lulus 100%)

---

## 🔮 Roadmap Jangka Panjang (Operasional Lanjutan Sesuai PRD Global)
- [ ] Integrasi Barcode / QR Code Fisik Scanner (Ditunda sementara sesuai arahan user agar fokus pematangan operasional)
- [ ] Modul Customer & Order (Input order Admin via WhatsApp, Quotation & Revisi)
- [ ] Modul SPK (Surat Perintah Kerja) & Cargo List / Surat Jalan
- [ ] Gate Kesiapan Keberangkatan Event (Barang, Kru, Armada, Dana Operasional)
- [ ] Demobilisasi & Rekonsiliasi Pasca-Event (Deteksi barang hilang/tertinggal di venue)
- [ ] Modul Work Order Servis Internal & Pemeliharaan Modul LED
- [ ] Modul Invoicing & Verifikasi Pembayaran (Finance)
