@extends('layouts.app')

@section('title', 'Edit Pengguna ' . $user->name)
@section('page_title', 'Perbarui Akun & Hak Akses Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('users.index') }}" class="hover:text-indigo-400 transition-colors">Manajemen Pengguna</a>
            <span>/</span>
            <span class="text-white font-semibold">Edit: {{ $user->name }}</span>
        </div>
        <a href="{{ route('users.index') }}" class="text-xs text-slate-400 hover:text-white transition-colors flex items-center gap-1 font-semibold">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-4 border-b border-slate-800 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-linear-to-tr from-indigo-600 to-cyan-500 text-white font-bold flex items-center justify-center text-sm shadow-md">
                    {{ $user->initials }}
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-white">{{ $user->name }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">ID: #{{ $user->id }} • Terdaftar: {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</p>
                </div>
            </div>

            <!-- Quick Reset Password Button in Edit Header -->
            <form action="{{ route('users.reset-password', $user) }}" method="POST" onsubmit="return confirm('Reset kata sandi pengguna ini ke default (password123)?')">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-bold transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span>Reset Password ke 'password123'</span>
                </button>
            </form>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Identity Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Contoh: Budi Santoso"
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
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Contoh: budi@omahvisual.com"
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
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890"
                    class="w-full md:w-1/2 px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                @error('phone')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Optional Password Change -->
            <div class="pt-4 border-t border-slate-800 space-y-3">
                <div>
                    <h3 class="text-sm font-bold text-white">Ganti Kata Sandi (Opsional)</h3>
                    <p class="text-xs text-slate-400">Kosongkan kolom di bawah jika Anda tidak ingin mengubah kata sandi pengguna ini.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                            Kata Sandi Baru
                        </label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter (opsional)"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                        @error('password')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru"
                            class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Role Selection with Explanations -->
            <div class="pt-4 border-t border-slate-800 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">
                        Pilih Peran &amp; Hak Akses (Multi-Role) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-[11px] text-slate-500">Centang satu atau beberapa peran sesuai tanggung jawab staf</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @php
                        $roleInfos = [
                            'Superuser' => [
                                'badge' => 'bg-rose-500/20 text-rose-400 border-rose-500/30',
                                'icon' => '⚡',
                                'desc' => 'Administrator sistem penuh: akses raw database manager, manajemen akun pengguna, dan konfigurasi inti.',
                            ],
                            'Owner' => [
                                'badge' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                                'icon' => '👑',
                                'desc' => 'Pemilik bisnis: otorisasi penyesuaian stok bernilai tinggi, laporan lengkap, dan supervisi seluruh modul.',
                            ],
                            'Manager Operasional' => [
                                'badge' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                                'icon' => '👔',
                                'desc' => 'Penyalur Admin & Lapangan: plotting kru & armada, pengesahan SPK resmi, approval foto QC & BAST event.',
                            ],
                            'Admin LED' => [
                                'badge' => 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',
                                'icon' => '📑',
                                'desc' => 'Administrasi rental & pesanan event: input order, kalkulator LED, layout panggung, data customer, dan SPK Live.',
                            ],
                            'Finance' => [
                                'badge' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                                'icon' => '💰',
                                'desc' => 'Keuangan & penagihan: penerbitan invoice/faktur, kuitansi pembayaran, approval honor kru & uang jalan.',
                            ],
                            'Warehouse' => [
                                'badge' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                                'icon' => '📦',
                                'desc' => 'Staf logistik gudang: checklist barang keluar & kembali (SPK Hal 2 & 3), stok opname, dan serah unit servis.',
                            ],
                            'Kepala Servis' => [
                                'badge' => 'bg-violet-500/20 text-violet-400 border-violet-500/30',
                                'icon' => '🔬',
                                'desc' => 'Supervisor ruang servis: triage modul rusak, delegasi tiket ke teknisi, approval spare part & validasi QC bengkel.',
                            ],
                            'Teknisi Servis' => [
                                'badge' => 'bg-teal-500/20 text-teal-400 border-teal-500/30',
                                'icon' => '🛠️',
                                'desc' => 'Teknisi reparasi modul di workshop: solder mata LED, ganti IC chip, PSU, dan input pencatatan sparepart.',
                            ],
                        ];
                        $selectedRoles = old('roles', old('role') ? (array)old('role') : ($userRoles ?? [$userRole]));
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
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

