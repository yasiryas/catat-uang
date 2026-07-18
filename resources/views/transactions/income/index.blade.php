@extends('layouts.app')

@section('content')
    <div x-data="transactionTypePage()" x-init="init()">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Pemasukan</h1>
                <p class="text-slate-500">Kelola pemasukan Anda</p>
            </div>

            <button @click="create()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pemasukan
            </button>
        </div>

        @include('transactions.partials.table', ['type' => 'income'])
        @include('transactions.partials.modal-form', ['type' => 'income', 'title' => 'Pemasukan', 'color' => 'emerald'])
        @include('transactions.partials.modal-delete', ['type' => 'income'])
    </div>
@endsection