<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteClient
{
    public function send(string $token, string $phone, string $message, array $options = []): array
    {
        $response = Http::asForm()
            ->withHeaders(['Authorization' => $token])
            ->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => $options['countryCode'] ?? '62',
                'delay' => $options['delay'] ?? null,
                'connectOnly' => $options['connectOnly'] ?? true,
                'preview' => $options['preview'] ?? true,
            ]);

        return [
            'ok' => $response->successful(),
            'payload' => $response->json(),
            'status' => $response->status(),
        ];
    }

    public function deviceProfile(string $deviceToken): array
    {
        $response = Http::withHeaders(['Authorization' => $deviceToken])
            ->post('https://api.fonnte.com/device');

        return [
            'ok' => (bool) ($response->json('status') ?? false),
            'payload' => $response->json(),
            'status' => $response->status(),
        ];
    }

    public function accountDevices(string $accountToken): array
    {
        $response = Http::withHeaders(['Authorization' => $accountToken])
            ->post('https://api.fonnte.com/get-devices');

        return [
            'ok' => (bool) ($response->json('status') ?? false),
            'payload' => $response->json(),
            'status' => $response->status(),
        ];
    }
}
