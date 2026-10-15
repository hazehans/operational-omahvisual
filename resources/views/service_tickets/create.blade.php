@extends('layouts.app')

@section('page_title', 'Buka Tiket Servis Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('service-tickets.index') }}" class="hover:text-indigo-400 transition-colors">Servis & Workshop</a>
            <span>/</span>
            <span class="text-slate-200 font-semibold">Buka Tiket Baru</span>
        </div>
        <a href="{{ route('service-tickets.index') }}" class="text-xs text-slate-400 hover:text-slate-200 transition-colors flex items-center gap-1 font-semibold">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <!-- Main Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl space-y-6">
        <div>
            <h1 class="text-xl md:text-2xl font-black text-white">Formulir Karantina & Tiket Servis</h1>
            <p class="text-sm text-slate-400 mt-1">Barang yang didaftarkan ke tiket ini akan otomatis dipindahkan ke area Workshop dan saldo siap sewanya dikunci sementara.</p>
        </div>

        <form action="{{ route('service-tickets.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Product Selection -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Pilih Produk / Barang <span class="text-rose-500">*</span>
                </label>
                <select name="product_id" id="product_id" required onchange="handleProductChange(this)" class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white focus:outline-none focus:border-indigo-500">
                    <option value="">-- Pilih Jenis Barang --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-tracking="{{ $p->tracking_type }}" {{ (old('product_id', $preselectedProduct->id ?? '') == $p->id) ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->code }}) — {{ $p->tracking_type === 'individual' ? 'Unit Serial Individual' : 'Stok Kuantitas' }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Asset Specific Selection (Conditional for Individual) -->
            <div id="asset_section" class="p-4 rounded-xl bg-indigo-950/30 border border-indigo-900/50 space-y-2 {{ (old('product_id', $preselectedProduct->tracking_type ?? '') === 'individual') ? '' : 'hidden' }}">
                <label class="block text-xs font-bold uppercase tracking-wider text-indigo-400 mb-1">
                    Pilih Unit Aset Bernomor Seri <span class="text-rose-500">*</span>
                </label>
                <select name="asset_id" id="asset_id" class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-900 border border-slate-800 text-white focus:outline-none focus:border-indigo-500">
                    <option value="">-- Pilih Unit Aset --</option>
                    @foreach($assets as $a)
                        <option value="{{ $a->id }}" data-product="{{ $a->product_id }}" data-location="{{ $a->current_location_id }}" {{ (old('asset_id', $preselectedAsset->id ?? '') == $a->id) ? 'selected' : '' }}>
                            {{ $a->asset_code }} {{ $a->serial_number ? '(SN: ' . $a->serial_number . ')' : '' }} — {{ $a->product->name }} [Lokasi: {{ $a->currentLocation->name ?? 'Gudang' }}]
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400">Unit individual akan otomatis berganti status menjadi <strong>Maintenance / In Service</strong>.</p>
                @error('asset_id')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity (For Bulk Products) -->
            <div id="quantity_section" class="{{ (old('product_id', $preselectedProduct->tracking_type ?? '') === 'individual') ? 'hidden' : '' }}">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Jumlah Barang Rusak / Masuk Servis <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                    <input type="number" step="0.01" min="0.01" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" required class="w-40 px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white font-mono font-bold focus:outline-none focus:border-indigo-500">
                    <span class="text-sm text-slate-400">Unit / Pcs</span>
                </div>
                @error('quantity')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location Routing -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Source Location -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Lokasi Asal (Tempat Barang Diambil) <span class="text-rose-500">*</span>
                    </label>
                    <select name="source_location_id" id="source_location_id" required class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white focus:outline-none focus:border-indigo-500">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ (old('source_location_id', $preselectedAsset->current_location_id ?? '') == $loc->id) ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('source_location_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Workshop Location -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Tujuan Bengkel Servis <span class="text-rose-500">*</span>
                    </label>
                    <select name="workshop_location_id" id="workshop_location_id" required class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white focus:outline-none focus:border-indigo-500">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ (old('workshop_location_id', $defaultWorkshop->id ?? '') == $loc->id) ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('workshop_location_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Priority & Technician -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Tingkat Prioritas <span class="text-rose-500">*</span>
                    </label>
                    <select name="priority" required class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white focus:outline-none focus:border-indigo-500">
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>🔵 Normal (Jadwal Reguler)</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>🟠 Tinggi (Persiapan Event Minggu Ini)</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>🔴 Darurat (Krusial / Segera Berangkat)</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>⚪ Rendah (Unit Cadangan / Santai)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                        Teknisi Penanggung Jawab (Opsional)
                    </label>
                    <select name="technician_id" class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Belum Ditugaskan / Bebas Antrean --</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ old('technician_id') == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }} ({{ $tech->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Issue Description -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Deskripsi Masalah / Gejala Kerusakan <span class="text-rose-500">*</span>
                </label>
                <textarea name="issue_description" rows="3" required placeholder="Contoh: Modul mati 5 titik lampu merah di baris 4. IC driver ICN2053 tampak panas berlebih saat tes pola warna putih..." class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 leading-relaxed">{{ old('issue_description') }}</textarea>
                @error('issue_description')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Catatan Tambahan (Opsional)
                </label>
                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Contoh: Ditemukan pasca-event di Grand Mercure, kotak flightcase no 3" class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('service-tickets.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-700 text-sm font-semibold text-slate-300 hover:bg-slate-800 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Terbitkan Tiket &amp; Pindahkan ke Workshop</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function handleProductChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    const tracking = selectedOption.getAttribute('data-tracking');
    const assetSection = document.getElementById('asset_section');
    const quantitySection = document.getElementById('quantity_section');
    const assetSelect = document.getElementById('asset_id');

    if (tracking === 'individual') {
        assetSection.classList.remove('hidden');
        quantitySection.classList.add('hidden');
        document.getElementById('quantity').value = 1;
        assetSelect.setAttribute('required', 'required');

        // Filter asset options based on chosen product
        const productId = select.value;
        Array.from(assetSelect.options).forEach(opt => {
            if (!opt.value) return;
            const pId = opt.getAttribute('data-product');
            if (pId === productId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    } else {
        assetSection.classList.add('hidden');
        quantitySection.classList.remove('hidden');
        assetSelect.removeAttribute('required');
        assetSelect.value = '';
    }
}

// Auto-fill source location when asset is picked
document.getElementById('asset_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const locId = selected.getAttribute('data-location');
    if (locId) {
        document.getElementById('source_location_id').value = locId;
    }
});
</script>
@endsection

