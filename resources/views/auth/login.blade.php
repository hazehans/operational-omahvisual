<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Omah Visual LED Operational</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-indigo-500 selection:text-white relative overflow-hidden">
    <!-- Ambient Minimalist Broadcast Gradients -->
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] bg-size-[24px_24px] opacity-25"></div>
    </div>

    <!-- Header Mini -->
    <header class="relative z-10 w-full px-6 py-6 max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-linear-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20 ring-1 ring-white/20">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <span class="text-base font-bold tracking-tight text-white block leading-tight">OMAH VISUAL LED</span>
                <span class="text-xs text-slate-400 font-medium">CV Han Media Tekindo</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                Sistem Operasional v1.0
            </span>
        </div>
    </header>

    <!-- Content / Login Card -->
    <main class="relative z-10 w-full max-w-md mx-auto px-6 py-8">
        <div class="bg-slate-900/70 border border-slate-800/80 rounded-2xl shadow-2xl shadow-black/40 backdrop-blur-xl p-8 transition-all">

            <div class="mb-8">
                <h1 class="text-2xl font-bold tracking-tight text-white">Selamat Datang</h1>
                <p class="text-sm text-slate-400 mt-1.5">Silakan masuk menggunakan kredensial akun Anda.</p>
            </div>

            <!-- Feedback Alerts -->
            @if (session('info'))
                <div class="mb-6 p-3.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-300 text-sm flex items-start gap-2.5">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm flex items-start gap-2.5">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@omahvisual.com"
                            class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required
                            placeholder="••••••••••••"
                            class="w-full pl-11 pr-4 py-2.5 rounded-xl bg-slate-950/60 border border-slate-800 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-200 transition-colors">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                    <span class="text-slate-500">Internal Office</span>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-linear-to-r from-indigo-600 via-indigo-500 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 text-white font-semibold text-sm shadow-lg shadow-indigo-600/25 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                        <span>Masuk ke Dashboard</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Catatan Kredensial untuk Pengujian -->
        <div class="mt-6 text-center">
            <p class="text-xs text-slate-500">
                Akun terdaftar: <span class="text-slate-400">owner@omahvisual.com</span> / <span class="text-slate-400">warehouse@omahvisual.com</span> (Password: <span class="text-slate-400">password123</span>)
            </p>
        </div>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 w-full px-6 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} CV Han Media Tekindo — Omah Visual LED. Hak Cipta Dilindungi.
    </footer>
</body>
</html>

