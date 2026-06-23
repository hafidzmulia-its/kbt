<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvitationAssistantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'couple_name_display' => ['required', 'string', 'max:255'],
            'bride_name' => ['required', 'string', 'max:255'],
            'groom_name' => ['required', 'string', 'max:255'],
            'occasion_type' => ['nullable', Rule::in(array_keys(config('nechcode.occasion_types', [])))],
            'language_variant' => ['nullable', Rule::in(array_keys(config('invitation_languages.variants', [])))],
            'gift_mode' => ['nullable', Rule::in(['no_gift', 'bank_transfer', 'guest_specific_qr', 'qris_gateway'])],
            'bundle_offer_enabled' => ['sometimes', 'boolean'],
            'schedules' => ['nullable', 'array'],
            'schedules.*.date' => ['nullable', 'date'],
            'schedules.*.venue_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
