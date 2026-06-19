<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">

        @include('partials.sidebar')

        <div class="flex-1">

            @include('partials.topbar')

            <main class="p-6">
                @yield('content')
            </main>

        </div>

    </div>

</body>

</html>
