@extends('layouts.app')

@section('title', 'Edit ' . $location->name)
@section('page_title', 'Edit Titik Lokasi / Lemari Penyimpanan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Back Button -->
    <a href="{{ route('locations.show', $location) }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Detail Lokasi</span>
    </a>

    <!-- Main Card Form -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl">
        <div class="border-b border-slate-800/80 pb-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-white">Edit Titik Lokasi & Foto</h1>
                <p class="text-xs text-slate-400 mt-1">Perbarui data atau foto fisik lemari/area: <span class="text-white font-semibold">{{ $location->name }}</span></p>
            </div>
            <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-lg border border-indigo-500/20 self-start sm:self-auto">
                {{ $location->code }}
            </span>
        </div>

        <form action="{{ route('locations.update', $location) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Upload / Ganti Foto Lokasi -->
            <div class="p-5 rounded-2xl bg-slate-950/40 border border-slate-800/80">
                <label class="block text-sm font-semibold text-slate-200 mb-2">
                    Foto Visual Titik Lokasi / Lemari
                </label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <div id="preview-container" class="w-36 h-28 rounded-xl border border-slate-700 bg-slate-900/60 flex items-center justify-center overflow-hidden shrink-0 shadow-md">
                        @if($location->image_url)
                            <img id="image-preview" src="{{ $location->image_url }}" alt="{{ $location->name }}" class="w-full h-full object-cover" />
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
                    <div class="flex-1 w-full space-y-2.5">
                        <input type="file" name="image" id="image-input" accept="image/png,image/jpeg,image/webp"
                            class="block w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer">
                        <p class="text-xs text-slate-500">Pilih berkas gambar baru untuk mengganti foto visual lokasi (JPG, PNG, WebP max 4MB).</p>

                        @if($location->image_path)
                            <div class="pt-2 flex items-center gap-2">
                                <input type="checkbox" name="remove_image" id="remove_image" value="1"
                                    class="w-4 h-4 rounded text-rose-600 bg-slate-950 border-slate-800 focus:ring-rose-500">
                                <label for="remove_image" class="text-xs text-rose-400 hover:text-rose-300 cursor-pointer font-medium">
                                    Hapus foto lokasi ini (kembali ke ikon visual standar)
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
                @error('image')
                    <p class="text-xs text-rose-400 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grid 2 Col: Kode & Nama -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Kode Lokasi *</label>
                    <input type="text" name="code" value="{{ old('code', $location->code) }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white uppercase font-mono focus:outline-none focus:border-indigo-500 transition-colors">
                    @error('code')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Nama Titik / Lemari *</label>
                    <input type="text" name="name" value="{{ old('name', $location->name) }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                    @error('name')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Grid 2 Col: Tipe & Lokasi Induk -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Tipe Area / Penyimpanan *</label>
                    <select name="type" required class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        <option value="cabinet" {{ old('type', $location->type) == 'cabinet' ? 'selected' : '' }}>Lemari (Cabinet)</option>
                        <option value="shelf" {{ old('type', $location->type) == 'shelf' ? 'selected' : '' }}>Rak Bertingkat (Shelf)</option>
                        <option value="rack" {{ old('type', $location->type) == 'rack' ? 'selected' : '' }}>Rack Server / Flightcase Rack</option>
                        <option value="staging_area" {{ old('type', $location->type) == 'staging_area' ? 'selected' : '' }}>Area Staging / Lantai Gudang</option>
                        <option value="locker" {{ old('type', $location->type) == 'locker' ? 'selected' : '' }}>Locker / Workshop</option>
                        <option value="bin" {{ old('type', $location->type) == 'bin' ? 'selected' : '' }}>Kotak Box / Bin</option>
                        <option value="room" {{ old('type', $location->type) == 'room' ? 'selected' : '' }}>Ruangan Khusus (Room)</option>
                        <option value="building" {{ old('type', $location->type) == 'building' ? 'selected' : '' }}>Gedung / Cabang (Building)</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-200 mb-1.5">Lokasi Induk (Gedung)</label>
                    <select name="parent_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                        <option value="">Tidak ada (Lokasi Tingkat Atas)</option>
                        @foreach($parentLocations as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $location->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }} ({{ $parent->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-semibold text-slate-200 mb-1.5">Deskripsi & Peruntukan Barang</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">{{ old('description', $location->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $location->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                    class="w-4 h-4 rounded text-indigo-600 bg-slate-950 border-slate-800 focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-medium text-slate-200 cursor-pointer">
                    Titik Lokasi Aktif & Siap Digunakan Menampung Barang
                </label>
            </div>

            <!-- Actions Submit -->
            <div class="pt-4 flex items-center justify-between border-t border-slate-800/80">
                <div>
                    @if($location->assets->count() === 0 && $location->stockBalances->where('quantity', '>', 0)->count() === 0)
                        <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus titik lokasi ini?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 text-xs font-semibold transition-all">
                                Hapus Lokasi
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-slate-500 italic">Lokasi menampung barang fisik (tidak dapat dihapus)</span>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('locations.show', $location) }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all">
                        Perbarui Titik Lokasi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('image-input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const previewImg = document.getElementById('image-preview');
                const placeholder = document.getElementById('preview-placeholder');
                previewImg.src = evt.target.result;
                previewImg.classList.remove('hidden');
                placeholder?.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
