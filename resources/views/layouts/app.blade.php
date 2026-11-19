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
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js?v={{ filemtime(public_path('sw.js')) }}', { updateViaCache: 'none' }));
        }
    </script>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-[100] focus:m-3 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow">Lewati ke konten utama</a>

    <div x-data="{
        sidebar: false,
        collapse: window.innerWidth >= 768 && localStorage.getItem('sidebarCollapse') === 'true',
        tooltipText: '',
        tooltipTop: 0,
        showTooltip(event, label) {
            if (! this.collapse || window.innerWidth < 768) return
            const rect = event.currentTarget.getBoundingClientRect()
            this.tooltipText = label
            this.tooltipTop = rect.top + rect.height / 2 - 14
        },
        hideTooltip() {
            this.tooltipText = ''
        },
        revealActiveMenu() {
            const nav = this.$refs.sidebarNav
            const active = nav?.querySelector('[aria-current=page]')

            if (! nav || ! active) {
                return
            }

            const gap = 12
            const navRect = nav.getBoundingClientRect()
            const activeRect = active.getBoundingClientRect()

            if (activeRect.bottom > navRect.bottom) {
                nav.scrollTop += activeRect.bottom - navRect.bottom + gap
            } else if (activeRect.top < navRect.top) {
                nav.scrollTop -= navRect.top - activeRect.top + gap
            }
        },
        init() {
            this.$watch('collapse', val => {
                localStorage.setItem('sidebarCollapse', val)
                if (! val) this.hideTooltip()
                this.$nextTick(() => this.revealActiveMenu())
            })
            this.$watch('sidebar', val => {
                if (val) this.$nextTick(() => this.revealActiveMenu())
            })
            this.$nextTick(() => this.revealActiveMenu())
        }
    }" class="flex h-screen overflow-hidden">

        @include('partials.sidebar')

        <div x-show="tooltipText && collapse" x-cloak aria-hidden="true"
            class="pointer-events-none fixed left-[88px] z-50 max-md:hidden"
            :style="`top: ${tooltipTop}px`">
            <div class="relative inline-block rounded-md bg-gray-900 px-2.5 py-1.5 text-xs font-medium text-white shadow-lg">
                <span x-text="tooltipText"></span>
                <span class="absolute -left-1 top-1/2 h-2 w-2 -translate-y-1/2 rotate-45 bg-gray-900"></span>
            </div>
        </div>

        <div class="flex flex-col flex-1 overflow-hidden">

            @include('partials.topbar')

            <main id="main-content" tabindex="-1" class="flex-1 overflow-y-auto p-3 md:p-4 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    @if (session('success'))
                        <div role="status" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 4000)"
                            class="mb-4 flex items-center gap-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3">
                            
                            <x-ledger-icon name="check" class="w-5 h-5 text-emerald-500 shrink-0" />
                            <span class="text-sm font-medium">{{ session('success') }}</span>

                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

        </div>

    </div>

    <x-confirm-logout-modal />

    @stack('scripts')

</body>

</html>
