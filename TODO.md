# 📌 TODO LIST — OMAH VISUAL LED OPERATIONAL & INVENTORY SYSTEM

## 🚀 Sesi Berikutnya: Autentikasi & Antarmuka Lengkap

### 1. Autentikasi & Keamanan (Priority 1)
- [ ] Buat `AuthController` (metode `showLoginForm`, `login`, `logout`, `quickLogin`)
- [ ] Buat view `resources/views/auth/login.blade.php` (tampilan elegan + tombol 1-klik login demo)
- [ ] Pasang middleware `auth` pada seluruh rute dashboard dan inventori
- [ ] Konfigurasi proteksi RBAC Spatie pada menu sidebar (`@can`, `@role`)

### 2. Layout & Shell Dashboard (Priority 1)
- [ ] Buat master layout `resources/views/layouts/app.blade.php`
  - [ ] Sidebar navigasi (Dashboard, Master Barang, Aset Serial, Lokasi, Mutasi, Approval, Audit)
  - [ ] Header / Topbar dengan avatar user, label role, dan tombol logout
  - [ ] Flash message container (Success, Warning, Error alerts)

### 3. Modul Master Data (`/products`)
- [ ] Buat `ProductController` (index, show, create, store, edit, update, toggleStatus)
- [ ] Halaman daftar produk dengan filter kategori, mode pelacakan, dan pencarian
- [ ] Form input produk dengan penanganan:
  - Mode individual vs kuantitas
  - Luas meter persegi per unit ($m^2$) khusus kabinet LED
  - Satuan barang dan kategori hierarkis

### 4. Modul Unit Aset Individual (`/assets`)
- [ ] Buat `AssetController` (index, show, edit)
- [ ] Tampilkan 25 unit processor Novastar (`OVL-PROC-001` s/d `025`), unit TV, dan Toolbox
- [ ] Kartu rincian aset: status lokasi saat ini, kondisi, histori mutasi, dan tombol ganti kondisi (Bagus/Rusak/Servis)

### 5. Modul Titik Lokasi Lemari (`/locations`)
- [ ] Buat `LocationController` (index, show)
- [ ] Tampilan visualisasi isi tiap lemari (Lemari 1, Lemari 2, Lemari 3, Staging LED, Workshop)

### 6. Modul Mutasi Stok (`/movements`)
- [ ] Buat `StockMovementController` (index, create, store, show)
- [ ] Form transaksi mutasi: Inbound, Outbound, Transfer antar lemari
- [ ] Eksekusi transaksi database atomik & pencatatan log mutasi

### 7. Modul Penyesuaian Stok & Approval Owner (`/adjustments`)
- [ ] Buat `StockAdjustmentController` (index, create, store, approve, reject)
- [ ] Form pengajuan koreksi stok fisik oleh tim Warehouse (+ alasan & foto)
- [ ] Tampilan review & tombol **Approval** khusus Owner (posting saldo resmi hanya setelah disetujui)

### 8. Modul Audit Trail (`/audit-logs`)
- [ ] Buat `AuditLogController` (index, show)
- [ ] Tabel log aktivitas pengguna (siapa, aksi apa, entitas apa, nilai lama vs baru, waktu) khusus Owner & Superuser

---

## 🔮 Roadmap Jangka Panjang (Operasional Lanjutan)
- [ ] Modul Order & Booking Event
- [ ] Modul SPK (Surat Perintah Kerja) & Manifest / Surat Jalan
- [ ] Checklist Rekonsiliasi Gudang Pasca-Event (Deteksi barang hilang/tertinggal)
- [ ] Modul Work Order Servis & Pemeliharaan
- [ ] Modul Invoicing & Pembayaran

