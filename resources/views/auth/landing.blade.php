<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KeuanganApp</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen"
       style="background-image: url('/images/landing-background.jpg'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="fixed inset-0 bg-slate-900/70" aria-hidden="true"></div>
    <main class="relative min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-5xl">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <section aria-labelledby="landing-title" class="p-8 md:p-12 bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex flex-col justify-between">
                        <div>
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-white/15 rounded-xl mb-5">
                                <svg aria-hidden="true" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>
                            <h1 id="landing-title" class="text-2xl font-bold leading-tight">Kelola Keuangan dengan Rapi</h1>
                            <p class="mt-3 text-blue-200 text-sm leading-relaxed">
                                Catat pemasukan dan pengeluaran per periode, pantau saldo setiap akun, 
                                dan lihat grafik keuangan dengan mudah.
                            </p>
                        </div>

                        <div role="list" class="mt-8 grid grid-cols-2 gap-3">
                            <div role="listitem" class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Periode</div>
                                <div class="text-blue-200/80 text-xs mt-0.5">Atur tahun/bulan</div>
                            </div>
                            <div role="listitem" class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Transaksi</div>
                                <div class="text-blue-200/80 text-xs mt-0.5">Income & Expense</div>
                            </div>
                            <div role="listitem" class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Mutasi</div>
                                <div class="text-blue-200/80 text-xs mt-0.5">Antar akun</div>
                            </div>
                            <div role="listitem" class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Pantau</div>
                                <div class="text-blue-200/80 text-xs mt-0.5">Saldo & Grafik</div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-400" aria-hidden="true"></div>
                            <span class="text-xs text-blue-200">Siap mulai? Login atau buat akun.</span>
                        </div>
                    </section>

                    <section aria-labelledby="landing-welcome" class="p-8 md:p-12 flex flex-col justify-center">
                        <div class="mb-6">
                            <h2 id="landing-welcome" class="text-2xl font-bold text-gray-900">Selamat Datang</h2>
                            <p class="text-gray-500 mt-2 text-sm leading-relaxed">
                                Aplikasi pencatatan keuangan pribadi yang sederhana dan terstruktur.
                            </p>
                        </div>

                        <nav aria-label="Aksi akun" class="space-y-3">
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

                            <a href="{{ route('usage') }}"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-blue-700 font-semibold hover:bg-blue-100 transition">
                                <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Cara Pemakaian
                            </a>
                        </nav>

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
                    </section>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
