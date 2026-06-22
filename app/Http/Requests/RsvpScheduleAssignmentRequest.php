<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RsvpScheduleAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assignments' => ['nullable', 'array'],
            'assignments.*' => ['nullable', 'array'],
            'assignments.*.*' => ['integer'],
        ];
    }
}
