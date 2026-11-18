@extends('layouts.app')

@section('title', 'Target & Capaian')

@section('content')
    @php
        $typeMeta = [
            'income' => ['label' => 'Target Pemasukan', 'color' => 'bg-emerald-100 text-emerald-800'],
            'expense' => ['label' => 'Batas Pengeluaran', 'color' => 'bg-red-100 text-red-800'],
            'savings' => ['label' => 'Target Tabungan', 'color' => 'bg-indigo-100 text-indigo-800'],
            'category' => ['label' => 'Batas Kategori', 'color' => 'bg-amber-100 text-amber-800'],
            'account' => ['label' => 'Target Saldo Dompet', 'color' => 'bg-purple-100 text-purple-800'],
        ];
    @endphp

    <div x-data="goalPage()"
         data-goals='{{ json_encode($goalStats) }}'
         data-periods='{{ json_encode($periods) }}'
         data-categories='{{ json_encode($categories) }}'
         data-accounts='{{ json_encode($accounts) }}'>

        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold">Target & Capaian</h1>
                <p class="text-sm text-slate-600">Tetapkan target keuangan, capaian dihitung otomatis dari transaksi.</p>
            </div>

            <button @click="create()" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Target
            </button>
        </div>

        {{-- AI Rekomendasi --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="bg-violet-100 p-3 rounded-xl shrink-0">
                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Rekomendasi AI</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Analisis otomatis pengeluaran, pemasukan, dan target Anda â€” saran hemat & cara menaikkan pendapatan.</p>
                    </div>
                </div>
                <button @click="askAi()" :disabled="ai.loading"
                    class="px-3 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 justify-center shrink-0">
                    <template x-if="ai.loading">
                        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </template>
                    <template x-if="!ai.loading">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </template>
                    <span x-text="ai.loading ? 'Menganalisis...' : 'Minta Rekomendasi'"></span>
                </button>
            </div>

            <div x-show="ai.loading" class="mt-4 space-y-2">
                <div class="h-4 bg-slate-100 rounded animate-pulse w-3/4"></div>
                <div class="h-4 bg-slate-100 rounded animate-pulse w-full"></div>
                <div class="h-4 bg-slate-100 rounded animate-pulse w-5/6"></div>
            </div>

            <div x-show="ai.error" x-cloak class="mt-4 text-sm text-red-600 bg-red-50 border border-red-100 rounded-lg p-3" x-text="ai.error"></div>

            <div x-show="ai.result && !ai.loading" x-cloak class="mt-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-wide">Hasil analisis</span>
                    <button @click="ai.result = null" class="text-xs text-slate-400 hover:text-slate-600 font-medium">Tutup</button>
                </div>
                <div class="prose prose-sm max-w-none prose-headings:text-slate-800 prose-li:my-0.5 text-slate-700 leading-relaxed" x-html="ai.result"></div>
            </div>

            <div x-show="!ai.loading && !ai.result && !ai.error" x-cloak class="mt-4 text-sm text-slate-400 border-t border-slate-100 pt-4">
                <span class="font-medium text-slate-500">Tips:</span> sediakan data transaksi beberapa periode agar analisis lebih akurat.
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <p class="text-sm font-medium text-slate-500">Total Target</p>
                <p class="text-xl font-bold text-slate-800 mt-1">{{ $summary['total'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <p class="text-sm font-medium text-slate-500">Tercapai</p>
                <p class="text-xl font-bold text-emerald-600 mt-1">{{ $summary['achieved'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
                <p class="text-sm font-medium text-slate-500">Rata-rata Capaian</p>
                <p class="text-xl font-bold text-indigo-600 mt-1">{{ $summary['avg_percentage'] }}%</p>
            </div>
        </div>

        {{-- Daftar Target --}}
        @if ($goalStats->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="font-semibold text-slate-700 mb-1">Belum ada target</h3>
                <p class="text-slate-500 text-sm mb-6">Mulai tetapkan target tabungan, pemasukan, atau batas pengeluaran.</p>
                <button @click="create()" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Buat Target Pertama
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach ($goalStats as $index => $goal)
                    @php
                        $meta = $typeMeta[$goal['type']] ?? $typeMeta['savings'];
                        $p = $goal['progress'];
                        $isCap = in_array($goal['type'], ['expense', 'category'], true);
                        $barColor = $p['achieved'] ? 'bg-emerald-500' : ($p['raw_percentage'] > 100 ? 'bg-red-500' : 'bg-blue-500');
                        $statusLabel = $p['achieved']
                            ? ($isCap ? 'Sesuai batas' : 'Tercapai')
                            : ($p['raw_percentage'] > 100 ? 'Melebihi batas' : 'Belum tercapai');
                        $statusColor = $p['achieved']
                            ? 'bg-emerald-100 text-emerald-700'
                            : ($p['raw_percentage'] > 100 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <h3 class="font-semibold text-slate-800 leading-snug">{{ $goal['name'] }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium shrink-0 {{ $meta['color'] }}">
                                {{ $meta['label'] }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">{{ $statusLabel }}</span>
                            <span class="text-xs text-slate-400">
                                @if ($goal['period'])
                                    Periode {{ $goal['period'] }}
                                @elseif ($goal['start_date'] || $goal['end_date'])
                                    {{ $goal['start_date'] }} s/d {{ $goal['end_date'] }}
                                @else
                                    Periode berjalan
                                @endif
                            </span>
                        </div>

                        @if ($goal['category'])
                            <p class="text-xs text-slate-500 mb-1">Kategori: <span class="font-medium">{{ $goal['category'] }}</span></p>
                        @endif
                        @if ($goal['account'])
                            <p class="text-xs text-slate-500 mb-1">Dompet: <span class="font-medium">{{ $goal['account'] }}</span></p>
                        @endif

                        <div class="mt-3">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="font-semibold {{ $p['achieved'] ? 'text-emerald-600' : ($p['raw_percentage'] > 100 ? 'text-red-600' : 'text-slate-700') }}">
                                    {{ $p['percentage'] }}%
                                </span>
                                <span class="text-slate-500">
                                    {{ rupiah($p['actual']) }}
                                    <span class="text-slate-400">/ {{ rupiah($p['target']) }}</span>
                                </span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $barColor }}" style="width: {{ min(100, max(0, $p['raw_percentage'])) }}%"></div>
                            </div>
                            <p class="text-xs text-slate-400 mt-2">
                                @if ($p['achieved'])
                                    @if ($isCap)
                                        Hemat {{ rupiah(abs($p['remaining'])) }} dari batas.
                                    @else
                                        Lebih {{ rupiah(abs($p['remaining'])) }} dari target.
                                    @endif
                                @else
                                    Sisa {{ $isCap ? 'batas' : 'menuju target' }}: {{ rupiah(abs($p['remaining'])) }}
                                @endif
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-slate-100">
                            <label class="flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer mr-auto" title="Aktif / nonaktif">
                                <input type="checkbox" x-model="goals[{{ $index }}].is_active" @change="toggleActive({{ $index }})" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                Aktif
                            </label>
                            <button @click="edit({{ $index }})" class="text-blue-600 hover:text-blue-800 font-medium text-sm transition-colors">Edit</button>
                            <button @click="confirmDelete({{ $index }})" class="text-red-600 hover:text-red-800 font-medium text-sm transition-colors">Hapus</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $goals->links() }}
            </div>
        @endif

        {{-- Create/Edit Modal --}}
        <div x-show="modal.show" style="display: none" role="dialog" aria-modal="true" aria-label="Form target" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
            <div x-show="modal.show" x-on:click="modal.show = false" aria-hidden="true" class="fixed inset-0 transform transition-all"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div x-show="modal.show" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-lg sm:mx-auto"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold" x-text="modal.mode === 'create' ? 'Tambah Target' : 'Edit Target'"></h2>
                        <button @click="modal.show = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form @submit.prevent="submit()">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Target</label>
                                <input type="text" x-model="modal.form.name" class="select-input" placeholder="Contoh: Tabungan Lebaran, Batas Belanja Bulanan" required>
                                <template x-if="modal.errors.name">
                                    <p class="mt-1 text-sm text-red-600" x-text="modal.errors.name[0]"></p>
                                </template>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Target</label>
                                <x-custom-select xModel="modal.form.type" variant="input"
                                    :items="[
                                        ['id' => 'savings', 'name' => 'Tabungan (Pemasukan - Pengeluaran)'],
                                        ['id' => 'income', 'name' => 'Total Pemasukan'],
                                        ['id' => 'expense', 'name' => 'Batas Pengeluaran'],
                                        ['id' => 'category', 'name' => 'Batas Pengeluaran Kategori'],
                                        ['id' => 'account', 'name' => 'Saldo Dompet'],
                                    ]"
                                    value-key="id" label-key="name" />
                                <template x-if="modal.errors.type">
                                    <p class="mt-1 text-sm text-red-600" x-text="modal.errors.type[0]"></p>
                                </template>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nominal Target</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                                    <input type="number" x-model.number="modal.form.target_amount" min="0" step="1000" class="w-full pl-8 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="0" required>
                                </div>
                                <template x-if="modal.errors.target_amount">
                                    <p class="mt-1 text-sm text-red-600" x-text="modal.errors.target_amount[0]"></p>
                                </template>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Periode (Opsional)</label>
                                <x-custom-select xModel="modal.form.period_id" variant="input"
                                    placeholder="Periode berjalan"
                                    alpine-items="periods"
                                    value-key="id" label-key="name" />
                                <p class="mt-1 text-xs text-slate-400">Kosongkan untuk menghitung di periode berjalan.</p>
                                <template x-if="modal.errors.period_id">
                                    <p class="mt-1 text-sm text-red-600" x-text="modal.errors.period_id[0]"></p>
                                </template>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Dari Tanggal</label>
                                    <input type="date" x-model="modal.form.start_date" class="select-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                                    <input type="date" x-model="modal.form.end_date" class="select-input">
                                </div>
                            </div>

                            <div x-show="modal.form.type === 'category'" x-cloak>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                                <x-custom-select xModel="modal.form.category_id" variant="input"
                                    placeholder="Pilih Kategori"
                                    alpine-items="categories"
                                    value-key="id" label-key="name" />
                                <template x-if="modal.errors.category_id">
                                    <p class="mt-1 text-sm text-red-600" x-text="modal.errors.category_id[0]"></p>
                                </template>
                            </div>

                            <div x-show="modal.form.type === 'account'" x-cloak>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Dompet</label>
                                <x-custom-select xModel="modal.form.account_id" variant="input"
                                    placeholder="Pilih Dompet"
                                    alpine-items="accounts"
                                    value-key="id" label-key="name" />
                                <template x-if="modal.errors.account_id">
                                    <p class="mt-1 text-sm text-red-600" x-text="modal.errors.account_id[0]"></p>
                                </template>
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" x-model="modal.form.is_active" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    Target aktif
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200">
                            <button type="button" @click="modal.show = false" class="px-3 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="submit" :disabled="modal.loading" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <template x-if="modal.loading">
                                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </template>
                                <span x-text="modal.mode === 'create' ? 'Simpan' : 'Perbarui'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div x-show="deleteModal.show" style="display: none" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
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
                    <h3 class="text-lg font-semibold mb-2">Hapus Target</h3>
                    <p class="text-slate-600 mb-6">Yakin ingin menghapus <strong x-text="deleteModal.goal?.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex justify-center gap-3">
                        <button @click="deleteModal.show = false" class="px-3 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                        <button @click="destroy()" :disabled="deleteModal.loading" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
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
