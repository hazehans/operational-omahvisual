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
    <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Section 1: Identitas Customer -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">1. Data Profil Utama</h2>
                <p class="text-xs text-slate-500">Nama panggilan dan nama lengkap sesuai identitas resmi.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Customer / Panggilan *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Misal: Hans Pratama / Mas Dimas"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap Customer</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Sesuai KTP / Nama Resmi"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('full_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir Customer</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('birth_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Tanggal Registrasi Customer</label>
                    <input type="date" name="registration_date" value="{{ old('registration_date', date('Y-m-d')) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('registration_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Section 2: Instansi & Kontak -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">2. Organisasi & Kontak Utama</h2>
                <p class="text-xs text-slate-500">Kategori bisnis, nama vendor/perusahaan, nomor WhatsApp dan email.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Vendor / Instansi / Perusahaan</label>
                    <input type="text" name="organization" value="{{ old('organization') }}" placeholder="Misal: CV Berkah Abadi / Bless EO"
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
                    label="No Telp / WhatsApp *" 
                    placeholder="812-3456-7890" 
                    :required="true" 
                    help-text="Pilih negara & ketik nomor. Misal: 812..., 0812..., atau +62812..." 
                />

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Email Customer</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="client@company.com"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Section 3: Domisili & Wilayah -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">3. Domisili & Alamat Kantor</h2>
                <p class="text-xs text-slate-500">Daftar dropdown 38 provinsi di Indonesia (dapat dipilih atau diketik langsung).</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Provinsi (Dropdown Editable)</label>
                    <input type="text" list="province_list" name="province" value="{{ old('province', 'Jawa Timur') }}" placeholder="Pilih provinsi atau ketik..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <datalist id="province_list">
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}">{{ $prov }}</option>
                        @endforeach
                    </datalist>
                    @error('province') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Kabupaten / Kota (Editable)</label>
                    <input type="text" name="city" value="{{ old('city', 'Kota Malang') }}" placeholder="Misal: Kota Malang / Surabaya / Sidoarjo"
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

            <!-- Section 4: Kontak Kedua / Pendamping PIC -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">4. Kontak Cadangan / PIC ke-2</h2>
                <p class="text-xs text-slate-500">Nomor alternatif jika PIC utama tidak dapat dihubungi saat loading acara.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama PIC ke-2</label>
                    <input type="text" name="pic_secondary_name" value="{{ old('pic_secondary_name') }}" placeholder="Nama rekan / partner"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('pic_secondary_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <x-phone-input 
                    name="pic_secondary_phone" 
                    :value="old('pic_secondary_phone')" 
                    label="No Telp ke-2 PIC" 
                    placeholder="819-8765-4321" 
                    help-text="Nomor alternatif jika nomor utama tidak merespon." 
                />

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Hubungan Nomor ke-2</label>
                    <input type="text" name="pic_secondary_relation" value="{{ old('pic_secondary_relation') }}" placeholder="Asisten / Keuangan / Pasangan"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('pic_secondary_relation') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
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
@endsection
