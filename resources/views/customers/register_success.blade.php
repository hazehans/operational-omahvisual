<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Berhasil — Omah Visual LED</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 text-center space-y-5 shadow-2xl backdrop-blur-xl">
        <div class="w-16 h-16 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center mx-auto text-2xl">
            ✓
        </div>

        <div class="space-y-1.5">
            <h1 class="text-2xl font-black text-white">Terima Kasih!</h1>
            <p class="text-sm text-slate-400">
                Pendaftaran data klien <span class="text-indigo-400 font-bold">{{ session('registered_name') ?? ($customer ? ($customer->salutation . ' ' . $customer->name) : 'Anda') }}</span> telah berhasil disimpan.
            </p>
        </div>

        @if($customer)
            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 text-left space-y-1 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">ID Customer:</span>
                    <span class="font-mono font-bold text-indigo-400">{{ $customer->customer_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status Akun:</span>
                    <span class="font-bold text-amber-400">⏳ Menunggu Verifikasi Admin</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">No. WhatsApp:</span>
                    <span class="text-slate-300 font-mono">{{ $customer->phone }}</span>
                </div>
            </div>
        @endif

        <p class="text-xs text-slate-500 leading-relaxed">
            Tim operasional CV Han Media Tekindo akan segera memverifikasi data Anda dan menghubungi via WhatsApp untuk proses order layar LED.
        </p>

        <div class="pt-2">
            <a href="https://wa.me/6281804010484" target="_blank" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-md shadow-emerald-600/30">
                <span>Hubungi Admin Omah Visual via WhatsApp</span>
            </a>
        </div>
    </div>
</body>
</html>
