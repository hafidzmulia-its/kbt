<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\InteractsWithEventContext;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GiftContribution;
use App\Services\AuditLogService;
use App\Services\CsvExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class GiftDashboardController extends Controller
{
    use InteractsWithEventContext;

    public function __construct(
        private readonly CsvExportService $csvExportService,
        private readonly AuditLogService $auditLogService,
    ) {
    }

    public function __invoke(Event $event): View
    {
        $this->authorizeEventView($event);
        $this->ensureTrackingEnabled($event);

        return view('dashboard.gifts.index', [
            'event' => $event,
            'setting' => $event->giftSetting,
            'contributions' => $event->giftContributions()->with('guest')->latest()->get(),
        ]);
    }

    public function export(Event $event): StreamedResponse
    {
        $this->authorizeEventView($event);
        $this->ensureTrackingEnabled($event);

        $contributions = $event->giftContributions()->with('guest')->latest()->get();

        return $this->csvExportService->download(
            sprintf('gifts-%s-%s.csv', $event->slug, now()->format('Ymd-His')),
            ['guest_name', 'reference_code', 'status', 'amount', 'verified_at', 'notes'],
            $contributions->map(fn (GiftContribution $contribution) => [
                $contribution->guest?->name,
                $contribution->reference_code,
                $contribution->status,
                $contribution->amount,
                optional($contribution->verified_at)->toDateTimeString(),
                $contribution->notes,
            ])
        );
    }

    public function verify(Event $event, GiftContribution $giftContribution): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);
        $this->ensureBelongsToEvent($giftContribution, $event);

        $giftContribution->update([
            'status' => 'verified',
            'verified_by' => request()->user()->id,
            'verified_at' => now(),
        ]);

        $this->auditLogService->log(
            'user',
            request()->user()->id,
            $event,
            'gift.verified',
            GiftContribution::class,
            $giftContribution->id
        );

        return back()->with('status', 'Gift berhasil diverifikasi.');
    }

    public function reject(Event $event, GiftContribution $giftContribution): RedirectResponse
    {
        $this->authorizeEventUpdate($event);
        $this->ensureTrackingEnabled($event);
        $this->ensureBelongsToEvent($giftContribution, $event);

        $giftContribution->update([
            'status' => 'rejected',
            'verified_by' => request()->user()->id,
            'verified_at' => now(),
        ]);

        $this->auditLogService->log(
            'user',
            request()->user()->id,
            $event,
            'gift.rejected',
            GiftContribution::class,
            $giftContribution->id
        );

        return back()->with('status', 'Gift ditandai ditolak.');
    }

    public function downloadProof(Event $event, GiftContribution $giftContribution): StreamedResponse|BinaryFileResponse
    {
        $this->authorizeEventView($event);
        $this->ensureTrackingEnabled($event);
        $this->ensureBelongsToEvent($giftContribution, $event);
        abort_unless($giftContribution->proof_file_path, 404);

        return Storage::disk('local')->download(
            $giftContribution->proof_file_path,
            basename($giftContribution->proof_file_path)
        );
    }
}
