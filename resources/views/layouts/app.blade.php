<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - KeuanganApp</title>

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-900">

    <div x-data="{
        sidebar: false,
        collapse: false
    }" class="flex h-screen overflow-hidden">

        @include('partials.sidebar')

        <div class="flex flex-col flex-1 overflow-hidden">

            @include('partials.topbar')

            <main class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-init="setTimeout(() => show = false, 4000)"
                            class="mb-6 flex items-center gap-3 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 px-5 py-4 shadow-sm shadow-emerald-100/50">
                            
                            <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-500" />
                            <span class="text-sm font-semibold">{{ session('success') }}</span>

                        </div>
                    @endif

                    @yield('content')
                </div>

                {{-- Global Footer inside Content --}}
                <footer class="mt-12 py-6 border-t border-slate-200/60 text-center">
                    <p class="text-xs text-slate-400 font-medium tracking-wide">
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
