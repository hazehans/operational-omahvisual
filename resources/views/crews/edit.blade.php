@extends('layouts.app')

@section('title', 'Edit Kru - ' . $crew->nickname)
@section('page_title', 'Edit Data Kru & Teknisi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('crews.index') }}" class="hover:text-indigo-600 transition-colors">Master Kru</a>
                <span>/</span>
                <span class="text-slate-800 dark:text-slate-200">Edit Kru</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit Kru: {{ $crew->nickname }} ({{ $crew->crew_code }})</h1>
        </div>

        <a href="{{ route('crews.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
            ← Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-slate-900/60 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form action="{{ route('crews.update', $crew) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nama Panggilan (Nickname) *</label>
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
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Posisi / Spesialisasi *</label>
                    <select name="position" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        @foreach(['Lead Technician', 'Visual Operator', 'Rigger & Ground Tech', 'Electrical Specialist', 'Driver & Logistik', 'Crew Lapangan'] as $pos)
                            <option value="{{ $pos }}" {{ old('position', $crew->position) === $pos ? 'selected' : '' }}>{{ $pos }}</option>
                        @endforeach
                    </select>
                    @error('position') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Akun Login Sistem (Opsional)</label>
                    <select name="user_id" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Tidak ditautkan ke akun login --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $crew->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nomor WhatsApp *</label>
                    <input type="text" name="phone" value="{{ old('phone', $crew->phone) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Nomor Telepon Darurat (Keluarga)</label>
                    <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $crew->emergency_phone) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                    @error('emergency_phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Kota Asal / Domisili</label>
                    <input type="text" name="city" value="{{ old('city', $crew->city) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Alamat Tinggal</label>
                    <input type="text" name="address" value="{{ old('address', $crew->address) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-sm text-slate-900 dark:text-white focus:outline-hidden focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $crew->is_active) ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Status Aktif & Siap Ditugaskan ke Event</span>
                </label>
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
@endsection

