@extends('layouts.app')

@section('title', 'Adjustment')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Adjustment</h1>
                <p class="text-gray-500">Kelola penyesuaian.</p>
            </div>
        </div>

        <x-table-card>
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left p-4">Tanggal</th>
                    <th class="text-left p-4">Periode</th>
                    <th class="text-left p-4">Jenis</th>
                    <th class="text-right p-4">Jumlah</th>
                    <th class="text-center p-4">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($adjustments as $adjustment)
                    <tr class="border-t">
                        <td class="p-4">{{ $adjustment->date }}</td>
                        <td class="p-4">{{ $adjustment->period?->name }}</td>
                        <td class="p-4">
                            @if ($adjustment->type === 'income')
                                <x-badge color="green">Income</x-badge>
                            @else
                                <x-badge color="red">Expense</x-badge>
                            @endif
                        </td>
                        <td class="p-4 text-right">Rp {{ number_format((float) $adjustment->amount, 0, ',', '.') }}</td>
                        <td class="p-4 text-center space-x-3">
                            <x-action-link href="{{ route('adjustments.edit', $adjustment) }}">Edit</x-action-link>

                            <x-action-delete action="{{ route('adjustments.destroy', $adjustment) }}"
                                confirm="Hapus adjustment ini?">Hapus</x-action-delete>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-500">Belum ada adjustment.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table-card>

        <div>
            {{ $adjustments->links() }}
        </div>
    </div>
@endsection
