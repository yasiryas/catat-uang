<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') - KeuanganApp</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100">

    <div x-data="{ sidebar: false }" class="flex h-screen overflow-hidden">

        @include('partials.sidebar')

        <div class="flex flex-col flex-1 overflow-hidden">

            @include('partials.topbar')

            <main class="flex-1 overflow-y-auto p-6">

                @yield('content')

            </main>

        </div>

    </div>

    @stack('scripts')

</body>

</html>
