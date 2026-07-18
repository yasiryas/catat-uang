<x-modal :modal-name="type + '-delete'" :show="false" max-width="md">
    <div class="p-6 text-center">
        <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <h3 class="text-lg font-semibold mb-2" x-text="'Hapus ' + title"></h3>
        <p class="text-slate-600 mb-6">Yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-center gap-3">
            <button @click="$dispatch('close-modal', { name: type + '-delete' })" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </button>
            <button @click="delete()" :disabled="deleteModalLoading" :class="submitButtonClass + ' text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 mx-auto'">
                <template x-if="deleteModalLoading">
                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </template>
                <template x-if="!deleteModalLoading">
                    <span>Hapus</span>
                </template>
            </button>
        </div>
    </div>
</x-modal>