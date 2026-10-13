@extends('layouts.app')

@section('title', 'Edit ' . $product->name)
@section('page_title', 'Perbarui Master Barang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Back -->
    <a href="{{ route('products.show', $product) }}" class="text-sm font-semibold text-slate-400 hover:text-white flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Detail Barang</span>
    </a>

    <!-- Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Perbarui Master Barang</h1>
                <p class="text-sm text-slate-400 mt-0.5">Kode SKU: <span class="font-mono text-indigo-400 font-bold">{{ $product->code }}</span></p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 self-start sm:self-auto">
                Mode: {{ $product->tracking_type === 'individual' ? 'Per-Unit Serial' : 'Stok Kuantitas' }}
            </span>
        </div>

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Foto Barang Upload & Preview -->
            <div class="p-4 rounded-xl bg-slate-950/50 border border-slate-800/80">
                <label class="block text-sm font-semibold text-slate-200 mb-2">
                    Foto Produk / Perangkat
                    <span class="text-xs font-normal text-slate-400 ml-2">(Opsional - format JPG, PNG, WEBP, maks 4MB)</span>
                </label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div id="preview-container" class="w-24 h-24 rounded-xl border-2 border-dashed border-slate-700 bg-slate-900/60 flex items-center justify-center overflow-hidden shrink-0 relative group">
                        @if($product->image_path)
                            <img id="image-preview" src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                            <svg id="preview-placeholder" class="w-8 h-8 text-slate-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @else
                            <svg id="preview-placeholder" class="w-8 h-8 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover hidden" />
                        @endif
                    </div>
                    <div class="flex-1 w-full space-y-2">
                        <input type="file" name="image" id="image-input" accept="image/png,image/jpeg,image/webp"
                            class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                        @if($product->image_path)
                            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-rose-400 hover:text-rose-300">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-700 bg-slate-900 text-rose-600 focus:ring-rose-500">
                                <span>Hapus foto yang saat ini terpasang</span>
                            </label>
                        @endif
                    </div>
                </div>
                @error('image')
                    <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nama Barang -->
            <div>
                <label class="block text-sm font-semibold text-slate-200 mb-1.5">Nama Barang *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                @error('name')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grid 2 Col: Kategori & Satuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Kategori *</label>
                    <select name="category_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Satuan Dasar *</label>
                    <select name="unit_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id', $product->unit_id) == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->symbol }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Grid 2 Col: Brand & Model -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Brand / Merk</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Model / Seri</label>
                    <input type="text" name="model_series" value="{{ old('model_series', $product->model_series) }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                </div>
            </div>

            <!-- Grid 2 Col: Luas m2 & Min Stock -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Luas m² / Unit (Khusus Kabinet)</label>
                    <input type="number" step="0.0001" name="sqm_per_unit" value="{{ old('sqm_per_unit', $product->sqm_per_unit) }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Batas Minimum Stok Alert</label>
                    <input type="number" step="0.01" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                </div>
            </div>

            <!-- Spesifikasi Tambahan -->
            <div>
                <label class="block text-sm font-semibold text-slate-200 mb-1.5">Spesifikasi Tambahan</label>
                <input type="text" name="specification" value="{{ old('specification', $product->specification) }}"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
            </div>

            <!-- Catatan Khusus -->
            <div>
                <label class="block text-sm font-semibold text-slate-200 mb-1.5">Catatan Khusus</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">{{ old('notes', $product->notes) }}</textarea>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-800/80">
                <a href="{{ route('products.show', $product) }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('image-input')?.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            openCropper({
                file: e.target.files[0],
                fileInput: e.target,
                previewImg: document.getElementById('image-preview'),
                placeholder: document.getElementById('preview-placeholder'),
                aspectRatio: 4 / 3 // 4:3 Proporsional untuk Modul / Hardware
            });
        }
    });
</script>
@endsection
