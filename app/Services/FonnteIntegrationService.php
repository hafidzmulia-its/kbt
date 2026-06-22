<?php

namespace App\Services;

use App\Models\FonnteIntegration;
use App\Models\User;
use RuntimeException;

class FonnteIntegrationService
{
    public function __construct(private readonly FonnteClient $fonnteClient)
    {
    }

    public function getOrCreateFor(User $user): FonnteIntegration
    {
        return $user->fonnteIntegration()->firstOrCreate(
            ['user_id' => $user->id],
            ['default_country_code' => '62']
        );
    }

    public function save(User $user, array $data): FonnteIntegration
    {
        $integration = $this->getOrCreateFor($user);

        $integration->fill([
            'account_token' => $data['account_token'] ?: $integration->account_token,
            'device_token' => $data['device_token'] ?: $integration->device_token,
            'device_token_last4' => filled($data['device_token'] ?? null) ? substr($data['device_token'], -4) : $integration->device_token_last4,
            'default_country_code' => $data['default_country_code'],
            'is_enabled' => (bool) $data['is_enabled'],
        ])->save();

        return $integration->fresh();
    }

    public function verifyDevice(FonnteIntegration $integration): FonnteIntegration
    {
        if (! filled($integration->device_token)) {
            throw new RuntimeException('Device token belum disimpan.');
        }

        $result = $this->fonnteClient->deviceProfile($integration->device_token);

        if (! $result['ok']) {
            $integration->update([
                'device_status' => 'invalid',
                'last_error_message' => $result['payload']['reason'] ?? 'Unable to verify device',
                'verified_at' => now(),
            ]);

            return $integration->fresh();
        }

        $payload = $result['payload'];
        $integration->update([
            'device_name' => $payload['name'] ?? null,
            'device_number' => $payload['device'] ?? null,
            'device_status' => $payload['device_status'] ?? 'unknown',
            'package_name' => $payload['package'] ?? null,
            'quota' => isset($payload['quota']) ? (int) $payload['quota'] : null,
            'expires_label' => $payload['expired'] ?? null,
            'verified_at' => now(),
            'last_error_message' => null,
        ]);

        return $integration->fresh();
    }

    public function accountDevices(FonnteIntegration $integration): array
    {
        if (! filled($integration->account_token)) {
            throw new RuntimeException('Account token belum disimpan.');
        }

        $result = $this->fonnteClient->accountDevices($integration->account_token);

        if (! $result['ok']) {
            throw new RuntimeException($result['payload']['reason'] ?? 'Gagal mengambil daftar device.');
        }

        return $result['payload']['data'] ?? [];
    }

    public function selectDevice(FonnteIntegration $integration, array $device): FonnteIntegration
    {
        $integration->update([
            'device_token' => $device['token'],
            'device_token_last4' => substr($device['token'], -4),
            'device_name' => $device['name'] ?? null,
            'device_number' => $device['device'] ?? null,
            'device_status' => $device['status'] ?? 'unknown',
            'package_name' => $device['package'] ?? null,
            'quota' => isset($device['quota']) ? (int) $device['quota'] : null,
            'expires_label' => $device['expired'] ?? null,
            'verified_at' => now(),
            'last_error_message' => null,
        ]);

        return $integration->fresh();
    }

    public function testSend(FonnteIntegration $integration, string $target, string $message): array
    {
        if (! $integration->hasUsableDeviceToken()) {
            throw new RuntimeException('Integrasi Fonnte belum aktif atau device token belum tersedia.');
        }

        return $this->fonnteClient->send($integration->device_token, $target, $message, [
            'countryCode' => $integration->default_country_code,
            'connectOnly' => true,
            'preview' => true,
        ]);
    }
}
