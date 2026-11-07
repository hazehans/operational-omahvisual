@extends('layouts.app')

@section('title', 'Detail Customer - ' . $customer->name)
@section('page_title', 'Profil Customer & Riwayat Transaksi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Navigation & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('customers.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center gap-1.5 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Master Customer</span>
        </a>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Verifikasi Cepat (Jika Pending) -->
            @if($customer->verification_status === 'pending_verification')
                <form action="{{ route('customers.verify', $customer) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Verifikasi Klien Ini</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat SPK / Order Baru</span>
            </a>

            <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold border border-slate-300 dark:border-slate-700 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span>Edit Data</span>
            </a>
        </div>
    </div>

    <!-- Main Profile Card -->
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col md:flex-row items-start gap-6">
            <!-- Foto Profil -->
            <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 overflow-hidden shrink-0 flex items-center justify-center shadow-inner">
                @if($customer->photo_path)
                    <img src="{{ asset('storage/' . $customer->photo_path) }}" alt="{{ $customer->name }}" class="w-full h-full object-cover">
                @else
                    <div class="flex flex-col items-center justify-center text-slate-400">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-[10px] mt-1 font-semibold text-slate-500">Tanpa Foto</span>
                    </div>
                @endif
            </div>

            <!-- Detail Identitas -->
            <div class="flex-1 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-2.5 py-1 rounded-md border border-indigo-200 dark:border-indigo-500/20">
                        {{ $customer->customer_code }}
                    </span>
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ $customer->category ?? 'Event Organizer (EO)' }}
                    </span>
                    @if($customer->verification_status === 'verified')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Terverifikasi Company</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30">
                            <span>⏳ Menunggu Verifikasi</span>
                        </span>
                    @endif
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        <span class="text-indigo-600 dark:text-indigo-400">{{ $customer->salutation ? $customer->salutation . ' ' : '' }}</span>{{ $customer->name }}
                    </h1>
                    @if($customer->full_name && $customer->full_name !== $customer->name)
                        <p class="text-xs text-slate-500 mt-0.5">Nama Resmi: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $customer->full_name }}</span></p>
                    @endif
                    @if($customer->organization)
                        <p class="text-sm font-semibold text-slate-600 dark:text-slate-300 mt-1">
                            🏢 {{ $customer->organization }}
                        </p>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-4 pt-1">
                    <a href="{{ $customer->whatsapp_url }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shadow-xs transition-all">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>WhatsApp: {{ $customer->national_phone }}</span>
                    </a>

                    @if($customer->email)
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            ✉️ {{ $customer->email }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Info Grid (Emergency Contact, Alamat, Verifikasi) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8 pt-6 border-t border-slate-200 dark:border-slate-800 text-xs">
            <!-- Kontak Darurat -->
            <div class="p-4 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40">
                <span class="font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300 block mb-1">🚨 Kontak Cadangan / Darurat</span>
                @if($customer->pic_secondary_name || $customer->pic_secondary_phone)
                    <div class="space-y-0.5 text-slate-700 dark:text-slate-300">
                        <div class="font-semibold text-sm text-slate-900 dark:text-white">{{ $customer->pic_secondary_name ?: '-' }}</div>
                        <div>{{ $customer->pic_secondary_phone ?: '-' }}</div>
                        <div class="text-[11px] text-slate-500">Hubungan: {{ $customer->pic_secondary_relation ?: 'Partner / Tim' }}</div>
                    </div>
                @else
                    <span class="text-slate-400 italic">Belum ada nomor darurat cadangan</span>
                @endif
            </div>

            <!-- Domisili & Alamat -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                <span class="font-bold uppercase tracking-wider text-slate-500 block mb-1">📍 Domisili & Wilayah</span>
                <div class="space-y-0.5 text-slate-700 dark:text-slate-300">
                    <div class="font-semibold">{{ $customer->city ?: '-' }}, {{ $customer->province ?: '-' }}</div>
                    <div class="text-slate-500 line-clamp-2">{{ $customer->address ?: 'Alamat belum diisi' }}</div>
                </div>
            </div>

            <!-- Verifikasi & Registrasi -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800">
                <span class="font-bold uppercase tracking-wider text-slate-500 block mb-1">🛡️ Status Verifikasi Company</span>
                <div class="space-y-0.5 text-slate-700 dark:text-slate-300">
                    <div>Status: <span class="font-bold {{ $customer->verification_status === 'verified' ? 'text-emerald-500' : 'text-amber-500' }}">{{ $customer->verification_status === 'verified' ? 'Terverifikasi' : 'Pending' }}</span></div>
                    <div class="text-[11px] text-slate-500">
                        @if($customer->verification_status === 'verified')
                            Oleh: {{ $customer->verifier?->name ?? 'Admin' }} ({{ $customer->verified_at?->format('d M Y') }})
                        @else
                            Menunggu persetujuan admin operasional
                        @endif
                    </div>
                    <div class="text-[11px] text-slate-500">Tgl Daftar: {{ $customer->registration_date?->format('d M Y') ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pesanan Event Orders -->
    <div class="bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Riwayat Pesanan &amp; SPK ({{ $customer->orders->count() }})</h2>
                <p class="text-xs text-slate-500">Daftar event yang diselenggarakan bersama {{ $customer->salutation ? $customer->salutation . ' ' : '' }}{{ $customer->name }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-600 dark:text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">No SPK / Order</th>
                        <th class="px-5 py-3.5">Nama Event &amp; Lokasi</th>
                        <th class="px-5 py-3.5">Tanggal Acara</th>
                        <th class="px-5 py-3.5">Luas Layar</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($customer->orders as $order)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $order->event_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $order->venue_location }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">
                                {{ $order->event_start_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $order->total_sqm ? number_format((float) $order->total_sqm, 2) . ' m²' : '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ ucfirst($order->status ?? 'Draft') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('orders.show', $order) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 font-semibold transition-colors">
                                    Detail SPK →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400 italic">
                                Belum ada riwayat pesanan event untuk customer ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
