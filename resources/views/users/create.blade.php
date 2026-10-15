@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('page_title', 'Tambah Akun Pengguna & Hak Akses')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('users.index') }}" class="hover:text-indigo-400 transition-colors">Manajemen Pengguna</a>
            <span>/</span>
            <span class="text-white font-semibold">Tambah Akun Baru</span>
        </div>
        <a href="{{ route('users.index') }}" class="text-xs text-slate-400 hover:text-white transition-colors flex items-center gap-1 font-semibold">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl space-y-6">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-white">Formulir Tambah Pengguna</h1>
            <p class="text-sm text-slate-400 mt-1">Buat akun login baru untuk staf operasional, gudang, administrasi, atau teknisi servis.</p>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Identity Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Alamat Email (Username Login) <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Contoh: budi@omahvisual.com"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Phone Number -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Nomor WhatsApp / HP (Opsional)
                </label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890"
                    class="w-full md:w-1/2 px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                @error('phone')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Passwords -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Kata Sandi Akun <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" required placeholder="Minimal 8 karakter"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                    @error('password')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi di atas"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                </div>
            </div>

            <!-- Role Selection with Explanations -->
            <div class="pt-2 border-t border-slate-800 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">
                        Pilih Peran &amp; Hak Akses (Multi-Role) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-500">Centang satu atau beberapa peran sesuai tanggung jawab staf</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @php
                        $roleInfos = [
                            'Teknisi Servis' => [
                                'badge' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                'icon' => '🛠️',
                                'desc' => 'Fokus bengkel & workshop: buka tiket servis, diagnosa modul rusak, potong sparepart lemari 2, dan uji QC.',
                            ],
                            'Warehouse' => [
                                'badge' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                'icon' => '📦',
                                'desc' => 'Staf gudang fisik: pencatatan mutasi rak, penerimaan barang masuk, stok opname berkala, dan penyerahan barang.',
                            ],
                            'Admin LED' => [
                                'badge' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                'icon' => '📑',
                                'desc' => 'Administrasi rental & pesanan event: input order, kalkulator panel LED, cetak SPK lapangan & checklist gate.',
                            ],
                            'Branch Manager' => [
                                'badge' => 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',
                                'icon' => '👔',
                                'desc' => 'Manajer operasional: pengawasan seluruh gudang, pengesahan mutasi, dan peninjauan riwayat log audit.',
                            ],
                            'Owner' => [
                                'badge' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                                'icon' => '👑',
                                'desc' => 'Pemilik bisnis: otorisasi penyesuaian stok bernilai tinggi, laporan lengkap, dan supervisi seluruh modul.',
                            ],
                            'Superuser' => [
                                'badge' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                'icon' => '⚡',
                                'desc' => 'Administrator sistem penuh: akses raw database manager, manajemen akun pengguna, dan konfigurasi inti.',
                            ],
                        ];
                        $selectedRoles = old('roles', old('role') ? (array)old('role') : ['Teknisi Servis']);
                    @endphp

                    @foreach($roles as $role)
                        @php
                            $info = $roleInfos[$role->name] ?? [
                                'badge' => 'bg-slate-800 text-slate-300 border-slate-700',
                                'icon' => '👤',
                                'desc' => 'Hak akses standar sistem.',
                            ];
                            $isChecked = in_array($role->name, (array)$selectedRoles);
                        @endphp
                        <label class="relative flex items-start gap-3 p-4 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-indigo-500/50 cursor-pointer transition-all has-checked:border-indigo-500 has-checked:bg-indigo-950/20">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ $isChecked ? 'checked' : '' }} class="mt-1 rounded text-indigo-600 focus:ring-indigo-500">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">{{ $info['icon'] }}</span>
                                    <span class="font-bold text-white text-sm">{{ $role->name }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $info['badge'] }}">
                                        {{ $role->name }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 leading-relaxed">{{ $info['desc'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
                @error('roles.*')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 text-sm font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Pengguna Baru</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

