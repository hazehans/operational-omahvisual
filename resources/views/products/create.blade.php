@extends('layouts.app')

@section('title', 'Tambah Master Barang')
@section('page_title', 'Form Input Master Produk Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Back -->
    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Master Barang</span>
    </a>

    <!-- Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <h1 class="text-xl font-bold text-white mb-1">Tambah Barang / Perangkat Baru</h1>
        <p class="text-xs text-slate-400 mb-6">Daftarkan SKU produk baru ke katalog master Omah Visual LED</p>

        <form action="{{ route('products.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Grid 2 Col: Kode & Nama -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Kode Barang (SKU) *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="MISAL: CAB-P3.9-OUT"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white uppercase font-mono focus:outline-none focus:border-indigo-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Nama Barang *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Misal: Kabinet LED Videotron P3.9 Outdoor (100x50 cm)"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Grid 2 Col: Kategori & Satuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Kategori *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Satuan Dasar *</label>
                    <select name="unit_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Pilih Satuan...</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Grid 2 Col: Tracking Type & Luas m2 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Mode Pelacakan *</label>
                    <select name="tracking_type" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="quantity" {{ old('tracking_type') == 'quantity' ? 'selected' : '' }}>Kuantitas (Kabel, Baut, Material)</option>
                        <option value="individual" {{ old('tracking_type') == 'individual' ? 'selected' : '' }}>Per-Unit Serial (Processor, TV, Toolbox)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Luas m² / Unit (Khusus Kabinet)</label>
                    <input type="number" step="0.0001" name="sqm_per_unit" value="{{ old('sqm_per_unit') }}" placeholder="Contoh: 0.5000 (100x50 cm)"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Grid 2 Col: Brand & Model -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Brand / Merk</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Misal: Novastar, Samsung, dll"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Model / Seri</label>
                    <input type="text" name="model_series" value="{{ old('model_series') }}" placeholder="Misal: VX20M, VX10, AU7000"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Spesifikasi Ringkas -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Spesifikasi Tambahan</label>
                <input type="text" name="specification" value="{{ old('specification') }}" placeholder="Misal: 10 port LAN output, flightcase included, dll"
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Submit -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all">
                    Simpan Master Barang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

