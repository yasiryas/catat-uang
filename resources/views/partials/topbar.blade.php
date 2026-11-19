<header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6">
    <div class="flex items-center gap-2">
        <button type="button" @click="collapse = !collapse" class="hidden md:flex p-2 rounded-lg hover:bg-gray-100 transition-colors" title="Sembunyikan/tampilkan sidebar" aria-label="Sembunyikan atau tampilkan sidebar" :aria-pressed="collapse.toString()">
            <span class="inline-flex transition-transform duration-300" :class="collapse ? 'rotate-180' : ''">
                <x-ledger-icon name="collapse" class="w-5 h-5 text-gray-500" />
            </span>
        </button>
        <button type="button" @click="sidebar = true" aria-label="Buka navigasi" aria-controls="sidebar-navigation" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
            <x-ledger-icon name="menu" class="w-6 h-6 text-gray-500" />
        </button>
    </div>

    <div x-data="{ open: false }" class="relative flex items-center gap-4">
        <button type="button" @click="open = !open" class="flex items-center gap-3 pr-2 pl-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors" aria-haspopup="menu" :aria-expanded="open.toString()">
            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-semibold" aria-hidden="true">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="hidden md:block text-left">
                <p class="font-semibold text-gray-700 text-sm">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">{{ auth()->user()->is_admin ? 'Administrator' : 'User' }}</p>
            </div>
            <x-ledger-icon name="chevron" class="w-4 h-4 text-gray-400 hidden md:block" />
        </button>

        <div x-show="open" x-on:click.outside="open = false" x-transition.origin.top.right
            role="menu"
            class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5 z-50">
            <a role="menuitem" href="{{ route('profile.edit') }}" @click="open = false"
                class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                <x-ledger-icon name="user" class="w-5 h-5 text-gray-400" />
                Profil
            </a>
            <hr class="my-1 border-gray-100">
            <button type="button" role="menuitem" x-on:click.prevent="$dispatch('open-modal', 'confirm-logout')"
                class="flex items-center gap-3 px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left">
                <x-ledger-icon name="logout" class="w-5 h-5" />
                Keluar
            </button>
        </div>
    </div>
</header>
