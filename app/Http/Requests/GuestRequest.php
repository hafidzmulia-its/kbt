<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $event = $this->route('event');

        return [
            'name' => ['required_without:import_file', 'nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'guest_group_id' => [
                'nullable',
                'integer',
                Rule::exists('guest_groups', 'id')->where(fn ($query) => $query->where('event_id', $event?->id)),
            ],
            'group_name' => ['nullable', 'string', 'max:255'],
            'address_note' => ['nullable', 'string'],
            'max_pax' => ['nullable', 'integer', 'min:1', 'max:10'],
            'status' => ['nullable', 'in:active,inactive'],
            'is_vip' => ['sometimes', 'boolean'],
            'needs_physical_invitation' => ['sometimes', 'boolean'],
            'import_file' => ['nullable', 'file', 'mimes:csv,txt,xlsx'],
        ];
    }
}
