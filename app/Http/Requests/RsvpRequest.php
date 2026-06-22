<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPersonalRoute = $this->route('guestToken') !== null;

        return [
            'status' => ['required', Rule::in(['hadir', 'tidak_hadir', 'ragu', 'pending'])],
            'pax_count' => ['required', 'integer', 'min:1', 'max:10'],
            'message' => ['nullable', 'string', 'max:1000'],
            'name' => [$isPersonalRoute ? 'nullable' : 'required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'event_schedule_id' => ['nullable', 'integer'],
        ];
    }
}
