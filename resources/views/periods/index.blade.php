@extends('layouts.app')

@section('title', 'Periode')

@section('content')
    <div x-data="periodPage()">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Periode</h1>
                <p class="text-slate-500">Kelola periode tahun/bulan.</p>
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
                            <th class="text-left p-4 font-medium text-slate-700">Nama</th>
                            <th class="text-left p-4 font-medium text-slate-700">Tanggal Mulai</th>
                            <th class="text-left p-4 font-medium text-slate-700">Tanggal Akhir</th>
                            <th class="text-center p-4 font-medium text-slate-700">Status</th>
                            <th class="text-center p-4 font-medium text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($periods as $period)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 font-medium">{{ $period->name }}</td>
                                <td class="p-4">{{ \Carbon\Carbon::parse($period->start_date)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                                <td class="p-4">{{ \Carbon\Carbon::parse($period->end_date)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                                <td class="p-4 text-center">
                                    @if ($period->is_closed)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">Closed</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Open</span>
                                    @endif
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('periods.report', $period) }}" class="text-emerald-600 hover:text-emerald-800 font-medium text-sm transition-colors">Laporan</a>
                                        <button @click="edit({{ $period->id }}, '{{ $period->name }}', '{{ $period->start_date }}', '{{ $period->end_date }}', {{ $period->is_closed ? 'true' : 'false' }})" class="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">Edit</button>
                                        <button @click="confirmDelete({{ $period->id }}, '{{ $period->name }}')" class="text-red-600 hover:text-red-800 font-medium text-sm transition-colors">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-10 text-center text-slate-500">Belum ada periode.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-slate-200">
                <nav class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">
                        Menampilkan {{ $periods->count() }} dari {{ $periods->total() }} periode
                    </span>
                    <div class="flex items-center gap-2">
                        @if ($periods->onFirstPage())
                            <span class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-400 opacity-50 cursor-not-allowed transition-colors">Sebelumnya</span>
                        @else
                            <a href="{{ $periods->previousPageUrl() }}" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50 transition-colors">Sebelumnya</a>
                        @endif
                        <span class="px-3 py-1.5 text-sm text-slate-600">Halaman {{ $periods->currentPage() }} dari {{ $periods->lastPage() }}</span>
                        @if ($periods->hasMorePages())
                            <a href="{{ $periods->nextPageUrl() }}" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-50 transition-colors">Selanjutnya</a>
                        @else
                            <span class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm text-slate-400 opacity-50 cursor-not-allowed transition-colors">Selanjutnya</span>
                        @endif
                    </div>
                </nav>
            </div>
        </div>

        {{-- Create/Edit Modal --}}
        <div x-show="modal.show" style="display: none"
             class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
            <div x-show="modal.show" x-on:click="modal.show = false" class="fixed inset-0 transform transition-all"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="modal.show" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold" x-text="modal.mode === 'create' ? 'Tambah Periode' : 'Edit Periode'"></h2>
                    <button @click="modal.show = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="submit()">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
                            <input type="text" x-model="modal.form.name" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="Contoh: Januari 2026" required>
                            <template x-if="modal.errors.name">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.name[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                            <input type="date" x-model="modal.form.start_date" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                            <template x-if="modal.errors.start_date">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.start_date[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Akhir</label>
                            <input type="date" x-model="modal.form.end_date" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                            <template x-if="modal.errors.end_date">
                                <p class="mt-1 text-sm text-red-600" x-text="modal.errors.end_date[0]"></p>
                            </template>
                        </div>

                        <div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" x-model="modal.form.is_closed" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-slate-700">Closed</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200">
                        <button type="button" @click="modal.show = false" class="px-4 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit" :disabled="modal.loading" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <template x-if="modal.loading">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
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

        {{-- Delete Modal --}}
        <div x-show="deleteModal.show" style="display: none"
             class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
            <div x-show="deleteModal.show" x-on:click="deleteModal.show = false" class="fixed inset-0 transform transition-all"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="deleteModal.show" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0">
            <div class="p-6 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Hapus Periode</h3>
                <p class="text-slate-600 mb-6">Yakin ingin menghapus periode <strong x-text="deleteModal.period?.name"></strong>?</p>
                <div class="flex justify-center gap-3">
                    <button @click="deleteModal.show = false" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                    <button @click="destroy()" :disabled="deleteModal.loading" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
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
