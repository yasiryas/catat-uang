<aside
    class="fixed md:static z-50 w-64 h-screen overflow-y-auto bg-blue-700 text-white transition-transform duration-300"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

    <!-- Logo -->
    <div class="border-b border-blue-600 p-5">
        <h1 class="text-2xl font-bold">
            KeuanganApp
        </h1>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-1">

        <a href="{{ route('dashboard') }}" class="block rounded-lg px-4 py-3 hover:bg-blue-600">
            Dashboard
        </a>

        <a href="#" class="block rounded-lg px-4 py-3 hover:bg-blue-600">
            Pemasukan
        </a>

        <a href="#" class="block rounded-lg px-4 py-3 hover:bg-blue-600">
            Pengeluaran
        </a>

        <a href="#" class="block rounded-lg px-4 py-3 hover:bg-blue-600">
            Mutasi
        </a>

        <a href="#" class="block rounded-lg px-4 py-3 hover:bg-blue-600">
            Periode
        </a>

        <a href="#" class="block rounded-lg px-4 py-3 hover:bg-blue-600">
            Adjustment
        </a>

        <!-- Setting -->
        <div x-data="{ open: false }">

            <button @click="open = !open"
                class="flex w-full items-center justify-between rounded-lg px-4 py-3 hover:bg-blue-600">

                <span>Setting</span>

                <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />

                </svg>

            </button>

            <!-- Sub Menu -->
            <div x-show="open" x-transition class="mt-1 space-y-1">
                <a href="#" class="block rounded-lg py-2 pl-10 text-sm hover:bg-blue-600">
                    <a href="{{ route('categories.index') }}"
                        class="block rounded-lg py-2 pl-10 text-sm hover:bg-blue-600">
                        Category
                    </a>

                    <a href="#" class="block rounded-lg py-2 pl-10 text-sm hover:bg-blue-600">
                        Account
                    </a>

                    <a href="#" class="block rounded-lg py-2 pl-10 text-sm hover:bg-blue-600">
                        User
                    </a>

                    <a href="#" class="block rounded-lg py-2 pl-10 text-sm hover:bg-blue-600">
                        Role
                    </a>

            </div>

        </div>

    </nav>

</aside>
