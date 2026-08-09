<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdjustmentLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period_id' => ['required', 'integer', Rule::exists('periods', 'id')->where($this->scopedClause())],
            'type' => ['required', 'in:income,expense'],
            'amount' => ['required', 'numeric'],
            'note' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date'],
        ];
    }

    protected function scopedClause(): \Closure
    {
        return function ($query): void {
            $user = $this->user();
            $query->where($user->belongsToFamily() ? 'family_id' : 'user_id', $user->scopeKey());
        };
    }
}
