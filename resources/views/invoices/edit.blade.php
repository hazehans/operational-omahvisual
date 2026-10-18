@extends('layouts.app')

@section('title', 'Edit Invoice ' . $invoice->invoice_number . ' - Omah Visual')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto"
     x-data="invoiceEditForm({
         initialType: '{{ old('invoice_type', $invoice->invoice_type) }}',
         initialDiscount: {{ old('discount', $invoice->discount) }},
         initialTaxPercent: {{ old('tax_percent', $invoice->tax_percent) }},
         initialItems: {{ Js::from(old('items', $invoice->items->map(fn($it) => [
             'item_description' => $it->item_description,
             'quantity' => (float) $it->quantity,
             'unit_name' => $it->unit_name,
             'unit_price' => (float) $it->unit_price,
         ]))) }}
     })">

    <!-- Top Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('invoices.index') }}" class="hover:text-indigo-600 transition-colors">Invoice</a>
                <span>/</span>
                <a href="{{ route('invoices.show', $invoice) }}" class="hover:text-indigo-600 transition-colors font-mono">{{ $invoice->invoice_number }}</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Edit Faktur</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                <span>✏️ Edit Invoice {{ $invoice->invoice_number }}</span>
            </h1>
        </div>

        <a href="{{ route('invoices.show', $invoice) }}" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
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

    <form method="POST" action="{{ route('invoices.update', $invoice) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Card 1: Detail & Status -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                <span>1. Parameter & Status Invoice</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Customer Info (Read-only) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Pelanggan</label>
                    <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $invoice->customer->name }}</div>
                    <div class="text-xs text-slate-500">{{ $invoice->customer->organization }}</div>
                </div>

                <!-- Event Reference (Read-only) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Pesanan Event</label>
                    <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $invoice->eventOrder->event_name ?? '-' }}</div>
                    <div class="text-xs text-slate-500 font-mono">{{ $invoice->eventOrder->order_number ?? '-' }}</div>
                </div>

                <!-- Status Tagihan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                        Status Tagihan <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white">
                        <option value="unpaid" {{ old('status', $invoice->status) === 'unpaid' ? 'selected' : '' }}>Belum Dibayar (Unpaid)</option>
                        <option value="partial" {{ old('status', $invoice->status) === 'partial' ? 'selected' : '' }}>Sebagian / DP (Partial)</option>
                        <option value="paid" {{ old('status', $invoice->status) === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                        <option value="cancelled" {{ old('status', $invoice->status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                    </select>
                </div>
            </div>

            <!-- Tipe, Tanggal, & Judul -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                        Tipe Termin <span class="text-rose-500">*</span>
                    </label>
                    <select name="invoice_type" x-model="invoiceType" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white">
                        <option value="full">Lunas Sekaligus (100%)</option>
                        <option value="down_payment">Uang Muka (DP)</option>
                        <option value="settlement">Pelunasan Sisa</option>
                        <option value="standard">Standar</option>
                        <option value="custom">Kustom</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                        Tanggal Invoice <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                        Jatuh Tempo <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                    Judul Faktur (Opsional)
                </label>
                <input type="text" name="title" value="{{ old('title', $invoice->title) }}" placeholder="Contoh: Tagihan Sewa LED Screen..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white">
            </div>
        </div>

        <!-- Card 2: Items Table -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span>2. Rincian Item Tagihan</span>
                </h2>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 text-xs font-semibold hover:bg-indigo-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tambah Baris</span>
                </button>
            </div>

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
                                    <input type="text" :name="'items[' + index + '][item_description]'" x-model="item.item_description" required
                                        class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0.01" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" required
                                        class="w-full px-2.5 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white text-right">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="text" :name="'items[' + index + '][unit_name]'" x-model="item.unit_name" required
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

            <!-- Financial Calculation Breakdown -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-end items-center gap-6">
                <div class="w-full md:w-80 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                        <span>Subtotal:</span>
                        <span class="font-bold text-slate-900 dark:text-white" x-text="'Rp ' + formatNumber(subtotal)"></span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-600 dark:text-slate-400">Diskon (Rp):</span>
                        <input type="number" name="discount" x-model.number="discount" min="0" step="5000"
                            class="w-36 px-2.5 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-right font-semibold text-slate-900 dark:text-white">
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="text-slate-600 dark:text-slate-400">Pajak PPN (%):</span>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="tax_percent" x-model.number="taxPercent" min="0" max="100" step="0.5"
                                class="w-20 px-2.5 py-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-right font-semibold text-slate-900 dark:text-white">
                            <span class="text-slate-400">%</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-slate-600 dark:text-slate-400" x-show="taxPercent > 0">
                        <span>Nominal PPN:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="'Rp ' + formatNumber(taxAmount)"></span>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-200 dark:border-slate-700 text-sm">
                        <span class="font-extrabold text-slate-900 dark:text-white">Total Tagihan:</span>
                        <span class="text-base font-extrabold text-indigo-600 dark:text-indigo-400" x-text="'Rp ' + formatNumber(grandTotal)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Rekening Bank & Syarat Ketentuan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    Instruksi Rekening Bank
                </label>
                <textarea name="bank_info" rows="5"
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white">{{ old('bank_info', $invoice->bank_info) }}</textarea>
            </div>

            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-xs space-y-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                    Syarat & Ketentuan Sewa
                </label>
                <textarea name="terms_conditions" rows="5"
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white">{{ old('terms_conditions', $invoice->terms_conditions) }}</textarea>
            </div>
        </div>

        <!-- Tombol Simpan -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('invoices.show', $invoice) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-semibold hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-md shadow-indigo-600/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Perbarui Invoice</span>
            </button>
        </div>
    </form>
</div>

<script>
function invoiceEditForm(config) {
    return {
        invoiceType: config.initialType || 'full',
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
        }
    };
}
</script>
@endsection

