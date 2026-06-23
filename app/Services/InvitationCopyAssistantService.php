<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class InvitationCopyAssistantService
{
    public function __construct(
        private readonly InvitationLanguageService $languageService,
    ) {
    }

    public function generate(array $payload): array
    {
        $language = $this->languageService->resolve($payload['language_variant'] ?? null);

        if ($this->canUseRemoteProvider()) {
            $remote = $this->generateViaRemoteProvider($payload, $language);

            if ($remote !== null) {
                return $remote;
            }
        }

        return $this->generateFallback($payload, $language);
    }

    public function canUseRemoteProvider(): bool
    {
        return filled(env('INVITELY_AI_API_KEY'));
    }

    private function generateViaRemoteProvider(array $payload, array $language): ?array
    {
        $baseUrl = rtrim((string) env('INVITELY_AI_BASE_URL', 'https://api.openai.com/v1'), '/');
        $apiKey = (string) env('INVITELY_AI_API_KEY');
        $model = (string) env('INVITELY_AI_MODEL', 'gpt-4o-mini');

        $prompt = $this->buildPrompt($payload, $language);

        $response = Http::withToken($apiKey)
            ->timeout(25)
            ->post($baseUrl.'/chat/completions', [
                'model' => $model,
                'response_format' => ['type' => 'json_object'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You generate polished digital invitation copy for weddings, graduations, birthdays, seminars, and similar events in JSON. Return keys: opening_text, invitation_text, closing_text, bride_bio, groom_bio, no_gift_message, gift_instructions, broadcast_message_template, ai_style_brief.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        if (! $response->successful()) {
            return null;
        }

        $content = $response->json('choices.0.message.content');

        if (! is_string($content) || $content === '') {
            return null;
        }

        $decoded = json_decode($content, true);

        if (! is_array($decoded)) {
            return null;
        }

        return Arr::only($decoded, [
            'opening_text',
            'invitation_text',
            'closing_text',
            'bride_bio',
            'groom_bio',
            'no_gift_message',
            'gift_instructions',
            'broadcast_message_template',
            'ai_style_brief',
        ]);
    }

    private function generateFallback(array $payload, array $language): array
    {
        $defaults = $language['defaults'];
        $schedule = collect($payload['schedules'] ?? [])->first() ?? [];
        $dateLabel = $this->formatFirstSchedule($schedule, $language['carbon_locale'] ?? 'id');
        $venue = $schedule['venue_name'] ?? 'venue pilihan keluarga';
        $bundleEnabled = (bool) ($payload['bundle_offer_enabled'] ?? false);
        $giftMode = $payload['gift_mode'] ?? 'no_gift';
        $occasion = $this->resolveOccasion($payload['occasion_type'] ?? null);

        $coupleDisplay = $payload['couple_name_display'] ?? trim(($payload['bride_name'] ?? '').' & '.($payload['groom_name'] ?? ''));
        $brideName = $payload['bride_name'] ?? 'Mempelai wanita';
        $groomName = $payload['groom_name'] ?? 'Mempelai pria';

        $bundleSentence = $bundleEnabled
            ? ' Kami juga menyiapkan alur gifting yang menyatu dengan undangan agar pengalaman tamu terasa lebih lengkap.'
            : '';

        return [
            'opening_text' => $defaults['opening_text'],
            'invitation_text' => trim("Dengan penuh sukacita, kami mengundang Anda untuk hadir pada {$occasion['public_label']} {$coupleDisplay} di {$venue} pada {$dateLabel}.{$bundleSentence}"),
            'closing_text' => $defaults['closing_text'],
            'bride_bio' => $this->buildShortBio($brideName, $language['key'], 'bride'),
            'groom_bio' => $this->buildShortBio($groomName, $language['key'], 'groom'),
            'no_gift_message' => $defaults['no_gift_message'],
            'gift_instructions' => $giftMode === 'no_gift' ? ($payload['gift_instructions'] ?? '') : $defaults['gift_instructions'],
            'broadcast_message_template' => $defaults['broadcast_template'],
            'ai_style_brief' => $this->buildStyleBrief($payload, $language, $bundleEnabled),
        ];
    }

    private function buildShortBio(string $name, string $languageKey, string $role): string
    {
        return match ($languageKey) {
            'en' => $role === 'bride'
                ? "{$name} is presented with a graceful, calm, and heartfelt narrative that highlights warmth and elegance."
                : "{$name} is introduced with a composed, welcoming, and sincere tone that fits the overall event atmosphere.",
            'jv' => $role === 'bride'
                ? "{$name} dipun aturaken kanthi narasi alus, ayem, lan kebak raos syukur."
                : "{$name} dipun aturaken kanthi swasana santun, anget, lan nggambaraken suka bingah adicara.",
            'id_friendly' => $role === 'bride'
                ? "{$name} diperkenalkan dengan nuansa hangat, lembut, dan dekat agar tamu merasa lebih akrab."
                : "{$name} ditampilkan dengan tone santai, ramah, dan tetap rapi untuk memperkuat suasana undangan.",
            default => $role === 'bride'
                ? "{$name} ditampilkan dengan narasi yang anggun, hangat, dan selaras dengan suasana pernikahan."
                : "{$name} diperkenalkan dengan tone yang tenang, sopan, dan menyatu dengan keseluruhan komposisi undangan.",
        };
    }

    private function buildStyleBrief(array $payload, array $language, bool $bundleEnabled): string
    {
        $variantLabel = $language['label'];
        $schedule = collect($payload['schedules'] ?? [])->first() ?? [];
        $venue = $schedule['venue_name'] ?? 'venue acara';
        $occasion = $this->resolveOccasion($payload['occasion_type'] ?? null);
        $bundleNote = $bundleEnabled
            ? ' Tambahkan penekanan visual ringan pada area gifting agar bundling event + hadiah terasa jelas tanpa mengganggu kesan elegan.'
            : '';

        return "Gunakan arah visual premium NechCode Invitely untuk {$occasion['label']}: headline besar, komposisi tenang, warna biru gelap dengan aksen cyan lembut, dan ruang baca mobile-first. Bahasa utama yang dipilih adalah {$variantLabel}, sehingga copy dan label publik perlu menjaga konsistensi tone pada seluruh section. Venue referensi: {$venue}.{$bundleNote}";
    }

    private function formatFirstSchedule(array $schedule, string $locale): string
    {
        if (blank($schedule['date'] ?? null)) {
            return match ($locale) {
                'en' => 'the selected date',
                default => 'tanggal yang telah dipilih',
            };
        }

        return Carbon::parse($schedule['date'])
            ->locale($locale)
            ->translatedFormat('d F Y');
    }

    private function buildPrompt(array $payload, array $language): string
    {
        return json_encode([
            'language' => $language['label'],
            'couple_name_display' => $payload['couple_name_display'] ?? null,
            'bride_name' => $payload['bride_name'] ?? null,
            'groom_name' => $payload['groom_name'] ?? null,
            'occasion_type' => $payload['occasion_type'] ?? 'wedding',
            'title' => $payload['title'] ?? null,
            'schedules' => $payload['schedules'] ?? [],
            'gift_mode' => $payload['gift_mode'] ?? 'no_gift',
            'bundle_offer_enabled' => (bool) ($payload['bundle_offer_enabled'] ?? false),
            'instructions' => 'Keep the output mobile-friendly, elegant, concise, and suitable for a digital event invitation. The design brief must stay textual, not code.',
        ], JSON_PRETTY_PRINT);
    }

    private function resolveOccasion(?string $occasionType): array
    {
        $occasionTypes = config('nechcode.occasion_types', []);
        $default = $occasionTypes['wedding'] ?? [
            'label' => 'Wedding',
            'public_label' => 'undangan acara',
        ];

        return $occasionTypes[$occasionType] ?? $default;
    }
}
