@extends('layouts.app')

@section('title', 'Edit Kru - ' . $crew->nickname)
@section('page_title', 'Edit Data Kru & Teknisi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('crews.index') }}" class="hover:text-indigo-600 transition-colors">Master Kru</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Edit Kru</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Kru: {{ $crew->nickname }}</h1>
        </div>

        <a href="{{ route('crews.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
            ← Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="{{ route('crews.update', $crew) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Section 1: Identitas Kru -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">1. Identitas Personal & HR</h2>
                    <p class="text-xs text-slate-500">ID Kru, nama panggilan, nama lengkap, dan tanggal lahir.</p>
                </div>
                <span class="px-3 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-bold border border-indigo-200 dark:border-indigo-800">
                    {{ $crew->crew_code ?? 'CRW-AUTO' }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Panggilan (Nickname Lapangan) *</label>
                    <input type="text" name="nickname" value="{{ old('nickname', $crew->nickname) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('nickname') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap (Sesuai KTP) *</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $crew->full_name) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('full_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $crew->birth_date?->format('Y-m-d')) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('birth_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Tanggal Awal Kerja</label>
                    <input type="date" name="join_date" value="{{ old('join_date', $crew->join_date?->format('Y-m-d') ?? $crew->created_at?->format('Y-m-d')) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('join_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Section 2: Posisi / Role (BISA LEBIH DARI SATU) & Status Kerja -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">2. Jabatan & Tipe Hubungan Kerja</h2>
                <p class="text-xs text-slate-500">Posisi dapat dipilih lebih dari satu (misal: PIC sekaligus Supir). Mendukung integrasi outsourcing / HRIS internal.</p>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Jabatan / Posisi Penugasan (Bisa Lebih Dari Satu) *
                </label>
                @php
                    $currentPositions = is_array($crew->positions) ? $crew->positions : (explode(', ', (string)$crew->position));
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($availablePositions as $posKey => $posLabel)
                        <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 cursor-pointer hover:border-indigo-500 transition-colors">
                            <input type="checkbox" name="positions[]" value="{{ $posKey }}"
                                {{ in_array($posKey, old('positions', $currentPositions)) ? 'checked' : '' }}
                                class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <span class="block text-xs font-bold text-slate-900 dark:text-white">{{ $posKey }}</span>
                                <span class="text-[10px] text-slate-500">{{ $posLabel }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('positions') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Status Hubungan Kerja *</label>
                    <select name="employment_type" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        @foreach($employmentTypes as $typeKey => $typeLabel)
                            <option value="{{ $typeKey }}" {{ old('employment_type', $crew->employment_type ?? 'freelance_outsourcing') === $typeKey ? 'selected' : '' }}>
                                {{ $typeLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('employment_type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">ID Karyawan HRIS (Opsional)</label>
                    <input type="text" name="hris_employee_id" value="{{ old('hris_employee_id', $crew->hris_employee_id) }}" placeholder="Misal: EMP-0042 (Jika dari webapp HRIS)"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika crew freelance murni / outsourcing non-karyawan.</p>
                </div>
            </div>

            <!-- Section 3: Kontak & Alamat -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">3. Kontak & Domisili</h2>
                <p class="text-xs text-slate-500">Nomor WhatsApp aktif, nomor darurat keluarga, dan alamat tempat tinggal.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp *</label>
                    <input type="text" name="phone" value="{{ old('phone', $crew->phone) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nomor Darurat (Keluarga)</label>
                    <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $crew->emergency_phone) }}" placeholder="081987654321"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('emergency_phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Email Kru</label>
                    <input type="email" name="email" value="{{ old('email', $crew->email) }}" placeholder="crew@gmail.com"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Alamat Tempat Tinggal</label>
                    <textarea name="address" rows="2"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">{{ old('address', $crew->address) }}</textarea>
                    @error('address') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Kota Domisili</label>
                    <input type="text" name="city" value="{{ old('city', $crew->city ?? 'Malang') }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('city') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Section 4: Foto Dokumen & Rate Card -->
            <div class="border-b border-slate-200 dark:border-slate-800 pb-2 pt-2">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">4. Lampiran Foto, KTP, dan Rate Card</h2>
                <p class="text-xs text-slate-500">Upload foto profil kru, kartu identitas KTP, dan foto tabel rate card bayaran kru dengan fitur crop dan hapus foto.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-2 bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Foto Profil Kru</label>
                    <input type="hidden" name="remove_photo" id="remove_crew_photo_input" value="0">
                    <div class="relative w-24 h-24 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                        <img id="crew_photo_preview" src="{{ $crew->photo_path ? asset('storage/' . $crew->photo_path) : '' }}" alt="{{ $crew->nickname }}" class="w-full h-full object-cover {{ $crew->photo_path ? '' : 'hidden' }}">
                        <div id="crew_photo_placeholder" class="text-center p-2 text-slate-400 {{ $crew->photo_path ? 'hidden' : '' }}">
                            <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-[9px]">1:1 Avatar Default</span>
                        </div>
                    </div>
                    <input type="file" name="photo" id="crew_photo_input" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    <div class="flex items-center justify-between pt-1">
                        <p class="text-[10px] text-slate-400">Pilih file baru untuk crop 1:1</p>
                        <div class="flex items-center gap-2">
                            <button type="button" id="crop_crew_photo_btn" class="hidden text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">✂️ Crop</button>
                            <button type="button" id="remove_crew_photo_btn" onclick="removeImage('crew_photo')" class="{{ $crew->photo_path ? '' : 'hidden' }} text-[11px] font-bold text-rose-500 hover:text-rose-600 hover:underline">🗑️ Hapus</button>
                            <button type="button" id="undo_crew_photo_btn" onclick="undoRemoveImage('crew_photo', '{{ $crew->photo_path ? asset('storage/' . $crew->photo_path) : '' }}')" class="hidden text-[11px] font-bold text-amber-500 hover:underline">↩️ Batal</button>
                        </div>
                    </div>
                    @error('photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2 bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Foto KTP Resmi</label>
                    <input type="hidden" name="remove_ktp_photo" id="remove_crew_ktp_input" value="0">
                    <div class="relative w-32 h-20 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                        <img id="crew_ktp_preview" src="{{ $crew->ktp_photo_path ? asset('storage/' . $crew->ktp_photo_path) : '' }}" alt="KTP {{ $crew->nickname }}" class="w-full h-full object-cover {{ $crew->ktp_photo_path ? '' : 'hidden' }}">
                        <div id="crew_ktp_placeholder" class="text-center p-2 text-slate-400 {{ $crew->ktp_photo_path ? 'hidden' : '' }}">
                            <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            <span class="text-[9px]">3:2 Rasio KTP</span>
                        </div>
                    </div>
                    <input type="file" name="ktp_photo" id="crew_ktp_input" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    <div class="flex items-center justify-between pt-1">
                        <p class="text-[10px] text-slate-400">Pilih file baru untuk crop 3:2</p>
                        <div class="flex items-center gap-2">
                            <button type="button" id="crop_crew_ktp_btn" class="hidden text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">✂️ Crop</button>
                            <button type="button" id="remove_crew_ktp_btn" onclick="removeImage('crew_ktp')" class="{{ $crew->ktp_photo_path ? '' : 'hidden' }} text-[11px] font-bold text-rose-500 hover:text-rose-600 hover:underline">🗑️ Hapus</button>
                            <button type="button" id="undo_crew_ktp_btn" onclick="undoRemoveImage('crew_ktp', '{{ $crew->ktp_photo_path ? asset('storage/' . $crew->ktp_photo_path) : '' }}')" class="hidden text-[11px] font-bold text-amber-500 hover:underline">↩️ Batal</button>
                        </div>
                    </div>
                    @error('ktp_photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2 bg-slate-50 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Foto Rate Card Bayaran</label>
                    <input type="hidden" name="remove_rate_card_photo" id="remove_crew_rate_card_input" value="0">
                    <div class="relative w-32 h-20 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden">
                        <img id="crew_rate_card_preview" src="{{ $crew->rate_card_photo_path ? asset('storage/' . $crew->rate_card_photo_path) : '' }}" alt="Rate Card {{ $crew->nickname }}" class="w-full h-full object-cover {{ $crew->rate_card_photo_path ? '' : 'hidden' }}">
                        <div id="crew_rate_card_placeholder" class="text-center p-2 text-slate-400 {{ $crew->rate_card_photo_path ? 'hidden' : '' }}">
                            <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-[9px]">Bebas / Freeform</span>
                        </div>
                    </div>
                    <input type="file" name="rate_card_photo" id="crew_rate_card_input" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-950/60 file:text-indigo-600 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                    <div class="flex items-center justify-between pt-1">
                        <p class="text-[10px] text-slate-400">Pilih file baru untuk crop bebas</p>
                        <div class="flex items-center gap-2">
                            <button type="button" id="crop_crew_rate_card_btn" class="hidden text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">✂️ Crop</button>
                            <button type="button" id="remove_crew_rate_card_btn" onclick="removeImage('crew_rate_card')" class="{{ $crew->rate_card_photo_path ? '' : 'hidden' }} text-[11px] font-bold text-rose-500 hover:text-rose-600 hover:underline">🗑️ Hapus</button>
                            <button type="button" id="undo_crew_rate_card_btn" onclick="undoRemoveImage('crew_rate_card', '{{ $crew->rate_card_photo_path ? asset('storage/' . $crew->rate_card_photo_path) : '' }}')" class="hidden text-[11px] font-bold text-amber-500 hover:underline">↩️ Batal</button>
                        </div>
                    </div>
                    @error('rate_card_photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Tautkan User Login & Status Aktif -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-200 dark:border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Tautkan Akun Login Sistem (Opsional)</label>
                    <select name="user_id" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Tidak Ditautkan (Kru Luar) --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $crew->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->roles->pluck('name')->implode(', ') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $crew->is_active) ? 'checked' : '' }}
                            class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Status Aktif (Tersedia untuk Ditugaskan SPK)</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('crews.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.setupImageCropper === 'function') {
            window.setupImageCropper('crew_photo_input', 'crew_photo_preview', 'crew_photo_placeholder', 1, 'crop_crew_photo_btn');
            window.setupImageCropper('crew_ktp_input', 'crew_ktp_preview', 'crew_ktp_placeholder', 3/2, 'crop_crew_ktp_btn');
            window.setupImageCropper('crew_rate_card_input', 'crew_rate_card_preview', 'crew_rate_card_placeholder', NaN, 'crop_crew_rate_card_btn');
        }
    });
</script>
@endsection
