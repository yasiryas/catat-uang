@php
    $menu = [
        'dashboard' => request()->routeIs('dashboard'),
        'account' => request()->routeIs('accounts.*'),
        'category' => request()->routeIs('categories.*'),
        'income' => request()->routeIs('transactions.income.*'),
        'expense' => request()->routeIs('transactions.expense.*'),
        'goal' => request()->routeIs('goals.*'),
        'mutation' => request()->routeIs('mutations.*'),
        'period' => request()->routeIs('periods.*'),
        'adjustment' => request()->routeIs('adjustments.*'),
        'family' => request()->routeIs('family.*'),
        'user' => request()->routeIs('users.*'),
    ];
@endphp

<div x-show="sidebar" x-transition.opacity @click="sidebar=false" aria-hidden="true" class="fixed inset-0 bg-black/50 z-30 md:hidden"></div>

<aside
    id="sidebar-navigation"
    aria-label="Navigasi aplikasi"
    class="fixed md:static inset-y-0 left-0 z-40 bg-gradient-to-b from-blue-600 to-blue-700 flex flex-col shrink-0 transition-all duration-300"
    :class="[
        sidebar ? 'translate-x-0 w-64' : '-translate-x-full md:translate-x-0',
        collapse ? 'md:w-20' : 'md:w-64',
    ]">

    <div class="h-16 flex items-center gap-2 px-7 border-b border-white/10" :class="collapse ? 'md:justify-center md:px-0' : ''">
        <x-keuangan-logo class="w-6 h-6 shrink-0" />
        <p :class="collapse ? 'md:hidden' : ''" class="text-base font-bold tracking-tight text-white whitespace-nowrap">KeuanganApp</p>
        <button type="button" @click="sidebar = false" aria-label="Tutup navigasi" class="ml-auto md:hidden p-1.5 rounded-lg hover:bg-white/10">
            <x-ledger-icon name="close" class="w-5 h-5 text-white" />
        </button>
    </div>

    <nav x-ref="sidebarNav" aria-label="Menu utama" class="no-scrollbar flex-1 px-3 py-4 space-y-3 md:space-y-2 overflow-y-auto overflow-x-hidden">
        <a href="{{ route('dashboard') }}"
            @if ($menu['dashboard']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Dashboard')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Dashboard')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Dashboard' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['dashboard'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="dashboard" class="w-5 h-5 shrink-0" />
            <span :class="collapse ? 'md:hidden' : ''">Dashboard</span>
        </a>

        <a href="{{ route('categories.index') }}"
            @if ($menu['category']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Kategori')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Kategori')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Kategori' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['category'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="category" class="w-5 h-5 shrink-0 {{ $menu['category'] ? 'text-white' : 'text-blue-200' }}" />
            <span :class="collapse ? 'md:hidden' : ''">Kategori</span>
        </a>

        <a href="{{ route('accounts.index') }}"
            @if ($menu['account']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Dompet')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Dompet')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Dompet' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['account'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="wallet" class="w-5 h-5 shrink-0 {{ $menu['account'] ? 'text-white' : 'text-blue-200' }}" />
            <span :class="collapse ? 'md:hidden' : ''">Dompet</span>
        </a>

        <div class="border-t border-white/10 my-3" :class="collapse ? 'md:hidden' : ''"></div>
        <p :class="collapse ? 'md:hidden' : ''" class="px-4 pt-1 pb-1 text-xs font-semibold text-blue-200 uppercase tracking-wider">Transaksi</p>

        <a href="{{ route('transactions.income.index') }}"
            @if ($menu['income']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Pemasukan')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Pemasukan')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Pemasukan' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['income'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="income" class="w-5 h-5 shrink-0 {{ $menu['income'] ? 'text-white' : 'text-blue-200' }}" />
            <span :class="collapse ? 'md:hidden' : ''">Pemasukan</span>
        </a>

        <a href="{{ route('transactions.expense.index') }}"
            @if ($menu['expense']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Pengeluaran')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Pengeluaran')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Pengeluaran' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['expense'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="expense" class="w-5 h-5 shrink-0 {{ $menu['expense'] ? 'text-white' : 'text-blue-200' }}" />
            <span :class="collapse ? 'md:hidden' : ''">Pengeluaran</span>
        </a>

        <a href="{{ route('goals.index') }}"
            @if ($menu['goal']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Target & Capaian')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Target & Capaian')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Target & Capaian' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['goal'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="goal" class="w-5 h-5 shrink-0 {{ $menu['goal'] ? 'text-white' : 'text-blue-200' }}" />
            <span :class="collapse ? 'md:hidden' : ''">Target & Capaian</span>
        </a>

        <a href="{{ route('mutations.index') }}"
            @if ($menu['mutation']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Mutasi')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Mutasi')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Mutasi' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['mutation'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="mutation" class="w-5 h-5 shrink-0 {{ $menu['mutation'] ? 'text-white' : 'text-blue-200' }}" />
            <span :class="collapse ? 'md:hidden' : ''">Mutasi</span>
        </a>

        <div class="border-t border-white/10 my-3" :class="collapse ? 'md:hidden' : ''"></div>
        <p :class="collapse ? 'md:hidden' : ''" class="px-4 pt-1 pb-1 text-xs font-semibold text-blue-200 uppercase tracking-wider">Pengaturan</p>

        <a href="{{ route('periods.index') }}"
            @if ($menu['period']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Periode')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Periode')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Periode' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['period'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="period" class="w-5 h-5 shrink-0 {{ $menu['period'] ? 'text-white' : 'text-blue-200' }}" />
            <span :class="collapse ? 'md:hidden' : ''">Periode</span>
        </a>

        <a href="{{ route('adjustments.index') }}"
            @if ($menu['adjustment']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Adjustment')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Adjustment')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Adjustment' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['adjustment'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="adjustment" class="w-5 h-5 shrink-0 {{ $menu['adjustment'] ? 'text-white' : 'text-blue-200' }}" />
            <span :class="collapse ? 'md:hidden' : ''">Adjustment</span>
        </a>

        <a href="{{ route('family.index') }}"
            @if ($menu['family']) aria-current="page" @endif
            @mouseenter="showTooltip($event, 'Keluarga')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Keluarga')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Keluarga' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['family'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="family" class="w-5 h-5 shrink-0 {{ $menu['family'] ? 'text-white' : 'text-blue-200' }}" />
            <span :class="collapse ? 'md:hidden' : ''">Keluarga</span>
        </a>

        @if (auth()->user()?->is_admin)
            <div class="border-t border-white/10 my-3" :class="collapse ? 'md:hidden' : ''"></div>
            <p :class="collapse ? 'md:hidden' : ''" class="px-4 pt-1 pb-1 text-xs font-semibold text-blue-200 uppercase tracking-wider">Administrator</p>

            <a href="{{ route('users.index') }}"
                @if ($menu['user']) aria-current="page" @endif
                @mouseenter="showTooltip($event, 'Pengguna')" @mouseleave="hideTooltip()"
                @focus="showTooltip($event, 'Pengguna')" @blur="hideTooltip()"
                :aria-label="collapse ? 'Pengguna' : null"
                class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors {{ $menu['user'] ? 'bg-white/20 text-white' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
                :class="collapse ? 'md:justify-center md:px-0' : ''">
                <x-ledger-icon name="user" class="w-5 h-5 shrink-0 {{ $menu['user'] ? 'text-white' : 'text-blue-200' }}" />
                <span :class="collapse ? 'md:hidden' : ''">Pengguna</span>
            </a>
        @endif
    </nav>

    <div class="p-4 border-t border-white/10" :class="collapse ? 'md:px-3' : 'md:px-4'">
        <button x-on:click.prevent="$dispatch('open-modal', 'confirm-logout')"
            @mouseenter="showTooltip($event, 'Keluar')" @mouseleave="hideTooltip()"
            @focus="showTooltip($event, 'Keluar')" @blur="hideTooltip()"
            :aria-label="collapse ? 'Keluar' : null"
            class="flex items-center gap-3 px-4 py-3 md:py-2.5 text-sm font-medium rounded-lg transition-colors text-blue-100 hover:bg-white/10 hover:text-white w-full text-left"
            :class="collapse ? 'md:justify-center md:px-0' : ''">
            <x-ledger-icon name="logout" class="w-5 h-5 shrink-0 text-blue-200" />
            <span :class="collapse ? 'md:hidden' : ''">Keluar</span>
        </button>
    </div>

</aside>
