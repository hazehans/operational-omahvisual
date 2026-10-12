@extends('layouts.app')

@section('title', 'Edit ' . $product->name)
@section('page_title', 'Perbarui Master Barang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Back -->
    <a href="{{ route('products.show', $product) }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Detail Barang</span>
    </a>

    <!-- Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-white">Perbarui Master Barang</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kode Barang: <span class="font-mono text-indigo-400 font-bold">{{ $product->code }}</span></p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                Mode: {{ $product->tracking_type }}
            </span>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Barang -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Nama Barang *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Grid 2 Col: Kategori & Satuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Kategori *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Satuan Dasar *</label>
                    <select name="unit_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id', $product->unit_id) == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Grid 2 Col: Brand & Model -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Brand / Merk</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Model / Seri</label>
                    <input type="text" name="model_series" value="{{ old('model_series', $product->model_series) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Luas m2 -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Luas m² / Unit (Khusus Kabinet)</label>
                <input type="number" step="0.0001" name="sqm_per_unit" value="{{ old('sqm_per_unit', $product->sqm_per_unit) }}"
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Spesifikasi Tambahan -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Spesifikasi Tambahan</label>
                <input type="text" name="specification" value="{{ old('specification', $product->specification) }}"
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Submit -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('products.show', $product) }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

