@extends('layouts.app')

@section('title', 'Tambah Dompet')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Tambah Dompet</h1>
            <a href="{{ route('accounts.index') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>

        <form method="POST" action="{{ route('accounts.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Dompet</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="Contoh: Mandiri, BCA, Cash" required autofocus>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tipe</label>
                <select name="type" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" required>
                    <option value="bank" {{ old('type') === 'bank' ? 'selected' : '' }}>Bank</option>
                    <option value="cash" {{ old('type') === 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="ewallet" {{ old('type') === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan (Opsional)</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow" placeholder="Keterangan tambahan...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('accounts.index') }}" class="px-4 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Simpan</button>
            </div>
        </form>
    </div>
@endsection
