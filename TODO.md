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
  - Rasio 16:9 untuk Titik Lokasi, Lemari Gudang, Foto Fisik Armada & QC Panggung
  - Rasio 4:3 untuk Master Barang & Modul LED
  - Rasio 3:2 untuk KTP Resmi Kru, STNK Armada, dan Kartu E-Toll
  - Rasio 1:1 (persegi) untuk Foto Avatar Kru, Foto Profil, dan Barcode Subsidi BBM
  - Rasio Bebas (Freeform) untuk Rate Card Kru dan Dokumen Kustom
- [x] Mekanisme injeksi berkas hasil crop via HTML5 `DataTransfer` sehingga controller backend tetap menerima objek `UploadedFile` standar tanpa modifikasi controller
- [x] **Perbaikan Bug Modal Crop Muncul Tiba-tiba**: Menambahkan `style="display: none;"` eksplisit pada root `#cropper-modal`, penanganan escape key, dan penutupan saat klik backdrop luar sehingga modal 100% tersembunyi saat login dan pergantian halaman menu.

### 17. Modul Profil Pengguna & Keamanan Kata Sandi (`/profile`) — ✅ Selesai
- [x] Migrasi database: Penambahan kolom `phone` dan `avatar_path` pada tabel `users`
- [x] Model `User`: Konfigurasi `$fillable`, accessor `avatar_url`, dan helper `initials`
- [x] Kontroler [`ProfileController.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/app/Http/Controllers/ProfileController.php) dengan metode `edit`, `update`, dan `updatePassword`
- [x] Tampilan formulir profil di [`profile/edit.blade.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/profile/edit.blade.php) lengkap dengan kartu identitas, upload avatar dengan crop 1:1, dan info hak akses role Spatie
- [x] Formulir mandiri pembaruan kata sandi akun dengan validasi password saat ini dan konfirmasi
- [x] Tautan cepat profil di Topbar navigasi [`layouts/app.blade.php`](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/layouts/app.blade.php) menampilkan avatar/inisial dan role aktif
- [x] Pengujian otomatis komprehensif di `tests/Feature/UserProfileAndAvatarTest.php` (6 tests baru, 29 tests total lulus 100%)

### 18. Modul Pesanan Event, Kalkulator Layar LED & SPK Otomatis 3-Lembar (`/orders` & `/spk`) — ✅ Selesai (Tahap 4)
- [x] Migrasi Skema Basis Data Terstruktur (Menggantikan 124 Kolom Google Sheets):
  - [x] Tabel `customers` (klien cepat, nomor WhatsApp, instansi/vendor)
  - [x] Tabel `event_orders` (nomor transaksi unik, jadwal loading, venue, armada mobil, finansial, multimedia add-ons, token akses crew)
  - [x] Tabel `order_led_screens` (lebar x tinggi layar, pitch, kabinet 1m vs 50cm, box hardcase, prosesor, jumper, spare)
  - [x] Tabel `spk_assignments` (penugasan Crew 1-7, armada mobil, kelengkapan surat kendaraan, gerbang keberangkatan & kepulangan)
  - [x] Tabel `spk_checklist_items` (daftar bawaan barang 53 item Halaman 2 dan 48 item Halaman 3)
- [x] Model Eloquent & Relasi Lengkap: `Customer`, `EventOrder`, `OrderLedScreen`, `SpkAssignment`, `SpkChecklistItem` beserta factory pengujiannya
- [x] Service Engine Perhitungan Layar LED (`LedCalculationService`):
  - [x] Kalkulasi otomatis meter persegi ($m^2$), kolom, kabinet 100cm & 50cm, estimasi box flight case
  - [x] Rekomendasi prosesor Novastar berdasarkan kapasitas piksel (VX400, VX600, VX1000, VX20 + Fiber Optic)
  - [x] Penghitungan otomatis kebutuhan kabel power jumper, LAN jumper, modul spare, receiving card, dan PSU cadangan
  - [x] Generator otomatis checklist muatan 53 item Halaman 2 dan 48 item Halaman 3
- [x] Kontroler & Antarmuka Cepat Admin LED (`EventOrderController`):
  - [x] Antarmuka daftar pesanan event (`orders/index.blade.php`) lengkap dengan metrik omset, status, filter, dan tombol aksi
  - [x] Formulir input cepat 30 detik (`orders/create.blade.php`) dengan toggle client baru/lama dan kalkulator layar real-time JavaScript & AJAX
  - [x] Halaman pusat rincian pesanan (`orders/show.blade.php`) menampilkan rincian layar, ringkasan gerbang serah terima, dan tautan live token
- [x] Kontroler & Tampilan Interaktif SPK 3 Lembar (`SpkController`):
  - [x] Tampilan interaktif 3-tab (`spk/show.blade.php`) matching template hardcopy `spk_ref.pdf`
  - [x] Pengesahan Gerbang Keberangkatan (Biru) & Kepulangan (Merah) dengan penegakan aturan: *"PIC Kepulangan Wajib Orang yg Berbeda"*
  - [x] Live checkbox interaktif checklist barang (Keluar & Kembali) via AJAX API
  - [x] Tampilan cetak presisi A4 (`spk/print.blade.php`) format cetak fisik identik 100% dengan `spk_ref.pdf`
  - [x] Halaman mobile publik crew lapangan via token rahasia (`spk/crew-live.blade.php`) tanpa perlu login (akses rute maps, jadwal loading, roster crew, dan checklist muatan)
- [x] Pengujian otomatis komprehensif: `LedCalculationServiceTest` (3 tests) & `EventOrderAndSpkManagementTest` (8 tests) — Total **40 tests lulus (100% Green), 248 assertions**

### 19. Migrasi & Impor Data Riil Pelanggan (379 Klien) & Roster Kru (113 Kru) (`app:import-excel-data`) — ✅ Selesai (Opsi 1)
- [x] Migrasi database: Tabel `crews` (nama panggilan, nama lengkap, telepon, nomor darurat, jabatan/posisi PIC/Kru/Operator, status aktif)
- [x] Model `Crew`, relasi ke `User`, dan `CrewFactory`
- [x] Ekstraktor Python cerdas yang menormalisasi format nomor telepon Indonesia (mengubah float `8113035202.0` menjadi `08113035202`) dan membersihkan nama instansi/organisasi
- [x] Perintah konsol Artisan `php artisan app:import-excel-data` & Seeder `OmahvisualExcelDataSeeder`:
  - [x] Berhasil mengimpor **379 Customer riil** dari sheet `Daftar Custumer Led` ke tabel `customers`
  - [x] Berhasil mengimpor **113 Kru lapangan riil** dari sheet `Nama Crew` ke tabel `crews`
- [x] Integrasi antarmuka input order baru (`orders/create.blade.php`):
  - [x] Pilihan dropdown customer terisi 379 pelanggan setia Omah Visual (Singham, Lunar, Garuda EO, Amicomedia, dll.)
  - [x] Bidang input PIC Event dan Crew 1 s/d 7 terhubung ke `<datalist id="crewList">` sehingga autocomplete langsung aktif untuk seluruh 113 kru (Boby, Yoga, Rio, Apip, Bagus, dll.)
### 20. Visual Virtual 3D Stage Designer & Dual Blueprint 2D (Tampak Atas & Depan) (`/orders` & `/spk`) — ✅ Selesai (Tahap 5)
- [x] Migrasi Skema Basis Data Tata Letak Panggung:
  - [x] Migrasi `add_stage_layout_to_event_orders_table` (`stage_width_m`, `stage_depth_m`, `stage_height_m`, `stage_layout_json`, `stage_layout_image`)
  - [x] Migrasi `add_stage_layout_front_image_to_event_orders_table` (`stage_layout_front_image`)
- [x] Komponen Interaktif Virtual 3D Stage Canvas (`components/stage-designer-3d.blade.php`):
  - [x] Render panggung 3D dinamis dengan Three.js & OrbitControls (dukungan offline lokal `public/vendor/three/`)
  - [x] Penempatan layar LED multi-unit (Main Screen, Wing Kiri, Wing Kanan) dengan visual kabinet dan truss gantung/ground stack
  - [x] Preset 1-klik: *Tengah Tunggal*, *Main + 2 Wings Miring*, *Triptych 3 Layar*
  - [x] Engine **Snap Magnet Action**:
    - [x] Snap garis tengah panggung ($X = 0$) dengan pemandu visual garis neon cyan 3D
    - [x] Snap kedalaman sejajar ($Z$-depth) terhadap layar lain dengan pemandu visual garis neon emerald 3D
    - [x] Snap kisi grid dinamis ($0.25\text{m}$, $0.5\text{m}$, $1.0\text{m}$) dengan magnetic feedback HUD badge
    - [x] Fitur bebas drag tanpa snap dengan menahan tombol `Shift`
  - [x] Dual 2D Architectural Blueprint Generator:
    - [x] `generate2DTopBlueprint()`: Tampak Atas (Top-Down Floor Plan) dengan kisi skala 1 meter, garis bibir panggung kuning menghadap penonton, dan sudut kemiringan sayap
    - [x] `generate2DFrontBlueprint()`: Tampak Depan (Front Elevation) dengan garis dasar tanah ($\pm0.00\text{m}$), podium level panggung, balok truss gantung & kabel sling vs kaki ground stack, serta elevasi puncak layar
  - [x] Integrasi sinkronisasi otomatis ke form input order baru (`orders/create.blade.php`) dan update AJAX di halaman detail order (`orders/show.blade.php`)
- [x] Peningkatan Cetak SPK A4 Lembar ke-4 Khusus Layout Teknis (`spk/print.blade.php`):
  - [x] Halaman 4 mandiri (A4 Portrait): **4 BLUEPRINT TEKNIS: TATA LETAK PANGGUNG & FORMASI LAYAR LED**
  - [x] Kotak ganda berdampingan: Diagram Tampak Atas (Denah Panggung) & Diagram Tampak Depan (Elevasi Penonton)
  - [x] Tabel spesifikasi teknis rigging (ukuran fisik, luas SQM, kabinet, flight case, prosesor, dan catatan panggung)
  - [x] Lembar tanda tangan kelayakan rigging (PIC Rigging / Stage Lead, Operator Visual, PIC Event Lapangan)
  - [x] Lembar 1 dilengkapi penunjuk rujukan: `📌 Detail Teknis Tampak Atas & Depan: Lihat Lembar 4`
- [x] Portal Mobile Crew Lapangan (`spk/crew-live.blade.php`):
  - [x] Kartu ganda Tampak Atas & Tampak Depan yang responsif untuk layar smartphone
  - [x] Accordion simulasi virtual 3D interaktif yang bisa diputar langsung dari HP kru
- [x] Pengujian otomatis: `EventOrderAndSpkManagementTest` diperluas mencakup validasi formasi ganda dan cetak lembar ke-4 — Total suite menjadi **43 tests lulus (100% Green), 287 assertions**

---

## 📌 Catatan Revisi & Penyempurnaan (Priority Status)
- [x] **Penyempurnaan Kalibrasi Cetak SPK A4 Sesuai Review Hans (`spk/print.blade.php` & `stage-designer-3d.blade.php`)** — ✅ Selesai:
  - [x] **Skalasi Tipografi & Keterbacaan Cetak Menyeluruh (Dibuat Jauh Lebih Besar & Tegas)**:
    - **Halaman 1 (Serah Terima & Surat Jalan)**: Font isi tabel detail event, penugasan kru, dan kelengkapan armada mobil diperbesar menjadi `11.5px` (sebelumnya `9.5px` - `11px`), judul header `16px`, tabel tanda tangan `10.5px` dengan tinggi bidang tanda tangan `44px`.
    - **Halaman 1 (Docking Tanda Tangan ke Dasar Halaman)**: Blok pernyataan tanggung jawab (*"- Dengan menandatangani checklist ini, berarti kamu bertanggung jawab penuh atas isi checklist..."*) beserta banner KEBERANGKATAN, peringatan PIC Berbeda, dan banner KEPULANGAN didorong rapat ke bagian paling bawah lembar cetak menggunakan flexbox column (`min-height: 275mm` dengan `margin-top: auto;`), berfungsi rapi menyerupai grounded footer.
    - **Halaman 2 (Checklist 53 Item) & Halaman 3 (Checklist 48 Item)**: Font isi tabel dinaikkan menjadi `10px`, judul kategori `10.5px bold`, padding sel `2.5px 4px`, mengisi bidang vertikal lembar A4 secara mantap dan nyaman dibaca saat berdiri tanpa tumpah ke lembar berikutnya.
  - [x] **Halaman 4 (Blueprint Teknis Panggung & Formasi Layar LED - A4 Portrait Vertikal)**:
    - **Peningkatan Resolusi & Skala Maksimal (~88% Kanvas)**: Resolusi kanvas dinaikkan ke **1200 × 600 px** (dari 1000 × 460) dengan margin ketat, sehingga panggung dan seluruh layar LED terproyeksikan hingga **70-100+ px/meter** (meningkat lebih dari +55% lebih besar dibanding versi awal).
    - **Skalasi Tipografi Ekstra Besar (14px s/d 18px Bold)**:
      - Judul layar dan dimensi dilengkapi *contrast pill badge* berbingkai neon dengan font `15px - 18px bold` putih dan kuning menyala, sangat kontras di atas grid.
      - Peringatan bibir panggung, ukuran panggung, elevasi puncak (+X.XXm), dan ketinggian vertikal diperbesar ke `13px - 16px bold`.
    - **Susunan Vertikal (ATAS - BAWAH) Selebar Penuh Kertas**:
      1. **Atas**: Diagram 1 Tampak Atas (Top-Down Floor Plan) lebar 100% dengan `max-height: 94mm`.
      2. **Bawah**: Diagram 2 Tampak Depan (Front Elevation / Perspektif Penonton) lebar 100% dengan `max-height: 94mm`.
      3. **Dasar**: Tabel Spesifikasi Rigging (kiri 62%) dan Lembar Pengesahan Kelayakan Rigging (kanan 38%).
    - **Resolusi Overlap Label Layar & Dimensi Panggung**: Label badge layar belakang (seperti *Main Screen*) kini secara cerdas diposisikan di sisi depan layar (`badgeY = +scD_px/2 + 20` mengarah ke lantai panggung yang lapang), dan teks `LEBAR PANGGUNG: XX METER` dilengkapi bilah garis dimensi arsitektural dengan batas tick mark mandiri di atas panggung, sehingga 100% bebas dari tumpang tindih teks dalam posisi formasi apa pun.
    - **Engine Auto-Render Real-Time di View Cetak**: File `print.blade.php` kini dilengkapi auto-renderer berbasis client-side canvas yang otomatis me-render blueprint resolusi tinggi 1200x600 saat halaman cetak dibuka, sehingga hasil cetak fisik maupun PDF selalu mendapatkan kualitas grafis terbaik tanpa terpengaruh cache gambar lama di database.
  - [x] **Restriksi Akses Menu Event & Persewaan LED (Fokus Operasional Inventori Murni)**:
    - Menu sidebar "Event & Persewaan LED" (`orders.index`, `spk.*`) kini dibatasi hanya dapat dilihat oleh user dengan role **Owner** dan **Superuser** via `@hasanyrole('Owner|Superuser')` di `layouts/app.blade.php`.
    - User staf gudang (Warehouse/Admin) difokuskan 100% pada alur inventori fisik murni (Master Barang, Lemari 1-3, Saldo Awal, dan Mutasi Stok harian).
  - [x] **Modul Terintegrasi: Servis & Pemeliharaan Unit LED (Workshop & Karantina Inventori)** — ✅ Selesai:
    - [x] **Skema Basis Data**: Tabel `service_tickets` (nomor tiket `SRV-YYYYMM-XXXX`, pelapor, teknisi PJ, prioritas, diagnosa, tindakan, mutasi lokasi) dan `service_spareparts` (suku cadang terpakai).
    - [x] **Siklus Hidup Karantina & Mutasi Stok Otomatis**:
      - Saat tiket dibuat: Unit individual otomatis berganti status menjadi `maintenance` & `in_service` dan ditransfer ke *Workshop*. Untuk barang kuantitas (modul/kabel), saldo `available` dipotong dan saldo `in_service` di Workshop bertambah.
      - Konsumsi sparepart: Mengambil komponen dari Lemari 2 (IC driver, PSU, mata LED) langsung memotong stok via mutasi *outbound*.
      - Penyelesaian Lolos QC: Status barang otomatis kembali menjadi `available` & `in_storage` dan ditransfer kembali ke rak tujuan, memulihkan saldo siap sewa.
      - Opsi Afkir / Rusak Total: Barang ditandai sebagai `scrapped` / `damaged`.
    - [x] **Antarmuka Pengguna Lengkap**:
      - Katalog tiket servis (`service_tickets/index.blade.php`) dengan kartu metrik (Antrean, Pengerjaan, Tunggu Part, Uji QC, Selesai).
      - Formulir tiket baru (`service_tickets/create.blade.php`) terintegrasi langsung dengan pilihan aset dan kuantitas.
      - Lembar kerja teknisi (`service_tickets/show.blade.php`) dengan timeline status, form diagnosa, tabel konsumsi sparepart modal, dan tombol pengembalian ke rak siap sewa.
      - Pintasan cepat *"🛠️ Buka Tiket Servis Workshop"* di halaman profil detail aset (`assets/show.blade.php`).
      - Peningkatan kolom tabel [Master Barang](file:///d:/Job/ID_CV%20Han%20Media%20Tekindo/operational-omahvisual/resources/views/products/index.blade.php) yang kini secara tegas menampilkan **"X unit siap sewa"** dan badge **"[Y dalam servis]"** secara real-time.
    - [x] **Pengujian Otomatis Komprehensif**: `tests/Feature/ServiceTicketAndWorkshopTest.php` (7 tests baru lulus 100%, total suite **50 tests, 319 assertions**).

---

## 🔮 Roadmap Jangka Panjang (Operasional Lanjutan Sesuai PRD Global)
- [ ] Integrasi Barcode / QR Code Fisik Scanner (Ditunda sementara sesuai arahan user agar fokus pematangan operasional)
- [ ] Demobilisasi & Rekonsiliasi Pasca-Event Otomatis ke Stok Gudang (Otomatis potong modul rusak ke status Service / Maintenance)
- [ ] Dashboard Analisis Manajer Operasional (Grafik utilisasi armada TRAGA 1/2/3, tren ukuran layar LED, performa crew)
- [ ] Invoicing & Verifikasi Pembayaran Bertahap (DP vs Pelunasan via Rekening BCA/BRI)
- [ ] Portal Pelacakan Mandiri oleh Customer (Tracking status persiapan LED screen via order token)
