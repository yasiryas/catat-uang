@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <div x-data="categoryPage()" x-init="fetchCategories()">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Kategori</h1>
                <p class="text-slate-500">Kelola kategori pemasukan dan pengeluaran.</p>
            </div>

            <button @click="create()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kategori
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left p-4 font-medium text-slate-700">Nama</th>
                            <th class="text-left p-4 font-medium text-slate-700">Jenis</th>
                            <th class="text-right p-4 font-medium text-slate-700">Budget</th>
                            <th class="text-center p-4 font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <template x-for="category in categories" :key="category.id">
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 font-medium" x-text="category.name"></td>
                                <td class="p-4">
                                    <span :class="'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' + (category.type === 'income' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800')"
                                        x-text="category.type === 'income' ? 'Pemasukan' : 'Pengeluaran'"></span>
                                </td>
                                <td class="p-4 text-right font-medium" x-text="formatCurrency(category.budget_limit)"></td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="edit(category)" class="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">Edit</button>
                                        <button @click="confirmDelete(category)" class="text-red-600 hover:text-red-800 font-medium text-sm transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="categories.length === 0">
                            <td colspan="4" class="p-10 text-center text-slate-500">Belum ada kategori.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <x-modal modal-name="category-form" :show="false" max-width="lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold" x-text="modal.mode === 'create' ? 'Tambah Kategori' : 'Edit Kategori'"></h2>
                    <button @click="$dispatch('close-modal', { name: 'category-form' })" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="submit">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori</label>
                            <input type="text" x-model="modal.form.name" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                            <template x-if="modal.errors.name">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.name[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jenis</label>
                            <select x-model="modal.form.type" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                            <template x-if="modal.errors.type">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.type[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Batas Anggaran (Opsional)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                                <input type="number" x-model.number="modal.form.budget_limit" min="0" step="10000" class="w-full pl-8 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="0">
                            </div>
                            <template x-if="modal.errors.budget_limit">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.budget_limit[0]"></p>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200">
                        <button type="button" @click="$dispatch('close-modal', { name: 'category-form' })" class="px-4 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="modal.loading" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
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
        </x-modal>

        <x-modal modal-name="category-delete" :show="false" max-width="md">
            <div class="p-6 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Hapus Kategori</h3>
                <p class="text-slate-600 mb-6">Yakin ingin menghapus kategori <strong x-text="deleteModal.category?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex justify-center gap-3">
                    <button @click="$dispatch('close-modal', { name: 'category-delete' })" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button @click="delete()" :disabled="deleteModal.loading" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <template x-if="deleteModal.loading">
                            <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </template>
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </x-modal>
    </div>
@endsection