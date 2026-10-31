# 📘 STANDAR OPERASIONAL PROSEDUR (SOP) & BUKU PANDUAN SISTEM
## CV HAN MEDIA TEKINDO — OMAH VISUAL LED
*Dokumen Resmi Tata Kelola Operasional, Inventaris, Rental Videotron, dan Panduan Penggunaan Web*

---

## 📑 DAFTAR ISI
1. [Profil Perusahaan & Arsitektur Sistem](#1-profil-perusahaan--arsitektur-sistem)
2. [Matriks Peran, Wewenang & Tanggung Jawab](#2-matriks-peran-wewenang--tanggung-jawab)
3. [Alur Bisnis Inti (10 Langkah End-to-End)](#3-alur-bisnis-inti-10-langkah-end-to-end)
4. [SOP Khusus Penyesuaian Stok & Opname Fisik (Warehouse & Owner Approval)](#4-sop-khusus-penyesuaian-stok--opname-fisik-warehouse--owner-approval)
5. [Panduan Penggunaan Aplikasi Web Langkah Demi Langkah](#5-panduan-penggunaan-aplikasi-web-langkah-demi-langkah)
6. [Format Laporan Bulanan & Analisis Ketersediaan Aset](#6-format-laporan-bulanan--analisis-ketersediaan-aset)
7. [Akun Pengguna Default & Kredensial Pengujian](#7-akun-pengguna-default--kredensial-pengujian)

---

## 🏢 1. Profil Perusahaan & Arsitektur Sistem

* **Nama Perusahaan:** CV Han Media Tekindo
* **Brand Komersial:** Omah Visual LED
* **Kantor Operasional & Workshop:** Jl. Raya Tlogomas No. 12, Kota Malang, Jawa Timur
* **Layanan Inti:** Persewaan Videotron P3.9 Outdoor, P3.9 Indoor, P2.6 High-Res, Novastar Video Processor, Rigging H Aluminium, dan Produksi Visual Panggung Konser/Event.

Sistem Web Operasional Omah Visual mengintegrasikan seluruh lini bisnis: kalkulasi teknis modul LED, plotting kru & armada logistik, verifikasi checklist barang keluar/masuk gudang, pengujian kelayakan instalasi panggung (QC), reparasi teknis modul di bengkel servis, penagihan invoice & kuitansi, rekap dompet honor kru, hingga audit trail keamanan data.

---

## 👥 2. Matriks Peran, Wewenang & Tanggung Jawab

| Peran (Role) | Kewenangan Utama di Sistem | Tanggung Jawab Lapangan |
| :--- | :--- | :--- |
| **Owner / Direktur** | Akses Penuh, Approval Penyesuaian Stok, Approval Tiket Servis, Monitoring Keuangan & Payroll | Pengambil keputusan strategis, pengesahan aset modal, dan penanggung jawab tertinggi perusahaan |
| **Manager Operasional** | Penerbitan SPK, Plotting Kru & Armada, Approval Hasil QC Lapangan, Akses Kalender Operasional | Pengawasan jalannya acara, kesiapan kru lapangan, ketepatan waktu armada, dan koordinasi dengan EO |
| **Admin LED** | Input Pesanan Event, Simulasi Kalkulator LED, Penerbitan Invoice Klien, Manajemen Data Klien | Komunikasi awal dengan EO/Klien, negosiasi harga, perhitungan kebutuhan kabinet & resolusi layar |
| **Tim Warehouse (Gudang)** | Pemuatan Barang (Loading Out), Audit Kepulangan (Loading In), Pengajuan Koreksi Stok, Penataan Lemari | Kesiapan fisik unit di staging, penghitungan fisik saat loading, pelaporan kehilangan/kerusakan modul |
| **Teknisi Servis & Workshop** | Investigasi Tiket Servis, Diagnosa Kerusakan, Pengambilan Sparepart Lemari, Uji QC 24 Jam | Perbaikan solder IC modul, penggantian power supply/receiving card, kalibrasi warna panel LED |
| **Finance (Keuangan)** | Verifikasi Pembayaran Masuk, Penerbitan Kuitansi Resmi, Pencairan Honor Kru Freelance | Manajemen arus kas, penagihan piutang customer, rekonsiliasi transfer BCA, dan transfer gaji kru |
| **Superuser** | Panel Manajemen Database Mentah (`/superuser/database`), Akses Konfigurasi Sistem Penuh | Pemeliharaan sistem, backup database, audit log forensik, dan troubleshooting developer |

---

## 🔄 3. Alur Bisnis Inti (10 Langkah End-to-End)

```
[1. Deal Klien] ➔ [2. Input Order & Kalkulator] ➔ [3. Terbitkan SPK & Kru] ➔ [4. Loading Out Gudang]
       ➔ [5. Instalasi & QC Panggung] ➔ [6. Acara Berlangsung] ➔ [7. Loading In & Audit Fisik]
       ➔ [8. Reparasi Bengkel Servis] ➔ [9. Pelunasan Invoice] ➔ [10. Payroll & Honor Kru]
```

### Langkah 1: Permintaan Sewa & Kesepakatan (Deal)
1. Klien menghubungi Admin LED membawa spesifikasi acara (venue indoor/outdoor, tanggal acara, ukuran panggung).
2. Admin mengecek ketersediaan modul pada **Kalender Jadwal Operasional** (`/calendar`) untuk memastikan tidak terjadi bentrok jadwal atau overload kuota luasan LED.

### Langkah 2: Input Pesanan & Kalkulator Layar LED
1. Buka menu **Pesanan Event** ➔ **Buat Pesanan Baru** (`/orders/create`).
2. Masukkan data venue, tanggal loading, jam standby, dan tambahkan layar LED:
   * Masukkan ukuran panjang & tinggi (misal: $8 \times 3$ meter).
   * Kalkulator sistem otomatis menghitung: jumlah kabinet $100 \times 50\text{ cm}$ dan $50 \times 50\text{ cm}$, total $\text{m}^2$, estimasi konsumsi listrik (Watt/Ampere), serta simulasi resolusi native piksel Novastar untuk acuan tim multimedia/VJ.

### Langkah 3: Penerbitan SPK, Penugasan Kru & Armada
1. Manager Operasional membuka pesanan berstatus *Confirmed* lalu menerbitkan Surat Perintah Kerja (SPK).
2. Tentukan Ketua Tim (Leader), Teknisi FOH Novastar, Teknisi Power Listrik, dan Kru Rigging.
3. Kirim rincian jadwal via WhatsApp otomatis dengan tombol **"⏰ Kirim Reminder WA"**.
4. Plot armada logistik (GranMax / Traga) lengkap dengan supir dan kesiapan kartu e-Toll.

### Langkah 4: Persiapan & Pemuatan Gudang (Loading Out)
1. Tim Gudang mencetak lembar checklist muat barang SPK.
2. Ambil flightcase kabinet dari area Staging LED (`GU-STG-LED`), kabel jumper dari Lemari 2 (`GU-ALM-2`), dan Toolbox teknisi dari Area Workshop (`GU-WS-TB`).
3. Scan atau periksa unit serial Novastar Processor.
4. Supir dan Kepala Gudang menandatangani Berita Acara Muat Barang sebelum truk berangkat.

### Langkah 5: Instalasi Venue & Pengujian Kelayakan (QC Panggung)
1. Tim tiba di lokasi, mendirikan konstruksi rigging H, memasang hanging bar, dan merakit kabinet LED.
2. Teknisi wajib melakukan pengetesan keamanan:
   * Cek grounding kelistrikan panggung (maksimal $< 2\text{ Volt}$).
   * Uji pola warna solid RGB (Red, Green, Blue, White) untuk memastikan tidak ada dead pixel / modul belang.
   * Cek kestabilan sinyal input video HDMI/SDI dari mixer FOH ke processor Novastar.
3. Leader mengunggah foto dokumentasi bukti instalasi panggung ke sistem via halaman SPK live.
4. Klien dapat memantau status panggung secara langsung via **Portal Tracking Mandiri Customer** (`/track/{token}`).

### Langkah 6: Acara Berlangsung (Showtime Standby)
1. Teknisi FOH standby di ruang kontrol selama acara berlangsung.
2. Selalu siapkan flightcase *cold-spare* (modul cadangan, kabel jumper serep, receiving card cadangan) di backstage untuk mitigasi darurat kendala teknis.

### Langkah 7: Pembongkaran (Loading In) & Audit Fisik Kepulangan
1. Setelah acara selesai, kru membongkar kabinet dengan hati-hati menggunakan sarung tangan kain untuk mencegah sudut modul gompal.
2. Masukkan kembali perangkat ke flightcase masing-masing.
3. Begitu tiba di gudang utama, Tim Gudang melakukan pemeriksaan fisik teliti:
   * Unit bagus dikembalikan ke saldo Staging Siap Sewa.
   * Unit modul yang mengalami dead pixel, IC korslet, atau fisik pecah wajib dipisahkan dan langsung diterbitkan **Tiket Servis Bengkel** (`/service-tickets/create`).
   * Barang hilang (misal baut rigging, kabel Neutrik) dicatat pada Berita Acara Selisih.

### Langkah 8: Penanganan Servis & Bengkel Modul
1. Kepala Servis memeriksa antrean tiket perbaikan di `/service-tickets`.
2. Teknisi mendiagnosa kerusakan, mengambil komponen cadangan dari Lemari 1 (`GU-ALM-1`), menyolder IC driver, dan melakukan pengetesan kestabilan modul minimal 12–24 jam di meja uji bengkel.
3. Modul yang lolos uji dikembalikan statusnya ke *Available* di gudang.

### Langkah 9: Penagihan Invoice & Pelunasan Customer
1. Bagian Finance menerbitkan Invoice Tagihan (`/invoices/create`) berdasarkan pesanan yang selesai.
2. Kirim pesan tagihan berformat resmi ke WhatsApp klien menggunakan tombol 1-klik share.
3. Catat pembayaran DP dan Pelunasan di sistem, lampirkan bukti transfer, dan cetak Kuitansi Resmi bertanda tangan digital/stempel lunas.

### Langkah 10: Penggajian & Pencairan Honor Kru (Payroll)
1. Finance membuka rekap honor di menu **Payroll Kru** (`/crew-payroll`).
2. Sistem secara presisi menghitung honor loading berangkat, honor standby event, honor pembongkaran, plus uang makan/bensin.
3. Kru dapat memeriksa sisa saldo dompet honor mereka di profil kru masing-masing (`/crews/{crew}`) dan mengajukan penarikan via WhatsApp ke Finance.
4. Cetak Slip Honor A4 resmi bertanda tangan sebagai bukti serah terima kas.

---

## 🔒 4. SOP Khusus Penyesuaian Stok & Opname Fisik (Warehouse & Owner Approval)

> [!IMPORTANT]
> **Kebijakan Kontrol Internal (Anti-Fraud Policy):**  
> Staf Gudang **TIDAK DIIZINKAN** mengubah atau mengedit angka saldo stok fisik secara langsung dari Master Barang. Setiap penambahan atau pengurangan saldo fisik berjalan wajib melalui alur audit berjenjang: **Pengajuan Gudang ➔ Peninjauan & ACC Owner**.

### Alur Baku Rekonsiliasi / Koreksi Selisih Stok:
1. **Pemeriksaan Lapangan:**
   * Dilakukan saat Stock Opname bulanan atau saat temuan barang rusak/hilang pasca-event.
2. **Pengajuan oleh Tim Warehouse:**
   * Tim Gudang membuka menu **Inventaris** ➔ **Penyesuaian Stok** (`/adjustments`).
   * Klik tombol **"Ajukan Penyesuaian"** (`/adjustments/create`).
   * Pilih Lokasi Gudang, Nama Barang, Kondisi Fisik (*Bagus/Rusak/Servis*), dan masukkan **Jumlah Fisik Hasil Temuan Sebenarnya**.
   * Wajib mengisi **Alasan Selisih** (misal: *"Modul tertimpa rigging saat loading panggung di venue outdoor"*) dan mengunggah **Foto Bukti Fisik**.
   * Status pengajuan menjadi `pending_approval`. Pada tahap ini, saldo resmi di sistem **BELUM BERUBAH**.
3. **Peninjauan & Keputusan oleh Owner:**
   * Owner menerima notifikasi dan membuka daftar pengajuan di `/adjustments`.
   * Owner memeriksa alasan, foto bukti fisik, dan histori log mutasi barang.
   * **Jika Disetujui (Approve):** Seketika saldo stok resmi di `stock_balances` diperbarui ke angka fisik baru, histori mutasi tipe *Adjustment* resmi tercatat, dan seluruh aksi tersimpan permanen di *Audit Trail*.
   * **Jika Ditolak (Reject):** Owner mengisi catatan penolakan, saldo sistem tidak berubah, dan tim gudang wajib melakukan kroscek ulang di lapangan.

---

## 🖥️ 5. Panduan Penggunaan Aplikasi Web Langkah Demi Langkah

### A. Navigasi & Tampilan Utama
* **Dashboard Terpadu (`/dashboard`):** Menampilkan metrik utama omzet, luasan LED tersewa, radar kondisi operasional 4 divisi (Ops, Finance, Warehouse, Servis), serta feed *Live Activity & Audit Stream*.
* **Buku Panduan Interaktif (`/guide`):** Pusat edukasi alur 10 langkah, panduan peran, dan contekan akun simulasi.
* **Kalender Operasional (`/calendar`):** Memetakan jadwal sewa bulanan, deteksi clash/konflik multi-event di hari yang sama, dan modal detail SPK & kru.

### B. Pengelolaan Master Barang (`/products`)
1. **Melihat Katalog & KPI Kesiapan:**
   * Buka menu **Master Barang** (`/products`).
   * Di bagian atas terdapat 3 kartu KPI: Total Katalog SKU terdaftar, SKU aktif beroperasi, dan Total Kapasitas Layar Siap Sewa ($\text{m}^2$).
2. **Menambah Master Barang Baru:**
   * Klik tombol **"+ Tambah Barang Baru"** (`/products/create`).
   * Isi SKU/Kode, Nama Barang, Kategori, Satuan, Brand, Seri Model, Spesifikasi Teknis, Luas $\text{m}^2$/unit (khusus kabinet), dan foto produk proporsional.
   * Klik **Simpan Master Barang**.
3. **Mengedit Data Barang:**
   * Klik ikon pensil pada tabel barang (`/products/{id}/edit`). Ubah data atau nonaktifkan barang jika sudah tidak disewakan.
4. **Menghapus Master Barang:**
   * Klik tombol tempat sampah merah pada baris tabel barang.
   * Sistem dilengkapi proteksi cerdas: jika barang belum pernah dipakai transaksi, data terhapus permanen. Jika barang sudah memiliki riwayat event/servis, sistem otomatis mengalihkan statusnya menjadi **Nonaktif** demi menjaga keutuhan riwayat pembukuan lama.

---

## 📊 6. Format Laporan Bulanan & Analisis Ketersediaan Aset

Untuk kebutuhan rapat koordinasi dan evaluasi manajemen bulanan bersama direksi/owner, sistem menyediakan dua metode ekspor data ketersediaan aset langsung dari halaman **Master Barang (`/products`)**:

### 1. Ekspor Data Mentah CSV / Excel (`/products/export/csv`)
* Klik tombol **"Export CSV (Excel)"** di pojok kanan atas tabel Master Barang.
* File otomatis terunduh dengan format UTF-8 BOM, sehingga langsung terbuka rapi di Microsoft Excel atau Google Sheets tanpa masalah format teks/koma.
* Memuat rincian: *SKU, Nama Barang, Kategori, Brand, Mode Pelacakan, Luas per Unit, Stok Ready, Stok Servis, Stok Rusak, Total Fisik, Total $\text{m}^2$ Siap Sewa, Satuan, dan Status*.

### 2. Cetak Dokumen Resmi Kesiapan Aset A4 / PDF (`/products/print/report`)
* Klik tombol **"Cetak Laporan Ketersediaan"** (`/products/print/report`).
* Menampilkan lembar formal berstandar A4 yang memuat:
  * **Kop Surat Resmi:** CV Han Media Tekindo — Omah Visual LED.
  * **Executive Summary Box:** Total SKU terdata, Total Luas Layar Siap Sewa ($\text{m}^2$), Luas Dalam Servis ($\text{m}^2$), dan **Indeks Kesiapan Armada (%)**.
  * **Tabel Komparasi Kesiapan:** Kuantitas siap pakai vs rusak vs servis per kategori barang.
  * **Lembar Pengesahan 3 Pihak:** Kolom tanda tangan resmi untuk *Kepala Gudang*, *Manager Operasional*, dan *Direktur Utama (Hans)*.
* Tekan tombol **"Cetak / Simpan PDF (A4)"** atau gunakan shortcut browser `Ctrl + P` untuk menyimpan sebagai file PDF atau mencetak langsung ke printer.

---

## 🔑 7. Akun Pengguna Default & Kredensial Pengujian

Seluruh akun simulasi di bawah ini menggunakan password standar yang sama: **`password123`**.

| Nama Akun | Role Sistem | Alamat Email | Fungsi Pengujian Utama |
| :--- | :--- | :--- | :--- |
| **Superuser Administrator** | `Superuser` | `superuser@omahvisual.com` | Akses penuh seluruh modul & manipulasi raw database |
| **Hans (Direktur Utama)** | `Owner` | `owner@omahvisual.com` | Approval koreksi stok, approval servis, monitoring keuangan |
| **Bambang Prakoso** | `Manager Operasional` | `manager.ops@omahvisual.com` | Terbitkan SPK, assign kru/armada, approval QC panggung |
| **Dewi Sartika** | `Admin LED` | `admin@omahvisual.com` | Input pesanan, kalkulator LED, generate invoice klien |
| **Rudi Hartono** | `Warehouse` | `warehouse@omahvisual.com` | Checklist loading barang, pengajuan penyesuaian stok |
| **Agus Santoso** | `Kepala Servis` | `kepala.servis@omahvisual.com` | Diagnosa modul rusak, alokasi sparepart, QC bengkel |
| **Siti Rahmawati** | `Finance` | `finance@omahvisual.com` | Rekonsiliasi pembayaran klien, verifikasi transfer honor kru |

---

*Dokumen ini disusun dan disahkan secara resmi oleh Manajemen CV Han Media Tekindo untuk menjadi pedoman operasional baku seluruh staf dan teknisi Omah Visual LED.*

