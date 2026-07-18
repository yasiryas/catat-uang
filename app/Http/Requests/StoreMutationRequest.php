<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMutationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period_id' => ['required', 'exists:periods,id'],
            'from_account_id' => ['nullable', 'exists:accounts,id'],
            'to_account_id' => ['nullable', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date'],
        ];
    }
}
