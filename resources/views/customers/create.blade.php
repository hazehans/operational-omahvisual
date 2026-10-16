@extends('layouts.app')

@section('title', 'Tambah Customer Baru')
@section('page_title', 'Tambah Customer & Klien Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
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
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Customer / PIC *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Misal: Hans Pratama / Mas Dimas"
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Organisasi / Vendor EO / Instansi</label>
                <input type="text" name="organization" value="{{ old('organization') }}" placeholder="Misal: CV Berkah Abadi / Bless EO / Personal"
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                @error('organization') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp *</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="081234567890"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Kota / Domisili</label>
                    <input type="text" name="city" value="{{ old('city', 'Malang') }}" placeholder="Malang / Surabaya / Batu"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('city') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Alamat Kantor / Domisili</label>
                <textarea name="address" rows="2" placeholder="Alamat lengkap..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                @error('address') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Preferensi teknis klien, PIC pendamping, dsb..."
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

