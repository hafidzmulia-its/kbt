<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->route('event')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('events', 'slug')->ignore($eventId)],
            'couple_name_display' => ['required', 'string', 'max:255'],
            'bride_name' => ['required', 'string', 'max:255'],
            'groom_name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'template_id' => ['nullable', 'exists:templates,id'],
            'music_asset_id' => ['nullable', 'exists:music_assets,id'],
            'is_rsvp_enabled' => ['sometimes', 'boolean'],
            'is_comment_enabled' => ['sometimes', 'boolean'],
            'is_gift_enabled' => ['sometimes', 'boolean'],
            'is_guest_personalization_enabled' => ['sometimes', 'boolean'],
            'wants_broadcast_addon' => ['sometimes', 'boolean'],
            'wants_custom_design_addon' => ['sometimes', 'boolean'],
            'bundle_offer_enabled' => ['sometimes', 'boolean'],
            'occasion_type' => ['nullable', Rule::in(array_keys(config('nechcode.occasion_types', [])))],
            'language_variant' => ['nullable', Rule::in(array_keys(config('invitation_languages.variants', [])))],
            'ai_style_brief' => ['nullable', 'string'],
            'broadcast_message_template_seed' => ['nullable', 'string'],
            'opening_text' => ['nullable', 'string'],
            'invitation_text' => ['nullable', 'string'],
            'closing_text' => ['nullable', 'string'],
            'bride_bio' => ['nullable', 'string'],
            'groom_bio' => ['nullable', 'string'],
            'no_gift_message' => ['nullable', 'string'],
            'schedules' => ['required', 'array', 'min:1'],
            'schedules.*.id' => [
                'nullable',
                'integer',
                Rule::exists('event_schedules', 'id')->where(
                    fn ($query) => $query->where('event_id', $eventId ?? 0)
                ),
            ],
            'schedules.*.label' => ['required', 'string', 'max:50'],
            'schedules.*.date' => ['required', 'date'],
            'schedules.*.start_time' => ['nullable', 'date_format:H:i'],
            'schedules.*.end_time' => ['nullable', 'date_format:H:i'],
            'schedules.*.timezone' => ['required', 'string', 'max:80'],
            'schedules.*.venue_name' => ['required', 'string', 'max:255'],
            'schedules.*.address' => ['nullable', 'string'],
            'schedules.*.maps_url' => ['nullable', 'url'],
            'schedules.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'schedules.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'album_photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'gift_mode' => ['nullable', Rule::in(['no_gift', 'bank_transfer', 'guest_specific_qr', 'qris_gateway'])],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'account_holder' => ['nullable', 'string', 'max:255'],
            'gift_instructions' => ['nullable', 'string'],
            'is_proof_upload_enabled' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $baseSlug = Str::slug((string) ($this->input('slug') ?: $this->input('title')));

        if ($baseSlug === '') {
            $baseSlug = 'event';
        }

        $eventId = $this->route('event')?->id;
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Event::query()
                ->when($eventId, fn ($query) => $query->whereKeyNot($eventId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        $schedules = collect($this->input('schedules', []))
            ->map(function (array $schedule): array {
                foreach (['start_time', 'end_time'] as $field) {
                    $value = $schedule[$field] ?? null;

                    if (! is_string($value) || $value === '') {
                        $schedule[$field] = $value;
                        continue;
                    }

                    $schedule[$field] = preg_match('/^\d{2}:\d{2}:\d{2}$/', $value) === 1
                        ? substr($value, 0, 5)
                        : $value;
                }

                foreach (['latitude', 'longitude'] as $field) {
                    $value = $schedule[$field] ?? null;
                    $schedule[$field] = $value === '' ? null : $value;
                }

                return $schedule;
            })
            ->values()
            ->all();

        $this->merge([
            'slug' => $slug,
            'schedules' => $schedules,
        ]);
    }
}
