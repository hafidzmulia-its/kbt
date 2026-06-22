<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\FonnteSettingsRequest;
use App\Http\Requests\FonnteTestMessageRequest;
use App\Services\FonnteIntegrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class FonnteIntegrationController extends Controller
{
    public function __construct(private readonly FonnteIntegrationService $fonnteIntegrationService)
    {
    }

    public function show(): View
    {
        $integration = $this->fonnteIntegrationService->getOrCreateFor(request()->user());
        $devices = [];

        try {
            if ($integration->account_token) {
                $devices = $this->fonnteIntegrationService->accountDevices($integration);
            }
        } catch (RuntimeException) {
        }

        return view('dashboard.fonnte.index', [
            'integration' => $integration,
            'devices' => $devices,
        ]);
    }

    public function update(FonnteSettingsRequest $request): RedirectResponse
    {
        $this->fonnteIntegrationService->save(request()->user(), [
            ...$request->validated(),
            'is_enabled' => $request->boolean('is_enabled'),
        ]);

        return back()->with('status', 'Pengaturan Fonnte disimpan.');
    }

    public function refresh(): RedirectResponse
    {
        try {
            $integration = $this->fonnteIntegrationService->getOrCreateFor(request()->user());
            $this->fonnteIntegrationService->verifyDevice($integration);

            return back()->with('status', 'Status device berhasil diperbarui.');
        } catch (RuntimeException $exception) {
            return back()->withErrors(['fonnte' => $exception->getMessage()]);
        }
    }

    public function selectDevice(): RedirectResponse
    {
        $integration = $this->fonnteIntegrationService->getOrCreateFor(request()->user());

        try {
            $devices = collect($this->fonnteIntegrationService->accountDevices($integration));
            $device = $devices->firstWhere('token', request('device_token'));
            abort_unless($device, 404);

            $this->fonnteIntegrationService->selectDevice($integration, $device);
            $this->fonnteIntegrationService->verifyDevice($integration->fresh());

            return back()->with('status', 'Device Fonnte berhasil dipilih.');
        } catch (RuntimeException $exception) {
            return back()->withErrors(['fonnte' => $exception->getMessage()]);
        }
    }

    public function testSend(FonnteTestMessageRequest $request): RedirectResponse
    {
        try {
            $integration = $this->fonnteIntegrationService->getOrCreateFor(request()->user());
            $result = $this->fonnteIntegrationService->testSend(
                $integration,
                $request->string('phone')->toString(),
                $request->string('message')->toString()
            );

            if (! $result['ok']) {
                return back()->withErrors(['fonnte' => $result['payload']['reason'] ?? 'Test send gagal.']);
            }

            return back()->with('status', 'Pesan uji berhasil dikirim.');
        } catch (RuntimeException $exception) {
            return back()->withErrors(['fonnte' => $exception->getMessage()]);
        }
    }
}
