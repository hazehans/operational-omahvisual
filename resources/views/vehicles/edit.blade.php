@extends('layouts.app')

@section('title', 'Edit Armada - ' . $vehicle->name)
@section('page_title', 'Edit Data Armada Kendaraan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('vehicles.index') }}" class="hover:text-indigo-600 transition-colors">Master Armada</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Edit Armada</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Armada: {{ $vehicle->name }}</h1>
        </div>

        <a href="{{ route('vehicles.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
            ← Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama / Panggilan Kendaraan *</label>
                    <input type="text" name="name" value="{{ old('name', $vehicle->name) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nomor Polisi (Plat)</label>
                    <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm font-mono text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('plate_number') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Jenis / Tipe Kendaraan</label>
                    <select name="type" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        @foreach(['Pick Up', 'Blind Van', 'Truk Engkel', 'Truk Double', 'Mobil Pribadi'] as $tp)
                            <option value="{{ $tp }}" {{ old('type', $vehicle->type) === $tp ? 'selected' : '' }}>{{ $tp }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Catatan Armada</label>
                    <input type="text" name="notes" value="{{ old('notes', $vehicle->notes) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">Standar Kelengkapan Kendaraan SPK</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="has_stnk" value="1" {{ old('has_stnk', $vehicle->has_stnk) ? 'checked' : '' }} class="rounded text-indigo-600">
                        <span>STNK Asli Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="has_kir" value="1" {{ old('has_kir', $vehicle->has_kir) ? 'checked' : '' }} class="rounded text-indigo-600">
                        <span>Kartu / Uji KIR</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="has_etoll" value="1" {{ old('has_etoll', $vehicle->has_etoll) ? 'checked' : '' }} class="rounded text-indigo-600">
                        <span>Kartu E-Toll</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="has_kunci_ban" value="1" {{ old('has_kunci_ban', $vehicle->has_kunci_ban) ? 'checked' : '' }} class="rounded text-indigo-600">
                        <span>Kunci Ban & Dongkrak</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer col-span-2">
                        <input type="checkbox" name="has_barcode_bbm" value="1" {{ old('has_barcode_bbm', $vehicle->has_barcode_bbm) ? 'checked' : '' }} class="rounded text-indigo-600">
                        <span>Barcode Subsidi BBM (MyPertamina)</span>
                    </label>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $vehicle->is_active) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Status Aktif & Siap Operasi Pengiriman</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('vehicles.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

