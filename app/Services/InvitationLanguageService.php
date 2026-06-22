<?php

namespace App\Services;

use App\Models\Event;

class InvitationLanguageService
{
    public function variants(): array
    {
        return config('invitation_languages.variants', []);
    }

    public function optionMap(): array
    {
        return collect($this->variants())
            ->mapWithKeys(fn (array $variant, string $key) => [$key => $variant['label']])
            ->all();
    }

    public function resolveForEvent(Event $event): array
    {
        return $this->resolve($event->settings_json['experience']['language_variant'] ?? null);
    }

    public function resolve(?string $variantKey = null): array
    {
        $variants = $this->variants();
        $key = $variantKey && isset($variants[$variantKey])
            ? $variantKey
            : config('invitation_languages.default', 'id_formal');

        $variant = $variants[$key] ?? $variants[config('invitation_languages.default', 'id_formal')];

        return [
            'key' => $key,
            ...$variant,
        ];
    }
}
