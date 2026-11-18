@extends('layouts.app')

@section('title', 'Undangan Keluarga')

@section('content')
    <div class="max-w-2xl mx-auto">
        @if (session('error'))
            <div role="alert" class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 mb-4 text-sm">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sm:p-8">
            <h1 class="text-xl font-bold mb-2">Undangan Bergabung Keluarga</h1>
            <p class="text-sm text-slate-600 mb-6">
                Anda diundang bergabung dengan <strong>{{ $family->name }}</strong> sebagai anggota.
            </p>

            <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 mb-6 text-sm text-slate-600 space-y-2">
                <p><span class="font-medium text-slate-800">Email undangan:</span> {{ $invitation->email }}</p>
                <p><span class="font-medium text-slate-800">Berlaku hingga:</span> {{ $invitation->expires_at->translatedFormat('d F Y H:i') }}</p>
                <p>Data pribadi Anda yang lama tetap di akun Anda dan tidak ikut digabung ke buku keluarga.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <form method="POST" action="{{ $acceptUrl }}">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">Terima Undangan</button>
                </form>
                <form method="POST" action="{{ $rejectUrl }}">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg font-medium">Tolak</button>
                </form>
            </div>
        </div>
    </div>
@endsection
