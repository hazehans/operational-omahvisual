@extends('layouts.app')

@section('title', 'Tambah Unit Aset Serial Baru')
@section('page_title', 'Registrasi Unit Aset Serial')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Back -->
    <a href="{{ route('assets.index') }}" class="text-sm font-semibold text-slate-400 hover:text-white flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Daftar Aset Serial</span>
    </a>

    <!-- Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Registrasi Unit Aset Serial Baru</h1>
            <p class="text-sm text-slate-400 mt-1">Daftarkan unit fisik baru untuk perangkat individual (Video Processor, TV, Tool Box, dll).</p>
        </div>

        <form action="{{ route('assets.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Product Selection -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Jenis Perangkat / Master Barang <span class="text-rose-500">*</span>
                </label>
                <select name="product_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                    <option value="">Pilih Jenis Perangkat Serial...</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ old('product_id', $selectedProductId) == $prod->id ? 'selected' : '' }}>
                            {{ $prod->code }} — {{ $prod->name }} ({{ $prod->brand ?? 'No Brand' }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grid 2 Col: Asset Code & Serial Number -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Kode Aset Internal
                        <span class="text-xs font-normal text-slate-500 lowercase">(kosongkan untuk auto-generate)</span>
                    </label>
                    <input type="text" name="asset_code" value="{{ old('asset_code') }}" placeholder="Contoh: OVL-AST-202610-0026"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white font-mono placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-all">
                    @error('asset_code')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Nomor Seri Pabrik (Serial Number)
                    </label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="Contoh: SN-NOVA-VX20M-98765"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white font-mono placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-all">
                    @error('serial_number')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Grid 2 Col: Barcode & Lokasi Penyimpanan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Barcode / QR Scanner (Opsional)
                    </label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Scan barcode fisik disini"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white font-mono placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-all">
                    @error('barcode')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Lokasi / Lemari Penyimpanan <span class="text-rose-500">*</span>
                    </label>
                    <select name="current_location_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                        <option value="">Pilih Titik Lokasi Penyimpanan...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('current_location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('current_location_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Grid 3 Col: Kepemilikan, Kondisi, Status -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Kepemilikan <span class="text-rose-500">*</span>
                    </label>
                    <select name="ownership" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                        <option value="company" {{ old('ownership', 'company') == 'company' ? 'selected' : '' }}>Aset Omah Visual</option>
                        <option value="external" {{ old('ownership') == 'external' ? 'selected' : '' }}>Vendor Rekanan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Kondisi Fisik <span class="text-rose-500">*</span>
                    </label>
                    <select name="condition" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                        <option value="available" {{ old('condition', 'available') == 'available' ? 'selected' : '' }}>Bagus (Siap Pakai)</option>
                        <option value="damaged" {{ old('condition') == 'damaged' ? 'selected' : '' }}>Rusak</option>
                        <option value="in_service" {{ old('condition') == 'in_service' ? 'selected' : '' }}>Dalam Servis</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Status Posisi <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                        <option value="in_storage" {{ old('status', 'in_storage') == 'in_storage' ? 'selected' : '' }}>Di Gudang</option>
                        <option value="deployed" {{ old('status') == 'deployed' ? 'selected' : '' }}>Dipakai Event</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Hilang / Tertinggal</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Catatan Unit / Riwayat Pembelian
                </label>
                <textarea name="notes" rows="2" placeholder="Contoh: Unit pembelian pengadaan baru batch Oktober 2026, kondisi mulus lengkap dengan hardcase..."
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-all">{{ old('notes') }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('assets.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 text-sm font-semibold transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Daftarkan Unit Aset</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

