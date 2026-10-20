# 🔐 Matriks Hak Akses & Peran (Role & Permission Matrix)
## CV. Han Media Tekindo (Omahvisual LED)

Dokumen ini merupakan panduan dan acuan resmi (*single source of truth*) mengenai pembagian peran, hak akses, wewenang, dan batasan penggunaan fitur pada sistem operasional Omahvisual.

---

## 👥 1. Daftar Peran (Roles)

Sistem membagi pengguna ke dalam **7 Peran Internal (Karyawan/Manajemen)** dan **1 Peran Eksternal (Mitra Lapangan)**:

| No | Nama Role Sistem | Identitas Teknis | Status Karyawan | Fokus Tanggung Jawab Utama |
| :---: | :--- | :--- | :--- | :--- |
| 1 | **Superuser / Owner** | `Superuser` / `Owner` | Pemilik / Direksi | Akses mutlak (*full control*) ke seluruh modul, bypass otorisasi, manajemen user, dan audit log. |
| 2 | **Manager Operasional** | `Manager Operasional` | Karyawan Tetap | Penyalur Admin $\leftrightarrow$ Kru Lapangan, alokasi armada & kru, pengesahan SPK & approval QC event. |
| 3 | **Admin LED** | `Admin LED` | Karyawan Tetap | Input pesanan baru, kalkulator LED & formasi panggung, master customer, dan jadwal event. |
| 4 | **Finance** | `Finance` | Karyawan Tetap | Pembuatan faktur/invoice, kuitansi, pencatatan pembayaran DP/lunas, dan pencairan honor kru. |
| 5 | **Warehouse (Gudang)** | `Warehouse` | Karyawan Tetap | Logistik keluar/masuk barang (SPK Hal 2 & 3), verifikasi gate pass, stok fisik, dan serah terima unit rusak. |
| 6 | **Kepala Servis** | `Kepala Servis` | Karyawan Tetap | Penanggung jawab bengkel/ruang servis, delegasi tiket servis ke teknisi, approval spare part & kelayakan QC. |
| 7 | **Teknisi Servis** | `Teknisi Servis` | Karyawan Tetap | Eksekusi reparasi modul LED di workshop (solder mata LED/ganti IC/PSU), catat spare part terpakai. |
| 8 | **Kru Lapangan** | *(Token-based)* | Freelance / Outsourcing | PIC Event, Kru Panggung, Operator VJ, Driver (akses via link smartphone SPK Live tanpa akun login). |

> **Catatan Multi-Role:**  
> Untuk operasional saat ini di mana Admin merangkap Keuangan, satu akun dapat diberikan dua role sekaligus: `['Admin LED', 'Finance']`. Sistem Spatie Permission secara otomatis menggabungkan seluruh hak akses kedua role tersebut.

---

## 📊 2. Matriks Hak Akses Lengkap (Berdasarkan `role_revised.xlsx`)

> **Keterangan Simbol:**  
> - `✅ Full` : Hak Penuh / CRUD (Buat, Lihat, Edit, Hapus)  
> - `📝 Input` : Draf / Input Terbatas / Pengerjaan  
> - `👀 View` : Hanya Melihat (*Read-Only*)  
> - `⚠️ Appr` : Khusus Persetujuan / Otorisasi / Tanda Tangan  
> - `❌ No` : Akses Diblokir / Ditolak  

| Modul | Fitur / Tindakan Spesifik | Owner / Superuser | Manager Ops | Admin LED | Finance | Warehouse | Kepala Servis | Teknisi Servis | Kru Lapangan (Token) |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Dashboard** | Ringkasan KPI Global (Omset, Kas Masuk, Piutang) | ✅ Full | 👀 View *(Non-keu)* | 👀 View *(Non-keu)* | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No |
| **Dashboard** | Status Event Aktif & Jadwal Loading | ✅ Full | ✅ Full | ✅ Full | 👀 View | 👀 View | 👀 View | ❌ No | ❌ No |
| **Dashboard** | Peringatan Modul Rusak & Stok Kritis Gudang | ✅ Full | 👀 View | ❌ No | ❌ No | ✅ Full | ✅ Full | 👀 View | ❌ No |
| **Pesanan** | Input Pesanan Baru & Kalkulator LED Screen | ✅ Full | ✅ Full | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **Pesanan** | Desain Layout 3D Panggung & Resolusi VJ | ✅ Full | ✅ Full | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **Pesanan** | Edit Rincian Pesanan (Tanggal, Venue, Spek Layar) | ✅ Full | ✅ Full | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **Pesanan** | Batalkan / Hapus Pesanan | ✅ Full | ⚠️ Appr | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **Pesanan** | Lihat Detail Pesanan & Salin Link Tracking Klien | ✅ Full | ✅ Full | ✅ Full | ✅ Full | ✅ Full | ❌ No | ❌ No | ❌ No |
| **SPK Event** | Plotting Kru Lapangan & Nomor HP | ✅ Full | ✅ Full *(Utama)* | 📝 Input *(Draf)* | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **SPK Event** | Alokasi Armada Kendaraan & Supir | ✅ Full | ✅ Full *(Utama)* | 📝 Input *(Draf)* | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **SPK Event** | Cetak SPK Resmi 5 Halaman (Print / PDF) | ✅ Full | ✅ Full | ✅ Full | 👀 View | ✅ Full | ❌ No | ❌ No | ❌ No |
| **SPK Event** | Tanda Tangan SPK: *Bagian Operasional* | ✅ Full | ⚠️ Appr *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **SPK Event** | Review & Approval Foto QC Pemasangan / BAST | ✅ Full | ⚠️ Appr *(Utama)* | 👀 View | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **SPK Live** | Akses SPK Live Mobile (Rundown & Scan QR Maps) | ✅ Full | ✅ Full | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ✅ Full *(Via Link)* |
| **SPK Live** | Upload Foto QC Pemasangan & BAST dari HP | ✅ Full | ✅ Full | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | 📝 Input *(Live)* |
| **Logistik** | Checklist Barang Muatan Keluar (Loading Out) | ✅ Full | 👀 View | ❌ No | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | 👀 View *(Mobile)* |
| **Logistik** | Verifikasi Gate Keberangkatan (*Departure Gate*) | ✅ Full | ⚠️ Appr | ❌ No | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No |
| **Logistik** | Checklist Barang Kembali Pasca-Event (Loading In) | ✅ Full | 👀 View | ❌ No | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No |
| **Logistik** | Verifikasi Gate Kepulangan (*Return Gate*) | ✅ Full | ⚠️ Appr | ❌ No | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No |
| **Logistik** | Tanda Tangan SPK: *PJ Barang Keluar & Kembali* | ✅ Full | ❌ No | ❌ No | ❌ No | ⚠️ Appr *(Utama)* | ❌ No | ❌ No | ❌ No |
| **Logistik** | Lapor Selisih Barang Hilang / Kerusakan Unit | ✅ Full | ✅ Full | ❌ No | ❌ No | 📝 Input | 👀 View | ❌ No | ❌ No |
| **Workshop** | Terbitkan Tiket Servis Baru (Modul/Unit Rusak) | ✅ Full | 📝 Input | ❌ No | ❌ No | 📝 Input *(Serah)* | ✅ Full | ❌ No | ❌ No |
| **Workshop** | Delegasi Tiket Servis ke Teknisi Bengkel | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No |
| **Workshop** | Eksekusi Servis (Solder Mata LED, IC, PSU, dsb) | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ✅ Full | 📝 Input *(Kerjakan)*| ❌ No |
| **Workshop** | Input & Pengambilan Spare Part Servis | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ⚠️ Appr | 📝 Input *(Catat)* | ❌ No |
| **Workshop** | Validasi QC Hasil Servis & Tutup Tiket (*Completed*)| ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ⚠️ Appr *(Utama)* | ❌ No | ❌ No |
| **Workshop** | Kembalikan Unit Servis ke Gudang Siap Sewa | ✅ Full | ❌ No | ❌ No | ❌ No | 📝 Input *(Terima)* | ✅ Full | ❌ No | ❌ No |
| **Inventaris**| Master Produk, Kategori & Aset Barcode | ✅ Full | 👀 View | 👀 View | ❌ No | ✅ Full | 👀 View | ❌ No | ❌ No |
| **Inventaris**| Kelola Lokasi Lemari / Rak Penyimpanan | ✅ Full | ❌ No | ❌ No | ❌ No | ✅ Full | 📝 Input *(Bengkel)*| ❌ No | ❌ No |
| **Inventaris**| Catat Mutasi Stok (Masuk, Keluar, Pindah Rak) | ✅ Full | ❌ No | ❌ No | ❌ No | ✅ Full *(Utama)* | 📝 Input *(Sparepart)*| ❌ No | ❌ No |
| **Inventaris**| Stock Opname Awal (*Initial Count Verification*) | ⚠️ Appr | ❌ No | ❌ No | ❌ No | 📝 Input *(Fisik)* | ❌ No | ❌ No | ❌ No |
| **Inventaris**| Pengajuan & Approval Penyesuaian Stok Selisih | ⚠️ Appr | ❌ No | ❌ No | ❌ No | 📝 Input *(Ajukan)* | 📝 Input *(Ajukan)* | ❌ No | ❌ No |
| **Keuangan** | Buat Faktur / Invoice Tagihan (DP / Borongan) | ✅ Full | ❌ No | ❌ No *(Kecuali rangkap)*| ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Keuangan** | Cetak Faktur Tagihan Resmi (1 Lembar A4) | ✅ Full | ❌ No | 👀 View | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Keuangan** | Catat Penerimaan Pembayaran (Cash / Transfer) | ✅ Full | ❌ No | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Keuangan** | Cetak Kuitansi Resmi Pembayaran (1 Lembar A4) | ✅ Full | ❌ No | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Keuangan** | Hapus / Void Transaksi Pembayaran Masuk | ✅ Full | ❌ No | ❌ No | ⚠️ Appr | ❌ No | ❌ No | ❌ No | ❌ No |
| **Payroll** | Input / Edit Nominal Honor Kru di SPK | ✅ Full | 📝 Input *(Usulan)* | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Payroll** | Input Biaya Operasional / Uang Jalan Armada | ✅ Full | 📝 Input *(Pengajuan)*| ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Payroll** | Pencairan Honor Kru (*Tandai Lunas / Mark Paid*)| ✅ Full | ❌ No | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Payroll** | Cetak Slip Honor Kru per Event | ✅ Full | 👀 View | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Payroll** | Cetak Slip Rekapitulasi Bulanan Kru | ✅ Full | 👀 View | ❌ No | ✅ Full *(Utama)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Master Data**| Master Customer (Klien / Vendor / Instansi) | ✅ Full | ✅ Full | ✅ Full *(Utama)* | 👀 View | ❌ No | ❌ No | ❌ No | ❌ No |
| **Master Data**| Master Kru Freelance (Nama, HP, Tarif Standar) | ✅ Full | ✅ Full *(Utama)* | 👀 View | ✅ Full *(Tarif)* | ❌ No | ❌ No | ❌ No | ❌ No |
| **Master Data**| Master Armada Kendaraan (Mobil, Plat & Supir) | ✅ Full | ✅ Full *(Utama)* | 👀 View | ❌ No | 👀 View | ❌ No | ❌ No | ❌ No |
| **Sistem** | Kelola Pengguna (*Users, Roles & Password*) | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **Sistem** | Akses Audit Trail Log Aktivitas Sistem | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |
| **Sistem** | Raw Database Manager Darurat (*Superuser DB*) | ✅ Full | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No | ❌ No |

---

## 🔍 3. Rincian Batasan & Wewenang Tiap Role

### 1. Superuser / Owner
- **Wewenang:** Kendali mutlak tanpa batasan. Otorisasi tertinggi untuk pembatalan pesanan besar, penghapusan data krusial, penyesuaian stok opname awal, serta manajemen akun user dan audit trail.

### 2. Manager Operasional
- **Wewenang:** Bertindak sebagai jembatan antara pesanan masuk dari Admin dan kesiapan tim lapangan. Mengesahkan SPK pada kolom resmi *"Hormat kami, Bagian Oprasional"*, menetapkan supir & armada, memvalidasi kehadiran kru, serta menyetujui laporan QC/BAST dari venue.
- **Batasan:** Tidak dapat menerbitkan invoice, mencairkan pembayaran, atau mengubah pengaturan hak akses sistem.

### 3. Admin LED
- **Wewenang:** Bertanggung jawab atas penerimaan pesanan sewa LED, negosiasi dimensi layar, kalkulasi kabinet modul, pembuatan data customer baru, pengisian jadwal loading/event, pembatalan pesanan (sesuai revisi terbaru), dan akses ke SPK Live mobile.
- **Batasan:** Tidak memiliki akses modul Keuangan (kecuali akun di-assign role `Finance`), tidak dapat menyetujui mutasi stok gudang, dan tidak menandatangani SPK operasional.

### 4. Finance
- **Wewenang:** Kendali penuh seluruh transaksi finansial: pembuatan faktur tagihan, kuitansi resmi, penerimaan pembayaran DP/Lunas, approval nominal honor kru & uang jalan armada, serta pencairan honor (*Mark as Paid*).
- **Batasan:** Tidak mengubah spesifikasi teknis layar, tidak mengotak-atik checklist barang gudang.

### 5. Warehouse (Gudang)
- **Wewenang:** Menjaga keakuratan stok fisik modul dan kabel. Memeriksa muatan barang berangkat (SPK Halaman 2 & 3), menandatangani kolom *"PJ Barang Keluar"*, memverifikasi kepulangan muatan, menandatangani *"PJ Barang Kembali"*, dan menyerahkan modul rusak ke divisi workshop.
- **Batasan:** Tidak dapat melihat rincian harga sewa, omset, atau tagihan invoice.

### 6. Kepala Servis (Head of Maintenance)
- **Wewenang:** Pemimpin teknis di ruang servis / workshop LED. Menerima unit bermasalah dari gudang, mendelegasikan tiket servis ke Teknisi Servis, menyetujui pemakaian spare part dari gudang bengkel, dan melakukan uji kelayakan (QC lulus) sebelum unit dikembalikan ke gudang.
- **Batasan:** Tidak mengurusi alur event panggung maupun keuangan persewaan.

### 7. Teknisi Servis (Workshop Technician)
- **Wewenang:** Karyawan tetap yang fokus pada perbaikan fisik perangkat LED (soldering modul, penggantian diode LED, IC chip, receiving card, dan power supply). Mencatat tindakan perbaikan dan spare part yang digunakan pada tiket servis yang ditugaskan.
- **Batasan:** Hanya melihat tiket perbaikan yang didelegasikan kepadanya, tidak memiliki wewenang menutup/meluluskan tiket tanpa approval Kepala Servis.

### 8. Kru Lapangan (Mitra Freelance)
- **Wewenang:** Mengakses portal mobile SPK Live melalui tautan token unik tanpa login: melihat instruksi panggung, petunjuk Google Maps via scan QR, checklist muatan di lokasi, dan upload foto hasil pengetesan layar (QC) & BAST dari kamera smartphone.
- **Batasan:** Akses terbatas per-event aktif dan tidak memiliki akses ke dashboard internal back-office.

