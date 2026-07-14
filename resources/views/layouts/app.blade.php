<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') - KeuanganApp</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100">

    <div x-data="{
        sidebar: false,
        collapse: false
    }" class="flex h-screen overflow-hidden">

        @include('partials.sidebar')

        <div class="flex flex-col flex-1 overflow-hidden">

            @include('partials.topbar')

            <main class="flex-1 overflow-y-auto p-6">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                        class="mb-4 rounded-lg bg-green-100 border border-green-300 text-green-700 px-4 py-3">

                        {{ session('success') }}

                    </div>
                @endif
                @yield('content')

            </main>

        </div>

    </div>

    <x-confirm-logout-modal />

    @stack('scripts')

</body>

</html>
