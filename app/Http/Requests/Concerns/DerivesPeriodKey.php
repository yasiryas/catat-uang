<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use Carbon\Carbon;

trait DerivesPeriodKey
{
    protected function prepareForValidation(): void
    {
        $startDate = $this->input('start_date');

        if (! is_string($startDate) || strtotime($startDate) === false) {
            return;
        }

        $date = Carbon::parse($startDate);

        $this->merge([
            'year' => $date->year,
            'month' => $date->month,
        ]);
    }
}
