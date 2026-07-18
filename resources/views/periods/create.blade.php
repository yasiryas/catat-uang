@extends('layouts.app')

@section('title', 'Tambah Periode')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Tambah Periode</h1>

        <form method="POST" action="{{ route('periods.store') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="name" :value="'Nama'" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="start_date" :value="'Tanggal Mulai'" />
                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="end_date" :value="'Tanggal Akhir'" />
                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_closed" value="1" class="rounded" />
                    <span>Closed</span>
                </label>
                <x-input-error :messages="$errors->get('is_closed')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('periods.index') }}" class="px-4 py-2 rounded-lg border">Batal</a>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
@endsection
