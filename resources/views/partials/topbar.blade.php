<header class="bg-white border-b px-6 py-4 flex items-center justify-between">

    <div class="flex items-center gap-3">

        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden">
            ☰
        </button>

        <h2 class="text-xl font-semibold">
            @yield('title')
        </h2>

    </div>

    <div class="flex items-center gap-4">

        <span>
            {{ auth()->user()->name }}
        </span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button class="text-red-500">
                Logout
            </button>
        </form>

    </div>

</header>
