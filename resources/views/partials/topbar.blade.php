<header class="bg-white border-b border-gray-200 shadow-sm h-16 flex items-center justify-between px-6">

    {{-- Kiri --}}
    <div class="flex items-center gap-4">

        {{-- Toggle Sidebar Mobile --}}
        <button @click="sidebar = true" class="md:hidden p-2 rounded-lg hover:bg-gray-100">

            <x-heroicon-o-bars-3 class="w-6 h-6" />

        </button>

        <h2 class="text-2xl font-bold text-gray-800">

            @yield('title')

        </h2>

    </div>

    {{-- Kanan --}}
    <div class="flex items-center gap-4">

        {{-- Avatar --}}
        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold">

            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

        </div>

        {{-- Nama --}}
        <div class="hidden md:block">

            <p class="font-semibold text-gray-800">

                {{ auth()->user()->name }}

            </p>

            <p class="text-xs text-gray-500">

                Administrator

            </p>

        </div>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST">

            @csrf

            <button class="p-2 rounded-lg hover:bg-red-100 text-red-600 transition" title="Logout">

                <x-heroicon-o-arrow-right-on-rectangle class="w-6 h-6" />

            </button>

        </form>

    </div>

</header>
