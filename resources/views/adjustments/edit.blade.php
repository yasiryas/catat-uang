@extends('layouts.app')

@section('title', 'Edit Adjustment')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Edit Adjustment</h1>
            <a href="{{ route('adjustments.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>

        <form method="POST" action="{{ route('adjustments.update', $adjustment) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Periode</label>
                <x-custom-select name="period_id" variant="input"
                    placeholder="Pilih Periode"
                    :items="$periods->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->toArray()"
                    value-key="id" label-key="name" value="{{ old('period_id', $adjustment->period_id) }}" />
                @error('period_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis</label>
                <x-custom-select name="type" variant="input"
                    :items="[['id' => 'income', 'name' => 'Pemasukan'], ['id' => 'expense', 'name' => 'Pengeluaran']]"
                    value-key="id" label-key="name" value="{{ old('type', $adjustment->type) }}" />
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                    <input type="number" name="amount" step="0.01" value="{{ old('amount', $adjustment->amount) }}" class="w-full pl-8 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="0" required>
                </div>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ old('date', $adjustment->date) }}" class="select-input" required>
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan (Opsional)</label>
                <textarea name="note" rows="3" class="select-input" placeholder="Catatan...">{{ old('note', $adjustment->note) }}</textarea>
                @error('note')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('adjustments.index') }}" class="px-4 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Perbarui</button>
            </div>
        </form>
    </div>
@endsection
