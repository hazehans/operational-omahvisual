@extends('layouts.app')

@section('title', 'Tambah Armada Baru')
@section('page_title', 'Tambah Armada Kendaraan Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('vehicles.index') }}" class="hover:text-indigo-600 transition-colors">Master Armada</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Tambah Armada</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tambah Armada Kendaraan Baru</h1>
        </div>

        <a href="{{ route('vehicles.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
            ← Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Section 1: Profil Unit -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">1. Identitas Unit Kendaraan</h2>
                <p class="text-xs text-slate-500">Nama armada operasional, plat nomor polisi, dan tipe kendaraan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Kendaraan *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Misal: TRAGA 1 / GRAN MAX"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Plat Nomor Polisi</label>
                    <input type="text" name="plate_number" value="{{ old('plate_number') }}" placeholder="N 8123 AB"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('plate_number') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Jenis / Tipe Unit</label>
                    <input type="text" name="type" value="{{ old('type', 'Pick Up Box') }}" placeholder="Pick Up / Engkel / Blindvan"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Section 2: Status Ketersediaan & Kondisi Khusus -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">2. Status Ketersediaan & Kondisi Khusus</h2>
                <p class="text-xs text-slate-500">Status dinamis untuk penugasan loading event, atau jika sedang diservis/dipakai non-order.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Status Ketersediaan *</label>
                    <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500 font-semibold">
                        @foreach($statusOptions as $sKey => $sLabel)
                            <option value="{{ $sKey }}" {{ old('status', 'tersedia') === $sKey ? 'selected' : '' }}>
                                {{ $sLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Kondisi Khusus Armada</label>
                    <input type="text" name="special_condition" value="{{ old('special_condition', 'Normal (Siap Jalan)') }}" placeholder="Normal / Servis di Bengkel / Ban Bocor / Dipakai Operasional"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[10px] text-slate-400 mt-1">Catatan real-time kondisi armada saat ini.</p>
                    @error('special_condition') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Section 3: Kelengkapan Fisik & Dokumen Bawaan -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">3. Kelengkapan Standar Armada</h2>
                <p class="text-xs text-slate-500">Peralatan wajib kendaraan sebelum keberangkatan kru ke lokasi panggung.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 cursor-pointer">
                    <input type="checkbox" name="has_etoll" value="1" {{ old('has_etoll', '1') == '1' ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kartu E-Toll Siap</span>
                </label>
                <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 cursor-pointer">
                    <input type="checkbox" name="has_kunci_ban" value="1" {{ old('has_kunci_ban', '1') == '1' ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kunci Ban & Dongkrak</span>
                </label>
                <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 cursor-pointer">
                    <input type="checkbox" name="has_stnk" value="1" {{ old('has_stnk', '1') == '1' ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">STNK Asli Aktif</span>
                </label>
                <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 cursor-pointer">
                    <input type="checkbox" name="has_kir" value="1" {{ old('has_kir', '1') == '1' ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Uji KIR Berlaku</span>
                </label>
                <label class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 cursor-pointer sm:col-span-2">
                    <input type="checkbox" name="has_barcode_bbm" value="1" {{ old('has_barcode_bbm', '1') == '1' ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Barcode Subsidi BBM (MyPertamina)</span>
                </label>
            </div>

            <!-- Section 4: Foto Fisik & Dokumen Wajib -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">4. Arsip Foto Dokumen & Kendaraan</h2>
                <p class="text-xs text-slate-500">Foto fisik armada, foto STNK, foto kartu E-Toll, dan cetak barcode subsidi BBM.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Foto Fisik Kendaraan</label>
                    <input type="file" name="photo" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    @error('photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Foto STNK Kendaraan</label>
                    <input type="file" name="stnk_photo" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    @error('stnk_photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Foto Kartu E-Toll</label>
                    <input type="file" name="etoll_photo" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    @error('etoll_photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Foto Barcode BBM (MyPertamina)</label>
                    <input type="file" name="barcode_bbm_photo" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    @error('barcode_bbm_photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Notes & Status Aktif -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan Armada</label>
                <textarea name="notes" rows="2" placeholder="Jadwal ganti oli, servis berkala, riwayat baret, dsb..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Status Aktif dalam Sistem Operasional</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('vehicles.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all">
                    Simpan Armada
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
