@extends('layouts.app')

@section('content')
    <div x-data="transactionTypePage()" x-init="init()">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Pengeluaran</h1>
                <p class="text-slate-500">Kelola pengeluaran Anda</p>
            </div>

            <button @click="create()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pengeluaran
            </button>
        </div>

        @include('transactions.partials.table', ['type' => 'expense'])
        @include('transactions.partials.modal-form', ['type' => 'expense', 'title' => 'Pengeluaran', 'color' => 'red'])
        @include('transactions.partials.modal-delete', ['type' => 'expense'])
    </div>
@endsection