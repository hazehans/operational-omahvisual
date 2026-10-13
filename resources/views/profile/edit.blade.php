@extends('layouts.app')

@section('title', 'Profil & Keamanan Akun')
@section('page_title', 'Pengaturan Profil Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <!-- Header Section -->
    <div class="p-6 md:p-8 rounded-2xl bg-linear-to-r from-slate-900 via-indigo-950/40 to-slate-900 border border-slate-800 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <!-- Avatar Display -->
            <div class="relative w-20 h-20 rounded-2xl bg-slate-950 border-2 border-indigo-500/30 overflow-hidden shrink-0 shadow-lg flex items-center justify-center">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-linear-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white text-2xl font-black">
                        {{ $user->initials }}
                    </div>
                @endif
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <h1 class="text-xl md:text-2xl font-extrabold text-white tracking-tight">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 capitalize">
                        {{ $user->roles->first()?->name ?? 'Pengguna' }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-400 font-mono">{{ $user->email }}</p>
                <div class="text-[11px] text-slate-500 mt-1">
                    Bergabung sejak {{ $user->created_at->format('d F Y') }}
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Akun Terverifikasi
            </span>
        </div>
    </div>

    <!-- Grid 2 Kolom: Edit Profil & Ganti Password -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Form 1: Edit Informasi Profil & Avatar -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="border-b border-slate-800/80 pb-4 mb-5">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Informasi Profil & Foto</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Perbarui nama tampilan, nomor kontak, dan foto avatar akun.</p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Upload & Crop Avatar -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Foto Avatar (Dapat Di-Crop 1:1)</label>
                        <div class="flex items-center gap-4 p-3.5 rounded-xl bg-slate-950/60 border border-slate-800">
                            <div class="w-16 h-16 rounded-xl border border-slate-700 bg-slate-900 overflow-hidden shrink-0 flex items-center justify-center">
                                @if($user->avatar_url)
                                    <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Preview" class="w-full h-full object-cover">
                                    <span id="avatar-placeholder" class="text-xs text-slate-500 font-bold hidden">{{ $user->initials }}</span>
                                @else
                                    <img id="avatar-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                                    <span id="avatar-placeholder" class="text-sm text-indigo-400 font-bold">{{ $user->initials }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <input type="file" name="avatar" id="avatar-input" accept="image/png,image/jpeg,image/webp"
                                    class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                                <p class="text-[10px] text-slate-500 mt-1">Pilih foto lalu atur potongan persegi di modal pop-up.</p>

                                @if($user->avatar_path)
                                    <div class="mt-2 flex items-center gap-1.5">
                                        <input type="checkbox" name="remove_avatar" id="remove_avatar" value="1"
                                            class="w-3.5 h-3.5 rounded text-rose-600 bg-slate-950 border-slate-800 focus:ring-rose-500">
                                        <label for="remove_avatar" class="text-[11px] text-rose-400 hover:text-rose-300 cursor-pointer font-medium">
                                            Hapus foto avatar saat ini
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @error('avatar')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        @error('name')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email (Readonly) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Alamat Email</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950/40 border border-slate-800 text-sm text-slate-400 font-mono cursor-not-allowed">
                        <p class="text-[10px] text-slate-500 mt-1">Email akun ini terikat ke sistem dan hanya dapat diubah oleh Superuser.</p>
                    </div>

                    <!-- Nomor WhatsApp / Telepon -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Nomor WhatsApp / Kontak</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Misal: 081234567890"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        @error('phone')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all active:scale-95">
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form 2: Ganti Password -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="border-b border-slate-800/80 pb-4 mb-5">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Keamanan & Kata Sandi</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Ubah kata sandi akun Anda secara berkala demi keamanan sistem.</p>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Password Saat Ini -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Kata Sandi Saat Ini *</label>
                        <input type="password" name="current_password" required placeholder="Masukkan kata sandi lama Anda"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500 transition-colors">
                        @error('current_password')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Kata Sandi Baru *</label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500 transition-colors">
                        @error('password')
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">Ulangi Kata Sandi Baru *</label>
                        <input type="password" name="password_confirmation" required placeholder="Ketik ulang kata sandi baru"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500 transition-colors">
                    </div>

                    <div class="p-3 rounded-xl bg-slate-950/50 border border-slate-800 text-[11px] text-slate-400 leading-relaxed">
                        <strong class="text-slate-300">Tips Keamanan:</strong> Gunakan kombinasi huruf besar, huruf kecil, angka, atau simbol agar kata sandi tidak mudah ditebak oleh pihak lain.
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-amber-600/30 transition-all active:scale-95">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Card 3: Informasi Hak Akses & Peran (Role & Permissions) -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="border-b border-slate-800/80 pb-4 mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Wewenang & Hak Akses Akun (Role Permissions)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar izin operasional yang aktif untuk peran <strong class="text-indigo-300">{{ $user->roles->first()?->name }}</strong>.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700">
                {{ $user->getAllPermissions()->count() }} Izin Terdaftar
            </span>
        </div>

        <div class="flex flex-wrap gap-2">
            @forelse($user->getAllPermissions() as $permission)
                <span class="px-2.5 py-1 rounded-lg bg-slate-950 text-slate-300 border border-slate-800 text-xs font-mono">
                    ✓ {{ $permission->name }}
                </span>
            @empty
                <span class="text-xs text-slate-500 italic">Tidak ada izin khusus yang terdaftar.</span>
            @endforelse
        </div>
    </div>

</div>

<!-- Hook Cropper untuk Avatar -->
<script>
    document.getElementById('avatar-input')?.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            openCropper({
                file: e.target.files[0],
                fileInput: e.target,
                previewImg: document.getElementById('avatar-preview'),
                placeholder: document.getElementById('avatar-placeholder'),
                aspectRatio: 1 // 1:1 Rasio Persegi untuk Avatar
            });
        }
    });
</script>
@endsection

