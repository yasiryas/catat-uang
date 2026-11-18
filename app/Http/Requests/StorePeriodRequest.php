<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Http\Requests\Concerns\DerivesPeriodKey;
use Illuminate\Foundation\Http\FormRequest;

final class StorePeriodRequest extends FormRequest
{
    use DerivesPeriodKey;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'is_closed' => ['nullable', 'boolean'],
        ];
    }
}
