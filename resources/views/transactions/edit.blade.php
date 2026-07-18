@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Edit Transaksi</h1>

        <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <input type="hidden" name="type" value="{{ $transaction->type }}" />

            <div>
                <x-input-label for="date" :value="'Tanggal'" />
                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full"
                    value="{{ old('date', $transaction->date) }}" required />
                <x-input-error :messages="$errors->get('date')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="period_id" :value="'Periode'" />
                <select id="period_id" name="period_id" class="mt-1 block w-full border rounded-lg p-3" required>
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}"
                            {{ (int) $period->id === (int) $transaction->period_id ? 'selected' : '' }}>
                            {{ $period->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('period_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="category_id" :value="'Kategori'" />
                <select id="category_id" name="category_id" class="mt-1 block w-full border rounded-lg p-3" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ (int) $category->id === (int) $transaction->category_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="amount" :value="'Jumlah'" />
                <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full"
                    value="{{ old('amount', $transaction->amount) }}" required />
                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="note" :value="'Catatan'" />
                <x-text-input id="note" name="note" type="text" class="mt-1 block w-full"
                    value="{{ old('note', $transaction->note) }}" maxlength="1000" />
                <x-input-error :messages="$errors->get('note')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('transactions.index', ['type' => $transaction->type]) }}"
                    class="px-4 py-2 rounded-lg border">Batal</a>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
@endsection
