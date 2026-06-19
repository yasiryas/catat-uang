<aside class="fixed md:static z-50 w-64 h-screen bg-blue-700 text-white transition-transform duration-300"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

    <div class="p-5 border-b border-blue-600">

        <h1 class="text-2xl font-bold">
            KeuanganApp
        </h1>

    </div>

    <nav class="p-4 space-y-2">

        <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-lg hover:bg-blue-600">
            Dashboard
        </a>

        <a href="#" class="block px-4 py-3 rounded-lg hover:bg-blue-600">
            Pemasukan
        </a>

        <a href="#" class="block px-4 py-3 rounded-lg hover:bg-blue-600">
            Pengeluaran
        </a>

        <a href="#" class="block px-4 py-3 rounded-lg hover:bg-blue-600">
            Mutasi
        </a>

        <a href="#" class="block px-4 py-3 rounded-lg hover:bg-blue-600">
            Periode
        </a>

        <a href="#" class="block px-4 py-3 rounded-lg hover:bg-blue-600">
            Adjustment
        </a>

    </nav>

</aside>
