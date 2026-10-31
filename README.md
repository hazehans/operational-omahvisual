# 🎬 Omah Visual LED — Operational & Inventory System
> **Sistem Manajemen Operasional, Rental Videotron, dan Inventori Terpadu**  
> Dikembangkan untuk **CV Han Media Tekindo (Omah Visual LED)**

---

## 📌 1. Gambaran Umum Sistem

Sistem ini merupakan aplikasi web operasional terintegrasi (*Modular Monolith*) yang dirancang khusus untuk menangani proses bisnis rental LED videotron, peralatan broadcast, armada visual, perlengkapan panggung, hingga pelacakan inventori gudang secara presisi.

Aplikasi ini mengintegrasikan data fisik riil dari spreadsheet inventori lapangan ke dalam database terstruktur dengan kalkulasi otomatis luasan meter persegi ($m^2$), status kesiapan sewa, mutasi perpindahan barang, approval selisih stok berjenjang, SPK event, penggajian kru, hingga audit trail keamanan menyeluruh.

📖 **Dokumen SOP & Panduan Lengkap:**  
Tersedia panduan operasional terperinci di: [`docs/SOP_DAN_PANDUAN_PENGGUNAAN_SISTEM.md`](docs/SOP_DAN_PANDUAN_PENGGUNAAN_SISTEM.md) dan halaman interaktif web di menu **[Buku Panduan](/guide)**.

---

## ✨ 2. Fitur Utama & Modul Sistem

### 🎯 A. Manajemen Inventori & Master Barang (`/products`)
1. **Katalog Master Barang & Full CRUD Aman:**
   * Registrasi SKU produk, nama, kategori, unit, brand, seri model, spesifikasi, luasan $\text{m}^2$/unit, dan foto produk dengan cropper terintegrasi.
   * Tombol Hapus Aman: Produk yang belum ada transaksi terhapus permanen; produk yang sudah memiliki riwayat operasional otomatis dialihkan statusnya menjadi **Nonaktif** agar integritas pembukuan lama tidak rusak.
2. **Laporan Ketersediaan & Kesiapan Armada Bulanan:**
   * **Export CSV (Excel Compatible):** Sekali klik mengunduh rekap ketersediaan seluruh aset ber-encoding UTF-8 BOM, kompatibel langsung dengan Microsoft Excel / Google Sheets.
   * **Cetak Dokumen Resmi A4 / PDF (`/products/print/report`):** Lembar ketersediaan A4 ber-kop surat resmi CV Han Media Tekindo, Executive Summary Box (Total $\text{m}^2$ Ready, Rusak, Servis, Indeks Kesiapan %), dan kolom tanda tangan 3 pihak (Kepala Gudang, Manager Ops, Direktur/Owner).
3. **Dual Tracking Mode (Individual vs Kuantitas):**
   * **Unit Serial Individual (`assets`):** Melacak 25 Video Processor Novastar (`VX20M`, `VX10`, `VX8M`, dll), armada Smart TV, dan 10 Toolbox Teknisi (`TB-01` s/d `TB-10`).
   * **Kuantitas Stok (`stock_balances`):** Melacak material massal seperti kabel jumper LAN, kabel power Neutrik, kabel roll FOH, dan 800+ baut rigging.
4. **Kalkulasi Otomatis Luasan LED ($m^2$):**
   * Kabinet $100 \times 50\text{ cm} = 0.50\,m^2$/unit.
   * Kabinet $50 \times 50\text{ cm} = 0.25\,m^2$/unit.
5. **Pemisahan Kondisi Fisik Riil:**
   * Status terpisah: `available` (siap sewa), `damaged` (rusak), dan `in_service` (dalam servis). Barang rusak/servis otomatis dikeluarkan dari kuota siap sewa.

---

### 🔒 B. Kontrol Internal & SOP Penyesuaian Stok (`/adjustments`)
> [!IMPORTANT]
> **Kebijakan Anti-Fraud:** Tim Gudang tidak diizinkan mengubah stok secara langsung dari Master Barang. Setiap penyesuaian selisih fisik wajib melewati alur approval berjenjang.

1. **Pengajuan oleh Tim Gudang (`/adjustments/create`):**
   * Tim Gudang memasukkan lokasi, barang, kondisi, dan jumlah fisik hasil temuan riil disertai alasan dan upload foto bukti fisik.
2. **Peninjauan & ACC oleh Owner (`/adjustments`):**
   * Saldo resmi sistem hanya diperbarui setelah disetujui (*Approved*) oleh Owner / Direktur.
   * Jika disetujui, saldo fisik terupdate seketika, nomor mutasi koreksi tercatat, dan log tersimpan permanen di *Audit Trail*.

---

### 📅 C. Kalender Jadwal Operasional & Kru (`/calendar`)
* Tampilan visual kalender bulanan interaktif untuk memetakan penugasan event persewaan videotron.
* **Deteksi Bentrok & Overload Otomatis:** Badge indikator warna merah/amber jika dalam 1 hari terdapat lebih dari satu event atau total volume layar melebihi 100 $\text{m}^2$.
* Filter status event (Confirmed, Loading, Live, Completed, Cancelled).
* Modal popup detail event lengkap dengan list kru yang bertugas dan armada logistik yang digunakan.

---

### 💬 D. Integrasi 1-Click WhatsApp Reminder & Dompet Kru
1. **WhatsApp Reminder Jadwal Kru (`/spk/{spk}` & `/calendar`):**
   * Tombol "⏰ Reminder" langsung membuka WhatsApp Web/Mobile dengan template pesan rapi berisi nama event, venue, jam loading, standby, dresscode, dan peran penugasan.
2. **Dompet & Histori Saldo Honor Kru (`/crews/{crew}`):**
   * Monitoring saldo honor kru freelance (Belum Cair vs Sudah Lunas, plus Lifetime Earnings).
   * Tombol 1-klik WhatsApp ajukan pencairan honor ke bagian Finance dengan rincian event dan rekening yang terisi otomatis.

---

### 📋 E. Siklus SPK, Pemuatan Gudang & QC Panggung
1. **Surat Perintah Kerja (SPK):** Plotting Leader, FOH, Teknisi Listrik, Crew Rigging, dan Armada (GranMax/Traga).
2. **Checklist Muat Barang (Loading Out):** Verifikasi barang keluar gudang dan unit serial processor.
3. **Uji Kelayakan QC Panggung (BAST):** Standar grounding kelistrikan ($< 2\text{V}$), uji pola warna RGB (Red, Green, Blue, White bebas dead pixel), sinyal input Novastar, dan upload foto panggung.

---

### 🛠️ F. Modul Servis & Bengkel Reparasi Modul (`/service-tickets`)
* Tiket servis untuk modul LED yang mengalami dead pixel, IC korslet, atau fisik pecah pasca-event.
* Pencatatan diagnosa teknisi, alokasi sparepart dari Lemari 1, dan uji ketahanan 24 jam sebelum dikembalikan ke stok siap sewa.

---

### 💰 G. Invoicing, Penagihan & Kuitansi Resmi (`/invoices`)
* Faktur tagihan standar A4 dengan kop surat resmi CV Han Media Tekindo.
* Dukungan pembayaran bertahap (Down Payment / DP dan Pelunasan).
* Kuitansi pembayaran resmi dengan stempel watermark digital (*LUNAS* / *DP DITERIMA*).
* 1-Klik bagikan rincian tagihan dan nomor rekening transfer BCA ke WhatsApp klien.

---

### 🚀 H. Portal Tracking Mandiri Customer & EO (`/track/{token}`)
* Akses mandiri untuk EO/Klien tanpa login menggunakan token unik 32-karakter.
* Live 5-Stage Stepper: *Terkonfirmasi* $\rightarrow$ *Loading Gudang* $\rightarrow$ *Perjalanan* $\rightarrow$ *Instalasi & QC Lolos* $\rightarrow$ *Event Selesai*.
* Panduan teknis resolusi piksel native ($W \times H\text{ px}$) dan aspek rasio otomatis untuk acuan tim multimedia/VJ.

---

### 🛡️ I. Superuser Database Manager (`/superuser/database`)
* Panel low-level CRUD setara Django Admin untuk 16 tabel database utama (khusus role `Superuser`).
* Pencarian teks/ID, paginasi, editing data cepat, dan pencatatan audit log otomatis.

---

## 💻 3. Tech Stack & Spesifikasi Sistem

* **Backend Framework:** Laravel 13 (PHP 8.5)
* **Database Engine:** PostgreSQL 18 (Local database: `inventory_db`)
* **Frontend:** Laravel Blade, Tailwind CSS v4, Alpine.js, Cropper.js, Vite
* **Autentikasi & RBAC:** Session Guard + `spatie/laravel-permission` v8.3
* **Code Formatter:** Laravel Pint
* **Automated Testing:** PHPUnit (141 tests, 880+ assertions, 100% Green)

---

## 🚀 4. Panduan Menjalankan Aplikasi

### Metode 1: Menggunakan Launcher Batch (Paling Praktis)
Cukup klik dua kali (*double-click*) file berikut di direktori utama:
```bat
nyalakan-inventory.bat
```
*Script otomatis menyalakan server lokal dan membuka browser ke `http://127.0.0.1:8000`.*

### Metode 2: Menggunakan Terminal Manual
1. Pastikan PostgreSQL berjalan dan database `inventory_db` tersedia.
2. Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```
3. Hubungkan symlink storage publik:
   ```bash
   php artisan storage:link
   ```
4. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
5. Buka browser pada alamat: **`http://127.0.0.1:8000`**

---

## 👥 5. Akun Pengguna Demo (Testing)

Semua akun menggunakan password standar: **`password123`**.

| Role / Peran | Alamat Email | Hak Akses Utama |
| :--- | :--- | :--- |
| **Owner (Hans)** | `owner@omahvisual.com` | Akses penuh dashboard, approval penyesuaian stok, payroll |
| **Manager Ops** | `manager.ops@omahvisual.com` | Penerbitan SPK, assign kru/armada, approval QC panggung |
| **Admin LED** | `admin@omahvisual.com` | Input order, kalkulator LED, invoicing & kuitansi klien |
| **Warehouse** | `warehouse@omahvisual.com` | Pemuatan barang, pengajuan koreksi stok, mutasi gudang |
| **Kepala Servis** | `kepala.servis@omahvisual.com` | Tiket servis modul LED, alokasi sparepart bengkel |
| **Finance** | `finance@omahvisual.com` | Verifikasi transfer invoice klien, pencairan honor kru |
| **Superuser** | `super@omahvisual.com` / `superuser@omahvisual.com` | Akses teknis penuh, Database Manager raw CRUD |

---

## 🧪 6. Menjalankan Pengujian Otomatis

Untuk memastikan seluruh alur bisnis, hak akses, dan integritas database valid:

```bash
# Jalankan seluruh test suite
php artisan test --compact

# Jalankan pengujian Master Barang & Laporan Ketersediaan
php artisan test --compact --filter=ProductCrudAndAvailabilityReportTest
```

---

*Hak Cipta © CV Han Media Tekindo — Omah Visual LED. Seluruh hak cipta dilindungi.*
