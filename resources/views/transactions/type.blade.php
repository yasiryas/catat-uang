@php
    $formModalName = ($type ?? 'income') . '-form';
    $deleteModalName = ($type ?? 'income') . '-delete';
@endphp

@extends('layouts.app')

@section('content')
    <div x-data="transactionTypePage()" x-init="init()"
         data-type="{{ $type }}"
         data-categories='{{ json_encode($categories) }}'
         data-periods='{{ json_encode($periods) }}'
         data-accounts='{{ json_encode($accounts) }}'>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold" x-text="title"></h1>
                <p class="text-slate-500" x-text="subtitle"></p>
            </div>

            <button @click="create()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left p-4 font-medium text-slate-700">Tanggal</th>
                            <th class="text-left p-4 font-medium text-slate-700">Kategori</th>
                            <th class="text-left p-4 font-medium text-slate-700">Periode</th>
                            <th class="text-left p-4 font-medium text-slate-700">Dompet</th>
                            <th class="text-right p-4 font-medium text-slate-700">Jumlah</th>
                            <th class="text-center p-4 font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <template x-for="transaction in transactions" :key="transaction.id">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 whitespace-nowrap" x-text="transaction.date"></td>
                                <td class="p-4" x-text="transaction.category?.name ?? '-'"></td>
                                <td class="p-4" x-text="transaction.period?.name ?? '-'"></td>
                                <td class="p-4" x-text="transaction.account?.name ?? '-'"></td>
                                <td class="p-4 text-right font-medium text-emerald-600" x-text="formatCurrency(transaction.amount)"></td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="edit(transaction)" class="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">Edit</button>
                                        <button @click="confirmDelete(transaction)" class="text-red-600 hover:text-red-800 font-medium text-sm transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="transactions.length === 0">
                            <td colspan="6" class="p-10 text-center text-slate-500" x-text="emptyMessage"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-slate-200" x-show="transactions.length > 0">
                <nav class="flex items-center justify-between">
                    <span class="text-sm text-slate-600" x-text="'Menampilkan ' + transactions.length + ' dari ' + pagination.total + ' data'"></span>
                    <div class="flex items-center gap-2">
                        <button @click="loadTransactions(pagination.current_page - 1)" :disabled="pagination.current_page <= 1" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">Sebelumnya</button>
                        <span class="px-3 py-1.5 text-sm text-slate-600" x-text="'Halaman ' + pagination.current_page + ' dari ' + pagination.last_page"></span>
                        <button @click="loadTransactions(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">Selanjutnya</button>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Create/Edit Modal (inlined to avoid x-data scope isolation) -->
        <div x-show="modal.show" style="display: none"
             x-on:open-modal.window="if ((typeof $event.detail === 'string' ? $event.detail : $event.detail?.name) === '{{ $formModalName }}') modal.show = true"
             x-on:close-modal.window="if ((typeof $event.detail === 'string' ? $event.detail : $event.detail?.name) === '{{ $formModalName }}') modal.show = false"
             class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
            <div x-on:click="modal.show = false" class="fixed inset-0 transform transition-all">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold" x-text="modal.mode === 'create' ? 'Tambah ' + title : 'Edit ' + title"></h2>
                    <button @click="modal.show = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="submit()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                            <input type="date" x-model="modal.form.date" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                            <template x-if="modal.errors.date">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.date[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Periode</label>
                            <select x-model="modal.form.period_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                                <option value="">Pilih Periode</option>
                                <template x-for="period in periods" :key="period.id">
                                    <option :value="period.id" x-text="period.name"></option>
                                </template>
                            </select>
                            <template x-if="modal.errors.period_id">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.period_id[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                            <select x-model="modal.form.category_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                                <option value="">Pilih Kategori</option>
                                <template x-for="category in categories" :key="category.id">
                                    <option :value="category.id" x-text="category.name"></option>
                                </template>
                            </select>
                            <template x-if="modal.errors.category_id">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.category_id[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Dompet</label>
                            <select x-model="modal.form.account_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                                <option value="">Pilih Dompet</option>
                                <template x-for="account in accounts" :key="account.id">
                                    <option :value="account.id" x-text="account.name"></option>
                                </template>
                            </select>
                            <template x-if="modal.errors.account_id">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.account_id[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                                <input type="number" x-model.number="modal.form.amount" min="0" step="100" class="w-full pl-8 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="0" required>
                            </div>
                            <template x-if="modal.errors.amount">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.amount[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Catatan (Opsional)</label>
                            <textarea x-model="modal.form.note" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200">
                        <button type="button" @click="modal.show = false" class="px-4 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="modal.loading" :class="submitButtonClass + ' px-4 py-2.5 text-white rounded-lg hover:opacity-90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2'">
                            <template x-if="modal.loading">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Menyimpan...
                            </template>
                            <template x-if="!modal.loading">
                                <span x-text="modal.mode === 'create' ? 'Simpan' : 'Perbarui'"></span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <!-- Delete Modal (inlined) -->
        <div x-show="deleteModal.show" style="display: none"
             x-on:open-modal.window="if ((typeof $event.detail === 'string' ? $event.detail : $event.detail?.name) === '{{ $deleteModalName }}') deleteModal.show = true"
             x-on:close-modal.window="if ((typeof $event.detail === 'string' ? $event.detail : $event.detail?.name) === '{{ $deleteModalName }}') deleteModal.show = false"
             class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
            <div x-on:click="deleteModal.show = false" class="fixed inset-0 transform transition-all">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto">
            <div class="p-6 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Hapus</h3>
                <p class="text-slate-600 mb-6">Yakin ingin menghapus <strong x-text="deleteModal.transaction?.category?.name"></strong> sebesar <strong x-text="formatCurrency(deleteModal.transaction?.amount)"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex justify-center gap-3">
                    <button @click="deleteModal.show = false" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button @click="deleteTransaction()" :disabled="deleteModal.loading" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <template x-if="deleteModal.loading">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </template>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection