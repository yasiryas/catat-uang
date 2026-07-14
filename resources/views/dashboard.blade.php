@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        {{-- Total Pemasukan --}}
        <div class="bg-white rounded-xl shadow-sm border border-green-100 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-gray-500 text-sm">
                        Total Pemasukan
                    </p>

                    <h2 class="text-2xl font-bold text-green-600 mt-2">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </h2>

                </div>

                <div class="bg-green-100 p-3 rounded-xl">

                    <x-heroicon-o-arrow-down-circle class="w-8 h-8 text-green-600" />

                </div>

            </div>

        </div>

        {{-- Total Pengeluaran --}}
        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-gray-500 text-sm">
                        Total Pengeluaran
                    </p>

                    <h2 class="text-2xl font-bold text-red-600 mt-2">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </h2>

                </div>

                <div class="bg-red-100 p-3 rounded-xl">

                    <x-heroicon-o-arrow-up-circle class="w-8 h-8 text-red-600" />

                </div>

            </div>

        </div>

        {{-- Saldo Bersih --}}
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-gray-500 text-sm">
                        Saldo Bersih
                    </p>

                    <h2 class="text-2xl font-bold text-blue-600 mt-2">
                        Rp {{ number_format($netBalance, 0, ',', '.') }}
                    </h2>

                </div>

                <div class="bg-blue-100 p-3 rounded-xl">

                    <x-heroicon-o-scale class="w-8 h-8 text-blue-600" />

                </div>

            </div>

        </div>

        {{-- Saldo Akhir --}}
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-gray-500 text-sm">
                        Saldo Akhir
                    </p>

                    <h2 class="text-2xl font-bold text-amber-600 mt-2">
                        Rp {{ number_format($closingBalance, 0, ',', '.') }}
                    </h2>

                </div>

                <div class="bg-amber-100 p-3 rounded-xl">

                    <x-heroicon-o-banknotes class="w-8 h-8 text-amber-600" />

                </div>

            </div>

        </div>

    </div>
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">

        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm p-6">

            <h3 class="font-semibold mb-4">

                Grafik Keuangan

            </h3>

            <div class="h-80 flex items-center justify-center text-gray-400">

                Grafik akan dibuat pada tahap berikutnya.

            </div>

        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">

            <h3 class="font-semibold mb-4">

                Ringkasan

            </h3>

            <div class="space-y-4 text-sm">

                <div class="flex justify-between">

                    <span>Total Transaksi</span>

                    <span>0</span>

                </div>

                <div class="flex justify-between">

                    <span>Kategori</span>

                    <span>6</span>

                </div>

                <div class="flex justify-between">

                    <span>Periode</span>

                    <span>1</span>

                </div>

            </div>


        </div>

    </div>
    <div class="bg-white rounded-xl shadow-sm p-6 mt-6">

        <div class="flex items-center justify-between mb-4">

            <h3 class="font-semibold">

                Transaksi Terbaru

            </h3>

            <a href="#" class="text-blue-600 text-sm hover:underline">

                Lihat Semua

            </a>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="border-b">

                        <th class="text-left py-3">Tanggal</th>
                        <th class="text-left">Kategori</th>
                        <th class="text-left">Jenis</th>
                        <th class="text-right">Nominal</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td colspan="4" class="text-center py-8 text-gray-400">

                            Belum ada transaksi.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

@endsection
