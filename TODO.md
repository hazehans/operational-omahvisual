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

---

## 🔮 Roadmap Jangka Panjang (Operasional Lanjutan Sesuai PRD Global)
- [ ] Modul Customer & Order (Input order Admin via WhatsApp, Quotation & Revisi)
- [ ] Modul SPK (Surat Perintah Kerja) & Cargo List / Surat Jalan
- [ ] Gate Kesiapan Keberangkatan Event (Barang, Kru, Armada, Dana Operasional)
- [ ] Demobilisasi & Rekonsiliasi Pasca-Event (Deteksi barang hilang/tertinggal di venue)
- [ ] Modul Work Order Servis Internal & Pemeliharaan Modul LED
- [ ] Modul Invoicing & Verifikasi Pembayaran (Finance)
