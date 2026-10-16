<!DOCTYPE html>
<html lang="id" class="dark" id="html-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Omah Visual LED</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        /* Scrollbar custom */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.3);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 116, 139, 0.5);
        }
    </style>
    <script>
        // Init theme immediately before DOM render
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="min-h-screen bg-white text-slate-800 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-200 selection:bg-indigo-500 selection:text-white flex overflow-x-hidden">

    <!-- Subtle Ambient Minimalist Gradients -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden z-0">
        <div class="absolute -top-32 -left-32 w-lg h-128 bg-indigo-600/10 dark:bg-indigo-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 -right-32 w-md h-112 bg-cyan-600/10 dark:bg-cyan-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 left-1/3 w-xl h-144 bg-blue-600/10 dark:bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] dark:bg-[radial-gradient(#334155_1px,transparent_1px)] bg-size-[24px_24px] opacity-20 dark:opacity-25"></div>
    </div>

    <!-- Sidebar Navigation -->
    <aside class="relative z-20 w-64 shrink-0 border-r border-slate-200 dark:border-slate-800/80 bg-slate-50/80 dark:bg-slate-900/70 backdrop-blur-xl flex flex-col justify-between min-h-screen">
        <div>
            <!-- Brand / Logo -->
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800/60 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-md shadow-indigo-500/20 text-white font-bold">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-bold tracking-tight text-slate-900 dark:text-white block leading-tight">OMAH VISUAL LED</span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Operasional & Gudang</span>
                </div>
            </div>

            <!-- Main Nav Links -->
            <nav class="px-3 py-4 space-y-1">
                <div class="px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    Navigasi Utama
                </div>

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Master Barang -->
                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Master Barang</span>
                </a>

                <!-- Unit Aset Individual -->
                <a href="{{ route('assets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('assets.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <div class="flex items-center justify-between flex-1">
                        <span>Aset Serial/Unit</span>
                        <span class="px-1.5 py-0.5 text-[10px] rounded bg-indigo-500/20 text-indigo-400 font-mono">25 Proc</span>
                    </div>
                </a>

                <!-- Titik Lokasi Lemari -->
                <a href="{{ route('locations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('locations.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Lokasi & Lemari</span>
                </a>

                @hasanyrole('Owner|Superuser')
                <div class="pt-4 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    Event & Persewaan LED
                </div>

                <!-- Pesanan Event & SPK -->
                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('orders.*') || request()->routeIs('spk.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div class="flex items-center justify-between flex-1">
                        <span>Pesanan & SPK LED</span>
                        <span class="px-1.5 py-0.5 text-[10px] rounded bg-emerald-500/20 text-emerald-400 font-bold">New</span>
                    </div>
                </a>

                <!-- Data Customer -->
                <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('customers.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Data Customer</span>
                </a>

                <!-- Kru & Teknisi -->
                <a href="{{ route('crews.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('crews.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Kru & Teknisi</span>
                </a>

                <!-- Armada Kendaraan -->
                <a href="{{ route('vehicles.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('vehicles.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    <span>Armada Kendaraan</span>
                </a>
                @endhasanyrole

                <div class="pt-4 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    Operasional Inventori
                </div>

                @can('record movement')
                <!-- Mutasi Stok -->
                <a href="{{ route('movements.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('movements.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span>Mutasi Stok</span>
                </a>
                @endcan

                @can('conduct stock count')
                <!-- Sahkan Stok Awal (Decision 2 User) -->
                @php
                    $isPendingInitialCount = \App\Models\InventoryCount::where('status', '!=', 'verified')->exists();
                @endphp
                <a href="{{ route('initial-counts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('initial-counts.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <div class="flex items-center justify-between flex-1">
                        <span>Sahkan Stok Awal</span>
                        @if($isPendingInitialCount)
                            <span class="relative flex h-2 w-2" title="Menunggu Pengesahan Warehouse">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-bold" title="Stok Awal Sudah Sah">
                                ✓
                            </span>
                        @endif
                    </div>
                </a>
                @endcan

                @can('request stock adjustment')
                <!-- Penyesuaian & Approval -->
                <a href="{{ route('adjustments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('adjustments.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Koreksi & Approval</span>
                </a>
                @endcan

                <!-- Servis & Workshop -->
                @php
                    $activeServiceCount = \App\Models\ServiceTicket::whereNotIn('status', ['completed', 'scrapped'])->count();
                @endphp
                <a href="{{ route('service-tickets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('service-tickets.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div class="flex items-center justify-between flex-1">
                        <span>Servis & Workshop</span>
                        @if($activeServiceCount > 0)
                            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-amber-500/20 text-amber-400 font-bold font-mono">
                                {{ $activeServiceCount }}
                            </span>
                        @endif
                    </div>
                </a>

                @can('view audit logs')
                <!-- Audit Trail -->
                <a href="{{ route('audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('audit-logs.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Audit Trail</span>
                </a>
                @endcan

                @hasanyrole('Owner|Superuser')
                <div class="pt-4 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    Sistem &amp; Hak Akses
                </div>

                <!-- Manajemen Pengguna -->
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <div class="flex items-center justify-between flex-1">
                        <span>Manajemen Pengguna</span>
                        <span class="px-1.5 py-0.5 text-[10px] rounded bg-indigo-500/20 text-indigo-400 font-mono font-bold">{{ \App\Models\User::count() }}</span>
                    </div>
                </a>
                @endhasanyrole

                @hasrole('Superuser')
                <!-- Superuser Database Manager -->
                <a href="{{ route('superuser.database.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all {{ request()->routeIs('superuser.database.*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/20' : 'text-rose-400/80 hover:text-rose-300 hover:bg-rose-500/10' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    <div class="flex items-center justify-between flex-1">
                        <span>Database Manager</span>
                        <span class="px-1.5 py-0.5 text-[10px] rounded bg-rose-500/20 text-rose-400 font-bold">RAW</span>
                    </div>
                </a>
                @endhasrole
            </nav>
        </div>

        <!-- Sidebar Footer / User Profile -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800/60 bg-slate-100/60 dark:bg-slate-950/40">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-linear-to-tr from-indigo-500 to-cyan-500 text-white font-bold flex items-center justify-center text-sm shadow-md shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate leading-tight">{{ Auth::user()->name ?? 'Guest' }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            {{ Auth::user()->getRoleNames()->first() ?? 'Member' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="relative z-10 flex-1 flex flex-col min-w-0">
        <!-- Topbar -->
        <header class="h-16 px-6 border-b border-slate-200 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/60 backdrop-blur-xl flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-200/60 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Gedung Utama Omah Visual
                    </span>
                    <span class="hidden sm:inline text-slate-400">/</span>
                    <span class="hidden sm:inline font-semibold text-slate-800 dark:text-slate-200">@yield('page_title', 'Ringkasan Sistem')</span>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <!-- User Profile Link -->
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-slate-200/60 dark:hover:bg-slate-800/60 transition-colors {{ request()->routeIs('profile.*') ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-700 dark:text-slate-200' }}" title="Pengaturan Profil & Keamanan">
                    @if(Auth::user()->avatar_url)
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-7 h-7 rounded-full object-cover border border-slate-700 shadow-xs">
                    @else
                        <div class="w-7 h-7 rounded-full bg-linear-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white text-[11px] font-bold shadow-xs">
                            {{ Auth::user()->initials }}
                        </div>
                    @endif
                    <div class="hidden md:block text-left">
                        <div class="text-xs font-bold leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-400 capitalize">{{ Auth::user()->roles->first()?->name ?? 'Pengguna' }}</div>
                    </div>
                </a>

                <!-- Theme Toggle Button (Dark / Light) -->
                <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/60 transition-colors" title="Ganti Mode Gelap/Terang">
                    <svg id="theme-icon-dark" class="w-4 h-4 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg id="theme-icon-light" class="w-4 h-4 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 p-6 md:p-8">
            <!-- Global Flash Message Container -->
            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-start gap-3 shadow-lg shadow-emerald-500/5">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1 font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if (session('warning'))
                <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm flex items-start gap-3 shadow-lg shadow-amber-500/5">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1 font-medium">{{ session('warning') }}</div>
                </div>
            @endif

            @if (session('error') || $errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm flex items-start gap-3 shadow-lg shadow-rose-500/5">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1 font-medium">
                        {{ session('error') ?? $errors->first() }}
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm flex items-start gap-3 shadow-lg shadow-blue-500/5">
                    <svg class="w-5 h-5 shrink-0 mt-0.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1 font-medium">{{ session('info') }}</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Cropper.js Library & Global Modal -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    @include('components.image-cropper-modal')

    <!-- Modals Stack (Renders at Root DOM to avoid stacking context issues) -->
    @stack('modals')

    <!-- Scripts Stack -->
    @stack('scripts')

    <!-- Theme Toggle Script -->
    <script>
        function toggleTheme() {
            const html = document.getElementById('html-root');
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
</body>
</html>

