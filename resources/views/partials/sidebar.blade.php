@php
    $menu = [
        'dashboard' => request()->routeIs('dashboard'),
        'account' => request()->routeIs('accounts.*'),
        'category' => request()->routeIs('categories.*'),
        'income' => request()->routeIs('transactions.income.*'),
        'expense' => request()->routeIs('transactions.expense.*'),
        'mutation' => request()->routeIs('mutations.*'),
        'period' => request()->routeIs('periods.*'),
        'adjustment' => request()->routeIs('adjustments.*'),
        'user' => request()->routeIs('users.*'),
    ];
@endphp

<div x-show="sidebar" x-transition.opacity @click="sidebar=false" class="fixed inset-0 bg-black/50 z-30 md:hidden"></div>

<aside
    class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-sky-600 to-sky-700 flex flex-col shrink-0 transition-transform duration-300"
    :class="sidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

    <div class="h-16 flex items-center gap-3 px-5 border-b border-white/10">
        <x-keuangan-logo class="w-8 h-8 shrink-0" />
        <h1 class="text-base font-bold tracking-tight text-white">KeuanganApp</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto">
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['dashboard'] ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}">
            <x-heroicon-o-home class="w-5 h-5 shrink-0 {{ $menu['dashboard'] ? 'text-white' : 'text-sky-200' }}" />
            <span>Dashboard</span>
        </a>

        <a href="{{ route('categories.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['category'] ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}">
            <x-heroicon-o-folder class="w-5 h-5 shrink-0 {{ $menu['category'] ? 'text-white' : 'text-sky-200' }}" />
            <span>Kategori</span>
        </a>

        <a href="{{ route('accounts.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['account'] ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}">
            <x-heroicon-o-wallet class="w-5 h-5 shrink-0 {{ $menu['account'] ? 'text-white' : 'text-sky-200' }}" />
            <span>Dompet</span>
        </a>

        <div class="border-t border-white/10 my-4"></div>
        <p class="px-4 pt-1 pb-2 text-xs font-semibold text-sky-200 uppercase tracking-wider">Transaksi</p>

        <a href="{{ route('transactions.income.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['income'] ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}">
            <x-heroicon-o-arrow-down-circle class="w-5 h-5 shrink-0 {{ $menu['income'] ? 'text-white' : 'text-sky-200' }}" />
            <span>Pemasukan</span>
        </a>

        <a href="{{ route('transactions.expense.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['expense'] ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}">
            <x-heroicon-o-arrow-up-circle class="w-5 h-5 shrink-0 {{ $menu['expense'] ? 'text-white' : 'text-sky-200' }}" />
            <span>Pengeluaran</span>
        </a>

        <a href="{{ route('mutations.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['mutation'] ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}">
            <x-heroicon-o-arrows-right-left class="w-5 h-5 shrink-0 {{ $menu['mutation'] ? 'text-white' : 'text-sky-200' }}" />
            <span>Mutasi</span>
        </a>

        <div class="border-t border-white/10 my-4"></div>
        <p class="px-4 pt-1 pb-2 text-xs font-semibold text-sky-200 uppercase tracking-wider">Pengaturan</p>

        <a href="{{ route('periods.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['period'] ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}">
            <x-heroicon-o-calendar-days class="w-5 h-5 shrink-0 {{ $menu['period'] ? 'text-white' : 'text-sky-200' }}" />
            <span>Periode</span>
        </a>

        <a href="{{ route('adjustments.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['adjustment'] ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}">
            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 shrink-0 {{ $menu['adjustment'] ? 'text-white' : 'text-sky-200' }}" />
            <span>Adjustment</span>
        </a>

        @if (auth()->user()?->is_admin)
            <div class="border-t border-white/10 my-4"></div>
            <p class="px-4 pt-1 pb-2 text-xs font-semibold text-sky-200 uppercase tracking-wider">Administrator</p>

            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['user'] ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}">
                <x-heroicon-o-users class="w-5 h-5 shrink-0 {{ $menu['user'] ? 'text-white' : 'text-sky-200' }}" />
                <span>Pengguna</span>
            </a>
        @endif
    </nav>

    <div class="p-4 border-t border-white/10">
        <button x-on:click.prevent="$dispatch('open-modal', 'confirm-logout')"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors text-sky-100 hover:bg-white/10 hover:text-white w-full text-left">
            <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 shrink-0 text-sky-200" />
            <span>Keluar</span>
        </button>
    </div>

</aside>
