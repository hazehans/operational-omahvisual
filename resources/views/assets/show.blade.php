@extends('layouts.app')

@section('title', 'Unit ' . $asset->asset_code)
@section('page_title', 'Kartu Identitas Unit Aset')

@section('content')
<div class="space-y-6">

    <!-- Top Back -->
    <a href="{{ route('assets.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Daftar Aset</span>
    </a>

    <!-- Asset Detail Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Info Profile (2 Col) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-mono text-sm font-bold text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-md border border-indigo-500/20">
                                {{ $asset->asset_code }}
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300">
                                {{ $asset->product->category->name ?? 'Aset' }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-extrabold text-white">{{ $asset->product->name }}</h1>
                        <p class="text-xs text-slate-400 mt-1 font-mono">
                            Seri: {{ $asset->product->model_series ?? $asset->product->brand ?? '-' }}
                        </p>
                    </div>

                    <!-- Badge Status -->
                    <div>
                        @if($asset->condition === 'available')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                Kondisi Bagus & Siap Sewa
                            </span>
                        @elseif($asset->condition === 'damaged')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                Kondisi Rusak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                Dalam Perbaikan (Servis)
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Attributes Table -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-800 text-xs">
                    <div>
                        <span class="text-slate-500 block">Nomor Seri Pabrik:</span>
                        <span class="font-mono font-bold text-slate-200">{{ $asset->serial_number ?? 'Belum ada' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Barcode / QR Tag:</span>
                        <span class="font-mono font-bold text-slate-200">{{ $asset->barcode ?? 'Belum dicetak' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Kepemilikan:</span>
                        <span class="font-semibold text-slate-200 capitalize">{{ $asset->ownership === 'company' ? 'Aset Omah Visual' : 'Vendor Rekanan' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Lokasi Saat Ini:</span>
                        <span class="font-semibold text-indigo-400">{{ $asset->currentLocation->name ?? 'Gedung Utama' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Status Operasional:</span>
                        <span class="font-semibold text-slate-200">
                            {{ $asset->status === 'in_storage' ? 'Tersimpan di Gudang' : ($asset->status === 'deployed' ? 'Dipakai di Event' : $asset->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Tanggal Registrasi:</span>
                        <span class="font-semibold text-slate-200">{{ $asset->created_at->format('d M Y') }}</span>
                    </div>
                </div>

                @if($asset->notes)
                    <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-slate-400">
                        <span class="text-slate-500 block font-semibold mb-0.5">Catatan Khusus:</span>
                        {{ $asset->notes }}
                    </div>
                @endif
            </div>

            <!-- Movement History Table -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
                <div class="px-6 py-4 border-b border-slate-800">
                    <h2 class="text-sm font-bold text-white">Histori Mutasi & Pergerakan Unit Ini</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-800/60 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-800">
                            <tr>
                                <th class="px-5 py-3">Nomor Mutasi</th>
                                <th class="px-5 py-3">Tipe</th>
                                <th class="px-5 py-3">Waktu</th>
                                <th class="px-5 py-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @forelse($asset->movementItems as $item)
                                <tr class="hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-mono font-bold text-indigo-400">
                                        {{ $item->stockMovement->movement_number ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3 capitalize text-slate-300">
                                        {{ $item->stockMovement->type ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-400">
                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-400">
                                        {{ $item->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-6 text-center text-slate-500">
                                        Belum ada riwayat mutasi untuk unit ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Ubah Kondisi & Status Cepat (1 Col) -->
        <div>
            <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl space-y-5 sticky top-24">
                <h3 class="text-sm font-bold text-white">Perbarui Status & Kondisi Fisik</h3>
                <p class="text-xs text-slate-400">Gunakan form ini untuk mencatat unit yang rusak di lapangan atau masuk ke bengkel servis.</p>

                <form action="{{ route('assets.update-status', $asset) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Kondisi Fisik -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Kondisi Fisik *</label>
                        <select name="condition" required class="w-full px-3 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="available" {{ $asset->condition === 'available' ? 'selected' : '' }}>Bagus / Siap Sewa</option>
                            <option value="damaged" {{ $asset->condition === 'damaged' ? 'selected' : '' }}>Rusak (Perlu Penanganan)</option>
                            <option value="in_service" {{ $asset->condition === 'in_service' ? 'selected' : '' }}>Dalam Servis (Workshop)</option>
                        </select>
                    </div>

                    <!-- Status Penggunaan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Status Penempatan *</label>
                        <select name="status" required class="w-full px-3 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="in_storage" {{ $asset->status === 'in_storage' ? 'selected' : '' }}>Tersimpan di Gudang</option>
                            <option value="deployed" {{ $asset->status === 'deployed' ? 'selected' : '' }}>Sedang di Venue / Event</option>
                            <option value="maintenance" {{ $asset->status === 'maintenance' ? 'selected' : '' }}>Perbaikan / Servis</option>
                            <option value="lost" {{ $asset->status === 'lost' ? 'selected' : '' }}>Hilang / Tertinggal</option>
                        </select>
                    </div>

                    <!-- Lokasi Penyimpanan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Lokasi / Lemari *</label>
                        <select name="current_location_id" required class="w-full px-3 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $asset->current_location_id == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase">Keterangan / Alasan Perubahan</label>
                        <textarea name="notes" rows="2" placeholder="Misal: Port HDMI 2 kendur saat event wedding..."
                            class="w-full px-3 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-xs text-white focus:outline-none focus:border-indigo-500">{{ $asset->notes }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/25 transition-all">
                        Perbarui Status Unit
                    </button>

                    <div class="pt-2 border-t border-slate-800">
                        <a href="{{ route('service-tickets.create', ['asset_id' => $asset->id]) }}" class="w-full py-2.5 px-4 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-bold transition-all flex items-center justify-center gap-2">
                            <span>🛠️ Buka Tiket Servis Workshop</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

