@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500">
                Total Pemasukan
            </h3>

            <p class="text-2xl font-bold text-emerald-600 mt-1">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500">
                Total Pengeluaran
            </h3>

            <p class="text-2xl font-bold text-red-600 mt-1">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500">
                Saldo Bersih
            </h3>

            <p class="text-2xl font-bold text-blue-600 mt-1">
                Rp {{ number_format($netBalance, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500">
                Saldo Akhir
            </h3>

            <p class="text-2xl font-bold text-indigo-600 mt-1">
                Rp {{ number_format($closingBalance, 0, ',', '.') }}
            </p>
        </div>
    </div>

@endsection
