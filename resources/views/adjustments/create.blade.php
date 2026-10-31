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
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl"
        x-data="adjustmentForm({
            selectedLocation: '{{ old('location_id', request('location_id', '')) }}',
            selectedProduct: '{{ old('product_id', request('product_id', '')) }}',
            condition: '{{ old('condition', request('condition', 'available')) }}',
            ownership: '{{ old('ownership', request('ownership', 'company')) }}',
            adjustedQty: '{{ old('adjusted_quantity', request('adjusted_quantity', request('quantity', ''))) }}',
            productsList: @js($productsList),
            locationStockMap: @js($locationProductsMap)
        })"
        x-init="init()"
    >
        <h1 class="text-xl font-bold text-white mb-1">Pengajuan Penyesuaian Stok Fisik</h1>
        <p class="text-xs text-slate-400 mb-6">Ajukan koreksi jika terdapat selisih hitung fisik di lemari atau barang rusak/hilang pasca-event</p>

        <form action="{{ route('adjustments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Lokasi & Barang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Lokasi / Lemari *</label>
                    <select name="location_id" x-model="selectedLocation" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Pilih Titik Lokasi...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', request('location_id')) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-300 uppercase">Barang / SKU *</label>
                        
                        <template x-if="selectedLocation">
                            <div class="flex items-center gap-2">
                                <span x-show="locationProductsCount > 0" class="text-[10px] px-2 py-0.5 rounded-full font-semibold bg-indigo-500/15 text-indigo-400 border border-indigo-500/30">
                                    <span x-text="locationProductsCount"></span> barang di lokasi ini
                                </span>
                                <label class="inline-flex items-center gap-1.5 text-[11px] text-slate-400 cursor-pointer hover:text-slate-200">
                                    <input type="checkbox" x-model="showAllProducts" class="rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-0">
                                    <span>Semua Katalog</span>
                                </label>
                            </div>
                        </template>
                    </div>

                    <select name="product_id" x-ref="productSelect" x-model="selectedProduct" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">Pilih Barang...</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ (old('product_id', request('product_id')) == $prod->id || (request('item_name') && stripos($prod->name, request('item_name')) !== false)) ? 'selected' : '' }}>
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
                    <select name="condition" x-model="condition" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="available" {{ old('condition', request('condition', 'available')) == 'available' ? 'selected' : '' }}>Bagus (Siap)</option>
                        <option value="damaged" {{ old('condition', request('condition')) == 'damaged' ? 'selected' : '' }}>Rusak</option>
                        <option value="in_service" {{ old('condition', request('condition')) == 'in_service' ? 'selected' : '' }}>Dalam Servis</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Kepemilikan *</label>
                    <select name="ownership" x-model="ownership" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="company" {{ old('ownership') == 'company' ? 'selected' : '' }}>Aset Omah Visual</option>
                        <option value="external" {{ old('ownership') == 'external' ? 'selected' : '' }}>Vendor Rekanan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Jumlah Fisik Sebenarnya *</label>
                    <input type="number" step="0.01" name="adjusted_quantity" x-model="adjustedQty" value="{{ old('adjusted_quantity', request('adjusted_quantity', request('quantity'))) }}" required min="0" placeholder="0.00"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500 font-mono">
                </div>
            </div>

            <!-- Live Stock Comparison Card -->
            <div x-show="selectedLocation && selectedProduct" x-cloak class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800/80">
                    <span class="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Kalkulasi & Komparasi Saldo Stok
                    </span>
                    <span class="text-[11px] text-slate-400 font-mono" x-text="currentProduct ? currentProduct.code : ''"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded-lg bg-slate-900/80 border border-slate-800">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold mb-0.5">Saldo Sistem Saat Ini</span>
                        <div class="font-mono font-bold text-slate-200 text-base">
                            <span x-text="currentSystemQty"></span>
                            <span class="text-xs font-normal text-slate-400 ml-0.5" x-text="currentUnit"></span>
                        </div>
                        <span class="text-[10px] text-slate-500 block mt-0.5">Kondisi & kepemilikan terpilih</span>
                    </div>

                    <div class="p-3 rounded-lg bg-slate-900/80 border border-slate-800">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold mb-0.5">Fisik Hasil Temuan</span>
                        <div class="font-mono font-bold text-white text-base">
                            <span x-text="adjustedQty !== '' ? adjustedQty : '0'"></span>
                            <span class="text-xs font-normal text-slate-400 ml-0.5" x-text="currentUnit"></span>
                        </div>
                        <span class="text-[10px] text-slate-500 block mt-0.5">Input jumlah fisik riil</span>
                    </div>

                    <div class="p-3 rounded-lg border"
                        :class="difference > 0 ? 'bg-emerald-500/10 border-emerald-500/30' : (difference < 0 ? 'bg-rose-500/10 border-rose-500/30' : 'bg-slate-900/80 border-slate-800')">
                        <span class="block text-[10px] uppercase font-semibold mb-0.5"
                            :class="difference > 0 ? 'text-emerald-400' : (difference < 0 ? 'text-rose-400' : 'text-slate-400')">
                            Estimasi Koreksi / Selisih
                        </span>
                        <div class="font-mono font-bold text-base"
                            :class="difference > 0 ? 'text-emerald-400' : (difference < 0 ? 'text-rose-400' : 'text-slate-300')">
                            <span x-text="(difference > 0 ? '+' : '') + difference"></span>
                            <span class="text-xs font-normal ml-0.5" x-text="currentUnit"></span>
                        </div>
                        <span class="text-[10px] block mt-0.5 font-medium"
                            :class="difference > 0 ? 'text-emerald-400' : (difference < 0 ? 'text-rose-400' : 'text-slate-500')"
                            x-text="difference > 0 ? 'Kelebihan fisik (akan menambah saldo)' : (difference < 0 ? 'Kekurangan fisik (akan mengurangi saldo)' : 'Jumlah sama / tidak ada selisih')">
                        </span>
                    </div>
                </div>
            </div>

            <!-- Alasan Koreksi -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Alasan Penyesuaian (Wajib) *</label>
                <textarea name="reason" rows="3" required placeholder="Contoh: Ditemukan 1 kabel power jumper terbakar soketnya saat demobilisasi event di Tentrem, perlu dialihkan ke kondisi rusak..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">{{ old('reason', request('reason', request('notes'))) }}</textarea>
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

<script>
function adjustmentForm(config) {
    return {
        selectedLocation: config.selectedLocation || '',
        selectedProduct: config.selectedProduct || '',
        condition: config.condition || 'available',
        ownership: config.ownership || 'company',
        adjustedQty: config.adjustedQty !== undefined && config.adjustedQty !== null ? config.adjustedQty : '',
        showAllProducts: false,
        productsList: config.productsList || [],
        locationStockMap: config.locationStockMap || {},

        init() {
            // Jika ada pre-selected product tapi belum ada di lokasi tersebut, auto aktifkan showAllProducts agar tetap terlihat
            if (this.selectedProduct && this.selectedLocation) {
                const locData = this.locationStockMap[this.selectedLocation] || {};
                const locProductIds = Object.keys(locData).map(id => parseInt(id));
                if (!locProductIds.includes(parseInt(this.selectedProduct))) {
                    this.showAllProducts = true;
                }
            }

            this.$watch('selectedLocation', () => {
                this.syncProductOptions(true);
            });
            this.$watch('showAllProducts', () => {
                this.syncProductOptions(false);
            });

            this.syncProductOptions(false);
        },

        get locationProductsCount() {
            if (!this.selectedLocation) return 0;
            const locData = this.locationStockMap[this.selectedLocation] || {};
            return Object.keys(locData).length;
        },

        get currentProduct() {
            if (!this.selectedProduct) return null;
            return this.productsList.find(p => String(p.id) === String(this.selectedProduct));
        },

        get currentUnit() {
            return this.currentProduct ? this.currentProduct.unit : 'unit';
        },

        get currentSystemQty() {
            if (!this.selectedLocation || !this.selectedProduct) return 0;
            const locData = this.locationStockMap[this.selectedLocation] || {};
            const productData = locData[this.selectedProduct];
            if (!productData) return 0;
            const key = `${this.condition}_${this.ownership}`;
            return productData.breakdown && productData.breakdown[key] !== undefined
                ? parseFloat(Number(productData.breakdown[key]).toFixed(2))
                : 0;
        },

        get difference() {
            if (this.adjustedQty === '' || this.adjustedQty === null) return 0;
            const adj = parseFloat(this.adjustedQty) || 0;
            const sys = this.currentSystemQty;
            return parseFloat((adj - sys).toFixed(2));
        },

        syncProductOptions(resetIfNotInLocation = false) {
            const select = this.$refs.productSelect;
            if (!select) return;

            const locId = this.selectedLocation;
            const locData = this.locationStockMap[locId] || {};
            const locProductIds = Object.keys(locData).map(id => parseInt(id));
            const hasItemsInLocation = locProductIds.length > 0;
            const shouldShowAll = this.showAllProducts || !locId || !hasItemsInLocation;

            const currentVal = this.selectedProduct;
            select.innerHTML = '';

            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            if (!locId) {
                defaultOpt.textContent = 'Pilih Titik Lokasi terlebih dahulu...';
            } else if (hasItemsInLocation && !this.showAllProducts) {
                defaultOpt.textContent = `Pilih Barang di Lokasi Ini (${locProductIds.length} jenis barang)...`;
            } else if (hasItemsInLocation && this.showAllProducts) {
                defaultOpt.textContent = 'Pilih Barang (Semua Katalog)...';
            } else {
                defaultOpt.textContent = 'Belum ada barang di lokasi ini (Pilih dari katalog)...';
            }
            select.appendChild(defaultOpt);

            const filtered = this.productsList.filter(p => {
                if (shouldShowAll) return true;
                return locProductIds.includes(p.id);
            });

            filtered.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                const stockInfo = locData[p.id];
                let label = `${p.code} — ${p.name}`;
                if (locId && stockInfo) {
                    label += ` (Stok: ${parseFloat(stockInfo.total_qty.toFixed(2))} ${p.unit})`;
                } else if (locId && shouldShowAll) {
                    label += ` (0 ${p.unit} di lokasi ini)`;
                }
                opt.textContent = label;
                if (String(p.id) === String(currentVal)) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });

            if (currentVal && !filtered.some(p => String(p.id) === String(currentVal))) {
                if (resetIfNotInLocation) {
                    this.selectedProduct = '';
                    select.value = '';
                }
            } else if (currentVal) {
                select.value = currentVal;
            }
        }
    };
}
</script>
@endsection
