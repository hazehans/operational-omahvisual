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
                <p class="text-xs text-slate-500">Foto fisik armada, foto STNK, foto kartu E-Toll, dan barcode subsidi BBM dengan fitur potong/crop.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2 bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Foto Fisik Kendaraan</label>
                    <div class="relative w-full h-32 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                        <img id="veh_photo_preview" src="" alt="Pratinjau Armada" class="w-full h-full object-cover hidden">
                        <div id="veh_photo_placeholder" class="text-center p-2 text-slate-400">
                            <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            <span class="text-[9px]">16:9 Landscape Armada</span>
                        </div>
                    </div>
                    <input type="file" name="photo" id="veh_photo_input" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] text-slate-400">Crop Otomatis 16:9</p>
                        <button type="button" id="crop_veh_photo_btn" class="hidden text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">✂️ Crop Ulang</button>
                    </div>
                    @error('photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2 bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Foto STNK Kendaraan</label>
                    <div class="relative w-full h-32 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                        <img id="veh_stnk_preview" src="" alt="Pratinjau STNK" class="w-full h-full object-cover hidden">
                        <div id="veh_stnk_placeholder" class="text-center p-2 text-slate-400">
                            <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-[9px]">3:2 Rasio STNK</span>
                        </div>
                    </div>
                    <input type="file" name="stnk_photo" id="veh_stnk_input" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] text-slate-400">Crop Otomatis 3:2</p>
                        <button type="button" id="crop_veh_stnk_btn" class="hidden text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">✂️ Crop Ulang</button>
                    </div>
                    @error('stnk_photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2 bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Foto Kartu E-Toll</label>
                    <div class="relative w-full h-32 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                        <img id="veh_etoll_preview" src="" alt="Pratinjau E-Toll" class="w-full h-full object-cover hidden">
                        <div id="veh_etoll_placeholder" class="text-center p-2 text-slate-400">
                            <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="text-[9px]">3:2 Rasio Kartu</span>
                        </div>
                    </div>
                    <input type="file" name="etoll_photo" id="veh_etoll_input" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] text-slate-400">Crop Otomatis 3:2</p>
                        <button type="button" id="crop_veh_etoll_btn" class="hidden text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">✂️ Crop Ulang</button>
                    </div>
                    @error('etoll_photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2 bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Foto Barcode BBM (MyPertamina)</label>
                    <div class="relative w-full h-32 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                        <img id="veh_bbm_preview" src="" alt="Pratinjau Barcode BBM" class="w-full h-full object-cover hidden">
                        <div id="veh_bbm_placeholder" class="text-center p-2 text-slate-400">
                            <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span class="text-[9px]">1:1 Kotak QR / Bebas</span>
                        </div>
                    </div>
                    <input type="file" name="barcode_bbm_photo" id="veh_bbm_input" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] text-slate-400">Crop Kotak 1:1</p>
                        <button type="button" id="crop_veh_bbm_btn" class="hidden text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">✂️ Crop Ulang</button>
                    </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.setupImageCropper === 'function') {
            window.setupImageCropper('veh_photo_input', 'veh_photo_preview', 'veh_photo_placeholder', 16/9, 'crop_veh_photo_btn');
            window.setupImageCropper('veh_stnk_input', 'veh_stnk_preview', 'veh_stnk_placeholder', 3/2, 'crop_veh_stnk_btn');
            window.setupImageCropper('veh_etoll_input', 'veh_etoll_preview', 'veh_etoll_placeholder', 3/2, 'crop_veh_etoll_btn');
            window.setupImageCropper('veh_bbm_input', 'veh_bbm_preview', 'veh_bbm_placeholder', 1, 'crop_veh_bbm_btn');
        }
    });
</script>
@endsection
