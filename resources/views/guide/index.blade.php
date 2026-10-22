@extends('layouts.app')

@section('title', 'Panduan Penggunaan Aplikasi & SOP Operasional')
@section('page_title', 'Buku Panduan & SOP Operasional')

@section('content')
<div x-data="{ 
    activeTab: 'alur',
    searchQuery: '',
    copiedEmail: null,
    copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        this.copiedEmail = text;
        setTimeout(() => this.copiedEmail = null, 2500);
    }
}" class="space-y-8">

    <!-- Header & Hero Section -->
    <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-indigo-950 via-slate-900 to-slate-950 border border-indigo-500/30 p-6 md:p-10 shadow-2xl">
        <div class="absolute -right-10 -top-10 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Pusat Informasi &amp; Uji Coba Mandiri Sistem</span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                    Buku Panduan &amp; Standar Operasional Sistem
                </h1>
                <p class="text-slate-300 text-sm md:text-base leading-relaxed">
                    Panduan lengkap alur bisnis persewaan videotron LED, rincian wewenang tiap peran, fitur unggulan panggung &amp; logistik, serta daftar kredensial akun uji coba bagi jajaran Direksi, Owner, dan Manajemen.
                </p>
            </div>

            <!-- Quick Action: Jump to Demo Accounts -->
            <div class="shrink-0 flex flex-col sm:flex-row lg:flex-col gap-3">
                <button @click="activeTab = 'akun'" class="px-5 py-3 rounded-2xl bg-linear-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm shadow-xl shadow-amber-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Daftar Akun Uji Coba Bos</span>
                </button>
                <button @click="activeTab = 'alur'" class="px-5 py-3 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold text-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Lihat Alur Bisnis Event</span>
                </button>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="mt-8 pt-6 border-t border-slate-800 flex flex-wrap gap-2">
            <button @click="activeTab = 'alur'" :class="activeTab === 'alur' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/60 text-slate-400 hover:text-white hover:bg-slate-800'" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>1. Alur Bisnis Event (10 Langkah)</span>
            </button>

            <button @click="activeTab = 'peran'" :class="activeTab === 'peran' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/60 text-slate-400 hover:text-white hover:bg-slate-800'" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>2. Panduan Tiap Peran (Roles)</span>
            </button>

            <button @click="activeTab = 'fitur'" :class="activeTab === 'fitur' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/60 text-slate-400 hover:text-white hover:bg-slate-800'" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <span>3. Fitur Kunci &amp; Keunggulan</span>
            </button>

            <button @click="activeTab = 'akun'" :class="activeTab === 'akun' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30' : 'bg-slate-800/60 text-amber-400 hover:text-white hover:bg-slate-800'" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                <span>4. Akun Simulasi Bos (Testing)</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: ALUR BISNIS EVENT (LIFECYCLE) -->
    <div x-show="activeTab === 'alur'" x-cloak class="space-y-6">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-white">Alur Operasional Persewaan Videotron (End-to-End)</h2>
                    <p class="text-sm text-slate-400 mt-1">SOP baku dari order masuk hingga penggajian kru panggung dan laporan penutupan event.</p>
                </div>
                <span class="hidden md:inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    Standar Omahvisual LED
                </span>
            </div>

            <!-- Visual Stepper / Timeline -->
            <div class="relative border-l-2 border-indigo-500/30 ml-4 md:ml-8 space-y-10 py-4">
                
                <!-- Step 1 -->
                <div class="relative pl-8 group">
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-indigo-600 text-white font-black text-xs flex items-center justify-center border-4 border-slate-900 shadow-md">
                        1
                    </div>
                    <div class="bg-slate-800/50 hover:bg-slate-800/80 transition-all rounded-2xl p-5 border border-slate-700/60">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>Input Pesanan &amp; Kalkulator Layar LED</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-sky-500/20 text-sky-400 font-semibold">Admin LED</span>
                            </h3>
                            <a href="{{ route('orders.create') }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                                Coba Input Pesanan &rarr;
                            </a>
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Admin menerima permintaan customer, memilih venue/lokasi, menginput ukuran layar (misal: 6×3 meter). Kalkulator sistem otomatis menghitung jumlah kabinet 100×50 cm dan 50×50 cm, total meter persegi (m²), simulasi resolusi pixel Novastar, serta estimasi daya listrik watt yang dibutuhkan.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700/80">Menu: Pesanan Sewa &rarr; Buat Pesanan Baru</span>
                            <span class="px-2 py-1 rounded bg-indigo-950 text-indigo-300 border border-indigo-800/40">Fitur: Kalkulator P×L &amp; Preview Panggung 3D</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative pl-8 group">
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-indigo-600 text-white font-black text-xs flex items-center justify-center border-4 border-slate-900 shadow-md">
                        2
                    </div>
                    <div class="bg-slate-800/50 hover:bg-slate-800/80 transition-all rounded-2xl p-5 border border-slate-700/60">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>Plotting Multi-Armada, Supir &amp; Kru Lapangan</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-purple-500/20 text-purple-400 font-semibold">Manager Operasional</span>
                            </h3>
                            @if($sampleAssignment)
                            <a href="{{ route('spk.show', $sampleAssignment) }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                                Buka SPK Sample &rarr;
                            </a>
                            @elseif($sampleOrder)
                            <a href="{{ route('orders.show', $sampleOrder) }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                                Buka Detail Pesanan &rarr;
                            </a>
                            @endif
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Manager Operasional mengatur armada logistik (Pick Up / Blind Van / Truk) beserta penugasan Driver, PIC Event, Operator VJ panggung, dan kru pemasangan. Sistem mendukung pengalokasian multi-armada jika volume barang melebihi 1 kendaraan.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700/80">Menu: Detail Pesanan &rarr; Tab SPK Operasional</span>
                            <span class="px-2 py-1 rounded bg-purple-950 text-purple-300 border border-purple-800/40">Otorisasi: Tanda Tangan Digital Kolom Bagian Operasional</span>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative pl-8 group">
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-indigo-600 text-white font-black text-xs flex items-center justify-center border-4 border-slate-900 shadow-md">
                        3
                    </div>
                    <div class="bg-slate-800/50 hover:bg-slate-800/80 transition-all rounded-2xl p-5 border border-slate-700/60">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>Loading Out &amp; Verifikasi Gate Keberangkatan</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-emerald-500/20 text-emerald-400 font-semibold">Tim Warehouse / Gudang</span>
                            </h3>
                            @if($sampleAssignment)
                            <a href="{{ route('spk.show', $sampleAssignment) }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1">
                                Verifikasi Muatan Berangkat &rarr;
                            </a>
                            @endif
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Tim Gudang menyiapkan modul videotron, kabel powercon, kabel LAN signal, video processor Novastar, hanging bar, dan toolbox. Melakukan checklist fisik muatan keberangkatan, memverifikasi barang di pintu gate gudang, dan menandatangani kolom <em>"PJ Barang Keluar"</em> pada dokumen SPK.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700/80">Menu: SPK Detail &rarr; Tombol "Gate Keberangkatan"</span>
                            <span class="px-2 py-1 rounded bg-emerald-950 text-emerald-300 border border-emerald-800/40">Dokumen: SPK Halaman 2 (Checklist Muatan Berangkat)</span>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="relative pl-8 group">
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-amber-500 text-slate-950 font-black text-xs flex items-center justify-center border-4 border-slate-900 shadow-md">
                        4
                    </div>
                    <div class="bg-slate-800/50 hover:bg-slate-800/80 transition-all rounded-2xl p-5 border border-slate-700/60">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>Mobile SPK Live &amp; Navigasi Venue (Tanpa Password)</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-amber-500/20 text-amber-300 font-semibold">Kru Lapangan / Freelance</span>
                            </h3>
                            @if($sampleOrder && $sampleOrder->public_token)
                            <a href="{{ route('spk.live', $sampleOrder->public_token) }}" target="_blank" class="text-xs text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1">
                                Buka Tampilan HP Kru &rarr;
                            </a>
                            @endif
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Kru di lapangan membuka tautan SPK Live melalui WhatsApp pada layar smartphone. Terdapat rundown jam loading, kontak darurat PIC, tombol satu-klik buka Google Maps lokasi venue, serta daftar peralatan yang wajib dipasang.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700/80">Akses: Link Berbasis Token Aman (Bisa dibuka di Android &amp; iPhone)</span>
                            <span class="px-2 py-1 rounded bg-amber-950 text-amber-300 border border-amber-800/40">Keunggulan: Kru tidak perlu login/ingat akun</span>
                        </div>
                    </div>
                </div>

                <!-- Step 5 & 6 -->
                <div class="relative pl-8 group">
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-indigo-600 text-white font-black text-xs flex items-center justify-center border-4 border-slate-900 shadow-md">
                        5
                    </div>
                    <div class="bg-slate-800/50 hover:bg-slate-800/80 transition-all rounded-2xl p-5 border border-slate-700/60">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>Upload Foto QC Pemasangan, Formasi Layar &amp; BAST</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-amber-500/20 text-amber-300 font-semibold">Kru</span>
                                <span class="text-slate-400">&amp;</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-purple-500/20 text-purple-400 font-semibold">Manager Ops</span>
                            </h3>
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Setelah instalasi selesai, kru mengambil foto bukti layar menyala utuh (QC test pattern / video loop) dan lembar Berita Acara Serah Terima (BAST). Sistem otomatis mengompres foto di browser HP kru agar hemat kuota dan cepat terkirim. Manager Ops menerima notifikasi visual dan menyetujui (Approve QC).
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700/80">Fitur: Auto-Compress HTML5 Canvas di HP Kru</span>
                            <span class="px-2 py-1 rounded bg-purple-950 text-purple-300 border border-purple-800/40">Status: QC Approved &rarr; Layar Siap Show</span>
                        </div>
                    </div>
                </div>

                <!-- Step 7 -->
                <div class="relative pl-8 group">
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-indigo-600 text-white font-black text-xs flex items-center justify-center border-4 border-slate-900 shadow-md">
                        6
                    </div>
                    <div class="bg-slate-800/50 hover:bg-slate-800/80 transition-all rounded-2xl p-5 border border-slate-700/60">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>Loading In &amp; Return Gate (Pemeriksaan Bongkaran)</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-emerald-500/20 text-emerald-400 font-semibold">Tim Warehouse / Gudang</span>
                            </h3>
                            @if($sampleAssignment)
                            <a href="{{ route('spk.show', $sampleAssignment) }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1">
                                Verifikasi Muatan Kembali &rarr;
                            </a>
                            @endif
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Setelah acara selesai dan muatan kembali ke gudang, Tim Warehouse melakukan verifikasi kepulangan (Return Gate). Jika ada kabel kurang atau modul mengalami dead-pixel / retak saat acara, tim gudang langsung menandai selisih dan membuat tiket serah terima unit rusak ke Ruang Servis.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700/80">Menu: SPK Detail &rarr; Tombol "Gate Kepulangan"</span>
                            <span class="px-2 py-1 rounded bg-emerald-950 text-emerald-300 border border-emerald-800/40">Dokumen: SPK Halaman 3 (Checklist Muatan Kembali)</span>
                        </div>
                    </div>
                </div>

                <!-- Step 8 -->
                <div class="relative pl-8 group">
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-indigo-600 text-white font-black text-xs flex items-center justify-center border-4 border-slate-900 shadow-md">
                        7
                    </div>
                    <div class="bg-slate-800/50 hover:bg-slate-800/80 transition-all rounded-2xl p-5 border border-slate-700/60">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>Reparasi Bengkel &amp; Penanganan Modul Rusak</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-violet-500/20 text-violet-400 font-semibold">Kepala &amp; Teknisi Servis</span>
                            </h3>
                            <a href="{{ route('service-tickets.index') }}" class="text-xs text-violet-400 hover:text-violet-300 font-semibold flex items-center gap-1">
                                Manajemen Tiket Servis &rarr;
                            </a>
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Kepala Servis menerima unit bermasalah, menugaskan tiket ke Teknisi Servis. Teknisi melakukan soldering mata lampu LED, penggantian IC Driver, atau reparasi Power Supply Unit (PSU). Pemakaian spare part dicatat secara otomatis memotong stok bengkel. Setelah lulus QC pengetesan, modul dikembalikan ke status "Ready Sewa" di gudang.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700/80">Menu: Bengkel &amp; Servis &rarr; Daftar Tiket</span>
                            <span class="px-2 py-1 rounded bg-violet-950 text-violet-300 border border-violet-800/40">Otorisasi: Approval QC oleh Kepala Servis</span>
                        </div>
                    </div>
                </div>

                <!-- Step 9 -->
                <div class="relative pl-8 group">
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-indigo-600 text-white font-black text-xs flex items-center justify-center border-4 border-slate-900 shadow-md">
                        8
                    </div>
                    <div class="bg-slate-800/50 hover:bg-slate-800/80 transition-all rounded-2xl p-5 border border-slate-700/60">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>Penerbitan Faktur Tagihan &amp; Kuitansi Pembayaran A4 Pas</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-blue-500/20 text-blue-400 font-semibold">Finance / Admin</span>
                            </h3>
                            <a href="{{ route('invoices.index') }}" class="text-xs text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-1">
                                Daftar Faktur Tagihan &rarr;
                            </a>
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Finance menerbitkan faktur tagihan resmi (Down Payment maupun Pelunasan), mencatat pembayaran masuk dari customer via transfer/tunai, serta mencetak kuitansi pembayaran yang diformat pas 1 halaman A4 siap tanda tangan dan stempel.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700/80">Menu: Faktur Tagihan &rarr; Buat / Cetak</span>
                            <span class="px-2 py-1 rounded bg-blue-950 text-blue-300 border border-blue-800/40">Desain: Standard 1 Lembar A4 Portrait Non-Bocor</span>
                        </div>
                    </div>
                </div>

                <!-- Step 10 -->
                <div class="relative pl-8 group">
                    <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full bg-indigo-600 text-white font-black text-xs flex items-center justify-center border-4 border-slate-900 shadow-md">
                        9
                    </div>
                    <div class="bg-slate-800/50 hover:bg-slate-800/80 transition-all rounded-2xl p-5 border border-slate-700/60">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <span>Payroll &amp; Pencairan Honor Kru / Uang Jalan Armada</span>
                                <span class="px-2 py-0.5 text-xs rounded bg-emerald-500/20 text-emerald-400 font-semibold">Finance &amp; Manager Ops</span>
                            </h3>
                            <a href="{{ route('crew-payroll.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1">
                                Buka Rekap Honor Kru &rarr;
                            </a>
                        </div>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Finance memverifikasi kehadiran kru dan biaya operasional uang bensin/tol armada pada event tersebut. Honor dicairkan (ditandai Lunas), dan sistem dapat mencetak Slip Honor Kru individual maupun Rekap Bulanan untuk arsip pembukuan.
                        </p>
                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                            <span class="px-2 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700/80">Menu: Honor Kru &amp; Biaya &rarr; Pembayaran</span>
                            <span class="px-2 py-1 rounded bg-emerald-950 text-emerald-300 border border-emerald-800/40">Dokumen: Cetak Slip Honor Resmi per Event</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- TAB 2: PANDUAN TIAP PERAN (ROLES BREAKDOWN) -->
    <div x-show="activeTab === 'peran'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- 1. Owner & Superuser -->
            <div class="bg-slate-900 border border-amber-500/30 rounded-3xl p-6 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-amber-500/20 text-amber-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Owner &amp; Superuser</h3>
                            <p class="text-xs text-amber-400 font-semibold">Pemilik Perusahaan &amp; Direksi (Akses Penuh)</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-amber-500/20 text-amber-300 font-bold">Full Control</span>
                </div>
                <div class="space-y-2 text-sm text-slate-300">
                    <p class="font-semibold text-slate-100">Fokus Wewenang &amp; Tanggung Jawab:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-slate-300">
                        <li>Melihat KPI omset finansial, laba event, piutang sewa, dan utilisasi armada.</li>
                        <li>Otorisasi pembatalan pesanan atau penghapusan data krusial.</li>
                        <li>Persetujuan koreksi stok opname selisih gudang (Stock Adjustment Approval).</li>
                        <li>Manajemen pengguna sistem (tambah karyawan, atur role &amp; reset password).</li>
                        <li>Akses audit trail sistem untuk memeriksa siapa melakukan apa.</li>
                    </ul>
                </div>
            </div>

            <!-- 2. Manager Operasional -->
            <div class="bg-slate-900 border border-purple-500/30 rounded-3xl p-6 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-purple-500/20 text-purple-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Manager Operasional</h3>
                            <p class="text-xs text-purple-400 font-semibold">Jembatan Kantor &amp; Lapangan (Dani FT)</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-purple-500/20 text-purple-300 font-bold">Operasional</span>
                </div>
                <div class="space-y-2 text-sm text-slate-300">
                    <p class="font-semibold text-slate-100">Fokus Wewenang &amp; Tanggung Jawab:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-slate-300">
                        <li>Menentukan alokasi armada mobil pick up/blind van &amp; driver bertugas.</li>
                        <li>Menunjuk PIC Event, Teknisi Panggung, dan Operator VJ Novastar.</li>
                        <li>Mengesahkan dokumen SPK pada kolom resmi <em>"Bagian Operasional"</em>.</li>
                        <li>Memantau SPK Live mobile kru dan meng-approve foto QC pengetesan layar di venue.</li>
                        <li>Mengajukan usulan honor kru &amp; uang jalan BBM armada ke Finance.</li>
                    </ul>
                </div>
            </div>

            <!-- 3. Admin LED -->
            <div class="bg-slate-900 border border-sky-500/30 rounded-3xl p-6 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-sky-500/20 text-sky-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Admin LED</h3>
                            <p class="text-xs text-sky-400 font-semibold">Penerimaan Order &amp; Spek Layar</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-sky-500/20 text-sky-300 font-bold">Order Master</span>
                </div>
                <div class="space-y-2 text-sm text-slate-300">
                    <p class="font-semibold text-slate-100">Fokus Wewenang &amp; Tanggung Jawab:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-slate-300">
                        <li>Menginput pesanan sewa baru dari klien/EO/vendor.</li>
                        <li>Menggunakan kalkulator kabinet untuk menentukan konfigurasi modul (100×50 / 50×50).</li>
                        <li>Mengelola master data pelanggan (Customer Directory &amp; riwayat order).</li>
                        <li>Membagikan tautan live tracking &amp; rundown kepada klien.</li>
                    </ul>
                </div>
            </div>

            <!-- 4. Finance (Keuangan) -->
            <div class="bg-slate-900 border border-blue-500/30 rounded-3xl p-6 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-blue-500/20 text-blue-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Finance (Keuangan)</h3>
                            <p class="text-xs text-blue-400 font-semibold">Penagihan, Kuitansi &amp; Payroll Kru</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-blue-500/20 text-blue-300 font-bold">Billing &amp; Cash</span>
                </div>
                <div class="space-y-2 text-sm text-slate-300">
                    <p class="font-semibold text-slate-100">Fokus Wewenang &amp; Tanggung Jawab:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-slate-300">
                        <li>Menerbitkan Faktur Tagihan (Invoice DP 30-50% maupun Pelunasan 100%).</li>
                        <li>Mencatat pembayaran masuk customer dan mencetak Kuitansi Resmi format A4.</li>
                        <li>Verifikasi rincian honor kru, uang makan, uang bensin, dan tol armada di SPK.</li>
                        <li>Mencairkan honor kru (Mark Paid) dan mencetak slip tanda terima honor.</li>
                    </ul>
                </div>
            </div>

            <!-- 5. Warehouse (Tim Gudang) -->
            <div class="bg-slate-900 border border-emerald-500/30 rounded-3xl p-6 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-emerald-500/20 text-emerald-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Warehouse (Gudang)</h3>
                            <p class="text-xs text-emerald-400 font-semibold">Logistik Muatan, Gate Pass &amp; Stok Fisik</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-emerald-500/20 text-emerald-300 font-bold">Logistik</span>
                </div>
                <div class="space-y-2 text-sm text-slate-300">
                    <p class="font-semibold text-slate-100">Fokus Wewenang &amp; Tanggung Jawab:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-slate-300">
                        <li>Menyiapkan modul, processor Novastar, kabel power/LAN, dan toolbox.</li>
                        <li>Memeriksa muatan berangkat (Loading Out) &amp; menandatangani <em>"PJ Barang Keluar"</em>.</li>
                        <li>Memeriksa muatan kepulangan pasca-event (Loading In) &amp; tanda tangan <em>"PJ Barang Kembali"</em>.</li>
                        <li>Menyerahkan modul rusak ke Ruang Servis &amp; melakukan pencatatan mutasi rak.</li>
                    </ul>
                </div>
            </div>

            <!-- 6. Kepala Servis & Teknisi Workshop -->
            <div class="bg-slate-900 border border-violet-500/30 rounded-3xl p-6 relative overflow-hidden shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-violet-500/20 text-violet-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Kepala &amp; Teknisi Ruang Servis</h3>
                            <p class="text-xs text-violet-400 font-semibold">Workshop Pemeliharaan LED (Internal)</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs rounded-full bg-violet-500/20 text-violet-300 font-bold">Bengkel LED</span>
                </div>
                <div class="space-y-2 text-sm text-slate-300">
                    <p class="font-semibold text-slate-100">Fokus Wewenang &amp; Tanggung Jawab:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs text-slate-300">
                        <li><strong>Kepala Servis:</strong> Menerima serah terima modul rusak dari gudang, mendelegasikan tiket ke teknisi, menyetujui pemakaian spare part, dan mengesahkan QC kelayakan pakai.</li>
                        <li><strong>Teknisi Servis:</strong> Melakukan solder mata lampu SMD LED, penggantian IC Driver, power supply (PSU), dan mencatat spare part yang terpakai.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- TAB 3: FITUR KUNCI & KEUNGGULAN SISTEM -->
    <div x-show="activeTab === 'fitur'" x-cloak class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Fitur 1 -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-black">
                    📐
                </div>
                <h3 class="text-lg font-bold text-white">Kalkulator Dimensi P×L Otomatis</h3>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Cukup masukkan panjang dan tinggi layar yang diinginkan (misal: 6×3 meter), sistem langsung merekomendasikan susunan kabinet 100×50 cm &amp; 50×50 cm, menghitung luas m², estimasi konsumsi listrik (kW), dan berat rigging.
                </p>
            </div>

            <!-- Fitur 2 -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center font-black">
                    🖨️
                </div>
                <h3 class="text-lg font-bold text-white">Cetak SPK 5 Halaman Resmi</h3>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Format dokumen SPK persis standar operasional: Halaman 1 Info Event &amp; Kru, Halaman 2 Checklist Loading Keluar, Halaman 3 Checklist Loading Kembali, Halaman 4 Petunjuk Teknis &amp; Kontak Darurat, Halaman 5 Lembar Tanda Tangan Resmi.
                </p>
            </div>

            <!-- Fitur 3 -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-black">
                    📱
                </div>
                <h3 class="text-lg font-bold text-white">SPK Live Mobile Tanpa Password</h3>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Kru lapangan freelance tidak perlu dipusingkan dengan hafalan password. Sistem menyediakan tautan berbasis token aman yang bisa dibuka langsung di browser smartphone, lengkap dengan peta Google Maps dan tombol darurat.
                </p>
            </div>

            <!-- Fitur 4 -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black">
                    ⚡
                </div>
                <h3 class="text-lg font-bold text-white">Kompresi Foto Otomatis di HP Kru</h3>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Foto kamera HP kru (yang biasanya 5MB - 12MB) dikompresi langsung di sisi browser menjadi ~300KB sebelum diunggah ke server. Upload foto QC panggung dan lembar BAST tetap lancar walau sinyal internet di lokasi konser lemah.
                </p>
            </div>

            <!-- Fitur 5 -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-sky-500/20 text-sky-400 flex items-center justify-center font-black">
                    📄
                </div>
                <h3 class="text-lg font-bold text-white">Faktur &amp; Kuitansi Presisi A4 Pas</h3>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Template invoice dan kuitansi pembayaran didesain dengan teknik print-CSS mutakhir, dijamin pas 1 lembar kertas A4 tanpa margin berlebih, tidak melompat ke halaman kedua secara tak sengaja, siap diberi stempel basah.
                </p>
            </div>

            <!-- Fitur 6 -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-black">
                    🛠️
                </div>
                <h3 class="text-lg font-bold text-white">Pemisahan Stok Rusak &amp; Siap Sewa</h3>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Modul yang mengalami dead pixel atau kendala teknis dipisahkan otomatis ke kategori <em>"Damaged/In Maintenance"</em> dan tidak bisa terhitung sebagai stok siap sewa oleh Admin, menjamin keandalan layar saat dikirim ke acara penting.
                </p>
            </div>

        </div>
    </div>

    <!-- TAB 4: AKUN SIMULASI & UJI COBA (KHUSUS BOS & DIREKSI) -->
    <div x-show="activeTab === 'akun'" x-cloak class="space-y-6">
        <div class="bg-slate-900 border border-amber-500/40 rounded-3xl p-6 md:p-8 shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 mb-2">
                        <span>Mode Uji Coba Cepat (Direksi &amp; Owner)</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Kredensial Akun Simulasi Tiap Peran</h2>
                    <p class="text-sm text-slate-300 mt-1">
                        Gunakan akun di bawah ini untuk mencoba sistem dari sudut pandang masing-masing divisi. Seluruh akun menggunakan password yang seragam.
                    </p>
                </div>

                <div class="px-4 py-2.5 rounded-2xl bg-slate-800 border border-slate-700 text-xs text-slate-300 flex items-center gap-2">
                    <span class="text-slate-400">Password Baku Semua Akun:</span>
                    <code class="px-2 py-0.5 rounded bg-slate-950 text-amber-400 font-mono font-bold text-sm">password123</code>
                </div>
            </div>

            <!-- Table of Accounts -->
            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">Peran (Role)</th>
                            <th class="px-5 py-3.5">Nama Akun</th>
                            <th class="px-5 py-3.5">Email Login</th>
                            <th class="px-5 py-3.5">Tujuan Pengujian yang Disarankan</th>
                            <th class="px-5 py-3.5 text-center">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 bg-slate-900/60 font-normal">
                        
                        <!-- Superuser -->
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-amber-500/20 text-amber-300 font-bold">Superuser</span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-white">Super Administrator</td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-200">super@omahvisual.com</td>
                            <td class="px-5 py-4 text-xs text-slate-300">Uji akses mutlak, bypass semua menu, audit logs, kelola user &amp; database.</td>
                            <td class="px-5 py-4 text-center">
                                <button @click="copyToClipboard('super@omahvisual.com')" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 transition-all cursor-pointer">
                                    <span x-text="copiedEmail === 'super@omahvisual.com' ? 'Tersalin!' : 'Salin Email'"></span>
                                </button>
                            </td>
                        </tr>

                        <!-- Owner -->
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-amber-500/20 text-amber-300 font-bold">Owner</span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-white">Owner Omahvisual</td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-200">owner@omahvisual.com</td>
                            <td class="px-5 py-4 text-xs text-slate-300">Uji dashboard eksekutif, rekap omset, persetujuan penyesuaian stok opname.</td>
                            <td class="px-5 py-4 text-center">
                                <button @click="copyToClipboard('owner@omahvisual.com')" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 transition-all cursor-pointer">
                                    <span x-text="copiedEmail === 'owner@omahvisual.com' ? 'Tersalin!' : 'Salin Email'"></span>
                                </button>
                            </td>
                        </tr>

                        <!-- Manager Operasional -->
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-purple-500/20 text-purple-300 font-bold">Manager Ops</span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-white">Dani FT</td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-200">manager.ops@omahvisual.com</td>
                            <td class="px-5 py-4 text-xs text-slate-300">Uji alokasi armada, supir, pemilihan kru panggung, tanda tangan SPK &amp; approval QC.</td>
                            <td class="px-5 py-4 text-center">
                                <button @click="copyToClipboard('manager.ops@omahvisual.com')" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 transition-all cursor-pointer">
                                    <span x-text="copiedEmail === 'manager.ops@omahvisual.com' ? 'Tersalin!' : 'Salin Email'"></span>
                                </button>
                            </td>
                        </tr>

                        <!-- Admin LED & Finance -->
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-sky-500/20 text-sky-300 font-bold">Admin LED &amp; Keuangan</span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-white">Admin Operasional</td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-200">admin@omahvisual.com</td>
                            <td class="px-5 py-4 text-xs text-slate-300">Uji pembuatan pesanan baru, simulasi kalkulator layar, buat faktur, catat DP.</td>
                            <td class="px-5 py-4 text-center">
                                <button @click="copyToClipboard('admin@omahvisual.com')" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 transition-all cursor-pointer">
                                    <span x-text="copiedEmail === 'admin@omahvisual.com' ? 'Tersalin!' : 'Salin Email'"></span>
                                </button>
                            </td>
                        </tr>

                        <!-- Warehouse -->
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-emerald-500/20 text-emerald-300 font-bold">Warehouse</span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-white">Byan (Tim Gudang)</td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-200">warehouse@omahvisual.com</td>
                            <td class="px-5 py-4 text-xs text-slate-300">Uji verifikasi Departure Gate (loading berangkat) &amp; Return Gate (bongkar muatan).</td>
                            <td class="px-5 py-4 text-center">
                                <button @click="copyToClipboard('warehouse@omahvisual.com')" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 transition-all cursor-pointer">
                                    <span x-text="copiedEmail === 'warehouse@omahvisual.com' ? 'Tersalin!' : 'Salin Email'"></span>
                                </button>
                            </td>
                        </tr>

                        <!-- Kepala Servis -->
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-violet-500/20 text-violet-300 font-bold">Kepala Servis</span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-white">Kepala Ruang Servis</td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-200">kepala.servis@omahvisual.com</td>
                            <td class="px-5 py-4 text-xs text-slate-300">Uji delegasi tiket servis modul rusak, persetujuan spare part &amp; penutupan tiket (QC).</td>
                            <td class="px-5 py-4 text-center">
                                <button @click="copyToClipboard('kepala.servis@omahvisual.com')" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 transition-all cursor-pointer">
                                    <span x-text="copiedEmail === 'kepala.servis@omahvisual.com' ? 'Tersalin!' : 'Salin Email'"></span>
                                </button>
                            </td>
                        </tr>

                        <!-- Teknisi Servis -->
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-xs rounded-full bg-violet-500/20 text-violet-300 font-bold">Teknisi Servis</span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-white">Teknisi Workshop</td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-200">teknisi@omahvisual.com</td>
                            <td class="px-5 py-4 text-xs text-slate-300">Uji pengerjaan modul rusak, input log solder mata LED, catat IC/PSU yang diganti.</td>
                            <td class="px-5 py-4 text-center">
                                <button @click="copyToClipboard('teknisi@omahvisual.com')" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-slate-200 border border-slate-700 transition-all cursor-pointer">
                                    <span x-text="copiedEmail === 'teknisi@omahvisual.com' ? 'Tersalin!' : 'Salin Email'"></span>
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- Simulation Tips -->
            <div class="mt-6 p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col md:flex-row items-start gap-4">
                <div class="p-2.5 rounded-xl bg-amber-500/20 text-amber-400 shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-amber-300">Rekomendasi Skenario Uji Coba Cepat (End-to-End Walkthrough)</h4>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        1. Login sebagai <strong>admin@omahvisual.com</strong> &rarr; Buat pesanan baru berukuran 6×3 meter, perhatikan kalkulator kabinet otomatis.<br>
                        2. Login sebagai <strong>manager.ops@omahvisual.com</strong> &rarr; Buka SPK pesanan tersebut, plotting kru &amp; armada, lalu klik Tanda Tangan SPK.<br>
                        3. Buka link <strong>SPK Live</strong> di smartphone (atau tab incognito) &rarr; Periksa tampilan mobile kru dan coba upload foto pengetesan.<br>
                        4. Login sebagai <strong>warehouse@omahvisual.com</strong> &rarr; Coba lakukan Departure Gate dan Return Gate muatan.<br>
                        5. Login kembali sebagai <strong>super@omahvisual.com</strong> atau <strong>owner@omahvisual.com</strong> &rarr; Pantau seluruh pergerakan di Dashboard utama.
                    </p>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
