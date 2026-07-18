@extends('layouts.guest')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-5xl">
            <div class="bg-white/80 backdrop-blur rounded-2xl shadow-xl overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <div class="p-8 md:p-12 bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 rounded-xl bg-white/15 border border-white/20 flex items-center justify-center">
                                <span class="text-xl font-bold">K</span>
                            </div>
                            <div>
                                <div class="text-sm opacity-90">KeuanganApp</div>
                                <div class="text-2xl font-bold leading-tight">Kelola pemasukan & pengeluaran</div>
                            </div>
                        </div>

                        <p class="opacity-95 text-sm md:text-base leading-relaxed">
                            Sistem pencatatan transaksi yang rapi, konsisten, dan mudah dipantau.
                            Siapkan periode, catat transaksi, lalu pantau saldo.
                        </p>

                        <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Periode</div>
                                <div class="text-white/80 text-xs mt-1">Buat & kelola periode tahun/bulan</div>
                            </div>
                            <div class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Transaksi</div>
                                <div class="text-white/80 text-xs mt-1">Income / Expense dengan kategori</div>
                            </div>
                            <div class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Mutasi</div>
                                <div class="text-white/80 text-xs mt-1">Perpindahan dari & ke akun</div>
                            </div>
                            <div class="rounded-xl bg-white/10 border border-white/15 p-4">
                                <div class="font-semibold text-sm">Adjustment</div>
                                <div class="text-white/80 text-xs mt-1">Penyesuaian untuk saldo yang akurat</div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-[#F8B803]"></div>
                            <div class="text-xs opacity-90">Siap mulai? Login untuk membuka dashboard.</div>
                        </div>
                    </div>

                    <div class="p-8 md:p-12">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-900">Selamat datang 👋</h2>
                            <p class="text-gray-600 mt-2 text-sm leading-relaxed">
                                Untuk mengakses data keuangan, silakan login terlebih dahulu.
                            </p>
                        </div>

                        <div class="space-y-4">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-white font-semibold hover:bg-blue-700 transition">
                                    Login
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-flex w-full items-center justify-center rounded-xl border border-gray-200 px-4 py-3 text-gray-900 font-semibold hover:bg-gray-50 transition">
                                    Buat Akun
                                </a>
                            @endif

                            <div class="pt-2 text-xs text-gray-500">
                                Dengan login, Anda bisa mengelola periode dan transaksi secara konsisten.
                            </div>
                        </div>

                        <div class="mt-8 rounded-2xl bg-gray-50 border border-gray-100 p-5">
                            <div class="text-sm font-semibold text-gray-900">Tips cepat</div>
                            <ul class="mt-2 text-sm text-gray-600 space-y-2">
                                <li>1) Buat periode dulu</li>
                                <li>2) Isi transaksi sesuai kategori</li>
                                <li>3) Pantau saldo di dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
