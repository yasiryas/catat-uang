<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;

class GoalService
{
    /**
     * Hitung capaian sebuah target berdasarkan transaksi nyata.
     *
     * @return array{actual: float, target: float, percentage: int, raw_percentage: int, achieved: bool, remaining: float, income: float, expense: float}
     */
    public function progressFor(Goal $goal): array
    {
        $query = $this->scopedQuery($goal)
            ->when(
                $goal->period_id,
                fn (Builder $q) => $q->where('period_id', $goal->period_id)
            )
            ->when(
                ! $goal->period_id && ! $goal->start_date && ! $goal->end_date,
                fn (Builder $q) => $q->where('period_id', app(PeriodService::class)->ensurePeriodExists($goal->user)->id)
            )
            ->when($goal->start_date, fn (Builder $q) => $q->whereDate('date', '>=', $goal->start_date))
            ->when($goal->end_date, fn (Builder $q) => $q->whereDate('date', '<=', $goal->end_date));

        $income = (float) (clone $query)->where('type', 'income')->sum('amount');
        $expense = (float) (clone $query)->where('type', 'expense')->sum('amount');

        $actual = match ($goal->type) {
            'income' => $income,
            'expense' => $expense,
            'savings' => $income - $expense,
            'category' => (float) (clone $query)->where('type', 'expense')->where('category_id', $goal->category_id)->sum('amount'),
            'account' => (float) (clone $query)->where('account_id', $goal->account_id)->where('type', 'income')->sum('amount')
                - (float) (clone $query)->where('account_id', $goal->account_id)->where('type', 'expense')->sum('amount'),
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
            'income' => $income,
            'expense' => $expense,
        ];
    }

    private function scopedQuery(Goal $goal): Builder
    {
        return $goal->family_id
            ? Transaction::query()->where('family_id', $goal->family_id)
            : Transaction::query()->where('user_id', $goal->user_id);
    }
}
