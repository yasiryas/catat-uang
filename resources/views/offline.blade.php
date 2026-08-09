<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luring - KeuanganApp</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900 min-h-screen flex items-center justify-center px-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-md w-full p-6 text-center">
        <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0-2.829-2.829m2.829 2.829L15.536 17M3 3l18 18m-9-12a3 3 0 013 3m-3-3v8"/></svg>
        </div>
        <h1 class="text-xl font-bold">Anda sedang luring</h1>
        <p class="text-slate-500 mt-2 text-sm leading-relaxed">
            Koneksi internet tidak tersedia. Periksa kembali koneksi Anda, lalu coba muat ulang halaman.
        </p>
        <button onclick="location.reload()"
            class="mt-6 inline-flex items-center justify-center w-full rounded-lg bg-blue-600 px-4 py-2.5 text-white font-semibold hover:bg-blue-700 transition shadow-sm">
            Coba Lagi
        </button>
    </div>
</body>
</html>