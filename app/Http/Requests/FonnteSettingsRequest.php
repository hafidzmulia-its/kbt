<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FonnteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_token' => ['nullable', 'string'],
            'device_token' => ['nullable', 'string'],
            'default_country_code' => ['required', 'string', 'max:8'],
            'is_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
