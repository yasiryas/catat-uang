@extends('layouts.app')

@section('title', 'Tambah Mutasi')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Tambah Mutasi</h1>
            <a href="{{ route('mutations.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>

        <form method="POST" action="{{ route('mutations.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Periode</label>
                <select name="period_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                    <option value="">Pilih Periode</option>
                    @foreach ($periods as $p)
                        <option value="{{ $p->id }}" {{ old('period_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('period_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Dari Dompet</label>
                <select name="from_account_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                    <option value="">Pilih Dompet Asal</option>
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('from_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
                @error('from_account_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Ke Dompet</label>
                <select name="to_account_id" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                    <option value="">Pilih Dompet Tujuan</option>
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('to_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                    @endforeach
                </select>
                @error('to_account_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                    <input type="number" name="amount" step="0.01" value="{{ old('amount') }}" class="w-full pl-8 pr-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="0" required>
                </div>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan (Opsional)</label>
                <textarea name="note" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="Catatan...">{{ old('note') }}</textarea>
                @error('note')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('mutations.index') }}" class="px-4 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Simpan</button>
            </div>
        </form>
    </div>
@endsection
