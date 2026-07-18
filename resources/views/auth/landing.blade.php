<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KeuanganApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-5xl">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <div class="p-8 md:p-12 bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex flex-col justify-between">
                        <div>
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-white/15 rounded-xl mb-5">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>
                            <h1 class="text-2xl font-bold leading-tight">Kelola Keuangan dengan Rapi</h1>
                            <p class="mt-3 text-blue-200 text-sm leading-relaxed">
                                Catat pemasukan dan pengeluaran per periode, pantau saldo setiap akun, 
                                dan lihat grafik keuangan dengan mudah.
                            </p>
                        </div>

                        <div class="mt-8 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Periode</div>
                                <div class="text-blue-200/80 text-xs mt-0.5">Atur tahun/bulan</div>
                            </div>
                            <div class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Transaksi</div>
                                <div class="text-blue-200/80 text-xs mt-0.5">Income & Expense</div>
                            </div>
                            <div class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Mutasi</div>
                                <div class="text-blue-200/80 text-xs mt-0.5">Antar akun</div>
                            </div>
                            <div class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Pantau</div>
                                <div class="text-blue-200/80 text-xs mt-0.5">Saldo & Grafik</div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                            <span class="text-xs text-blue-200">Siap mulai? Login atau buat akun.</span>
                        </div>
                    </div>

                    <div class="p-8 md:p-12 flex flex-col justify-center">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Selamat Datang</h2>
                            <p class="text-gray-500 mt-2 text-sm leading-relaxed">
                                Aplikasi pencatatan keuangan pribadi yang sederhana dan terstruktur.
                            </p>
                        </div>

                        <div class="space-y-3">
                            <a href="{{ route('login') }}"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-3 text-white font-semibold hover:bg-blue-700 transition shadow-sm">
                                Login
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-3 text-gray-700 font-semibold hover:bg-gray-50 transition">
                                    Buat Akun Baru
                                </a>
                            @endif
                        </div>

                        <div class="mt-6 bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <div class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Cara Kerja</div>
                            <ol class="mt-3 text-sm text-gray-600 space-y-2">
                                <li class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">1</span>
                                    Buat periode keuangan
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">2</span>
                                    Catat pemasukan & pengeluaran
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">3</span>
                                    Pantau saldo di dashboard
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'KeuanganApp') }}
            </p>
        </div>
    </div>
</body>
</html>
