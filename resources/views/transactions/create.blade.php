@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Tambah Transaksi</h1>

        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="type" value="{{ $type ?? 'income' }}" />

            <div>
                <x-input-label for="date" :value="'Tanggal'" />
                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('date')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="period_id" :value="'Periode'" />
                <select id="period_id" name="period_id" class="mt-1 block w-full border rounded-lg p-3" required>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('period_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="category_id" :value="'Kategori'" />
                <select id="category_id" name="category_id" class="mt-1 block w-full border rounded-lg p-3" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="amount" :value="'Jumlah'" />
                <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full"
                    required />
                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="note" :value="'Catatan'" />
                <x-text-input id="note" name="note" type="text" class="mt-1 block w-full" maxlength="1000" />
                <x-input-error :messages="$errors->get('note')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('transactions.index', ['type' => $type ?? 'income']) }}"
                    class="px-4 py-2 rounded-lg border">Batal</a>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
@endsection
