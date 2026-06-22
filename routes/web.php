<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MusicAssetController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Public\CommentController as PublicCommentController;
use App\Http\Controllers\Public\GiftController as PublicGiftController;
use App\Http\Controllers\Public\InvitationController;
use App\Http\Controllers\Public\RsvpController as PublicRsvpController;
use App\Http\Controllers\Staff\CheckinController;
use App\Http\Controllers\User\AttendanceDashboardController;
use App\Http\Controllers\User\BroadcastCampaignController;
use App\Http\Controllers\User\CommentModerationController;
use App\Http\Controllers\User\EventController;
use App\Http\Controllers\User\FonnteIntegrationController;
use App\Http\Controllers\User\GiftDashboardController;
use App\Http\Controllers\User\GuestController;
use App\Http\Controllers\User\InvitationAssistantController;
use App\Http\Controllers\User\InvitationPreviewController;
use App\Http\Controllers\User\RsvpDashboardController;
use App\Http\Controllers\User\StaffAccessLinkController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('inv')->name('public.')->middleware('throttle:public-invitation')->group(function () {
    Route::get('/{event}', [InvitationController::class, 'showGeneral'])->name('invitation.general');
    Route::get('/{event}/g/{guestToken}', [InvitationController::class, 'showPersonal'])->name('invitation.personal');
    Route::post('/{event}/rsvp', [PublicRsvpController::class, 'storeGeneral'])->middleware('throttle:public-interaction')->name('rsvp.general');
    Route::post('/{event}/g/{guestToken}/rsvp', [PublicRsvpController::class, 'storePersonal'])->middleware('throttle:public-interaction')->name('rsvp.personal');
    Route::get('/{event}/g/{guestToken}/gift', [PublicGiftController::class, 'show'])->name('gift.show');
    Route::post('/{event}/g/{guestToken}/gift/proof', [PublicGiftController::class, 'uploadProof'])->middleware('throttle:public-interaction')->name('gift.upload');
    Route::post('/{event}/comment', [PublicCommentController::class, 'store'])->middleware('throttle:public-interaction')->name('comment.general');
    Route::post('/{event}/g/{guestToken}/comment', [PublicCommentController::class, 'store'])->middleware('throttle:public-interaction')->name('comment.personal');
});

Route::prefix('staff/checkin')->name('staff.checkin.')->middleware('throttle:public-interaction')->group(function () {
    Route::get('/{staffToken}', [CheckinController::class, 'scanner'])->name('scanner');
    Route::get('/{staffToken}/search', [CheckinController::class, 'search'])->name('search');
    Route::post('/{staffToken}/scan', [CheckinController::class, 'scan'])->name('scan');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::resource('events', EventController::class)->except(['show']);
        Route::get('events/{event}/workspace', [EventController::class, 'workspace'])->name('events.workspace');
        Route::match(['post', 'put'], 'events/copy-assistant', [InvitationAssistantController::class, 'generate'])->name('events.copy-assistant');
        Route::get('fonnte', [FonnteIntegrationController::class, 'show'])->name('fonnte.show');
        Route::put('fonnte', [FonnteIntegrationController::class, 'update'])->name('fonnte.update');
        Route::post('fonnte/refresh', [FonnteIntegrationController::class, 'refresh'])->name('fonnte.refresh');
        Route::post('fonnte/select-device', [FonnteIntegrationController::class, 'selectDevice'])->name('fonnte.select-device');
        Route::post('fonnte/test-send', [FonnteIntegrationController::class, 'testSend'])->name('fonnte.test-send');
        Route::get('events/{event}/preview', [InvitationPreviewController::class, 'general'])->name('events.preview');
        Route::get('events/{event}/preview/personal', [InvitationPreviewController::class, 'personal'])->name('events.preview.personal');
        Route::get('events/{event}/guests', [GuestController::class, 'index'])->name('guests.index');
        Route::get('events/{event}/guests/export', [GuestController::class, 'export'])->name('guests.export');
        Route::post('events/{event}/guests/groups', [GuestController::class, 'storeGroup'])->name('guests.groups.store');
        Route::post('events/{event}/guests/import-preview', [GuestController::class, 'previewImport'])->name('guests.import-preview');
        Route::post('events/{event}/guests/import-commit', [GuestController::class, 'commitImport'])->name('guests.import-commit');
        Route::delete('events/{event}/guests/import-preview', [GuestController::class, 'clearImportPreview'])->name('guests.import-preview.clear');
        Route::post('events/{event}/guests/bulk', [GuestController::class, 'bulkUpdate'])->name('guests.bulk');
        Route::post('events/{event}/guests', [GuestController::class, 'store'])->name('guests.store');
        Route::put('events/{event}/guests/{guest}', [GuestController::class, 'update'])->name('guests.update');
        Route::delete('events/{event}/guests/{guest}', [GuestController::class, 'destroy'])->name('guests.destroy');
        Route::patch('events/{event}/guests/{guest}/restore', [GuestController::class, 'restore'])->name('guests.restore');
        Route::post('events/{event}/guests/{guest}/regenerate-token', [GuestController::class, 'regenerateToken'])->name('guests.regenerate-token');
        Route::get('events/{event}/rsvps', RsvpDashboardController::class)->name('rsvps.index');
        Route::get('events/{event}/rsvps/export', [RsvpDashboardController::class, 'export'])->name('rsvps.export');
        Route::post('events/{event}/rsvps/assignments', [RsvpDashboardController::class, 'updateAssignments'])->name('rsvps.assignments');
        Route::get('events/{event}/attendance', AttendanceDashboardController::class)->name('attendance.index');
        Route::get('events/{event}/attendance/export', [AttendanceDashboardController::class, 'export'])->name('attendance.export');
        Route::get('events/{event}/gifts', GiftDashboardController::class)->name('gifts.index');
        Route::get('events/{event}/gifts/export', [GiftDashboardController::class, 'export'])->name('gifts.export');
        Route::get('events/{event}/gifts/{giftContribution}/proof', [GiftDashboardController::class, 'downloadProof'])->name('gifts.proof');
        Route::patch('events/{event}/gifts/{giftContribution}/verify', [GiftDashboardController::class, 'verify'])->name('gifts.verify');
        Route::patch('events/{event}/gifts/{giftContribution}/reject', [GiftDashboardController::class, 'reject'])->name('gifts.reject');
        Route::get('events/{event}/broadcasts', [BroadcastCampaignController::class, 'index'])->name('broadcasts.index');
        Route::post('events/{event}/broadcasts/preview', [BroadcastCampaignController::class, 'preview'])->name('broadcasts.preview');
        Route::post('events/{event}/broadcasts/templates', [BroadcastCampaignController::class, 'storeTemplate'])->name('broadcasts.templates.store');
        Route::post('events/{event}/broadcasts', [BroadcastCampaignController::class, 'store'])->name('broadcasts.store');
        Route::post('events/{event}/broadcasts/{broadcastCampaign}/retry-failed', [BroadcastCampaignController::class, 'retryFailed'])->name('broadcasts.retry-failed');
        Route::patch('events/{event}/broadcasts/{broadcastCampaign}/cancel', [BroadcastCampaignController::class, 'cancel'])->name('broadcasts.cancel');
        Route::get('events/{event}/comments', [CommentModerationController::class, 'index'])->name('comments.index');
        Route::patch('events/{event}/comments/{comment}', [CommentModerationController::class, 'update'])->name('comments.update');
        Route::post('events/{event}/staff-links', [StaffAccessLinkController::class, 'store'])->name('staff-links.store');
        Route::patch('events/{event}/staff-links/{staffAccessLink}/revoke', [StaffAccessLinkController::class, 'revoke'])->name('staff-links.revoke');
    });
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
    Route::put('/templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
    Route::get('/music-assets', [MusicAssetController::class, 'index'])->name('music.index');
    Route::post('/music-assets', [MusicAssetController::class, 'store'])->name('music.store');
    Route::put('/music-assets/{musicAsset}', [MusicAssetController::class, 'update'])->name('music.update');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
});
