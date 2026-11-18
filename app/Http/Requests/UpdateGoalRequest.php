<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasScopedValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGoalRequest extends FormRequest
{
    use HasScopedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = $this->input('type');

        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense,savings,category,account'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'period_id' => [
                'nullable', 'integer',
                Rule::exists('periods', 'id')->where($this->scopedClause()),
            ],
            'category_id' => [
                'nullable', 'integer',
                Rule::exists('categories', 'id')->where($this->categoryClause()),
                Rule::requiredIf($type === 'category'),
            ],
            'account_id' => [
                'nullable', 'integer',
                Rule::exists('accounts', 'id')->where($this->scopedClause()),
                Rule::requiredIf($type === 'account'),
            ],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
