<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuestBulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $event = $this->route('event');

        return [
            'guest_ids' => ['required', 'array', 'min:1'],
            'guest_ids.*' => ['integer'],
            'action' => ['required', Rule::in([
                'assign_group',
                'clear_group',
                'mark_vip',
                'unmark_vip',
                'require_physical',
                'clear_physical',
                'archive',
                'restore',
                'activate',
                'deactivate',
                'regenerate_tokens',
            ])],
            'guest_group_id' => [
                Rule::requiredIf($this->input('action') === 'assign_group'),
                'nullable',
                'integer',
                Rule::exists('guest_groups', 'id')->where(fn ($query) => $query->where('event_id', $event?->id)),
            ],
        ];
    }
}
