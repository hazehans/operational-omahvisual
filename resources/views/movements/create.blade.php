@extends('layouts.app')

@section('title', 'Catat Mutasi Stok')
@section('page_title', 'Formulir Transaksi Mutasi Stok')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Back -->
    <a href="{{ route('movements.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Riwayat Mutasi</span>
    </a>

    <!-- Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <h1 class="text-xl font-bold text-white mb-1">Catat Transaksi Mutasi Barang</h1>
        <p class="text-xs text-slate-400 mb-6">Penerimaan barang baru, pengeluaran event, atau pemindahan antar lemari</p>

        <form action="{{ route('movements.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Jenis Transaksi -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Jenis Transaksi *</label>
                <select name="type" id="type-select" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="transfer" {{ old('type') == 'transfer' ? 'selected' : '' }}>Transfer Antar Lemari / Lokasi</option>
                    <option value="inbound" {{ old('type') == 'inbound' ? 'selected' : '' }}>Barang Masuk (Inbound / Pengadaan)</option>
                    <option value="outbound" {{ old('type') == 'outbound' ? 'selected' : '' }}>Barang Keluar (Outbound / Operasional)</option>
                </select>
            </div>

            <!-- Grid 2 Col: Asal & Tujuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Lokasi Asal</label>
                    <select name="source_location_id" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Pilih Lokasi Asal...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('source_location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Lokasi Tujuan</label>
                    <select name="destination_location_id" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Pilih Lokasi Tujuan...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Pilih Barang -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Barang / Komponen *</label>
                <select name="product_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                    <option value="">Pilih Barang...</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                            {{ $prod->code }} — {{ $prod->name }} ({{ $prod->unit->symbol ?? 'unit' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Grid 3 Col: Qty, Kondisi, Kepemilikan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Jumlah Mutasi *</label>
                    <input type="number" step="0.01" name="quantity" value="{{ old('quantity', 1) }}" required min="0.01"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500 font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Kondisi Fisik *</label>
                    <select name="condition" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="available" {{ old('condition') == 'available' ? 'selected' : '' }}>Bagus (Siap)</option>
                        <option value="damaged" {{ old('condition') == 'damaged' ? 'selected' : '' }}>Rusak</option>
                        <option value="in_service" {{ old('condition') == 'in_service' ? 'selected' : '' }}>Servis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Kepemilikan *</label>
                    <select name="ownership" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="company" {{ old('ownership') == 'company' ? 'selected' : '' }}>Aset Omah Visual</option>
                        <option value="external" {{ old('ownership') == 'external' ? 'selected' : '' }}>Vendor Rekanan</option>
                    </select>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Catatan / Referensi Surat Jalan</label>
                <textarea name="notes" rows="2" placeholder="Misal: Pindahan kabel jumper dari Lemari 2 ke Staging untuk event Sabtu..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('movements.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all">
                    Eksekusi Mutasi Stok
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

