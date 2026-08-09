<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - KeuanganApp</title>

    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="KeuanganApp">
    <meta name="description" content="Aplikasi pencatatan keuangan pribadi: pemasukan, pengeluaran, mutasi, target & capaian.">

    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
        }
    </script>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900">

    <div x-data="{
        sidebar: false,
        collapse: window.innerWidth >= 768 && localStorage.getItem('sidebarCollapse') === 'true',
        init() {
            this.$watch('collapse', val => localStorage.setItem('sidebarCollapse', val))
        }
    }" class="flex h-screen overflow-hidden">

        @include('partials.sidebar')

        <div class="flex flex-col flex-1 overflow-hidden">

            @include('partials.topbar')

            <main class="flex-1 overflow-y-auto p-3 md:p-6 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 4000)"
                            class="mb-6 flex items-center gap-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4">
                            
                            <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-500 shrink-0" />
                            <span class="text-sm font-medium">{{ session('success') }}</span>

                        </div>
                    @endif

                    @yield('content')
                </div>

                {{-- Global Footer inside Content --}}
                <footer class="mt-12 py-6 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-400 font-medium">
                        &copy; {{ date('Y') }} KeuanganApp &bull; Sistem Pencatatan Finansial Terpadu
                    </p>
                </footer>
            </main>

        </div>

    </div>

    <x-confirm-logout-modal />

    @stack('scripts')

</body>

</html>
