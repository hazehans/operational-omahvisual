@extends('layouts.app')

@section('title', 'Tambah Customer Baru')
@section('page_title', 'Tambah Customer & Klien Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('customers.index') }}" class="hover:text-indigo-600 transition-colors">Master Customer</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Tambah Baru</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tambah Customer Baru</h1>
        </div>

        <a href="{{ route('customers.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
            ← Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-slate-900/60 p-6 sm:p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Nama Sapaan & Panggilan (Ditaruh di Paling Atas UI) -->
            <div class="p-5 rounded-2xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-900/50 space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-indigo-900 dark:text-indigo-300 mb-1.5">
                        Nama Sapaan / Gelar * (Paling Atas)
                    </label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Pilih sapaan akrab resmi untuk komunikasi WhatsApp, surat penawaran, dan SPK lapangan.</p>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($salutations as $sal)
                            <label class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm font-semibold text-slate-800 dark:text-slate-200 cursor-pointer hover:border-indigo-500 has-checked:border-indigo-600 has-checked:bg-indigo-600 has-checked:text-white transition-all">
                                <input type="radio" name="salutation" value="{{ $sal }}" {{ old('salutation', 'Mas') === $sal ? 'checked' : '' }} class="hidden">
                                <span>{{ $sal }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('salutation') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Panggilan / Klien *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Misal: Edi / Hans / Dimas"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap (Sesuai KTP/Resmi)</label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Misal: Edi Purnomo, S.T."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        @error('full_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Foto Customer Upload -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    Foto Customer / Klien (Boleh Formal Maupun Santai/Lapangan)
                    <span class="text-[11px] font-normal text-slate-500 normal-case ml-2">(Opsional - JPG, PNG, WEBP, maks 4MB)</span>
                </label>
                <div class="flex items-center gap-4">
                    <div id="photo-preview-container" class="w-16 h-16 rounded-full border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 flex items-center justify-center overflow-hidden shrink-0">
                        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <img id="photo-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="photo" id="photo-input" accept="image/png,image/jpeg,image/webp"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                        <p class="text-[11px] text-slate-500 mt-1">Membantu tim PIC dan kru mengenali wajah klien saat bertemu langsung di venue acara.</p>
                    </div>
                </div>
                @error('photo') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Status Verifikasi Perusahaan -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Status Verifikasi Perusahaan</label>
                    <p class="text-xs text-slate-500 mt-0.5">Customer harus diverifikasi oleh pihak CV Han Media Tekindo sebelum order disetujui.</p>
                </div>
                <div class="flex items-center gap-3">
                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="verification_status" value="verified" {{ old('verification_status', 'verified') === 'verified' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">✓ Terverifikasi Langsung</span>
                    </label>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="verification_status" value="pending_verification" {{ old('verification_status') === 'pending_verification' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400">⏳ Pending Verifikasi</span>
                    </label>
                </div>
            </div>

            <!-- Section 2: Instansi & Kontak Utama -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">2. Organisasi & Kontak Utama</h2>
                <p class="text-xs text-slate-500">Kategori bisnis, nama vendor/perusahaan, nomor WhatsApp dan email.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Vendor / Instansi / Perusahaan</label>
                    <input type="text" name="organization" value="{{ old('organization') }}" placeholder="Misal: Evio Multimedia / Bless EO"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('organization') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Kategori Customer</label>
                    <input type="text" list="category_list" name="category" value="{{ old('category', 'Event Organizer (EO)') }}" placeholder="Pilih atau ketik kategori..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <datalist id="category_list">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </datalist>
                    @error('category') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-phone-input 
                    name="phone" 
                    :value="old('phone')" 
                    label="No Telp / WhatsApp Utama *" 
                    placeholder="818-0401-0484" 
                    :required="true" 
                    help-text="Pilih negara & ketik nomor. Misal: 812..., 0812..., atau +62812..." 
                />

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Email Customer</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="client@evio.com"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Section 3: Kontak Cadangan / Nomor Darurat -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">3. Kontak Cadangan / Nomor Darurat Lapangan</h2>
                <p class="text-xs text-slate-500">Nomor alternatif yang dapat dihubungi saat situasi darurat atau jika PIC utama tidak aktif di lokasi.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama PIC Cadangan / Darurat</label>
                    <input type="text" name="pic_secondary_name" value="{{ old('pic_secondary_name') }}" placeholder="Misal: Mbak Rina"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('pic_secondary_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <x-phone-input 
                    name="pic_secondary_phone" 
                    :value="old('pic_secondary_phone')" 
                    label="No Telp Darurat / Alternatif" 
                    placeholder="819-8765-4321" 
                    help-text="Nomor aktif darurat saat loading/gladi resik." 
                />

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Hubungan / Jabatan</label>
                    <input type="text" name="pic_secondary_relation" value="{{ old('pic_secondary_relation') }}" placeholder="Asisten / Event Director / Manager"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('pic_secondary_relation') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Section 4: Domisili & Alamat -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">4. Domisili & Alamat Vendor</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Provinsi</label>
                    <input type="text" list="province_list" name="province" value="{{ old('province', 'Jawa Timur') }}" placeholder="Pilih atau ketik provinsi..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <datalist id="province_list">
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </datalist>
                    @error('province') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Kabupaten / Kota</label>
                    <input type="text" name="city" value="{{ old('city', 'Kota Malang') }}" placeholder="Misal: Kota Malang / Surabaya"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('city') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="2" placeholder="Nama jalan, gedung, nomor kantor, RT/RW, kelurahan..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                @error('address') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Preferensi teknis klien, riwayat acara, dsb..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('customers.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all">
                    Simpan Customer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('photo-input')?.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const img = document.getElementById('photo-preview');
                img.src = evt.target.result;
                img.classList.remove('hidden');
                document.getElementById('photo-preview-container').querySelector('svg')?.classList.add('hidden');
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endsection
