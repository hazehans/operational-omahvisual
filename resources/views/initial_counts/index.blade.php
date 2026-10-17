@extends('layouts.app')

@section('title', 'Sahkan Stok Awal')
@section('page_title', 'Sesi Pengesahan Saldo Awal Gudang')

@section('content')
<div class="space-y-6" x-data="{ showAddForm: false }">

    <!-- Header Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/70 border border-slate-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-md border border-indigo-500/20">
                    {{ $session->count_number }}
                </span>
                @if($session->status === 'verified')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Disahkan Resmi (Verified)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        Menunggu Pengesahan Warehouse
                    </span>
                @endif
            </div>

            <h1 class="text-xl md:text-2xl font-extrabold text-white">
                {{ $session->title }}
            </h1>
            <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                {{ $session->notes }}
            </p>

            <div class="flex flex-wrap items-center gap-4 mt-4 text-xs text-slate-400">
                <div>
                    Pendata: <span class="font-semibold text-slate-200">{{ $session->conductor?->name ?? 'Tim Warehouse' }}</span>
                </div>
                <div>•</div>
                <div>
                    Lokasi: <span class="font-semibold text-slate-200">{{ $session->location?->name ?? 'Gedung Utama' }}</span>
                </div>
                @if($session->verified_by)
                    <div>•</div>
                    <div>
                        Disahkan oleh: <span class="font-semibold text-emerald-400">{{ $session->verifier?->name }}</span> ({{ $session->finalized_at?->format('d/m/Y H:i') }})
                    </div>
                @endif
            </div>
        </div>

        <!-- Tombol Pengesahan, Kosongkan, & Simulasi -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            @if($session->status !== 'verified')
                <form action="{{ route('initial-counts.verify', $session) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin data fisik ini sudah valid dan siap disahkan sebagai saldo awal resmi operasional Omah Visual LED?');">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-5 py-3 rounded-xl bg-linear-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Sahkan Saldo Stok Awal</span>
                    </button>
                </form>
            @else
                <div class="px-4 py-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Saldo Telah Aktif & Diposting</span>
                </div>

                <!-- Tombol Simulasi Ulang (Demo Feature) -->
                <form action="{{ route('initial-counts.reset-simulation', $session) }}" method="POST" onsubmit="return confirm('Reset status stok awal ke mode simulasi (Pending) agar Anda dapat mencoba alur tombol pengesahan kembali?');">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-semibold flex items-center justify-center gap-2 transition-all shadow-md active:scale-95" title="Gunakan ini untuk mencoba alur verifikasi berkali-kali">
                        <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset ke Pending</span>
                    </button>
                </form>
            @endif

            <!-- Tombol Kosongkan Sesi (Mulai dari Nol) -->
            <form action="{{ route('initial-counts.clear-items', $session) }}" method="POST" onsubmit="return confirm('Kosongkan semua item di sesi stok awal ini agar Anda bisa mengisi data fisik barang secara mandiri dari awal?');">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-rose-950/40 hover:bg-rose-900/50 text-rose-300 hover:text-rose-100 border border-rose-800/40 text-xs font-semibold flex items-center justify-center gap-2 transition-all shadow-md active:scale-95" title="Kosongkan item sesi opname stok awal">
                    <svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Kosongkan Sesi (Mulai dari Nol)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Panel Input Tambah Barang Manual -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg p-5">
        <div class="flex items-center justify-between cursor-pointer" @click="showAddForm = !showAddForm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold">
                    +
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Input Tambah Barang Manual ke Stok Awal</h3>
                    <p class="text-[11px] text-slate-400">Pilih barang dari master produk dan tentukan jumlah fisik riil di gudang.</p>
                </div>
            </div>
            <button type="button" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1">
                <span x-text="showAddForm ? 'Tutup Form' : 'Buka Form Input'"></span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="showAddForm ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <div x-show="showAddForm" x-collapse class="mt-4 pt-4 border-t border-slate-800/80">
            <form action="{{ route('initial-counts.add-item', $session) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                @csrf
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Master Barang <span class="text-rose-400">*</span></label>
                    <select name="product_id" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($availableProducts as $prod)
                            <option value="{{ $prod->id }}">
                                [{{ $prod->code }}] {{ $prod->name }} ({{ $prod->unit->symbol ?? 'unit' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kondisi Fisik <span class="text-rose-400">*</span></label>
                    <select name="condition" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="available">Tersedia (Bagus)</option>
                        <option value="damaged">Rusak</option>
                        <option value="in_service">Dalam Servis</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kepemilikan <span class="text-rose-400">*</span></label>
                    <select name="ownership" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="company">Aset Omah Visual</option>
                        <option value="external">Vendor Rekanan</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kuantitas Fisik <span class="text-rose-400">*</span></label>
                    <input type="number" step="0.01" name="physical_quantity" required placeholder="Contoh: 10" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500 font-mono">
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-indigo-600/30 transition-all flex items-center justify-center gap-1.5 active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>Simpan Item</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-sm font-bold text-white">Rincian Fisik Barang dalam Sesi Stok Awal ({{ $items->count() }} Item)</h2>
            <span class="text-xs text-slate-400">Kombinasi Kondisi & Kepemilikan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Kode & Nama Barang</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Kondisi Fisik</th>
                        <th class="px-5 py-3.5">Kepemilikan</th>
                        <th class="px-5 py-3.5 text-right">Fisik Terhitung</th>
                        <th class="px-5 py-3.5 text-center">Status Verifikasi</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-indigo-400 font-bold block">{{ $item->product->code }}</span>
                                <span class="font-semibold text-white">{{ $item->product->name }}</span>
                                @if($item->product->sqm_per_unit)
                                    <span class="block text-[11px] text-slate-400">Luas: {{ $item->product->sqm_per_unit }} m²/unit</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-300">
                                {{ $item->product->category->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($item->condition === 'available')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Tersedia (Bagus)
                                    </span>
                                @elseif($item->condition === 'damaged')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Rusak
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        Dalam Servis
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-slate-300 capitalize font-medium">
                                    {{ $item->ownership === 'company' ? 'Aset Omah Visual' : 'Vendor Rekanan' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold text-white text-sm">
                                {{ (float) $item->physical_quantity }} <span class="text-xs text-slate-400 font-normal">{{ $item->product->unit->symbol ?? 'unit' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($item->is_verified)
                                    <span class="inline-flex items-center gap-1 text-emerald-400 font-medium">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Sah</span>
                                    </span>
                                @else
                                    <span class="text-amber-400">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <form action="{{ route('initial-counts.delete-item', [$session, $item]) }}" method="POST" onsubmit="return confirm('Hapus item ini dari sesi stok awal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors" title="Hapus dari daftar">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="max-w-md mx-auto text-slate-400 space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="font-semibold text-slate-300">Belum ada item dalam sesi stok awal ini.</p>
                                    <p class="text-[11px] text-slate-500">
                                        Sesi telah dikosongkan. Anda dapat mulai menambahkan saldo fisik barang secara manual menggunakan tombol <strong>"Buka Form Input"</strong> di atas, lalu tekan <strong>"Sahkan Saldo Stok Awal"</strong> jika sudah selesai.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
