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
    class="fixed md:static inset-y-0 left-0 z-40 bg-gradient-to-b from-blue-600 to-blue-700 flex flex-col shrink-0 transition-all duration-300"
    :class="collapse ? 'w-20' : 'w-64'">

    <div class="h-16 flex items-center justify-between px-3 border-b border-white/10" :class="collapse ? 'px-3' : 'px-5'">
        <div class="flex items-center gap-3 overflow-hidden">
            <x-keuangan-logo class="w-8 h-8 shrink-0" />
            <h1 x-show="!collapse" class="text-base font-bold tracking-tight text-white whitespace-nowrap">KeuanganApp</h1>
        </div>
        <button @click="collapse = !collapse" class="shrink-0 p-1.5 rounded-lg text-blue-200 hover:bg-white/10 hover:text-white transition-colors hidden md:block">
            <svg class="w-5 h-5 transition-transform duration-300" :class="collapse ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
        </button>
    </div>

    <nav class="flex-1 px-3 py-6 space-y-3 overflow-y-auto overflow-x-hidden">
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['dashboard'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'justify-center px-0' : ''">
            <x-heroicon-o-home class="w-5 h-5 shrink-0 {{ $menu['dashboard'] ? 'text-white' : 'text-blue-200' }}" />
            <span x-show="!collapse">Dashboard</span>
        </a>

        <a href="{{ route('categories.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['category'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'justify-center px-0' : ''">
            <x-heroicon-o-folder class="w-5 h-5 shrink-0 {{ $menu['category'] ? 'text-white' : 'text-blue-200' }}" />
            <span x-show="!collapse">Kategori</span>
        </a>

        <a href="{{ route('accounts.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['account'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'justify-center px-0' : ''">
            <x-heroicon-o-wallet class="w-5 h-5 shrink-0 {{ $menu['account'] ? 'text-white' : 'text-blue-200' }}" />
            <span x-show="!collapse">Dompet</span>
        </a>

        <div class="border-t border-white/10 my-4" x-show="!collapse"></div>
        <p x-show="!collapse" class="px-4 pt-1 pb-2 text-xs font-semibold text-blue-200 uppercase tracking-wider">Transaksi</p>

        <a href="{{ route('transactions.income.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['income'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'justify-center px-0' : ''">
            <x-heroicon-o-arrow-down-circle class="w-5 h-5 shrink-0 {{ $menu['income'] ? 'text-white' : 'text-blue-200' }}" />
            <span x-show="!collapse">Pemasukan</span>
        </a>

        <a href="{{ route('transactions.expense.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['expense'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'justify-center px-0' : ''">
            <x-heroicon-o-arrow-up-circle class="w-5 h-5 shrink-0 {{ $menu['expense'] ? 'text-white' : 'text-blue-200' }}" />
            <span x-show="!collapse">Pengeluaran</span>
        </a>

        <a href="{{ route('mutations.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['mutation'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'justify-center px-0' : ''">
            <x-heroicon-o-arrows-right-left class="w-5 h-5 shrink-0 {{ $menu['mutation'] ? 'text-white' : 'text-blue-200' }}" />
            <span x-show="!collapse">Mutasi</span>
        </a>

        <div class="border-t border-white/10 my-4" x-show="!collapse"></div>
        <p x-show="!collapse" class="px-4 pt-1 pb-2 text-xs font-semibold text-blue-200 uppercase tracking-wider">Pengaturan</p>

        <a href="{{ route('periods.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['period'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'justify-center px-0' : ''">
            <x-heroicon-o-calendar-days class="w-5 h-5 shrink-0 {{ $menu['period'] ? 'text-white' : 'text-blue-200' }}" />
            <span x-show="!collapse">Periode</span>
        </a>

        <a href="{{ route('adjustments.index') }}"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['adjustment'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'justify-center px-0' : ''">
            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 shrink-0 {{ $menu['adjustment'] ? 'text-white' : 'text-blue-200' }}" />
            <span x-show="!collapse">Adjustment</span>
        </a>

        @if (auth()->user()?->is_admin)
            <div class="border-t border-white/10 my-4" x-show="!collapse"></div>
            <p x-show="!collapse" class="px-4 pt-1 pb-2 text-xs font-semibold text-blue-200 uppercase tracking-wider">Administrator</p>

            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $menu['user'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
                :class="collapse ? 'justify-center px-0' : ''">
                <x-heroicon-o-users class="w-5 h-5 shrink-0 {{ $menu['user'] ? 'text-white' : 'text-blue-200' }}" />
                <span x-show="!collapse">Pengguna</span>
            </a>
        @endif
    </nav>

    <div class="p-4 border-t border-white/10" :class="collapse ? 'px-3' : 'px-4'">
        <button x-on:click.prevent="$dispatch('open-modal', 'confirm-logout')"
            class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors text-blue-100 hover:bg-white/10 hover:text-white w-full text-left"
            :class="collapse ? 'justify-center px-0' : ''">
            <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5 shrink-0 text-blue-200" />
            <span x-show="!collapse">Keluar</span>
        </button>
    </div>

</aside>
