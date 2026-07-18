<x-modal :modal-name="type + '-form'" :show="false" max-width="lg">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold" x-text="modalMode === 'create' ? 'Tambah ' + title : 'Edit ' + title"></h2>
            <button @click="$dispatch('close-modal', { name: type + '-form' })" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form @submit.prevent="submit">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                    <input type="datetime-local" x-model="modalForm.date" class="select-input" required>
                    <template x-if="modalErrors.date">
                        <p class="mt-1 text-sm text-red-600" x-text="modalErrors.date[0]"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Periode</label>
                    <x-custom-select xModel="modalForm.period_id" variant="input"
                        placeholder="Pilih Periode"
                        alpine-items="periods"
                        value-key="id" label-key="name" />
                    <template x-if="modalErrors.period_id">
                        <p class="mt-1 text-sm text-red-600" x-text="modalErrors.period_id[0]"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <x-custom-select xModel="modalForm.category_id" variant="input"
                        placeholder="Pilih Kategori"
                        alpine-items="categories"
                        value-key="id" label-key="name" />
                    <template x-if="modalErrors.category_id">
                        <p class="mt-1 text-sm text-red-600" x-text="modalErrors.category_id[0]"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dompet</label>
                    <x-custom-select xModel="modalForm.account_id" variant="input"
                        placeholder="Pilih Dompet"
                        alpine-items="accounts"
                        value-key="id" label-key="name" />
                    <template x-if="modalErrors.account_id">
                        <p class="mt-1 text-sm text-red-600" x-text="modalErrors.account_id[0]"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                        <input type="number" x-model.number="modalForm.amount" min="0" step="1000" class="w-full pl-8 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="0" required>
                    </div>
                    <template x-if="modalErrors.amount">
                        <p class="mt-1 text-sm text-red-600" x-text="modalErrors.amount[0]"></p>
                    </template>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan (Opsional)</label>
                    <textarea x-model="modalForm.note" rows="3" class="select-input" placeholder="Catatan tambahan..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200">
                <button type="button" @click="$dispatch('close-modal', { name: type + '-form' })" class="px-4 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" :disabled="modalLoading" :class="submitButtonClass + ' text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2'">
                    <template x-if="modalLoading">
                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Menyimpan...
                    </template>
                    <template x-if="!modalLoading">
                        <span x-text="modalMode === 'create' ? 'Simpan' : 'Perbarui'"></span>
                    </template>
                </button>
            </div>
        </form>
    </div>
</x-modal>