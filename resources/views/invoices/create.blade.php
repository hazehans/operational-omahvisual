@extends('layouts.app')

@section('title', 'Buat Invoice Baru - Omah Visual')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto"
     x-data="invoiceForm({
         orders: {{ Js::from($orders->map(fn($o) => [
             'id' => $o->id,
             'customer_id' => $o->customer_id,
             'customer_name' => $o->customer->name,
             'event_name' => $o->event_name,
             'order_number' => $o->order_number,
             'total_price' => (float) $o->total_price,
             'paid_amount' => (float) $o->paid_amount,
             'remaining_balance' => (float) $o->remaining_balance,
             'event_date' => $o->event_start_date ? $o->event_start_date->format('Y-m-d') : '',
             'screens' => $o->screens->map(fn($s) => [
                 'desc' => "Sewa LED Screen {$s->pixel_pitch} {$s->brand_series} - {$s->screen_name} ({$s->width_m}m x {$s->height_m}m / {$s->total_sqm} m²)",
                 'sqm' => (float) $s->total_sqm,
             ]),
         ])) }},
         initialOrderId: '{{ old('event_order_id', $selectedOrder->id ?? '') }}',
         initialCustomerId: '{{ old('customer_id', $selectedOrder->customer_id ?? '') }}',
         initialType: '{{ old('invoice_type', 'full') }}',
         initialDiscount: {{ old('discount', 0) }},
         initialTaxPercent: {{ old('tax_percent', 0) }},
         initialItems: {{ Js::from(old('items', !empty($suggestedItems) ? $suggestedItems : [
             ['item_description' => 'Sewa LED Screen P3.9 Indoor', 'quantity' => 1, 'unit_name' => 'paket', 'unit_price' => 0]
         ])) }}
     })">

    <!-- Top Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('invoices.index') }}" class="hover:text-indigo-600 transition-colors">Invoice</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Buat Faktur Baru</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                <span>📄 Terbitkan Invoice Baru</span>
            </h1>
        </div>

        <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
            Batal
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/40 text-rose-700 dark:text-rose-400 text-sm">
            <div class="font-bold mb-1">Terdapat kesalahan pengisian formulir:</div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('invoices.store') }}" class="space-y-6">
        @csrf

        <!-- Card 1: Hubungan Pesanan & Customer -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <span>1. Referensi Pesanan Event & Pelanggan</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Pilih Pesanan Event -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                        Pilih Pesanan Event <span class="text-rose-500">*</span>
                    </label>
                    <select name="event_order_id" x-model="selectedOrderId" @change="onOrderChange()" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="">-- Pilih Pesanan Event --</option>
                        @foreach($orders as $o)
                            <option value="{{ $o->id }}">
                                {{ $o->order_number }} - {{ $o->event_name }} ({{ $o->customer->name }}) - Rp {{ number_format($o->total_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Memilih pesanan akan otomatis mengisi pelanggan & saran item rincian.</p>
                </div>

                <!-- Customer -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                        Ditagihkan Kepada (Customer) <span class="text-rose-500">*</span>
                    </label>
                    <select name="customer_id" x-model="selectedCustomerId" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->name }} {{ $c->organization ? '('.$c->organization.')' : '' }} - {{ $c->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tipe Invoice & Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-2">
                <!-- Tipe Tagihan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                        Tipe Termin Tagihan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="setInvoiceType('full')"
                            :class="invoiceType === 'full' ? 'bg-indigo-600 text-white font-bold border-indigo-600' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'"
                            class="px-3 py-2 text-xs rounded-xl border transition-all text-center">
                            Lunas (100%)
                        </button>
                        <button type="button" @click="setInvoiceType('down_payment')"
                            :class="invoiceType === 'down_payment' ? 'bg-amber-600 text-white font-bold border-amber-600' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'"
                            class="px-3 py-2 text-xs rounded-xl border transition-all text-center">
                            Uang Muka (DP)
                        </button>
                        <button type="button" @click="setInvoiceType('settlement')"
                            :class="invoiceType === 'settlement' ? 'bg-emerald-600 text-white font-bold border-emerald-600' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700'"
                            class="px-3 py-2 text-xs rounded-xl border transition-all text-center">
                            Pelunasan Sisa
                        </button>
                    </div>
                    <input type="hidden" name="invoice_type" :value="invoiceType">
                </div>

                <!-- Tanggal Invoice -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                        Tanggal Invoice <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>

                <!-- Jatuh Tempo -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                        Jatuh Tempo <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="due_date" x-model="dueDate" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
            </div>

            <!-- Judul Faktur Opsional -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Judul / Keterangan Faktur (Opsional)
                </label>
                <input type="text" name="title" x-model="invoiceTitle" placeholder="Contoh: Tagihan Sewa LED Screen P3.9 Konser Musik..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>
        </div>

        <!-- Card 2: Rincian Item Tagihan (Line Items) -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>2. Rincian Item Tagihan</span>
                </h2>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 text-xs font-semibold hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tambah Baris</span>
                </button>
            </div>

            <!-- Table Items -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 uppercase text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-3 py-2.5 font-bold w-1/2">Deskripsi Layanan / Barang</th>
                            <th class="px-3 py-2.5 font-bold w-24">Kuantitas</th>
                            <th class="px-3 py-2.5 font-bold w-20">Satuan</th>
                            <th class="px-3 py-2.5 font-bold w-36">Harga Satuan (Rp)</th>
                            <th class="px-3 py-2.5 font-bold text-right w-36">Total (Rp)</th>
                            <th class="px-2 py-2.5 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="px-3 py-2">
                                    <input type="text" :name="'items[' + index + '][item_description]'" x-model="item.item_description" required placeholder="Nama item..."
                                        class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0.01" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" required
                                        class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white text-right">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'items[' + index + '][unit_name]'" x-model="item.unit_name" required placeholder="m²/unit"
                                        class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" min="0" step="1000" :name="'items[' + index + '][unit_price]'" x-model.number="item.unit_price" required
                                        class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white text-right">
                                </td>
                                <td class="px-3 py-2 text-right font-bold text-slate-900 dark:text-white">
                                    <span x-text="formatNumber(item.quantity * item.unit_price)"></span>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="p-1 rounded text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Subtotal, Diskon, Pajak & Grand Total -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-start gap-6">
                <div class="text-xs text-slate-500 space-y-1">
                    <div>💡 <em>Tip: Klik "+ Tambah Baris" untuk menambahkan rincian sewa tv, sound, atau rigging.</em></div>
                </div>

                <div class="w-full md:w-80 space-y-2.5 text-xs">
                    <!-- Subtotal -->
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>Subtotal:</span>
                        <span class="font-bold text-slate-900 dark:text-white" x-text="'Rp ' + formatNumber(subtotal)"></span>
                    </div>

                    <!-- Diskon (Rp) -->
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-600 dark:text-slate-400">Diskon (Rp):</span>
                        <input type="number" name="discount" x-model.number="discount" min="0" step="5000"
                            class="w-36 px-2.5 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-right font-semibold text-slate-900 dark:text-white">
                    </div>

                    <!-- PPN (%) -->
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-600 dark:text-slate-400">Pajak PPN (%):</span>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="tax_percent" x-model.number="taxPercent" min="0" max="100" step="0.5"
                                class="w-20 px-2.5 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-right font-semibold text-slate-900 dark:text-white">
                            <span class="text-slate-400">%</span>
                        </div>
                    </div>

                    <!-- Pajak Terhitung -->
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400" x-show="taxPercent > 0">
                        <span>Nominal PPN:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="'Rp ' + formatNumber(taxAmount)"></span>
                    </div>

                    <!-- Grand Total -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700 text-sm">
                        <span class="font-extrabold text-slate-900 dark:text-white">Total Tagihan:</span>
                        <span class="text-base font-extrabold text-indigo-600 dark:text-indigo-400" x-text="'Rp ' + formatNumber(grandTotal)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Rekening Bank & Syarat Ketentuan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Rekening Pembayaran -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    Instruksi Rekening Bank Pembayaran
                </label>
                <textarea name="bank_info" rows="5"
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">{{ old('bank_info', $defaultBankInfo) }}</textarea>
                <p class="text-[11px] text-slate-400">Akan dicetak di bagian bawah lembar invoice fisik A4.</p>
            </div>

            <!-- Syarat & Ketentuan -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    Syarat & Ketentuan Sewa
                </label>
                <textarea name="terms_conditions" rows="5"
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">{{ old('terms_conditions', $defaultTerms) }}</textarea>
                <p class="text-[11px] text-slate-400">Ketentuan DP, pembatalan, dan batas waktu pelunasan.</p>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('invoices.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-md shadow-indigo-600/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Terbitkan & Simpan Invoice</span>
            </button>
        </div>
    </form>
</div>

<script>
function invoiceForm(config) {
    return {
        orders: config.orders,
        selectedOrderId: config.initialOrderId || '',
        selectedCustomerId: config.initialCustomerId || '',
        invoiceType: config.initialType || 'full',
        dueDate: '{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}',
        invoiceTitle: '{{ old('title', '') }}',
        discount: config.initialDiscount || 0,
        taxPercent: config.initialTaxPercent || 0,
        items: config.initialItems || [],

        get subtotal() {
            return this.items.reduce((sum, item) => sum + ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)), 0);
        },

        get taxable() {
            return Math.max(0, this.subtotal - (parseFloat(this.discount) || 0));
        },

        get taxAmount() {
            return (parseFloat(this.taxPercent) || 0) > 0 ? (this.taxable * (parseFloat(this.taxPercent) / 100)) : 0;
        },

        get grandTotal() {
            return this.taxable + this.taxAmount;
        },

        formatNumber(val) {
            return Math.round(val || 0).toLocaleString('id-ID');
        },

        addItem() {
            this.items.push({
                item_description: '',
                quantity: 1,
                unit_name: 'unit',
                unit_price: 0
            });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        setInvoiceType(type) {
            this.invoiceType = type;
            this.applyTypeAmounts();
        },

        onOrderChange() {
            const order = this.orders.find(o => String(o.id) === String(this.selectedOrderId));
            if (!order) return;

            this.selectedCustomerId = String(order.customer_id);
            this.invoiceTitle = `Tagihan Sewa LED Screen - ${order.event_name}`;

            if (order.event_date) {
                this.dueDate = order.event_date;
            }

            // Auto-populate line items from screens if available
            if (order.screens && order.screens.length > 0) {
                const totalSqm = order.screens.reduce((s, scr) => s + scr.sqm, 0);
                const unitPrice = totalSqm > 0 ? Math.round(order.total_price / totalSqm) : 0;

                this.items = order.screens.map(scr => ({
                    item_description: scr.desc,
                    quantity: scr.sqm,
                    unit_name: 'm²',
                    unit_price: unitPrice
                }));
            } else {
                this.items = [{
                    item_description: `Paket Sewa LED Screen & Multimedia - ${order.event_name}`,
                    quantity: 1,
                    unit_name: 'paket',
                    unit_price: order.total_price
                }];
            }

            this.applyTypeAmounts();
        },

        applyTypeAmounts() {
            const order = this.orders.find(o => String(o.id) === String(this.selectedOrderId));
            if (!order) return;

            if (this.invoiceType === 'down_payment') {
                // Default 50% DP
                const dpTarget = Math.round(order.total_price * 0.5);
                this.items = [{
                    item_description: `Uang Muka (DP 50%) Sewa LED Screen - ${order.event_name}`,
                    quantity: 1,
                    unit_name: 'termin',
                    unit_price: dpTarget
                }];
            } else if (this.invoiceType === 'settlement') {
                const remain = order.remaining_balance > 0 ? order.remaining_balance : Math.round(order.total_price * 0.5);
                this.items = [{
                    item_description: `Pelunasan Sisa Tagihan Sewa LED Screen - ${order.event_name}`,
                    quantity: 1,
                    unit_name: 'termin',
                    unit_price: remain
                }];
            }
        }
    };
}
</script>
@endsection

