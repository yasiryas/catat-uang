@extends('layouts.app')

@section('title', 'Tambah Mutasi')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Tambah Mutasi</h1>

        <form method="POST" action="{{ route('mutations.store') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="period_id" :value="'Periode'" />
                <x-text-input id="period_id" name="period_id" type="number" class="mt-1 block w-full" required />
            </div>

            <div>
                <x-input-label for="from_account" :value="'Dari'" />
                <x-text-input id="from_account" name="from_account" type="text" class="mt-1 block w-full" />
            </div>

            <div>
                <x-input-label for="to_account" :value="'Ke'" />
                <x-text-input id="to_account" name="to_account" type="text" class="mt-1 block w-full" />
            </div>

            <div>
                <x-input-label for="amount" :value="'Jumlah'" />
                <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full"
                    required />
            </div>

            <div>
                <x-input-label for="date" :value="'Tanggal'" />
                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required />
            </div>

            <div>
                <x-input-label for="note" :value="'Catatan'" />
                <x-text-input id="note" name="note" type="text" class="mt-1 block w-full" maxlength="1000" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('mutations.index') }}" class="px-4 py-2 rounded-lg border">Batal</a>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
@endsection
