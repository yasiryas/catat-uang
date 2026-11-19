@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Dashboard</h1>
            <p class="text-sm text-slate-600">Ringkasan keuangan Anda</p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}"
              x-data="{ period_id: @js($selectedPeriodId), initialPeriodId: @js($selectedPeriodId) }"
              x-init="$watch('period_id', (value) => value !== initialPeriodId && $el.submit())">
            <x-custom-select name="period_id" xModel="period_id" variant="filter"
                placeholder="Semua Periode"
                :items="collect($periods)->map(fn($p) => ['id' => (string) $p->id, 'name' => $p->name])->prepend(['id' => 'all', 'name' => 'Semua Periode'])->toArray()"
                value-key="id" label-key="name" />
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl shadow-sm border border-green-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm">Total Pemasukan</p>
                    <h2 class="text-xl font-bold text-green-600 mt-1">{{ rupiah($totalIncome) }}</h2>
                </div>
                <div class="bg-green-100 p-2.5 rounded-lg">
                    <x-ledger-icon name="income" class="w-6 h-6 text-green-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm">Total Pengeluaran</p>
                    <h2 class="text-xl font-bold text-red-600 mt-1">{{ rupiah($totalExpense) }}</h2>
                </div>
                <div class="bg-red-100 p-2.5 rounded-lg">
                    <x-ledger-icon name="expense" class="w-6 h-6 text-red-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm">Saldo Bersih</p>
                    <h2 class="text-xl font-bold text-blue-600 mt-1">{{ rupiah($netBalance) }}</h2>
                </div>
                <div class="bg-blue-100 p-2.5 rounded-lg">
                    <x-ledger-icon name="chart" class="w-6 h-6 text-blue-600" />
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-amber-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-600 text-sm">Total Transaksi</p>
                    <h2 class="text-xl font-bold text-amber-600 mt-1">{{ $totalTransactions }}</h2>
                </div>
                <div class="bg-amber-100 p-2.5 rounded-lg">
                    <x-ledger-icon name="receipt" class="w-6 h-6 text-amber-600" />
                </div>
            </div>
        </div>
    </div>

    {{-- Target & Capaian --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mt-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="font-semibold">Target & Capaian</h2>
                <p class="text-xs text-slate-500">Capaian dihitung otomatis dari transaksi Anda.</p>
            </div>
            <a href="{{ route('goals.index') }}" class="text-blue-600 text-sm hover:underline font-medium">Kelola Target</a>
        </div>

        @if ($goals->isEmpty())
            <p class="text-slate-500 text-center py-8">Belum ada target. <a href="{{ route('goals.index') }}" class="text-blue-600 hover:underline font-medium">Buat target pertama</a> untuk mulai memantau capaian.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                @foreach ($goals as $goal)
                    @php
                        $isCap = in_array($goal['type'], ['expense', 'category'], true);
                        $barColor = $goal['achieved'] ? 'bg-emerald-500' : ($goal['raw_percentage'] > 100 ? 'bg-red-500' : 'bg-blue-500');
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-medium text-slate-700 truncate" title="{{ $goal['name'] }}">{{ $goal['name'] }}</span>
                            <span class="font-semibold {{ $goal['achieved'] ? 'text-emerald-600' : ($goal['raw_percentage'] > 100 ? 'text-red-600' : 'text-slate-600') }}">{{ $goal['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 mb-1">
                            <div class="h-2 rounded-full {{ $barColor }}" style="width: {{ min(100, max(0, $goal['raw_percentage'])) }}%"></div>
                        </div>
                        <p class="text-xs text-slate-500">
                            {{ rupiah($goal['actual']) }}
                            / {{ rupiah($goal['target']) }}
                            @if ($goal['achieved'])
                                <span class="text-emerald-600 font-medium">â€” {{ $isCap ? 'sesuai batas' : 'tercapai' }}</span>
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-3 mt-4">
        {{-- Grafik --}}
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm p-4">
            <h2 class="font-semibold mb-3">Grafik Keuangan {{ $year }}</h3>
            <div class="h-64 relative">
                <canvas id="chartKeuangan" role="img" aria-label="Grafik pemasukan dan pengeluaran tahun {{ $year }}">Grafik keuangan tahun {{ $year }} tidak dapat ditampilkan pada browser ini.</canvas>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="bg-white rounded-xl shadow-sm p-4">
            <h2 class="font-semibold mb-3">Ringkasan</h3>
            <div class="space-y-3 text-sm">
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

            <hr class="my-3">

            <h3 class="text-sm font-semibold text-slate-500 mb-3">Saldo per Dompet</h3>
            <div class="space-y-3 text-sm">
                @forelse($accounts as $account)
                    <div class="flex justify-between">
                        <span class="flex items-center gap-2">
                            @if ($account->type === 'bank')
                                <x-ledger-icon name="bank" class="w-4 h-4 text-blue-500" />
                            @elseif ($account->type === 'cash')
                                <x-ledger-icon name="cash" class="w-4 h-4 text-emerald-500" />
                            @else
                                <x-ledger-icon name="ewallet" class="w-4 h-4 text-purple-500" />
                            @endif
                            {{ $account->name }}
                        </span>
                        <span class="font-semibold {{ $account->saldo >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ rupiah($account->saldo) }}</span>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-4">Belum ada dompet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Income vs Expense Breakdown --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-3 mt-4">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <h2 class="font-semibold mb-3">Pemasukan per Kategori</h3>
            @if ($incomeByCategory->isNotEmpty())
                <div class="space-y-3">
                    @php $totalIncomeAmount = $incomeByCategory->sum('total'); @endphp
                    @foreach ($incomeByCategory as $item)
                        @php $pct = $totalIncomeAmount > 0 ? round(($item->total / $totalIncomeAmount) * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium">{{ $item->category?->name ?? '-' }}</span>
                                <span class="text-emerald-600 font-semibold">{{ rupiah((float) $item->total) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500 text-center py-8">Belum ada pemasukan.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4">
            <h2 class="font-semibold mb-3">Pengeluaran per Kategori</h3>
            @if ($expenseByCategory->isNotEmpty())
                <div class="space-y-3">
                    @php $totalExpenseAmount = $expenseByCategory->sum('total'); @endphp
                    @foreach ($expenseByCategory as $item)
                        @php $pct = $totalExpenseAmount > 0 ? round(($item->total / $totalExpenseAmount) * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium">{{ $item->category?->name ?? '-' }}</span>
                                <span class="text-red-600 font-semibold">{{ rupiah((float) $item->total) }}</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500 text-center py-8">Belum ada pengeluaran.</p>
            @endif
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mt-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold">Transaksi Terbaru</h2>
            <a href="{{ route('transactions.income.index') }}" class="text-blue-600 text-sm hover:underline">Lihat Semua</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full md:text-sm">
                <thead>
                    <tr class="border-b">
                        <th scope="col" class="text-left py-3 md:py-2.5 text-sm text-slate-500 font-medium">Tanggal</th>
                        <th scope="col" class="text-left text-sm text-slate-500 font-medium">Kategori</th>
                        <th scope="col" class="text-left text-sm text-slate-500 font-medium">Akun</th>
                        <th scope="col" class="text-left text-sm text-slate-500 font-medium">Jenis</th>
                        <th scope="col" class="text-right text-sm text-slate-500 font-medium">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 md:py-2.5 text-sm whitespace-nowrap"><time datetime="{{ $tx->date?->toIso8601String() }}">{{ tanggal($tx->date, true) }}</time></td>
                            <td class="py-3 md:py-2.5 text-sm font-medium">{{ $tx->category?->name ?? '-' }}</td>
                            <td class="py-3 md:py-2.5 text-sm text-slate-500">{{ $tx->account?->name ?? '-' }}</td>
                            <td class="py-3 md:py-2.5">
                                @if ($tx->type === 'income')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Pemasukan</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Pengeluaran</span>
                                @endif
                            </td>
                            <td class="py-3 md:py-2.5 text-sm font-medium text-right {{ $tx->type === 'expense' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $tx->type === 'expense' ? '-' : '+' }}{{ rupiah($tx->amount) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-slate-500">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script type="module">
    import { Chart } from 'chart.js/auto';

    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('chartKeuangan').getContext('2d');
        const fontFamily = 'ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
        const currency = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartMonths),
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: @json($chartIncome),
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 28,
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($chartExpense),
                        backgroundColor: 'rgba(239, 68, 68, 0.85)',
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 28,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                animation: { duration: 500, easing: 'easeOutQuart' },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 6,
                            boxHeight: 6,
                            padding: 16,
                            color: '#64748b',
                            font: { size: 12, family: fontFamily },
                        },
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 8,
                        boxPadding: 6,
                        usePointStyle: true,
                        callbacks: {
                            label: (context) => `${context.dataset.label}: ${currency.format(context.parsed.y)}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 12, family: fontFamily } },
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: 'rgba(148, 163, 184, 0.18)', drawTicks: false },
                        ticks: {
                            padding: 8,
                            color: '#94a3b8',
                            font: { size: 12, family: fontFamily },
                            callback: function (value) {
                                if (value >= 1000000) return 'Rp' + (value / 1000000).toFixed(0) + 'jt';
                                if (value >= 1000) return 'Rp' + (value / 1000).toFixed(0) + 'rb';
                                return 'Rp' + value;
                            },
                        },
                    },
                },
            },
        });
    });
</script>
@endpush