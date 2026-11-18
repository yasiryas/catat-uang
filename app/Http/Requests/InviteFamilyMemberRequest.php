<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteFamilyMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        ];
    }
}
