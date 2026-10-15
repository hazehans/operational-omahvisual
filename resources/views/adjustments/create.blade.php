@extends('layouts.app')

@section('title', 'Ajukan Koreksi Stok')
@section('page_title', 'Formulir Pengajuan Penyesuaian Stok')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Back -->
    <a href="{{ route('adjustments.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Daftar Penyesuaian</span>
    </a>

    <!-- Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <h1 class="text-xl font-bold text-white mb-1">Pengajuan Penyesuaian Stok Fisik</h1>
        <p class="text-xs text-slate-400 mb-6">Ajukan koreksi jika terdapat selisih hitung fisik di lemari atau barang rusak/hilang pasca-event</p>

        <form action="{{ route('adjustments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Lokasi & Barang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Lokasi / Lemari *</label>
                    <select name="location_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Pilih Titik Lokasi...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', request('location_id')) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Barang / SKU *</label>
                    <select name="product_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Pilih Barang...</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ old('product_id', request('product_id')) == $prod->id ? 'selected' : '' }}>
                                {{ $prod->code }} — {{ $prod->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Grid 3 Col: Kondisi, Kepemilikan, Qty Fisik Temuan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Kondisi Fisik *</label>
                    <select name="condition" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="available" {{ old('condition') == 'available' ? 'selected' : '' }}>Bagus (Siap)</option>
                        <option value="damaged" {{ old('condition') == 'damaged' ? 'selected' : '' }}>Rusak</option>
                        <option value="in_service" {{ old('condition') == 'in_service' ? 'selected' : '' }}>Dalam Servis</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Kepemilikan *</label>
                    <select name="ownership" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="company" {{ old('ownership') == 'company' ? 'selected' : '' }}>Aset Omah Visual</option>
                        <option value="external" {{ old('ownership') == 'external' ? 'selected' : '' }}>Vendor Rekanan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Jumlah Fisik Sebenarnya *</label>
                    <input type="number" step="0.01" name="adjusted_quantity" value="{{ old('adjusted_quantity') }}" required min="0" placeholder="0.00"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500 font-mono">
                </div>
            </div>

            <!-- Alasan Koreksi -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Alasan Penyesuaian (Wajib) *</label>
                <textarea name="reason" rows="3" required placeholder="Contoh: Ditemukan 1 kabel power jumper terbakar soketnya saat demobilisasi event di Tentrem, perlu dialihkan ke kondisi rusak..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">{{ old('reason') }}</textarea>
            </div>

            <!-- Bukti Foto -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Lampiran Foto Bukti Fisik (Opsional)</label>
                <input type="file" name="proof_attachment" accept="image/*,.pdf"
                    class="w-full px-3.5 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200">
            </div>

            <!-- Notice -->
            <div class="p-3.5 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-xs text-indigo-300">
                <span class="font-bold">Perhatian:</span> Pengajuan ini tidak akan langsung mengubah saldo resmi. Permintaan akan diteruskan ke akun Owner untuk peninjauan dan persetujuan.
            </div>

            <!-- Submit -->
            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('adjustments.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all">
                    Kirim Pengajuan ke Owner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

