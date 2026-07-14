@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Transaksi</h1>
                <p class="text-gray-500">{{ isset($type) && $type ? strtoupper($type) : 'ALL' }} transaksi</p>
            </div>

            <a href="{{ route('transactions.create', ['type' => $type ?? 'income']) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                + Tambah
            </a>
        </div>

        <x-table-card>
            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left p-4">Tanggal</th>
                    <th class="text-left p-4">Kategori</th>
                    <th class="text-left p-4">Periode</th>
                    <th class="text-right p-4">Jumlah</th>
                    <th class="text-center p-4">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($transactions as $transaction)
                    <tr class="border-t">
                        <td class="p-4">{{ $transaction->date }}</td>
                        <td class="p-4">{{ $transaction->category?->name }}</td>
                        <td class="p-4">{{ $transaction->period?->name }}</td>
                        <td class="p-4 text-right">
                            @php $sign = $transaction->type === 'expense' ? '-' : ''; @endphp
                            {{ $sign }} Rp {{ number_format(abs((float) $transaction->amount), 0, ',', '.') }}
                        </td>
                        <td class="p-4 text-center space-x-3">
                            <x-action-link href="{{ route('transactions.edit', $transaction) }}">Edit</x-action-link>

                            <x-action-delete action="{{ route('transactions.destroy', $transaction) }}"
                                confirm="Hapus transaksi ini?">Hapus</x-action-delete>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-500">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table-card>

        <div>
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
