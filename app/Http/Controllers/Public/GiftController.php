<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\GiftProofUploadRequest;
use App\Models\Event;
use App\Services\AuditLogService;
use App\Services\EventFeatureService;
use App\Services\GiftService;
use App\Services\TokenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GiftController extends Controller
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly GiftService $giftService,
        private readonly EventFeatureService $eventFeatureService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function show(Event $event, string $guestToken): View
    {
        abort_unless($event->is_gift_enabled, 404);
        abort_unless($this->eventFeatureService->trackingEnabled($event), 404);

        $invitation = $this->tokenService->resolveInvitationByPublicToken($guestToken);
        abort_unless($invitation && $invitation->event_id === $event->id, 404);

        $gift = $this->giftService->intentFor($event, $invitation->guest, $invitation);

        return view('invitation.gift', [
            'event' => $event->load('giftSetting'),
            'guest' => $invitation->guest,
            'invitation' => $invitation,
            'gift' => $gift,
        ]);
    }

    public function uploadProof(GiftProofUploadRequest $request, Event $event, string $guestToken): RedirectResponse
    {
        abort_unless($event->is_gift_enabled, 404);
        abort_unless($this->eventFeatureService->trackingEnabled($event), 404);

        $invitation = $this->tokenService->resolveInvitationByPublicToken($guestToken);
        abort_unless($invitation && $invitation->event_id === $event->id, 404);

        $gift = $this->giftService->intentFor($event, $invitation->guest, $invitation);
        $path = Storage::disk('local')->putFile('gift-proofs', $request->file('proof'));
        abort_unless(is_string($path) && $path !== '', 500, 'Gagal menyimpan bukti transfer.');

        $gift->update([
            'amount' => $request->integer('amount'),
            'notes' => $request->input('notes'),
            'proof_file_path' => $path,
            'status' => 'proof_uploaded',
        ]);

        $this->auditLogService->log('guest', null, $event, 'gift.proof_uploaded', get_class($gift), $gift->id);

        return back()->with('status', 'Bukti transfer berhasil diunggah.');
    }
}
