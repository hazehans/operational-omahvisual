<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal & Checklist Crew - {{ $order->event_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen pb-12 font-sans antialiased selection:bg-indigo-500 selection:text-white">

<div class="max-w-md mx-auto px-4 pt-6 space-y-4">
    <!-- Top Branding -->
    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center font-bold text-xs text-white">
                OV
            </div>
            <div>
                <div class="text-xs font-bold tracking-tight text-white">OMAH VISUAL LED</div>
                <div class="text-[10px] text-slate-400">Portal Crew Lapangan</div>
            </div>
        </div>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
            Token Valid
        </span>
    </div>

    @if(session('status'))
        <div class="p-3.5 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-semibold flex items-center gap-2">
            <span>✓</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 rounded-xl bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs space-y-1">
            @foreach($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <!-- Event Hero Card -->
    <div class="bg-linear-to-br from-indigo-950 via-slate-900 to-slate-900 p-5 rounded-2xl border border-indigo-800/50 shadow-xl space-y-3">
        <div class="text-xs font-mono text-indigo-400 font-bold uppercase tracking-wider">
            {{ $order->order_number }}
        </div>
        <h1 class="text-xl font-extrabold text-white leading-snug">
            {{ $order->event_name }}
        </h1>
        <div class="text-xs text-slate-300 flex items-center gap-1.5">
            <span>📍 {{ $order->venue_name }}</span>
        </div>

        @if($order->google_maps_url)
            <a href="{{ $order->google_maps_url }}" target="_blank" class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/30 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Buka Rute Google Maps Venue</span>
            </a>
        @endif
    </div>

    <!-- Schedule Box -->
    <div class="grid grid-cols-2 gap-3">
        <div class="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
            <div class="text-[10px] uppercase font-bold text-slate-400">Jadwal Loading</div>
            <div class="text-sm font-extrabold text-white mt-0.5">{{ $order->loading_date->format('d M Y') }}</div>
            <div class="text-xs font-semibold text-amber-400 mt-0.5">🕒 Pukul {{ $order->loading_time }}</div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/80">
            <div class="text-[10px] uppercase font-bold text-slate-400">Jadwal Acara</div>
            <div class="text-sm font-extrabold text-white mt-0.5">{{ $order->event_start_date->format('d M Y') }}</div>
            <div class="text-xs font-semibold text-emerald-400 mt-0.5">{{ $order->ready_time ?? 'Show Siap' }}</div>
        </div>
    </div>

    <!-- Stage & LED Layout Blueprint (2D & 3D Interactive) -->
    <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 space-y-3">
        <div class="flex items-center justify-between">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                <span>📐 Blueprint Layout Panggung</span>
            </div>
            <span class="text-[10px] px-2 py-0.5 rounded font-bold bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">
                Panggung {{ $order->stage_width_m ?? 10 }}m × {{ $order->stage_depth_m ?? 6 }}m
            </span>
        </div>

        @if($order->stage_layout_image || $order->stage_layout_front_image)
            <div class="space-y-3">
                @if($order->stage_layout_image)
                    <div class="rounded-xl overflow-hidden border border-slate-700 bg-slate-950 p-1.5 space-y-1">
                        <div class="text-[10px] font-bold text-cyan-400 px-1 flex items-center justify-between">
                            <span>🔲 Pemetaan Panel LED (Panel Grid & VJ Mapping)</span>
                            <span class="text-slate-500 font-normal">Nomor Urut Modul & Resolusi</span>
                        </div>
                        <img src="{{ $order->stage_layout_image }}" alt="Pemetaan Panel & Kabinet LED" class="w-full object-contain rounded-lg">
                    </div>
                @endif

                @if($order->stage_layout_front_image)
                    <div class="rounded-xl overflow-hidden border border-slate-700 bg-slate-950 p-1.5 space-y-1">
                        <div class="text-[10px] font-bold text-amber-400 px-1 flex items-center justify-between">
                            <span>🎭 Tampak Depan (Front Elevation)</span>
                            <span class="text-slate-500 font-normal">Level +{{ $order->stage_height_m ?? 0.6 }}m & Rigging</span>
                        </div>
                        <img src="{{ $order->stage_layout_front_image }}" alt="Layout Panggung Tampak Depan" class="w-full object-contain rounded-lg">
                    </div>
                @endif
            </div>
        @else
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-center space-y-1">
                <div class="text-xs font-bold text-slate-200">
                    Formasi: {{ $order->screens->map(fn($s) => $s->screen_name . ' (' . $s->width_m . 'x' . $s->height_m . 'm)')->implode(' + ') }}
                </div>
                <div class="text-[10px] text-slate-400">
                    Tinggi level panggung: {{ $order->stage_height_m ?? 0.6 }} meter
                </div>
            </div>
        @endif

        <!-- Toggle for 3D Viewer on Mobile -->
        <details class="group">
            <summary class="cursor-pointer text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center justify-between py-1">
                <span>🎮 Buka Simulasi Virtual 3D di Smartphone</span>
                <span class="group-open:rotate-180 transition-transform">▼</span>
            </summary>
            <div class="pt-2">
                <x-stage-designer-3d :order="$order" :screens="$order->screens" :isEditable="false" canvasHeight="320px" />
            </div>
        </details>
    </div>

    <!-- LED Screen Technical Summary -->
    <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-2.5">
        <div class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center justify-between">
            <span>🖥️ Spek Layar LED (Total {{ $order->total_sqm }} m²)</span>
            <span class="text-[11px] text-indigo-400 font-mono">{{ $order->screens->count() }} Layar</span>
        </div>

        @foreach($order->screens as $screen)
            <div class="p-2.5 rounded-lg bg-slate-900/80 border border-slate-800 text-xs space-y-1">
                <div class="flex items-center justify-between font-bold text-white">
                    <span>{{ $screen->screen_name }} ({{ $screen->width_m }}m × {{ $screen->height_m }}m)</span>
                    <span class="text-indigo-400">{{ $screen->total_sqm }} m²</span>
                </div>
                <div class="text-slate-400 text-[11px] flex justify-between">
                    <span>Seri: {{ $screen->brand_series }} ({{ $screen->pixel_pitch }})</span>
                    <span>{{ $screen->total_cabinets }} Kabinet ({{ $screen->total_hardcases }} Box)</span>
                </div>
                <div class="text-[11px] text-emerald-400 font-medium">
                    Prosesor: {{ $screen->processor_model }}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Crew Roster & Vehicle -->
    @if($order->spkAssignment)
        <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-2.5">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center justify-between">
                <span>🚐 Tim Lapangan & Armada</span>
                <span class="text-xs font-semibold text-slate-400">{{ $order->spkAssignment->vehicle_name ?? 'TRAGA 1' }}</span>
            </div>

            <div class="text-xs text-slate-300">
                PIC Event: <span class="font-bold text-white">{{ $order->spkAssignment->crew_pic_event_name ?? '-' }}</span>
            </div>

            <div class="grid grid-cols-2 gap-1.5 pt-1 text-xs">
                @for($i = 1; $i <= 7; $i++)
                    @php $field = "crew_{$i}_name"; @endphp
                    @if(!empty($order->spkAssignment->$field))
                        <div class="p-1.5 rounded bg-slate-900/60 border border-slate-800 flex items-center gap-1.5">
                            <span class="text-[10px] text-slate-500 font-mono">#{{ $i }}</span>
                            <span class="font-medium text-slate-200 truncate">{{ $order->spkAssignment->$field }}</span>
                        </div>
                    @endif
                @endfor
            </div>
        </div>

        <!-- Quick Checklist Accordion for Mobile Crew -->
        <div class="p-4 rounded-xl bg-slate-800/60 border border-slate-700/60 space-y-3">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-300">
                    📦 Checklist Muatan Barang
                </div>
                <span class="text-[11px] text-slate-400">
                    {{ $order->spkAssignment->checklistItems->count() }} Item
                </span>
            </div>

            <div class="space-y-1.5 max-h-80 overflow-y-auto pr-1">
                @foreach($order->spkAssignment->checklistItems as $item)
                    <div class="p-2 rounded-lg bg-slate-900/80 border border-slate-800 flex items-center justify-between text-xs">
                        <div class="pr-2">
                            <div class="font-medium text-slate-200">{{ $item->item_name }}</div>
                            <div class="text-[10px] text-slate-500">{{ $item->item_detail ?? $item->category }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="font-bold text-indigo-400">{{ $item->quantity }} {{ $item->unit }}</div>
                            <span class="text-[10px] {{ $item->checked_out ? 'text-emerald-400' : 'text-slate-500' }}">
                                {{ $item->checked_out ? '✓ Keluar' : '○ Pending' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Venue QC & BAST Card -->
        <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 space-y-3">
            <div class="flex items-center justify-between">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-200 flex items-center gap-1.5">
                    <span>⚡ Uji Kelayakan Venue & BAST</span>
                </div>
                @if($order->spkAssignment->qc_status === 'passed')
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        ✓ Lolos QC Siap Show
                    </span>
                @elseif($order->spkAssignment->qc_status === 'issues_found')
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                        ⚠️ Ada Kendala Teknis
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">
                        ○ Belum Diverifikasi
                    </span>
                @endif
            </div>

            @if($order->spkAssignment->qc_verified_at)
                <!-- QC Result Summary -->
                <div class="p-3 rounded-lg bg-slate-900/90 border border-slate-800 space-y-2 text-xs">
                    <div class="flex justify-between items-center text-[11px] text-slate-400">
                        <span>Diverifikasi oleh: <strong class="text-white">{{ $order->spkAssignment->qc_technician_name }}</strong></span>
                        <span>{{ $order->spkAssignment->qc_verified_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5 text-[10px] text-center pt-1 font-semibold">
                        <div class="p-1.5 rounded {{ $order->spkAssignment->qc_grounding_safe ? 'bg-emerald-950/40 text-emerald-400 border border-emerald-800/50' : 'bg-slate-800 text-slate-500' }}">
                            ⚡ Grounding {{ $order->spkAssignment->qc_grounding_safe ? 'OK' : '-' }}
                        </div>
                        <div class="p-1.5 rounded {{ $order->spkAssignment->qc_test_rgb ? 'bg-emerald-950/40 text-emerald-400 border border-emerald-800/50' : 'bg-slate-800 text-slate-500' }}">
                            🎨 RGB Test {{ $order->spkAssignment->qc_test_rgb ? 'OK' : '-' }}
                        </div>
                        <div class="p-1.5 rounded {{ $order->spkAssignment->qc_video_signal_ok ? 'bg-emerald-950/40 text-emerald-400 border border-emerald-800/50' : 'bg-slate-800 text-slate-500' }}">
                            📺 Sinyal {{ $order->spkAssignment->qc_video_signal_ok ? 'OK' : '-' }}
                        </div>
                    </div>
                    @if($order->spkAssignment->qc_notes)
                        <div class="text-[11px] text-slate-300 italic pt-1 border-t border-slate-800">
                            "{{ $order->spkAssignment->qc_notes }}"
                        </div>
                    @endif
                    @if($order->spkAssignment->qc_photo_path)
                        <div class="pt-1">
                            <a href="{{ asset('storage/' . $order->spkAssignment->qc_photo_path) }}" target="_blank" class="block rounded-lg overflow-hidden border border-slate-700 bg-slate-950">
                                <img src="{{ asset('storage/' . $order->spkAssignment->qc_photo_path) }}" alt="Bukti Panggung LED" class="w-full h-40 object-cover">
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Collapsible Form for Crew to Submit / Update -->
            <details class="group" {{ $order->spkAssignment->qc_status === 'pending' ? 'open' : '' }}>
                <summary class="cursor-pointer text-xs font-bold text-indigo-400 hover:text-indigo-300 flex items-center justify-between py-2 px-3 rounded-xl bg-slate-900/60 border border-slate-700/60">
                    <span>✏️ {{ $order->spkAssignment->qc_verified_at ? 'Edit / Perbarui Laporan QC & Foto' : 'Isi Laporan Uji Kelayakan & BAST' }}</span>
                    <span class="group-open:rotate-180 transition-transform">▼</span>
                </summary>

                <form action="{{ route('spk.submit-qc', $order->public_token) }}" method="POST" enctype="multipart/form-data" class="pt-3 space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Nama Teknisi / PIC Pemeriksa *</label>
                        <input type="text" name="qc_technician_name" value="{{ old('qc_technician_name', $order->spkAssignment->qc_technician_name ?? $order->spkAssignment->crew_pic_event_name) }}" required placeholder="Nama lengkap teknisi..." class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white">
                    </div>

                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-900/60 border border-slate-800 cursor-pointer">
                            <input type="checkbox" name="qc_grounding_safe" value="1" {{ old('qc_grounding_safe', $order->spkAssignment->qc_grounding_safe) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-0">
                            <span class="text-slate-200">⚡ Grounding Aman (< 2V AC, terhubung panggung)</span>
                        </label>

                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-900/60 border border-slate-800 cursor-pointer">
                            <input type="checkbox" name="qc_test_rgb" value="1" {{ old('qc_test_rgb', $order->spkAssignment->qc_test_rgb) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-0">
                            <span class="text-slate-200">🎨 Pola Warna RGB (Red, Green, Blue, White bersih)</span>
                        </label>

                        <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-900/60 border border-slate-800 cursor-pointer">
                            <input type="checkbox" name="qc_video_signal_ok" value="1" {{ old('qc_video_signal_ok', $order->spkAssignment->qc_video_signal_ok) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-0">
                            <span class="text-slate-200">📺 Sinyal Video & Refresh 3840Hz Stabil (No flicker)</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Status Kelayakan Layar *</label>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-900/60 border border-slate-800 cursor-pointer">
                                <input type="radio" name="qc_status" value="passed" {{ old('qc_status', $order->spkAssignment->qc_status) !== 'issues_found' ? 'checked' : '' }} class="text-emerald-500">
                                <span class="text-emerald-400 font-bold">✅ Siap Show</span>
                            </label>
                            <label class="flex items-center gap-2 p-2 rounded-lg bg-slate-900/60 border border-slate-800 cursor-pointer">
                                <input type="radio" name="qc_status" value="issues_found" {{ old('qc_status', $order->spkAssignment->qc_status) === 'issues_found' ? 'checked' : '' }} class="text-amber-500">
                                <span class="text-amber-400 font-bold">⚠️ Ada Kendala</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2 bg-slate-900/70 p-3 rounded-lg border border-slate-700/60">
                        <label class="block text-[11px] font-bold text-slate-300">Foto Bukti Layar Panggung (BAST Visual)</label>
                        <input type="hidden" name="remove_qc_photo" id="remove_qc_photo_live" value="0">

                        <div class="relative w-full h-36 rounded-lg border border-slate-700 bg-slate-950 flex items-center justify-center overflow-hidden">
                            <img id="live_qc_photo_preview" src="{{ $order->spkAssignment->qc_photo_path ? asset('storage/' . $order->spkAssignment->qc_photo_path) : '' }}" alt="QC Layar" class="w-full h-full object-cover {{ $order->spkAssignment->qc_photo_path ? '' : 'hidden' }}">
                            <div id="live_qc_photo_placeholder" class="text-center p-3 text-slate-500 {{ $order->spkAssignment->qc_photo_path ? 'hidden' : '' }}">
                                <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-[10px]">Belum ada foto panggung yang diunggah</span>
                            </div>
                        </div>

                        <input type="file" name="qc_photo" id="live_qc_photo_input" accept="image/*" capture="environment" class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-slate-200 cursor-pointer">
                        <div id="compress_notice" class="hidden text-[10px] text-emerald-400 font-semibold pt-0.5"></div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[10px] text-slate-500">Kamera atau Galeri HP (Maks 15MB)</span>
                            <div class="flex items-center gap-2">
                                <button type="button" id="btn_delete_live_qc_photo" class="{{ $order->spkAssignment->qc_photo_path ? '' : 'hidden' }} text-[11px] font-bold text-rose-400 hover:text-rose-300 hover:underline">🗑️ Hapus Foto</button>
                                <button type="button" id="btn_undo_live_qc_photo" class="hidden text-[11px] font-bold text-amber-400 hover:underline">↩️ Batal Hapus</button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-300 mb-1">Catatan Teknis Lapangan (Opsional)</label>
                        <textarea name="qc_notes" rows="2" placeholder="Contoh: Modul cadangan terpasang 1 pcs di layar kiri..." class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white placeholder-slate-500">{{ old('qc_notes', $order->spkAssignment->qc_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30 transition-all">
                        <span>{{ $order->spkAssignment->qc_verified_at ? 'Simpan Perubahan Laporan QC & BAST' : 'Kirim Laporan QC & BAST ke Kantor' }}</span>
                    </button>
                </form>
            </details>
        </div>
    @endif

    <div class="text-center text-[10px] text-slate-500 pt-4">
        Omah Visual LED ERP System • Halaman Resmi Tim Lapangan
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInp = document.getElementById('live_qc_photo_input');
    const preview = document.getElementById('live_qc_photo_preview');
    const placeholder = document.getElementById('live_qc_photo_placeholder');
    const removeInp = document.getElementById('remove_qc_photo_live');
    const btnDelete = document.getElementById('btn_delete_live_qc_photo');
    const btnUndo = document.getElementById('btn_undo_live_qc_photo');
    const originalSrc = "{{ $order->spkAssignment->qc_photo_path ? asset('storage/' . $order->spkAssignment->qc_photo_path) : '' }}";

    const compressNotice = document.getElementById('compress_notice');

    fileInp?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (compressNotice) {
                compressNotice.textContent = 'Memproses & mengompresi foto...';
                compressNotice.classList.remove('hidden');
            }

            const reader = new FileReader();
            reader.onload = function(evt) {
                const img = new Image();
                img.onload = function() {
                    const maxDim = 1920;
                    let w = img.width;
                    let h = img.height;

                    if (w > maxDim || h > maxDim) {
                        if (w > h) {
                            h = Math.round((h * maxDim) / w);
                            w = maxDim;
                        } else {
                            w = Math.round((w * maxDim) / h);
                            h = maxDim;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = w;
                    canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);

                    canvas.toBlob(function(blob) {
                        if (blob) {
                            try {
                                const dt = new DataTransfer();
                                const compressedFile = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), { type: 'image/jpeg' });
                                dt.items.add(compressedFile);
                                fileInp.files = dt.files;

                                const originalMB = (file.size / (1024 * 1024)).toFixed(1);
                                const compressedKB = Math.round(blob.size / 1024);
                                if (compressNotice) {
                                    compressNotice.textContent = `✓ Foto dikompres: ${originalMB}MB → ${compressedKB}KB (HD Siap Upload)`;
                                    compressNotice.classList.remove('hidden');
                                }
                            } catch (err) {
                                console.warn('DataTransfer File replacement not supported, using original file', err);
                            }
                        }

                        preview.src = canvas.toDataURL('image/jpeg', 0.85);
                        preview.classList.remove('hidden');
                        placeholder.classList.add('hidden');
                        if (removeInp) removeInp.value = '0';
                        btnDelete?.classList.remove('hidden');
                        btnUndo?.classList.add('hidden');
                    }, 'image/jpeg', 0.85);
                };
                img.src = evt.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    btnDelete?.addEventListener('click', function() {
        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        placeholder?.classList.remove('hidden');
        if (fileInp) fileInp.value = '';
        if (removeInp) removeInp.value = '1';
        btnDelete?.classList.add('hidden');
        compressNotice?.classList.add('hidden');
        if (originalSrc && btnUndo) {
            btnUndo.classList.remove('hidden');
        }
    });

    btnUndo?.addEventListener('click', function() {
        if (originalSrc) {
            if (preview) {
                preview.src = originalSrc;
                preview.classList.remove('hidden');
            }
            placeholder?.classList.add('hidden');
            if (removeInp) removeInp.value = '0';
            btnDelete?.classList.remove('hidden');
            btnUndo?.classList.add('hidden');
            compressNotice?.classList.add('hidden');
        }
    });
});
</script>

</body>
</html>

