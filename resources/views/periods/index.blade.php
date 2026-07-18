@extends('layouts.app')

@section('title', 'Periode')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Periode</h1>
                <p class="text-gray-500">Kelola periode tahun/bulan.</p>
            </div>

            <a href="{{ route('periods.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">+
                Tambah</a>
        </div>

        <x-table-card>
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left p-4">Nama</th>
                    <th class="text-left p-4">Tanggal Mulai</th>
                    <th class="text-left p-4">Tanggal Akhir</th>
                    <th class="text-center p-4">Status</th>
                    <th class="text-center p-4">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($periods as $period)
                    <tr class="border-t">
                        <td class="p-4">{{ $period->name }}</td>
                        <td class="p-4">{{ $period->start_date }}</td>
                        <td class="p-4">{{ $period->end_date }}</td>
                        <td class="p-4 text-center">
                            @if ($period->is_closed)
                                <x-badge color="gray">Closed</x-badge>
                            @else
                                <x-badge color="green">Open</x-badge>
                            @endif
                        </td>
                        <td class="p-4 text-center space-x-3">
                            <x-action-link href="{{ route('periods.edit', $period) }}">Edit</x-action-link>
                            <x-action-delete action="{{ route('periods.destroy', $period) }}"
                                confirm="Hapus periode ini?">Hapus</x-action-delete>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-500">Belum ada periode.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table-card>

        <div>
            {{ $periods->links() }}
        </div>
    </div>
@endsection
@endsection
