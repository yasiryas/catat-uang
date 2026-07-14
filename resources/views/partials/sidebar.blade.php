{{-- Overlay Mobile --}}
@php
    $menu = [
        'dashboard' => request()->routeIs('dashboard'),
        'category' => request()->routeIs('categories.*'),
        'income' => request()->routeIs('income.*'),
        'expense' => request()->routeIs('expense.*'),
        'mutation' => request()->routeIs('mutation.*'),
        'period' => request()->routeIs('periods.*'),
        'adjustment' => request()->routeIs('adjustments.*'),
    ];
@endphp
<div x-show="sidebar" x-transition.opacity @click="sidebar=false" class="fixed inset-0 bg-black/50 z-30 md:hidden"></div>

<aside
    class="
        fixed md:static

        inset-y-0 left-0

        z-40

        w-72

        bg-blue-700

        text-white

        transform

        transition-transform

        duration-300

        flex

        flex-col

        shrink-0
    "
    :class="sidebar
        ?
        'translate-x-0' :
        '-translate-x-full md:translate-x-0'">

    <div class="h-16 flex items-center px-6 border-b border-blue-600">

        <h1 class="text-2xl font-bold">

            KeuanganApp

        </h1>

    </div>

    <nav class="flex-1 p-4 space-y-2">

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $menu['dashboard'] ? 'bg-blue-800 shadow' : 'hover:bg-blue-600' }}">
            <x-heroicon-o-home class="w-5 h-5 shrink-0" />
            <span x-show="!collapse">Dashboard</span>
        </a>

        <a href="{{ route('categories.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $menu['category'] ? 'bg-blue-800 shadow' : 'hover:bg-blue-600' }}">
            <x-heroicon-o-folder class="w-5 h-5 shrink-0" />
            <span x-show="!collapse">Kategori</span>
        </a>

        <a href="{{ route('categories.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $menu['category'] ? 'bg-blue-800 shadow' : 'hover:bg-blue-600' }}">
            <x-heroicon-o-folder class="w-5 h-5 shrink-0" />
            <span x-show="!collapse">Kategori</span>
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
