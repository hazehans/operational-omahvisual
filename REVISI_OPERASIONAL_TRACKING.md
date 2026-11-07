# 📋 TRACKING & ROADMAP REVISI SISTEM OPERASIONAL OMAH VISUAL LED (CV HAN MEDIA TEKINDO)
> **Tanggal Dibuat:** 7 November 2026  
> **Status Dokumen:** Master Tracking & Execution Guide  
> **Prinsip Utama:** *Strictly Database-Driven* (Semua opsi dropdown, unit, layar, prosesor, dan armada murni ditarik dari database master barang & aset nyata — zero hardcoded dummy).

---

## 🧭 Ringkasan Arsitektur & Tahapan Eksekusi (Sprint Phases)

```
┌────────────────────────────────────────────────────────────────────────┐
│ TAHAP 1: Master Data GA & Customer (Pondasi Data Riil)                 │
│ • Import & mapping data riil GA (Stok Rental LED, Armada, Laptop, TB)  │
│ • Flag is_rental_item (Operasional vs Aset Internal Kantor)            │
│ • Field nominal harga perolehan & rasio ketersediaan (Ready X dari Y)  │
│ • Modul Customer: Sapaan, Kontak Darurat, Self-Register, Foto, Verif   │
└──────────────────────────────────┬─────────────────────────────────────┘
                                   │
                                   ▼
┌────────────────────────────────────────────────────────────────────────┐
│ TAHAP 2: SPK Printout & Checklist Dokumen Lapangan                     │
│ • Redesain Halaman 1 SPK (100% presisi referensi_halaman1_spk.png)     │
│ • QR Code dinamis Share Lokasi venue & ringkasan spesifikasi order     │
│ • Halaman 2-3: Checklist Bawaan (Keluar/Kembali) sesuai spk_mba_de.pdf │
│ • Dokumen Berita Acara Serah Terima (BAST) & Kodefikasi Brand (SG, MV) │
└──────────────────────────────────┬─────────────────────────────────────┘
                                   │
                                   ▼
┌────────────────────────────────────────────────────────────────────────┐
│ TAHAP 3: Dashboard Orders & Kalender Operasional                       │
│ • /orders: Typography compact, metrik operasional harian               │
│ • Sembunyikan No HP di preview awal tabel, jam loading/acara lengkap   │
│ • /calendar: Event pill informatif, Hari Libur Nasional & Tab Highlight│
└──────────────────────────────────┬─────────────────────────────────────┘
                                   │
                                   ▼
┌────────────────────────────────────────────────────────────────────────┐
│ TAHAP 4: Multi-Area Order Input, Kalkulasi Jarak & Routing Teknis      │
│ • /orders/create: Multi-Area screen mapping (Area 1, Area 2, dst)      │
│ • Estimasi jarak KM dari Gudang Omah Visual                            │
│ • Penugasan Kru per jobdesk (fee otomatis) & alur status Armada        │
│ • Visualisasi 3D stage layout & Diagram Kelistrikan (Watt/Amp/Fasa)    │
│ • Sub-bab terpisah untuk sewa multimedia & perlengkapan non-LED        │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 📌 Rincian Checklist Pekerjaan per Modul

### 1. Modul Master Data Barang & Inventaris GA (Ground Zero)
- [x] **1.1 Database Migration & Schema Update**:
  - [x] Tambahkan field `is_rental_item` (boolean, default: true) pada tabel `products` untuk memisahkan barang operasional sewa vs aset internal kantor (kursi, AC, dispenser).
  - [x] Tambahkan field `purchase_price` / `asset_value` (decimal/bigint) untuk mencatat nominal harga aset.
  - [x] Tambahkan relasi dan method kalkulasi ketersediaan: `ready_count` dari `total_count` (contoh UI: *"Ready 10 dari 40 unit"*).
- [x] **1.2 Sinkronisasi Data Riil GA (`references/General_Affair_...`)**:
  - [x] Import Stok Rental LED dari `INVENTORY OMVIS LED 2026.xlsx`:
    - Kabinet SAGA 100x50 (264 cabinet / 132 m²), Kabinet SAGA 50x50 (72 cabinet / 18 m²), Hardcase SAGA.
    - Kabinet LITE 100x50 (54 cabinet / 27 m²), Kabinet LITE 50x50 (36 cabinet / 9 m²), Hardcase LITE.
    - Kabel Power Jumper Waterproof, Kabel Legrand, LAN Jumper.
  - [x] Import Data Armada Kendaraan dari `DATA ASET KENDARAAN_.xlsx`:
    - Isuzu Traga 01 (W 8514 DV), Traga 02 (N 8105 BK), Traga 03 (N 8091 FC).
    - Isuzu Elf (N 8221 BK), Suzuki Carry 01 (N 8052 EU), Suzuki Carry 02 (N 8308 EU).
    - Motor operasional Yamaha Nmax (N 3275 ABG), Honda Grand (N 2077 YA).
  - [x] Import Data Laptop Operasional dari `DATA LAPTOP.xlsx` (Laptop LOQ 1, LOQ 2, dsb).
  - [x] Import Master Toolbox & Operator Kit dari `DATA TOOLS BOX.xlsx` & `NEW 0.1.xlsx`:
    - Toolbox TB-01 s/d TB-05 (13-14 item wajib: tang, obeng, kunci L/Y, cutter, lan checker).
    - Toolkit Operator (Behringer UMC22, HDMI 4K, USB Hub, Splitter Bafo 4CH, HT, Capture Card).
  - [x] Import Aset Internal Non-Rental (`is_rental_item = false`): Aset Ruang Office (PC, Monitor, AC, Loker, Meja Service, Mess Dormitory).
- [x] **1.3 UI Master Barang (`/products`)**:
  - [x] Tampilkan badge status jenis barang: `[Operasional/Rental]` vs `[Aset Internal]`.
  - [x] Tampilkan rasio ketersediaan stok fisik real-time: `Tersedia X / Y`.
  - [x] Filter tab: Semua, Barang Sewa/Operasional, Aset Internal Kantor.

---

### 2. Modul Data Customer (`/customers`)
- [x] **2.1 Database Migration**:
  - [x] Tambahkan kolom pada tabel `customers`:
    - `salutation` (enum/string: `Bapak`, `Ibu`, `Kak`, `Mas`, `Mbak`, `Dek`).
    - `emergency_contact_name` & `emergency_contact_phone` (kontak darurat/cadangan).
    - `photo_path` (foto profil customer/klien).
    - `verification_status` (enum: `pending_verification`, `verified`, default: `verified` jika dibuat admin, `pending` jika self-register).
    - `verified_at` & `verified_by` (audit trail verifikasi pihak perusahaan).
- [x] **2.2 Form Input & Edit Customer**:
  - [x] Letakkan input **Nama Sapaan / Gelar** di posisi paling atas form customer.
  - [x] Tambahkan input Nomor Kontak Cadangan / Emergency.
  - [x] Tambahkan upload foto customer (formal maupun informal).
  - [x] Tampilkan badge verifikasi (`Terverifikasi` / `Menunggu Verifikasi`).
- [x] **2.3 Halaman Publik Registrasi Customer Mandiri (`/register-client`)**:
  - [x] Buat route dan form publik bagi customer untuk input data mandiri + unggah foto.
  - [x] Notifikasi status pending verifikasi ke admin operasional.

---

### 3. Modul Format SPK & Dokumen Lapangan (Printout & Digital)
- [x] **3.1 Halaman 1 SPK (100% Sesuai `referensi_halaman1_spk.png`)**:
  - [x] Header resmi: Logo CV Han Media Tekindo + Judul `SURAT PERINTAH KERJA & IZIN BARANG KELUAR`.
  - [x] No. Transaksi di pojok kiri atas.
  - [x] Kolom Kiri:
    - *Informasi Customer*: Nama Client (dengan sapaan), Alamat, Nama Vendor/Instansi, No. Telp.
    - *Informasi Acara*: Lokasi Acara, Jenis Event, Tgl Loading, Tgl Acara, Jam Loading, Jam Acara.
  - [x] Kolom Kanan:
    - *Penanggung Jawab Lapangan*: Nama PJ, No. Telp PJ.
    - *QR Share Lokasi*: Render QR Code dinamis yang langsung membuka tautan Google Maps venue (dukungan offline vector canvas + dynamic API).
  - [x] Tabel Spesifikasi Order Ringkas:
    - Hanya uraian utama: Total Sqm Yang di-Order & Ukuran LED (format desimal rapi, e.g. 4,5 m²).
    - Bersihkan kolom kabinet, hardcase, dan prosesor dari Halaman 1.
    - Bersihkan detail kendaraan dari Halaman 1.
  - [x] Kotak Keterangan Detail (e.g. catatan level stage, rigging, instalasi khusus).
  - [x] Tabel Team Crew (No, Nama, Jabatan, No. Telp) hingga 10 baris.
  - [x] Bagian Tanda Tangan Ringkas (3 kolom):
    - Penanggung Jawab Barang Keluar (Pink/Red box) | Penanggung Jawab Barang Kembali (Blue box) | Bagian Operasional.
    - Hilangkan teks pernyataan disclaimer hukum yang panjang.
- [x] **3.2 Halaman 2 & 3: Checklist Barang Bawaan (Sesuai `spk_dari_mba_de.pdf`)**:
  - [x] Tabel Checklist Terstruktur dengan kolom: `NO`, `Barang Bawaan KE VENUE`, `Detail / Keterangan`, `QTY`, `Checklist Keluar`, `Checklist Kembali`.
  - [x] Kategori Barang:
    - LED (disamarkan dengan kode aman: `SAGA` $\rightarrow$ `SG`, `MAVIC` $\rightarrow$ `MV`, `LITE` $\rightarrow$ `LT`, `LAMPRO` $\rightarrow$ `LP`).
    - Kabel & Distribusi: Power Corn Jumper (1m & 1.5m checklist per area), Legrand, LAN Jumper, Grounding 50m, Panel Listrik 16A, Kabel FOH, Babok, Kupu-kupu, Baut.
    - Toolbox No: Checklist 14 item wajib (Tang, Obeng, Kunci L/Y, dll).
    - Spare Part LED Wajib: Modul cadangan, Receiving Card, Power Box, PSU.
    - Video Processor: Prosesor 1-3, Kabel AC, Setting, HDMI, DVI, FO 2/4 Core.
    - Rigging & Ground Stake: Besi profil, Besi H, Catwalk, Pemberat jirgen/barbel, Hanging bar, Takel, Webbing, Harness, Tangga teleskopik.
    - Peralatan Tidur & Stage Level: Tenda/kasur lipat/sleeping bag, Ukuran stage, triplek, galon air, panggung rangka.
    - Laptop & Kit Operator: Laptop no, mouse, HDMI 4K, splitter, capture card, HT, soundcard.
    - Tambahan Extra Perkabelan: Converter Legrand, ext jumper, terpal.
  - [x] Checklist Digital interaktif (bisa dicentang langsung via HP/tablet kru di lapangan) serta versi siap print.
- [x] **3.3 Dokumen Berita Acara Serah Terima (BAST)**:
  - [x] Buat template cetak dan digital BAST serah terima hasil pemasangan LED di venue ke pihak klien/vendor lengkap dengan 5 checklist uji kelayakan bersama dan tanda tangan kedua belah pihak.
- [x] **3.4 Halaman Teknis & Routing (4A)**:
  - [x] Diagram pembagian panel listrik 16A dan keseimbangan 3-fasa (Fasa R, S, T).
  - [x] Routing power daisy-chain & kalkulasi beban daya listrik (Watt / Ampere).
  - [x] Routing data LAN & pengkabelan antar kabinet NovaStar.
  - [x] Resolusi & Pixel Mapping layar VJ.
  - [x] Rekap master seluruh area layar dijadikan dalam satu tabel terpadu.

---

### 4. Modul Orders & SPK Dashboard (`/orders`)
- [x] **4.1 Desain & Tipografi**:
  - [x] Perkecil ukuran font tabel agar lebih proporsional, compact (text-xs / 11-12px), dan muat lebih banyak data tanpa scroll berlebih.
  - [x] Sembunyikan nomor HP klien dari kolom tabel preview awal (pindahkan ke tooltip atau halaman detail order).
- [x] **4.2 Highlight Metrik Operasional (Mengikuti Sheet "KASIR LED")**:
  - [x] Ganti metrik omset/finansial murni di dashboard dengan metrik operasional lapangan:
    - *Event Bulan Ini* (Total booking bulan berjalan)
    - *Terkonfirmasi (Siap Jalan)*
    - *Sedang Loading / Live Hari Ini* (Aktif di lapangan)
    - *Event Selesai (Completed)*
- [x] **4.3 Filter & Kolom Tabel Transaksi**:
  - [x] Filter multi-kriteria: Filter bulan, tahun, status pengerjaan, dan filter instalasi (LED Gantung vs Ground Stack).
  - [x] Quick Filter Pills: `Semua`, `Bulan Ini`, `Terkonfirmasi`, `Sedang Loading`, `Selesai`, `🎯 LED Gantung`.
  - [x] Kolom jadwal lengkap: Tanggal Loading & Jam Loading, Tanggal Acara & Jam Acara.
  - [x] Badge penanda khusus untuk event dengan *LED Gantung / Rigging*.

---

### 5. Modul Kalender Jadwal (`/calendar`)
- [x] **5.1 Tampilan Event Pill Interaktif**:
  - [x] Tampilkan informasi lengkap di pill tanggal kalender:
    - Nama Klien (dengan sapaan)
    - Nama Event & Lokasi Venue
    - Luas Layar (Sqm)
    - Jam Loading-In
    - Indikator badge `🎯 Gantung/Rigging`
  - [x] Modal klik event: "Click for more detail" menampilkan ringkasan cepat kru PIC, armada mobil, jadwal loading/acara, link WA reminder, dan link ke SPK.
- [x] **5.2 Tab Highlight Monitoring (Mengikuti Tab Kasir LED)**:
  - [x] Tambahkan tab navigasi di atas kalender:
    - `[📅 Kalender Bulanan]`
    - `[⚡ Highlight: Project Berjalan / Live]`
    - `[🗓️ Highlight: Project Kedepannya]`
    - `[✅ Highlight: Project Selesai & Termin]`
  - [x] Tampilan tabel terstruktur pada masing-masing tab highlight monitoring.
- [x] **5.3 Integrasi Hari Libur Nasional & Tanggal Merah Indonesia**:
  - [x] Implementasi helper resmi `App\Support\IndonesianHolidays` (SKB 3 Menteri).
  - [x] Penanda tanggal merah pada hari Minggu dan hari libur nasional resmi dengan badge merah `🚩 [Nama Hari Libur Nasional]`.

---

### 6. Modul Form Input Order (`/orders/create`)
- [ ] **6.1 Pembersihan Field**:
  - [ ] Hapus field manual: Kabupaten/Kota, Provinsi Acara, dan Status Wilayah.
- [ ] **6.2 Fitur Estimasi Jarak Rute Gudang**:
  - [ ] Tambahkan kalkulasi estimasi jarak (KM) otomatis dari koordinat Gudang Omah Visual ke lokasi venue acara.
- [ ] **6.3 Jam Acara**:
  - [ ] Tambahkan input Jam Acara (Mulai s/d Selesai).
- [ ] **6.4 Spesifikasi Layar Multi-Area (Multi-Screen Mapping)**:
  - [ ] Struktur input layar dinamis per area:
    - Area 1 (e.g. Main Screen): Ukuran P x L, Jenis Layar (dropdown database), Video Processor (dropdown database).
    - Tombol `[+ Tambah Area Layar]` $\rightarrow$ Area 2 (e.g. Wings Kiri/Kanan), Area 3 (Delay / Stage Belakang), dst.
  - [ ] Tabel rekapitulasi otomatis seluruh area layar digabung menjadi satu ringkasan total.
- [ ] **6.5 Visualisasi Panggung 3D & Formasi Layar**:
  - [ ] Kanvas interaktif penataan layar panggung (Drag & Drop kabinet).
  - [ ] Fitur rotasi layar (horizontal/vertikal) dan custom formasi panggung non-standar (misal festival horeg / lengkung).
- [ ] **6.6 Visualisasi Jalur Kelistrikan & Data (Inspirasi *led.fyi*)**:
  - [ ] Visualisasi pembagian jalur panel listrik (Panel 1, Panel 2, dst).
  - [ ] Kalkulasi pembagian fasa beban R, S, T serta estimasi konsumsi watt dan ampere.
- [ ] **6.7 Penugasan Kru & Estimasi Fee Lapangan**:
  - [ ] Pembagian slot kru: Kru Loading-In, Kru Standby, Kru Loading-Out, PIC Lapangan, dan Operator.
  - [ ] Kalkulasi otomatis honor/fee kru berdasarkan jobdesk (mengikuti ketentuan sheet `NEW ADMIN UPDATE`).
- [ ] **6.8 Alur Penugasan Armada / Kendaraan**:
  - [ ] Opsi kendaraan: Mobil Loading-In, Mobil Loading-Out, Mobil Standby.
  - [ ] Logika status otomatis armada:
    `if (loading_in && loading_out == standby) -> Standby di Venue; else -> Kembali ke Gudang`.
- [ ] **6.9 Pemisahan Pesanan Tambahan Multimedia**:
  - [ ] Sub-form terpisah untuk item multimedia tambahan di luar LED (sewa alat capture, TV pendukung, soundcard, rental kendaraan luar).

---

### 7. Finansial & Pricing
- [ ] **7.1 Pembersihan Field Usang**:
  - [ ] Hapus field TV statis yang sudah tidak relevan.
- [ ] **7.2 Penetapan Tarif & Breakdown Harga**:
  - [ ] Harga per sqm / harga per unit pada masing-masing tipe LED di database master.
  - [ ] Kalkulasi otomatis total biaya sewa untuk integrasi laporan transparan ke bagian Finance.
  - [ ] Format export/import dataset tagihan keuangan.

---

## 🚦 Status Pengerjaan (Live Progress Tracker)

| No | Modul / Pekerjaan | Status | Catatan Validasi |
|:---|:---|:---:|:---|
| **1** | Master Data GA & Klasifikasi Barang Rental vs Internal | ✅ *Selesai* | 101 item rental + 69 aset internal + 8 armada tersinkron |
| **2** | Modul Customer (Gelar, Emergency Contact, Foto, Verifikasi) | ✅ *Selesai* | Sapaan di atas UI, Foto profil, Self-Register publik, Verifikasi company |
| **3** | SPK Halaman 1 (`referensi_halaman1_spk.png`), Checklist 2-3, 4A Routing & BAST | ✅ *Selesai* | 100% presisi Hal 1, Checklist per area, Brand obfuscation, 4A Routing 3-Fasa, BAST resmi |
| **4** | Dashboard `/orders` (Tipografi, Metrik Operasional, Filter) | ✅ *Selesai* | Font compact (text-xs), metrik operasional (omset dihapus), No HP disembunyikan di preview, multi-filter bulan/status/gantung |
| **5** | Kalender `/calendar` (Event Pill, Libur Nasional, Tab Monitoring)| ✅ *Selesai* | Info pill lengkap (klien/venue/sqm/loading), libur nasional resmi SKB 3 Menteri, 4 tab highlight monitoring Kasir LED |
| **6** | Form `/orders/create` (Multi-Area, Rute KM, Kelistrikan led.fyi) | ⏳ *Target Tahap 4* | Menunggu Sprint Tahap 4 |
| **7** | Finansial & Pricing Otomatis | ⏳ *Target Tahap 4* | Menunggu Sprint Tahap 4 |

---

*Dokumen ini diperbarui secara berkala setiap kali ada implementasi kode atau perubahan kesepakatan fitur.*
