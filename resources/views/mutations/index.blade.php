@extends('layouts.app')

@section('title', 'Mutasi')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Mutasi</h1>
                <p class="text-gray-500">Kelola mutasi.</p>
            </div>
        </div>

        <x-table-card>
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left p-4">Tanggal</th>
                    <th class="text-left p-4">Periode</th>
                    <th class="text-left p-4">Dari</th>
                    <th class="text-left p-4">Ke</th>
                    <th class="text-right p-4">Jumlah</th>
                    <th class="text-center p-4">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($mutations as $mutation)
                    <tr class="border-t">
                        <td class="p-4">{{ $mutation->date }}</td>
                        <td class="p-4">{{ $mutation->period?->name }}</td>
                        <td class="p-4">{{ $mutation->from_account }}</td>
                        <td class="p-4">{{ $mutation->to_account }}</td>
                        <td class="p-4 text-right">Rp {{ number_format((float) $mutation->amount, 0, ',', '.') }}</td>
                        <td class="p-4 text-center space-x-3">
                            <x-action-link href="{{ route('mutations.edit', $mutation) }}">Edit</x-action-link>

                            <x-action-delete action="{{ route('mutations.destroy', $mutation) }}"
                                confirm="Hapus mutasi ini?">Hapus</x-action-delete>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-gray-500">Belum ada mutasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table-card>

        <div>
            {{ $mutations->links() }}
        </div>
    </div>
@endsection
