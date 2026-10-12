@extends('layouts.app')

@section('title', 'Edit Record #' . $id . ' - ' . $table)
@section('page_title', 'Edit Data Mentah: ' . $table . ' #' . $id)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Back -->
    <a href="{{ route('superuser.database.show', $table) }}" class="text-sm font-semibold text-slate-400 hover:text-white flex items-center gap-1.5 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Kembali ke Tabel {{ $meta['name'] }} ({{ $table }})</span>
    </a>

    <!-- Form Card -->
    <div class="p-6 md:p-8 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-xl">
        <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-800">
            <div>
                <span class="font-mono text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded border border-indigo-500/20">
                    {{ $table }} #{{ $id }}
                </span>
                <h1 class="text-2xl font-bold text-white mt-2">Edit Record Raw Database</h1>
                <p class="text-xs text-slate-400 mt-0.5">Perubahan langsung pada kolom database PostgreSQL</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                Mode Superuser
            </span>
        </div>

        <form action="{{ route('superuser.database.update', ['table' => $table, 'id' => $id]) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5">
                @foreach($columns as $col)
                    @php
                        $name = $col['name'];
                        $val = $record[$name] ?? null;
                        $typeName = $col['type_name'];
                        $isReadonly = in_array($name, ['id', 'created_at']);
                    @endphp

                    <div class="p-4 rounded-xl bg-slate-950/40 border border-slate-800/80">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-slate-200 font-mono">
                                {{ $name }}
                                @if(!$col['nullable'])
                                    <span class="text-rose-400">*</span>
                                @endif
                            </label>
                            <span class="text-[11px] text-slate-500 font-mono">
                                {{ $typeName }} {{ $col['nullable'] ? '• nullable' : '• required' }}
                            </span>
                        </div>

                        @if($name === 'id')
                            <input type="text" value="{{ $val }}" readonly
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-sm font-mono text-slate-400 cursor-not-allowed">
                        @elseif($name === 'password' && $table === 'users')
                            <input type="password" name="{{ $name }}" placeholder="Biarkan kosong jika tidak ingin mengubah password"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                            <p class="text-xs text-slate-500 mt-1">Kosongkan jika password lama tetap dipertahankan.</p>
                        @elseif($typeName === 'bool' || $col['type'] === 'boolean')
                            <select name="{{ $name }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                                <option value="1" {{ $val ? 'selected' : '' }}>true (1)</option>
                                <option value="0" {{ !$val ? 'selected' : '' }}>false (0)</option>
                            </select>
                        @elseif(in_array($typeName, ['json', 'jsonb']))
                            <textarea name="{{ $name }}" rows="4" font-mono
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-800 text-xs font-mono text-emerald-400 focus:outline-none focus:border-indigo-500 transition-colors">{{ is_array($val) || is_object($val) ? json_encode($val, JSON_PRETTY_PRINT) : $val }}</textarea>
                        @elseif($typeName === 'text')
                            <textarea name="{{ $name }}" rows="3"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-950/80 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">{{ $val }}</textarea>
                        @else
                            <input type="text" name="{{ $name }}" value="{{ $val }}" {{ $isReadonly ? 'readonly' : '' }}
                                class="w-full px-4 py-2.5 rounded-xl {{ $isReadonly ? 'bg-slate-900 text-slate-400 cursor-not-allowed' : 'bg-slate-950/80 text-white focus:border-indigo-500' }} border border-slate-800 text-sm font-mono focus:outline-none transition-colors">
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Submit -->
            <div class="pt-6 flex items-center justify-end gap-3 border-t border-slate-800">
                <a href="{{ route('superuser.database.show', $table) }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all">
                    Simpan Perubahan Data
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

