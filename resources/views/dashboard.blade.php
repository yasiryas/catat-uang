@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <p class="text-slate-500">Ringkasan keuangan Anda</p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}"
              x-data="{ period_id: '{{ $selectedPeriodId && $selectedPeriodId !== 'all' ? (string) $selectedPeriodId : 'all' }}' }"
              x-init="$watch('period_id', () => period_id !== '{{ $selectedPeriodId && $selectedPeriodId !== 'all' ? (string) $selectedPeriodId : 'all' }}' && $el.submit())">
            <x-custom-select name="period_id" xModel="period_id" variant="filter"
                placeholder="Semua Periode"
                :items="collect($periods)->map(fn($p) => ['id' => (string) $p->id, 'name' => $p->name])->prepend(['id' => 'all', 'name' => 'Semua Periode'])->toArray()"
                value-key="id" label-key="name" />
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-green-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Pemasukan</p>
                    <h2 class="text-2xl font-bold text-green-600 mt-2">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h2>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                    <h2 class="text-2xl font-bold text-red-600 mt-2">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h2>
                </div>
                <div class="bg-red-100 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Saldo Bersih</p>
                    <h2 class="text-2xl font-bold text-blue-600 mt-2">Rp {{ number_format($netBalance, 0, ',', '.') }}</h2>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-amber-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Transaksi</p>
                    <h2 class="text-2xl font-bold text-amber-600 mt-2">{{ $totalTransactions }}</h2>
                </div>
                <div class="bg-amber-100 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mt-6">
        {{-- Grafik --}}
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Grafik Keuangan {{ $year }}</h3>
            <div class="h-80 relative">
                <canvas id="chartKeuangan"></canvas>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Ringkasan</h3>
            <div class="space-y-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Periode Aktif</span>
                    <span class="font-semibold">{{ $period?->name ?? ($selectedPeriodId === 'all' || !$selectedPeriodId ? 'Semua' : '-') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Transaksi</span>
                    <span class="font-semibold">{{ $totalTransactions }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Kategori</span>
                    <span class="font-semibold">{{ $totalCategories }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Periode</span>
                    <span class="font-semibold">{{ $totalPeriods }}</span>
                </div>
            </div>

            <hr class="my-4">

            <h4 class="text-sm font-semibold text-slate-500 mb-3">Saldo per Dompet</h4>
            <div class="space-y-3 text-sm">
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

    {{-- Income vs Expense Breakdown --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Pemasukan per Kategori</h3>
            @if ($incomeByCategory->isNotEmpty())
                <div class="space-y-3">
                    @php $totalIncomeAmount = $incomeByCategory->sum('total'); @endphp
                    @foreach ($incomeByCategory as $item)
                        @php $pct = $totalIncomeAmount > 0 ? round(($item->total / $totalIncomeAmount) * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium">{{ $item->category?->name ?? '-' }}</span>
                                <span class="text-emerald-600 font-semibold">Rp {{ number_format((float) $item->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-400 text-center py-8">Belum ada pemasukan.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4">Pengeluaran per Kategori</h3>
            @if ($expenseByCategory->isNotEmpty())
                <div class="space-y-3">
                    @php $totalExpenseAmount = $expenseByCategory->sum('total'); @endphp
                    @foreach ($expenseByCategory as $item)
                        @php $pct = $totalExpenseAmount > 0 ? round(($item->total / $totalExpenseAmount) * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium">{{ $item->category?->name ?? '-' }}</span>
                                <span class="text-red-600 font-semibold">Rp {{ number_format((float) $item->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-400 text-center py-8">Belum ada pengeluaran.</p>
            @endif
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold">Transaksi Terbaru</h3>
            <a href="{{ route('transactions.income.index') }}" class="text-blue-600 text-sm hover:underline">Lihat Semua</a>
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
                            <td class="py-3 text-sm whitespace-nowrap">{{ \Carbon\Carbon::parse($tx->date)->locale('id')->isoFormat('DD MMM YYYY, HH:mm') }}</td>
                            <td class="py-3 text-sm font-medium">{{ $tx->category?->name ?? '-' }}</td>
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