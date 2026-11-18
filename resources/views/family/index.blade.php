@extends('layouts.app')

@section('title', 'Keluarga')

@section('content')
    @php
        $user = auth()->user();
        $family = $user->family;
    @endphp

    <div x-data="familyPage()">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl font-bold">Keluarga</h1>
                <p class="text-sm text-slate-600">Kelola buku keuangan bersama untuk 2 akun atau lebih.</p>
            </div>
        </div>

        @if (session('error'))
            <div role="alert" class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 mb-6 text-sm">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div role="status" class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg p-3 mb-6 text-sm">{{ session('success') }}</div>
        @endif

        @if (! $family)
            {{-- Belum punya keluarga --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 max-w-2xl">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h2 class="text-lg font-bold mb-1">Buat Keluarga</h2>
                <p class="text-slate-500 text-sm mb-6">Kepala keluarga membuat buku keuangan bersama, lalu mengundang pasangan/anak (akun yang sudah terdaftar) untuk mengelola keuangan dalam satu buku yang sama.</p>

                <form method="POST" action="{{ route('family.store') }}" class="max-w-md space-y-4">
                    @csrf
                    <div>
                        <label for="family-name" class="block text-sm font-medium text-slate-700 mb-1">Nama Keluarga</label>
                        <input id="family-name" type="text" name="name" value="{{ old('name') }}" class="select-input" placeholder="Contoh: Keluarga Santoso" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">Buat Keluarga</button>
                </form>
            </div>
        @else
            {{-- Kelola keluarga --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                            <div>
                                <h2 class="font-semibold text-slate-800">{{ $family->name }}</h2>
                                <p class="text-xs text-slate-400">{{ $family->members->count() }} anggota</p>
                            </div>
                            @if ($user->isFamilyAdmin())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Kepala Keluarga</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Anggota</span>
                            @endif
                        </div>

                        <ul class="divide-y divide-slate-100">
                            @foreach ($family->members as $member)
                                <li class="px-6 py-4 flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-semibold text-slate-600 shrink-0">
                                        {{ strtoupper(mb_substr($member->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-slate-800 truncate">
                                            {{ $member->name }}
                                            @if ($member->id === $user->id)
                                                <span class="text-slate-400 font-normal">(Anda)</span>
                                            @endif
                                        </p>
                                        <p class="text-sm text-slate-500 truncate">{{ $member->email }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $member->family_role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $member->family_role === 'admin' ? 'Admin' : 'Anggota' }}
                                    </span>
                                    @if ($user->isFamilyAdmin() && $member->id !== $user->id)
                                        <button @click="confirmRemove(@js(['id' => $member->id, 'name' => $member->name]))" class="text-red-600 hover:text-red-800 font-medium text-sm transition-colors">Keluarkan</button>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($user->isFamilyAdmin())
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                            <h3 class="font-semibold mb-1">Undang Anggota</h3>
                            <p class="text-sm text-slate-500 mb-4">Masukkan email akun yang <strong>sudah terdaftar</strong> di aplikasi ini. Anggota yang bergabung akan ikut melihat & mengelola buku keuangan yang sama.</p>
                            <form method="POST" action="{{ route('family.invite') }}" class="flex flex-col sm:flex-row gap-3 max-w-lg" x-data="{ email: '' }">
                                @csrf
                                <label for="invite-email" class="sr-only">Email anggota yang diundang</label>
                                <input id="invite-email" type="email" name="email" x-model="email" class="select-input flex-1" placeholder="email@contoh.com" required>
                                <button type="submit" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium shrink-0">Undang</button>
                            </form>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="font-semibold mb-2 text-slate-800">Cara Kerja Keluarga</h3>
                        <ul class="text-sm text-slate-500 space-y-2 list-disc list-inside">
                            <li>Semua anggota melihat transaksi, dompet, periode, dan target yang sama.</li>
                            <li>Setiap transaksi tetap tercatat siapa yang membuatnya.</li>
                            <li>Capaian target & rekomendasi AI dihitung dari seluruh data keluarga.</li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
                        <h3 class="font-semibold mb-2 text-red-700">
                            {{ $user->isFamilyAdmin() ? 'Bubarkan Keluarga' : 'Keluar dari Keluarga' }}
                        </h3>
                        <p class="text-sm text-slate-500 mb-4">
                            {{ $user->isFamilyAdmin() ? 'Semua anggota akan dilepas, data tetap tersimpan per pemiliknya.' : 'Anda akan berhenti berbagi buku keuangan ini. Data tetap tersimpan di akun Anda.' }}
                        </p>
                        @if ($user->isFamilyAdmin())
                            <button @click="confirmDestroy()" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm">Bubarkan Keluarga</button>
                        @else
                            <button @click="confirmLeave()" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm">Keluar dari Keluarga</button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Confirm remove member --}}
            <div x-show="removeModal.show" style="display: none" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
                <div x-show="removeModal.show" x-on:click="removeModal.show = false" class="fixed inset-0 transform transition-all"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>
                <div x-show="removeModal.show" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0">
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Keluarkan Anggota</h3>
                        <p class="text-slate-600 mb-6">Yakin ingin mengeluarkan <strong x-text="removeModal.member?.name"></strong> dari keluarga?</p>
                        <div class="flex justify-center gap-3">
                            <button @click="removeModal.show = false" class="px-3 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                            <button @click="removeMember()" :disabled="removeModal.loading" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 flex items-center gap-2">
                                <template x-if="removeModal.loading"><svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></template>
                                <span>Keluarkan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Confirm leave / destroy --}}
            <div x-show="dangerModal.show" style="display: none" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center">
                <div x-show="dangerModal.show" x-on:click="dangerModal.show = false" class="fixed inset-0 transform transition-all"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>
                <div x-show="dangerModal.show" class="bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0">
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2" x-text="dangerModal.title"></h3>
                        <p class="text-slate-600 mb-6" x-text="dangerModal.message"></p>
                        <div class="flex justify-center gap-3">
                            <button @click="dangerModal.show = false" class="px-3 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                            <button @click="runDanger()" :disabled="dangerModal.loading" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 flex items-center gap-2">
                                <template x-if="dangerModal.loading"><svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></template>
                                <span x-text="dangerModal.actionLabel"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
