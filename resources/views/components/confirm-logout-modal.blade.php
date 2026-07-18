@props([
    'name' => 'confirm-logout',
    'maxWidth' => '2xl',
])

<x-modal :modal-name="$name" :show="false" :maxWidth="$maxWidth">
    <div class="p-6">
        <div class="flex items-start gap-3">
            <div class="mt-1 h-9 w-9 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Konfirmasi Logout</h2>
                <p class="mt-1 text-sm text-gray-600">Anda yakin ingin keluar dari aplikasi?</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-6 flex items-center justify-end gap-3">
            @csrf
            <x-secondary-button type="button" x-on:click.stop="show = false">Batal</x-secondary-button>
            <button type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-white font-semibold hover:bg-red-700 transition">
                Logout
            </button>
        </form>
    </div>
</x-modal>
