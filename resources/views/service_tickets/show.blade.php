@extends('layouts.app')

@section('page_title', 'Detail Tiket Servis ' . $ticket->ticket_number)

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-400">
            <a href="{{ route('service-tickets.index') }}" class="hover:text-indigo-400 transition-colors">Servis & Workshop</a>
            <span>/</span>
            <span class="font-mono font-bold text-slate-200">{{ $ticket->ticket_number }}</span>
        </div>
        <a href="{{ route('service-tickets.index') }}" class="text-xs text-slate-400 hover:text-slate-200 transition-colors flex items-center gap-1 font-semibold">
            &larr; Kembali ke Daftar Servis
        </a>
    </div>

    <!-- Ticket Master Header Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-xl space-y-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-xs font-bold font-mono bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        {{ $ticket->ticket_number }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $ticket->priority_badge_class }}">
                        Prioritas: {{ $ticket->priority_label }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $ticket->status_badge_class }}">
                        {{ $ticket->status_label }}
                    </span>
                </div>

                <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white">
                    {{ $ticket->product->name }}
                </h1>

                @if($ticket->asset)
                    <div class="flex items-center gap-3 pt-1">
                        <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-mono font-bold border border-slate-300 dark:border-slate-700">
                            Unit ID: {{ $ticket->asset->asset_code }}
                        </span>
                        @if($ticket->asset->serial_number)
                            <span class="text-xs text-slate-400 font-mono">SN Pabrik: {{ $ticket->asset->serial_number }}</span>
                        @endif
                        <a href="{{ route('assets.show', $ticket->asset) }}" class="text-xs font-bold text-indigo-400 hover:underline">
                            Lihat Profil Aset &rarr;
                        </a>
                    </div>
                @else
                    <p class="text-xs text-slate-400 font-medium">
                        Kuantitas: <strong class="text-slate-200 text-sm font-mono">{{ (float) $ticket->quantity }}</strong> {{ $ticket->product->unit->symbol ?? 'unit' }}
                    </p>
                @endif
            </div>

            <!-- Metric Box / Current Location -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/70 border border-slate-200 dark:border-slate-800 text-right min-w-56 space-y-1">
                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">Posisi Fisik Saat Ini</span>
                <span class="text-sm font-extrabold text-amber-500 dark:text-amber-400 block">
                    📍 {{ $ticket->workshopLocation->name ?? 'Workshop' }}
                </span>
                <span class="text-[11px] text-slate-400 block">
                    Asal: {{ $ticket->sourceLocation->name ?? 'Gudang' }}
                </span>
                @if($ticket->completion_date)
                    <span class="text-[10px] text-emerald-400 block pt-1 font-semibold">
                        Selesai: {{ $ticket->completion_date->format('d/m/Y H:i') }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Meta Details Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs pt-2">
            <div>
                <span class="text-slate-400 block mb-1">Tanggal Masuk:</span>
                <span class="font-bold text-slate-700 dark:text-slate-200 font-mono">
                    {{ $ticket->intake_date ? $ticket->intake_date->format('d M Y, H:i') : '-' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Pelapor Kerusakan:</span>
                <span class="font-bold text-slate-700 dark:text-slate-200">
                    {{ $ticket->reporter->name ?? 'Staf Gudang' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Teknisi Penanggung Jawab:</span>
                <span class="font-bold text-indigo-400">
                    {{ $ticket->technician->name ?? 'Belum Ditugaskan' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 block mb-1">Durasi Karantina:</span>
                <span class="font-bold text-slate-700 dark:text-slate-200 font-mono">
                    {{ $ticket->intake_date ? $ticket->intake_date->diffForHumans() : '-' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content: Left (Diagnosis & Spareparts) / Right (Actions & Progress) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Technical Work & Spareparts -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Issue Description Card -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-500 dark:text-amber-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Laporan Masalah / Gejala Awal</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-sm text-slate-800 dark:text-slate-200 leading-relaxed font-sans">
                    {{ $ticket->issue_description }}
                </div>
                @if($ticket->notes)
                    <p class="text-xs text-slate-400">
                        <span class="font-semibold text-slate-300">Catatan:</span> {{ $ticket->notes }}
                    </p>
                @endif
            </div>

            <!-- Technician Diagnosis & Action Update Form -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-indigo-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Lembar Kerja Teknisi: Diagnosa &amp; Penanganan</span>
                    </div>
                </div>

                <form action="{{ route('service-tickets.update', $ticket) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                                Teknisi Penanggung Jawab
                            </label>
                            <select name="technician_id" class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white">
                                <option value="">-- Pilih Teknisi --</option>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ old('technician_id', $ticket->technician_id) == $tech->id ? 'selected' : '' }}>
                                        {{ $tech->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                                Tingkat Prioritas
                            </label>
                            <select name="priority" class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white">
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>⚪ Rendah</option>
                                <option value="normal" {{ $ticket->priority === 'normal' ? 'selected' : '' }}>🔵 Normal</option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>🟠 Tinggi</option>
                                <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>🔴 Darurat</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                            Diagnosa Kerusakan Fisik / Elektronik
                        </label>
                        <textarea name="diagnosis" rows="2" placeholder="Catat hasil pemeriksaan: misal IC Driver baris 3 korslet, jalur PCB putus, elco PSU kembung..." class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 leading-relaxed">{{ old('diagnosis', $ticket->diagnosis) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                            Tindakan Perbaikan yang Dilakukan
                        </label>
                        <textarea name="action_taken" rows="2" placeholder="Tindakan yang diambil: misal solder ulang kaki IC, ganti 2 titik mata LED merah, kalibrasi ulang parameter port..." class="w-full px-4 py-2.5 rounded-xl text-sm bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 leading-relaxed">{{ old('action_taken', $ticket->action_taken) }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition-colors flex items-center gap-1.5 shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Lembar Kerja</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Spareparts Consumption Card (Direct Inventory Integration) -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md space-y-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-purple-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            <span>Suku Cadang Terpakai (Potong Stok Lemari)</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Komponen yang diambil dari lemari untuk memperbaiki modul ini.</p>
                    </div>

                    @if(!in_array($ticket->status, ['completed', 'scrapped']))
                        <button type="button" onclick="document.getElementById('addSparepartModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs transition-all flex items-center gap-1.5 shadow-md shadow-purple-600/20">
                            <span>+ Ambil Suku Cadang</span>
                        </button>
                    @endif
                </div>

                <!-- Table of Used Spareparts -->
                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-800/80 text-slate-400 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-4 py-3">Nama Suku Cadang</th>
                                <th class="px-4 py-3">Sumber Lemari</th>
                                <th class="px-4 py-3 text-center">Jumlah Dipakai</th>
                                <th class="px-4 py-3">Waktu Ambil</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60">
                            @forelse($ticket->spareparts as $sp)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                        {{ $sp->product->name }}
                                        @if($sp->notes)
                                            <span class="block text-[10px] text-slate-400 font-normal">{{ $sp->notes }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-300 font-medium">
                                        {{ $sp->sourceLocation->name ?? 'Gudang' }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-bold text-indigo-400">
                                        {{ (float) $sp->quantity }} {{ $sp->product->unit->symbol ?? 'pcs' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-400 font-mono">
                                        {{ $sp->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if(!in_array($ticket->status, ['completed', 'scrapped']))
                                            <form action="{{ route('service-tickets.spareparts.destroy', [$ticket, $sp]) }}" method="POST" onsubmit="return confirm('Batalkan pemakaian sparepart ini? Stok akan dikembalikan ke lemari asal.')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-300 font-bold transition-colors" title="Batalkan & Kembalikan Stok">
                                                    ✕ Batal
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400 italic">
                                        Belum ada suku cadang yang digunakan untuk perbaikan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right 1 Col: Status Transition Controls & Inventory Action -->
        <div class="space-y-6">

            <!-- Status Transition Controller Card -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 shadow-md space-y-5">
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Tahapan Pengerjaan Servis</span>
                </div>

                <!-- Status Timeline Indicator -->
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $ticket->status === 'pending_inspection' ? 'bg-amber-500 text-slate-950 font-black ring-4 ring-amber-500/20' : 'bg-slate-800 text-slate-400' }}">
                            1
                        </div>
                        <div class="flex-1 text-xs">
                            <span class="font-bold block {{ $ticket->status === 'pending_inspection' ? 'text-amber-400' : 'text-slate-300' }}">Antrean Masuk</span>
                            <span class="text-slate-500 text-[10px]">Menunggu di meja workshop</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ in_array($ticket->status, ['in_progress', 'waiting_parts']) ? 'bg-indigo-600 text-white font-black ring-4 ring-indigo-500/20' : 'bg-slate-800 text-slate-400' }}">
                            2
                        </div>
                        <div class="flex-1 text-xs">
                            <span class="font-bold block {{ in_array($ticket->status, ['in_progress', 'waiting_parts']) ? 'text-indigo-400' : 'text-slate-300' }}">Pengerjaan &amp; Penggantian Part</span>
                            <span class="text-slate-500 text-[10px]">Solder &amp; komponen elektronik</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $ticket->status === 'quality_control' ? 'bg-cyan-500 text-slate-950 font-black ring-4 ring-cyan-500/20' : 'bg-slate-800 text-slate-400' }}">
                            3
                        </div>
                        <div class="flex-1 text-xs">
                            <span class="font-bold block {{ $ticket->status === 'quality_control' ? 'text-cyan-400' : 'text-slate-300' }}">Uji Coba &amp; QC</span>
                            <span class="text-slate-500 text-[10px]">Aging test layar &amp; pola warna</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $ticket->status === 'completed' ? 'bg-emerald-500 text-white font-black ring-4 ring-emerald-500/20' : ($ticket->status === 'scrapped' ? 'bg-rose-500 text-white' : 'bg-slate-800 text-slate-400') }}">
                            ✓
                        </div>
                        <div class="flex-1 text-xs">
                            <span class="font-bold block {{ $ticket->status === 'completed' ? 'text-emerald-400' : ($ticket->status === 'scrapped' ? 'text-rose-400' : 'text-slate-300') }}">
                                {{ $ticket->status === 'scrapped' ? 'Afkir / Rusak Total' : 'Selesai & Kembali ke Rak' }}
                            </span>
                            <span class="text-slate-500 text-[10px]">
                                {{ $ticket->status === 'completed' ? 'Saldo siap sewa bertambah' : 'Siklus hidup selesai' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Transition Buttons Form -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 space-y-3">
                    @if($ticket->status === 'pending_inspection')
                        <form action="{{ route('service-tickets.update-status', $ticket) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition-all shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2">
                                <span>🛠️ Mulai Kerjakan Sekarang</span>
                            </button>
                        </form>
                    @endif

                    @if(in_array($ticket->status, ['in_progress', 'waiting_parts']))
                        <div class="grid grid-cols-2 gap-2">
                            <form action="{{ route('service-tickets.update-status', $ticket) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="quality_control">
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs transition-all shadow-md shadow-cyan-600/20">
                                    🔬 Masuk Uji QC
                                </button>
                            </form>

                            <form action="{{ route('service-tickets.update-status', $ticket) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="{{ $ticket->status === 'waiting_parts' ? 'in_progress' : 'waiting_parts' }}">
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all">
                                    {{ $ticket->status === 'waiting_parts' ? 'Part Tiba (Lanjut)' : '⏳ Tunggu Part' }}
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Final Completion (Return to Inventory) -->
                    @if(in_array($ticket->status, ['in_progress', 'quality_control', 'waiting_parts']))
                        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 space-y-3">
                            <span class="text-xs font-bold text-emerald-400 block">
                                ✅ Penyelesaian &amp; Pengembalian Rak
                            </span>
                            <form action="{{ route('service-tickets.update-status', $ticket) }}" method="POST" class="space-y-2">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="completed">

                                <label class="block text-[11px] font-semibold text-slate-300">
                                    Kembalikan ke Rak / Lemari:
                                </label>
                                <select name="return_location_id" required class="w-full px-3 py-2 rounded-xl text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white">
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" {{ $loc->id == $ticket->source_location_id ? 'selected' : '' }}>
                                            {{ $loc->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="submit" onclick="return confirm('Pastikan barang sudah lolos uji QC. Barang akan otomatis kembali ke saldo siap sewa.')" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-1.5 mt-2">
                                    <span>Lolos QC &amp; Kembalikan ke Stok</span>
                                </button>
                            </form>
                        </div>

                        <!-- Scrapped / Write-off -->
                        <form action="{{ route('service-tickets.update-status', $ticket) }}" method="POST" onsubmit="return confirm('PERINGATAN: Barang ini akan ditandai sebagai AFKIR / RUSAK TOTAL dan tidak bisa diperbaiki lagi. Lanjutkan?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="scrapped">
                            <button type="submit" class="w-full py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 font-bold text-xs transition-all">
                                ⚠️ Tandai Afkir / Rusak Total (Scrap)
                            </button>
                        </form>
                    @endif

                    @if($ticket->status === 'completed')
                        <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-medium text-center">
                            ✓ Perbaikan telah rampung dan barang telah dikembalikan ke rak siap pakai.
                        </div>
                    @elseif($ticket->status === 'scrapped')
                        <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-medium text-center">
                            ✕ Barang dinyatakan afkir / rusak permanen.
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Modal Ambil Sparepart -->
<div id="addSparepartModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-base">Ambil Suku Cadang dari Lemari</h3>
            <button type="button" onclick="document.getElementById('addSparepartModal').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold">✕</button>
        </div>

        <form action="{{ route('service-tickets.spareparts.store', $ticket) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                    Pilih Jenis Suku Cadang / Sparepart <span class="text-rose-500">*</span>
                </label>
                <select name="sparepart_product_id" required class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white">
                    <option value="">-- Pilih Suku Cadang --</option>
                    @foreach($sparepartProducts as $spProd)
                        <option value="{{ $spProd->id }}">
                            {{ $spProd->name }} ({{ $spProd->code }}) — Stok Tersedia: {{ (float) $spProd->total_available_stock }} {{ $spProd->unit->symbol ?? 'pcs' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Sumber Lemari <span class="text-rose-500">*</span>
                    </label>
                    <select name="source_location_id" required class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ $loc->id == 3 ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                        Jumlah Dipakai <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="quantity" value="1" required class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                    Keterangan Penempatan (Opsional)
                </label>
                <input type="text" name="notes" placeholder="Misal: Pasang di modul baris 2 kolom 3" class="w-full px-3 py-2 rounded-xl text-sm bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-500">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="document.getElementById('addSparepartModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-700 text-xs font-semibold text-slate-300">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md">
                    Potong Stok &amp; Gunakan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

