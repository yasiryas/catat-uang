@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

        {{-- Total Pemasukan --}}
        <div class="bg-white rounded-xl shadow-sm border border-green-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Pemasukan</p>
                    <h2 class="text-2xl font-bold text-green-600 mt-2">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h2>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <x-heroicon-o-arrow-down-circle class="w-8 h-8 text-green-600" />
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                    <h2 class="text-2xl font-bold text-red-600 mt-2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h2>
                </div>
                <div class="bg-red-100 p-3 rounded-xl">
                    <x-heroicon-o-arrow-up-circle class="w-8 h-8 text-red-600" />
                </div>
            </div>
        </div>

        {{-- Saldo Bersih --}}
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Saldo Bersih</p>
                    <h2 class="text-2xl font-bold text-blue-600 mt-2">Rp {{ number_format($netBalance, 0, ',', '.') }}</h2>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <x-heroicon-o-scale class="w-8 h-8 text-blue-600" />
                </div>
            </div>
        </div>

        {{-- Saldo Akhir --}}
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Saldo Akhir</p>
                    <h2 class="text-2xl font-bold text-amber-600 mt-2">Rp {{ number_format($closingBalance, 0, ',', '.') }}</h2>
                </div>
                <div class="bg-amber-100 p-3 rounded-xl">
                    <x-heroicon-o-banknotes class="w-8 h-8 text-amber-600" />
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">

        {{-- Grafik Keuangan --}}
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Grafik Keuangan {{ date('Y') }}</h3>
            <div class="h-80 relative">
                <canvas id="chartKeuangan"></canvas>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Ringkasan</h3>
            <div class="space-y-4 text-sm">
                <div class="flex justify-between">
                    <span>Periode Aktif</span>
                    <span class="font-semibold">{{ $period?->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Total Transaksi</span>
                    <span class="font-semibold">{{ $totalTransactions }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Kategori</span>
                    <span class="font-semibold">{{ $totalCategories }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Periode</span>
                    <span class="font-semibold">{{ $totalPeriods }}</span>
                </div>
            </div>

            <hr class="my-4">

            <h4 class="text-sm font-semibold text-slate-500 mb-3">Saldo per Dompet</h4>
            <div class="space-y-3 text-sm">
                @php
                    $accounts = \App\Models\Account::where('user_id', auth()->id())->get();
                @endphp
                @forelse($accounts as $account)
                    @php
                        $incomeTotal = \App\Models\Transaction::where('account_id', $account->id)->where('type', 'income')->sum('amount');
                        $expenseTotal = \App\Models\Transaction::where('account_id', $account->id)->where('type', 'expense')->sum('amount');
                        $saldo = $incomeTotal - $expenseTotal;
                    @endphp
                    <div class="flex justify-between">
                        <span class="flex items-center gap-2">
                            @if ($account->type === 'bank')
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            @elseif ($account->type === 'cash')
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @endif
                            {{ $account->name }}
                        </span>
                        <span class="font-semibold {{ $saldo >= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-slate-400 text-center py-4">Belum ada dompet.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Transaksi Terbaru --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold">Transaksi Terbaru</h3>
            <a href="{{ route('transactions.index') }}" class="text-blue-600 text-sm hover:underline">Lihat Semua</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-3 text-sm text-slate-500 font-medium">Tanggal</th>
                        <th class="text-left text-sm text-slate-500 font-medium">Kategori</th>
                        <th class="text-left text-sm text-slate-500 font-medium">Akun</th>
                        <th class="text-left text-sm text-slate-500 font-medium">Jenis</th>
                        <th class="text-right text-sm text-slate-500 font-medium">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 text-sm">{{ \Carbon\Carbon::parse($tx->date)->locale('id')->isoFormat('DD MMM YYYY') }}</td>
                            <td class="py-3 text-sm font-medium">{{ $tx->category?->name }}</td>
                            <td class="py-3 text-sm text-slate-500">{{ $tx->account?->name ?? '-' }}</td>
                            <td class="py-3">
                                @if ($tx->type === 'income')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Pemasukan</span>
                                @elseif ($tx->type === 'expense')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Pengeluaran</span>
                                @elseif ($tx->type === 'mutation')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Mutasi</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Penyesuaian</span>
                                @endif
                            </td>
                            <td class="py-3 text-sm font-medium text-right {{ $tx->type === 'expense' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $tx->type === 'expense' ? '-' : '+' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-400">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chartKeuangan').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartMonths),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: @json($chartIncome),
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Pengeluaran',
                    data: @json($chartExpense),
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, padding: 16 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            if (value >= 1000000) return 'Rp' + (value / 1000000).toFixed(0) + 'jt';
                            if (value >= 1000) return 'Rp' + (value / 1000).toFixed(0) + 'rb';
                            return 'Rp' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
