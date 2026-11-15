<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;

class GoalService
{
    public function __construct(
        private readonly PeriodService $periodService,
    ) {}

    /**
     * Hitung capaian sebuah target berdasarkan transaksi nyata.
     *
     * @return array{actual: float, target: float, percentage: int, raw_percentage: int, achieved: bool, remaining: float, income: float, expense: float}
     */
    public function progressFor(Goal $goal): array
    {
        return $this->progressForMany([$goal])[$goal->id];
    }

    /**
     * Hitung capaian banyak target sekaligus.
     * Target dengan filter identik berbagi satu query agregasi.
     *
     * @param  iterable<Goal>  $goals
     * @return array<int, array> keyed by goal id
     */
    public function progressForMany(iterable $goals): array
    {
        $aggregates = [];

        foreach ($goals as $goal) {
            $signature = $this->filterSignature($goal);
            $aggregates[$signature] ??= $this->aggregateScope($goal);

            $progress[$goal->id] = $this->buildProgress($goal, $aggregates[$signature]);
        }

        return $progress ?? [];
    }

    private function buildProgress(Goal $goal, array $aggregate): array
    {
        $actual = match ($goal->type) {
            'income' => $aggregate['income'],
            'expense' => $aggregate['expense'],
            'savings' => $aggregate['income'] - $aggregate['expense'],
            'category' => $aggregate['expenseByCategory'][$goal->category_id] ?? 0.0,
            'account' => ($aggregate['byAccount'][$goal->account_id]['income'] ?? 0.0)
                - ($aggregate['byAccount'][$goal->account_id]['expense'] ?? 0.0),
            default => 0.0,
        };

        $target = (float) $goal->target_amount;
        $isCapType = in_array($goal->type, ['expense', 'category'], true);
        $rawPercentage = $target > 0 ? ($actual / $target) * 100 : 0.0;

        return [
            'actual' => round($actual, 2),
            'target' => $target,
            'percentage' => (int) round(min(100, max(0, $rawPercentage))),
            'raw_percentage' => (int) round($rawPercentage),
            'achieved' => $target > 0 && ($isCapType ? $actual <= $target : $actual >= $target),
            'remaining' => round($target - $actual, 2),
            'income' => $aggregate['income'],
            'expense' => $aggregate['expense'],
        ];
    }

    private function aggregateScope(Goal $goal): array
    {
        $rows = $this->scopedQuery($goal)
            ->selectRaw('type, category_id, account_id, SUM(amount) as total')
            ->groupBy('type', 'category_id', 'account_id')
            ->get();

        $income = 0.0;
        $expense = 0.0;
        $expenseByCategory = [];
        $byAccount = [];

        foreach ($rows as $row) {
            $total = (float) $row->total;

            if ($row->type === 'income') {
                $income += $total;
                $byAccount[$row->account_id]['income'] = ($byAccount[$row->account_id]['income'] ?? 0.0) + $total;
            } elseif ($row->type === 'expense') {
                $expense += $total;
                $expenseByCategory[$row->category_id] = ($expenseByCategory[$row->category_id] ?? 0.0) + $total;
                $byAccount[$row->account_id]['expense'] = ($byAccount[$row->account_id]['expense'] ?? 0.0) + $total;
            }
        }

        return compact('income', 'expense', 'expenseByCategory', 'byAccount');
    }

    private function scopedQuery(Goal $goal): Builder
    {
        $query = Transaction::query()
            ->when($goal->family_id, fn (Builder $q) => $q->where('family_id', $goal->family_id))
            ->when(! $goal->family_id, fn (Builder $q) => $q->where('user_id', $goal->user_id))
            ->when($goal->period_id, fn (Builder $q) => $q->where('period_id', $goal->period_id))
            // Tanpa periode & rentang tanggal: pakai periode berjalan.
            ->when(
                ! $goal->period_id && ! $goal->start_date && ! $goal->end_date,
                fn (Builder $q) => $q->where('period_id', $this->periodService->ensurePeriodExists($goal->user)->id)
            )
            ->when($goal->start_date, fn (Builder $q) => $q->whereDate('date', '>=', $goal->start_date))
            ->when($goal->end_date, fn (Builder $q) => $q->whereDate('date', '<=', $goal->end_date));

        return $query;
    }

    private function filterSignature(Goal $goal): string
    {
        return implode('|', [
            $goal->family_id ?: 'u'.$goal->user_id,
            $goal->period_id ?: ($goal->start_date || $goal->end_date ? 'range' : 'auto'),
            $goal->start_date?->toDateString(),
            $goal->end_date?->toDateString(),
        ]);
    }
}
