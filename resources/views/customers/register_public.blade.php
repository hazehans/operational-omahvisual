<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Klien &amp; Customer — Omah Visual LED</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto space-y-6">

        <!-- Header Branding -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 mb-2 shadow-lg shadow-indigo-600/20">
                <span class="text-2xl font-black">OV</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                Registrasi Klien &amp; Vendor Event
            </h1>
            <p class="text-sm text-slate-400 max-w-md mx-auto">
                CV Han Media Tekindo — Omah Visual LED. Daftarkan profil Anda untuk koordinasi teknis layar LED panggung dan kelancaran SPK acara.
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
            <form action="{{ route('customers.public-register.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Section: Nama Sapaan (Di Paling Atas) -->
                <div class="p-5 rounded-2xl bg-indigo-950/40 border border-indigo-500/30 space-y-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-indigo-300 mb-1">
                            Nama Sapaan Akrab *
                        </label>
                        <p class="text-xs text-slate-400 mb-3">Bagaimana tim kami menyapa Anda saat koordinasi teknis dan di lokasi acara?</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($salutations as $sal)
                                <label class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-700 bg-slate-900 text-sm font-semibold text-slate-200 cursor-pointer hover:border-indigo-500 has-checked:border-indigo-500 has-checked:bg-indigo-600 has-checked:text-white transition-all">
                                    <input type="radio" name="salutation" value="{{ $sal }}" {{ old('salutation', 'Mas') === $sal ? 'checked' : '' }} class="hidden">
                                    <span>{{ $sal }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('salutation') <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Nama Panggilan *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Misal: Edi / Hans"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                            @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Nama Lengkap (Opsional)</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Sesuai KTP"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Foto Klien -->
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">
                        Foto Profil / Klien (Boleh Formal atau Santai)
                        <span class="text-[11px] font-normal text-slate-500 ml-2">(Opsional)</span>
                    </label>
                    <input type="file" name="photo" accept="image/png,image/jpeg,image/webp"
                        class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                    <p class="text-[11px] text-slate-500 mt-1">Membantu PIC lapangan kami mengenali wajah Anda saat tiba di lokasi panggung.</p>
                    @error('photo') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Organisasi & Kategori -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Nama Vendor / EO / Perusahaan</label>
                        <input type="text" name="organization" value="{{ old('organization') }}" placeholder="Misal: Evio Multimedia / Bless EO"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Kategori Kegiatan</label>
                        <select name="category" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- No WhatsApp Utama -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">No WhatsApp Utama *</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 081804010484"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        @error('phone') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Email (Opsional)</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@domain.com"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>
                </div>

                <!-- Kontak Cadangan / Darurat -->
                <div class="p-4 rounded-xl bg-amber-950/20 border border-amber-500/30 space-y-3">
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-amber-300 mb-0.5">🚨 Kontak Cadangan / Nomor Darurat Lapangan</span>
                        <p class="text-xs text-slate-400">Nomor rekan/asisten jika nomor utama Anda sedang tidak aktif saat loading tengah malam atau gladi resik.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Nama Rekan Darurat</label>
                            <input type="text" name="pic_secondary_name" value="{{ old('pic_secondary_name') }}" placeholder="Misal: Mbak Rina"
                                class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">No WhatsApp Darurat</label>
                            <input type="text" name="pic_secondary_phone" value="{{ old('pic_secondary_phone') }}" placeholder="081987654321"
                                class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Hubungan / Peran</label>
                            <input type="text" name="pic_secondary_relation" value="{{ old('pic_secondary_relation') }}" placeholder="Asisten / Manager"
                                class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Domisili & Alamat -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Provinsi</label>
                        <input type="text" list="province_list" name="province" value="{{ old('province', 'Jawa Timur') }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500">
                        <datalist id="province_list">
                            @foreach($provinces as $prov)
                                <option value="{{ $prov }}">{{ $prov }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Kabupaten / Kota</label>
                        <input type="text" name="city" value="{{ old('city', 'Kota Malang') }}" placeholder="Kota domisili"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Alamat Kantor / Domisili</label>
                    <textarea name="address" rows="2" placeholder="Nama jalan, gedung, nomor..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500">{{ old('address') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-800">
                    <button type="submit" class="w-full py-3.5 px-6 rounded-2xl bg-linear-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white font-bold text-sm tracking-wide shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
                        <span>Daftarkan Data Klien</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                    <p class="text-center text-[11px] text-slate-500 mt-2">Data Anda akan diverifikasi secara aman oleh tim operasional Omah Visual LED.</p>
                </div>
            </form>
        </div>

        <div class="text-center text-xs text-slate-600">
            &copy; {{ date('Y') }} CV Han Media Tekindo — Omah Visual LED Operational System.
        </div>
    </div>
</body>
</html>
