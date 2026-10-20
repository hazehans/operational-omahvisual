@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Kelola Pengguna & Peran Hak Akses')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-indigo-400 mb-1">
                <span>Keamanan Sistem</span>
                <span>•</span>
                <span>Role &amp; Permissions</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-slate-400 mt-1">Daftar akun tim operasional, penetapan peran (role), dan kontrol akses sistem.</p>
        </div>

        <a href="{{ route('users.create') }}" class="px-5 py-2.5 rounded-xl bg-linear-to-r from-indigo-600 to-cyan-600 hover:from-indigo-500 hover:to-cyan-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>Tambah Pengguna Baru</span>
        </a>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-8 gap-3">
        <!-- Total -->
        <a href="{{ route('users.index') }}" class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md hover:border-indigo-500/50 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</span>
                <span class="w-2 h-2 rounded-full bg-indigo-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-xl font-black text-white font-mono">
                {{ $metrics['total'] }}
            </div>
            <p class="text-[9.5px] text-slate-500 mt-0.5">Semua akun</p>
        </a>

        <!-- Superuser & Owner -->
        <a href="{{ route('users.index', ['role' => 'Superuser']) }}" class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md hover:border-rose-500/50 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider">Super/Owner</span>
                <span class="w-2 h-2 rounded-full bg-rose-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-xl font-black text-rose-400 font-mono">
                {{ $metrics['superuser'] + $metrics['owner'] }}
            </div>
            <p class="text-[9.5px] text-slate-500 mt-0.5">Direksi &amp; root</p>
        </a>

        <!-- Manager Operasional -->
        <a href="{{ route('users.index', ['role' => 'Manager Operasional']) }}" class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md hover:border-blue-500/50 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Manager Ops</span>
                <span class="w-2 h-2 rounded-full bg-blue-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-xl font-black text-blue-400 font-mono">
                {{ $metrics['manager_ops'] }}
            </div>
            <p class="text-[9.5px] text-slate-500 mt-0.5">Kru &amp; SPK</p>
        </a>

        <!-- Admin LED -->
        <a href="{{ route('users.index', ['role' => 'Admin LED']) }}" class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md hover:border-cyan-500/50 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider">Admin LED</span>
                <span class="w-2 h-2 rounded-full bg-cyan-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-xl font-black text-cyan-400 font-mono">
                {{ $metrics['admin_led'] }}
            </div>
            <p class="text-[9.5px] text-slate-500 mt-0.5">Order sewa</p>
        </a>

        <!-- Finance -->
        <a href="{{ route('users.index', ['role' => 'Finance']) }}" class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md hover:border-emerald-500/50 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Finance</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-xl font-black text-emerald-400 font-mono">
                {{ $metrics['finance'] }}
            </div>
            <p class="text-[9.5px] text-slate-500 mt-0.5">Faktur &amp; gaji</p>
        </a>

        <!-- Warehouse -->
        <a href="{{ route('users.index', ['role' => 'Warehouse']) }}" class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md hover:border-amber-500/50 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Warehouse</span>
                <span class="w-2 h-2 rounded-full bg-amber-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-xl font-black text-amber-400 font-mono">
                {{ $metrics['warehouse'] }}
            </div>
            <p class="text-[9.5px] text-slate-500 mt-0.5">Fisik &amp; gate</p>
        </a>

        <!-- Kepala Servis -->
        <a href="{{ route('users.index', ['role' => 'Kepala Servis']) }}" class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md hover:border-violet-500/50 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-violet-400 uppercase tracking-wider">K. Servis</span>
                <span class="w-2 h-2 rounded-full bg-violet-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-xl font-black text-violet-400 font-mono">
                {{ $metrics['kepala_servis'] }}
            </div>
            <p class="text-[9.5px] text-slate-500 mt-0.5">Lead bengkel</p>
        </a>

        <!-- Teknisi Servis -->
        <a href="{{ route('users.index', ['role' => 'Teknisi Servis']) }}" class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md hover:border-teal-500/50 transition-all group">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-bold text-teal-400 uppercase tracking-wider">Teknisi</span>
                <span class="w-2 h-2 rounded-full bg-teal-500 group-hover:scale-125 transition-transform"></span>
            </div>
            <div class="text-xl font-black text-teal-400 font-mono">
                {{ $metrics['teknisi'] }}
            </div>
            <p class="text-[9.5px] text-slate-500 mt-0.5">Reparasi modul</p>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-md">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <!-- Search -->
            <div class="sm:col-span-7 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengguna, email, atau nomor WhatsApp..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-all">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Role Filter -->
            <div class="sm:col-span-3">
                <select name="role" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-sm text-white focus:outline-none focus:border-indigo-500 transition-all">
                    <option value="">Semua Peran / Role</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Filter -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold border border-slate-700 transition-all">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('users.index') }}" class="p-2.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white transition-colors" title="Reset filter">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-800/70 text-slate-300 uppercase tracking-wider font-semibold text-xs border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-4">Pengguna</th>
                        <th class="px-5 py-4">Peran / Role</th>
                        <th class="px-5 py-4">Kontak WhatsApp</th>
                        <th class="px-5 py-4">Waktu Pendaftaran</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-linear-to-tr from-indigo-600 via-indigo-500 to-cyan-500 text-white font-bold flex items-center justify-center text-xs shadow-md shrink-0">
                                        {{ $u->initials }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-white text-sm">{{ $u->name }}</span>
                                            @if($u->id === auth()->id())
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">Anda</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-slate-400 block">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($u->roles as $role)
                                        @php
                                            $badgeClass = match($role->name) {
                                                'Superuser' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                                'Owner' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                                'Branch Manager' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30',
                                                'Warehouse' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                                'Teknisi Servis' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                                'Admin LED' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                                                default => 'bg-slate-800 text-slate-300 border-slate-700',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $badgeClass }}">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-500 italic">Tanpa Peran</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-300">
                                @if($u->phone)
                                    <span>{{ $u->phone }}</span>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-400 font-mono">
                                {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit -->
                                    <a href="{{ route('users.edit', $u) }}" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Edit Data & Peran">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <!-- Quick Reset Password -->
                                    <form action="{{ route('users.reset-password', $u) }}" method="POST" onsubmit="return confirm('Reset kata sandi pengguna {{ $u->name }} ke default (password123)?')" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 transition-colors" title="Reset Password ke default (password123)">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </button>
                                    </form>

                                    <!-- Delete (Only if not self) -->
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-colors" title="Hapus Akun">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500 italic">
                                Belum ada data pengguna yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

