<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period_id' => ['required', 'integer', Rule::exists('periods', 'id')->where($this->scopedClause())],
            'category_id' => ['required', 'exists:categories,id'],
            'account_id' => ['nullable', 'integer', Rule::exists('accounts', 'id')->where($this->scopedClause())],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date'],
            'receipt_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
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