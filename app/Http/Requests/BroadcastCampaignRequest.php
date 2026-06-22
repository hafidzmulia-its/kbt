<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BroadcastCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $event = $this->route('event');

        return [
            'name' => ['required', 'string', 'max:255'],
            'broadcast_template_id' => ['nullable', 'integer', Rule::exists('broadcast_templates', 'id')->where(function ($query) use ($event) {
                $query->where(function ($nested) use ($event) {
                    $nested->where('user_id', $this->user()?->id)
                        ->where(function ($scopeQuery) use ($event) {
                            $scopeQuery->whereNull('event_id')->orWhere('event_id', $event?->id);
                        });
                });
            })],
            'message_template' => ['required', 'string'],
            'country_code' => ['nullable', 'string', 'max:8'],
            'delay' => ['nullable', 'string', 'max:20'],
            'connect_only' => ['sometimes', 'boolean'],
            'scheduled_at' => ['nullable', 'date'],
            'guest_group_id' => ['nullable', 'integer', Rule::exists('guest_groups', 'id')->where(fn ($query) => $query->where('event_id', $event?->id))],
            'guest_status' => ['nullable', Rule::in(['active', 'inactive'])],
            'rsvp_status' => ['nullable', Rule::in(['hadir', 'tidak_hadir', 'ragu', 'pending'])],
            'opened_state' => ['nullable', Rule::in(['all', 'opened', 'not_opened'])],
            'vip_only' => ['sometimes', 'boolean'],
            'physical_only' => ['sometimes', 'boolean'],
        ];
    }
}
