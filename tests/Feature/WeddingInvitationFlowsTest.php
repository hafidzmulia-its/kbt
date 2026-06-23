<?php

namespace Tests\Feature;

use App\Models\BroadcastCampaign;
use App\Models\Event;
use App\Models\FonnteIntegration;
use App\Models\GiftContribution;
use App\Models\Guest;
use App\Models\StaffAccessLink;
use App\Models\Template;
use App\Models\User;
use App\Services\FonnteMessageRenderer;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WeddingInvitationFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_another_users_event(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $event = $this->createEvent($owner);

        $response = $this->actingAs($intruder)->get(route('dashboard.events.edit', $event));

        $response->assertForbidden();
    }

    public function test_landing_page_is_accessible(): void
    {
        $this->get(route('welcome'))
            ->assertOk()
            ->assertSee('Invitely by NechCode')
            ->assertSee('Automation')
            ->assertSee('kirim undangan');
    }

    public function test_public_invitation_uses_selected_language_variant(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner, [
            'settings_json' => [
                'experience' => [
                    'language_variant' => 'en',
                ],
            ],
        ]);

        $this->get(route('public.invitation.general', $event))
            ->assertOk()
            ->assertSee('General Invitation')
            ->assertSee('Please confirm your attendance.');
    }

    public function test_draft_event_can_be_previewed_by_owner(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner, [
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard.events.preview', $event))
            ->assertOk()
            ->assertSee($event->couple_name_display);
    }

    public function test_public_invitation_token_resolves_correct_guest(): void
    {
        [$event, $guest, $token] = $this->createPersonalInvitation();

        $response = $this->get(route('public.invitation.personal', [$event, 'guestToken' => $token]));

        $response->assertOk();
        $response->assertSee($guest->name);
    }

    public function test_invalid_token_fails_safely(): void
    {
        [$event] = $this->createPersonalInvitation();

        $response = $this->get(route('public.invitation.personal', [$event, 'guestToken' => 'invalid-token']));

        $response->assertNotFound();
    }

    public function test_rsvp_submit_via_personal_link_associates_correct_guest(): void
    {
        [$event, $guest, $token, $invitation] = $this->createPersonalInvitation();

        $this->post(route('public.rsvp.personal', [$event, 'guestToken' => $token]), [
            'status' => 'hadir',
            'pax_count' => 2,
            'message' => 'Sampai jumpa',
        ])->assertRedirect();

        $this->assertDatabaseHas('rsvps', [
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'guest_invitation_id' => $invitation->id,
            'status' => 'hadir',
        ]);
    }

    public function test_personal_comment_submit_uses_guest_identity_without_manual_name(): void
    {
        [$event, $guest, $token] = $this->createPersonalInvitation();

        $this->post(route('public.comment.personal', [$event, 'guestToken' => $token]), [
            'message' => '<b>Selamat ya</b> semoga lancar.',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'name_snapshot' => $guest->name,
            'message' => 'Selamat ya semoga lancar.',
            'status' => 'pending',
        ]);
    }

    public function test_general_invitation_does_not_leak_guest_data(): void
    {
        [$event, $guest] = $this->createPersonalInvitation();

        $response = $this->get(route('public.invitation.general', $event));

        $response->assertOk();
        $response->assertDontSee($guest->name);
        $response->assertDontSee((string) $guest->phone);
    }

    public function test_gift_proof_upload_validates_file_and_links_to_correct_guest(): void
    {
        Storage::fake('local');
        [$event, $guest, $token] = $this->createPersonalInvitation();
        $event->giftSetting()->create([
            'mode' => 'bank_transfer',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'NechCode',
            'is_proof_upload_enabled' => true,
        ]);

        $this->post(route('public.gift.upload', [$event, 'guestToken' => $token]), [
            'amount' => 500000,
            'notes' => 'Transfer hadiah',
            'proof' => UploadedFile::fake()->create('proof.pdf', 120, 'application/pdf'),
        ])->assertRedirect();

        $contribution = GiftContribution::query()->where('guest_id', $guest->id)->first();

        $this->assertNotNull($contribution);
        $this->assertSame('proof_uploaded', $contribution->status);
        Storage::disk('local')->assertExists($contribution->proof_file_path);
    }

    public function test_tracking_dashboard_requires_tracking_addon(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner, [
            'is_rsvp_enabled' => false,
            'is_gift_enabled' => false,
            'is_guest_personalization_enabled' => false,
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard.guests.index', $event))
            ->assertForbidden();
    }

    public function test_broadcast_dashboard_requires_broadcast_addon(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner);

        $this->actingAs($owner)
            ->get(route('dashboard.broadcasts.index', $event))
            ->assertForbidden();
    }

    public function test_duplicate_checkin_does_not_double_count(): void
    {
        [$event, $guest, $token, $invitation] = $this->createPersonalInvitation();
        $tokenService = app(TokenService::class);
        [$staffLink, $staffToken] = $tokenService->createStaffLink($event);

        $payload = $invitation->checkin_url_cached;

        $first = $this->postJson(route('staff.checkin.scan', ['staffToken' => $staffToken]), ['payload' => $payload]);
        $second = $this->postJson(route('staff.checkin.scan', ['staffToken' => $staffToken]), ['payload' => $payload]);

        $first->assertOk()->assertJson(['status' => 'checked_in']);
        $second->assertOk()->assertJson(['status' => 'duplicate']);
        $this->assertDatabaseCount('attendance_checkins', 1);
    }

    public function test_staff_link_revoke_blocks_scanning(): void
    {
        [$event, , , $invitation] = $this->createPersonalInvitation();
        $staff = StaffAccessLink::create([
            'event_id' => $event->id,
            'token_hash' => hash('sha256', 'revoked-token'),
            'label' => 'Staff',
            'revoked_at' => now(),
        ]);

        $this->postJson(route('staff.checkin.scan', ['staffToken' => 'revoked-token']), [
            'payload' => $invitation->checkin_url_cached,
        ])->assertForbidden();
    }

    public function test_staff_search_returns_matching_guest(): void
    {
        [$event, $guest] = $this->createPersonalInvitation();
        [, $staffToken] = app(TokenService::class)->createStaffLink($event);

        $this->getJson(route('staff.checkin.search', ['staffToken' => $staffToken, 'q' => 'Andi']))
            ->assertOk()
            ->assertJsonPath('guests.0.name', $guest->name);
    }

    public function test_broadcast_message_renders_unique_invitation_link_per_guest(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner);
        [$guestOne, $invitationOne, $tokenOne] = $this->createGuestInvitationFor($event, 'Guest Satu', '081111111111');
        [$guestTwo, $invitationTwo, $tokenTwo] = $this->createGuestInvitationFor($event, 'Guest Dua', '082222222222');

        $campaign = BroadcastCampaign::create([
            'event_id' => $event->id,
            'user_id' => $owner->id,
            'name' => 'Test Campaign',
            'message_template' => '{{guest_name}} {{invitation_link}}',
            'status' => 'draft',
        ]);

        $renderer = app(FonnteMessageRenderer::class);
        $messageOne = $renderer->render($campaign, $guestOne, $invitationOne);
        $messageTwo = $renderer->render($campaign, $guestTwo, $invitationTwo);

        $this->assertNotSame($messageOne, $messageTwo);
        $this->assertStringContainsString($invitationOne->invitation_url_cached, $messageOne);
        $this->assertStringContainsString($invitationTwo->invitation_url_cached, $messageTwo);
    }

    public function test_user_can_save_fonnte_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('dashboard.fonnte.update'), [
            'account_token' => 'account-token-123',
            'device_token' => 'device-token-4567',
            'default_country_code' => '62',
            'is_enabled' => '1',
        ])->assertRedirect();

        $integration = FonnteIntegration::query()->where('user_id', $user->id)->first();

        $this->assertNotNull($integration);
        $this->assertSame('4567', $integration->device_token_last4);
        $this->assertTrue($integration->is_enabled);
    }

    public function test_user_can_refresh_fonnte_device_status(): void
    {
        Http::fake([
            'https://api.fonnte.com/device' => Http::response([
                'device' => '628123456789',
                'device_status' => 'connect',
                'expired' => '18 November 2029',
                'name' => 'Client Device',
                'package' => 'Reguler',
                'quota' => '78',
                'status' => true,
            ], 200),
        ]);

        $user = User::factory()->create();
        $user->fonnteIntegration()->create([
            'device_token' => 'device-token-4567',
            'device_token_last4' => '4567',
            'is_enabled' => true,
        ]);

        $this->actingAs($user)->post(route('dashboard.fonnte.refresh'))->assertRedirect();

        $integration = $user->fresh()->fonnteIntegration;
        $this->assertSame('connect', $integration->device_status);
        $this->assertSame('Client Device', $integration->device_name);
    }

    public function test_ai_copy_assistant_generates_draft_flash_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboard.events.copy-assistant'), [
            'couple_name_display' => 'Alya & Bima',
            'bride_name' => 'Alya',
            'groom_name' => 'Bima',
            'language_variant' => 'en',
            'bundle_offer_enabled' => '1',
            'gift_mode' => 'bank_transfer',
            'schedules' => [[
                'date' => now()->addMonth()->toDateString(),
                'venue_name' => 'Garden Hall',
            ]],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('assistant_style_brief');
        $response->assertSessionHas('status', 'Draft copy dan style brief berhasil digenerate.');
    }

    public function test_broadcast_queue_requires_active_fonnte_integration(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner, [
            'settings_json' => ['addons' => ['broadcast' => true]],
        ]);

        $response = $this->actingAs($owner)->post(route('dashboard.broadcasts.store', $event), [
            'name' => 'Campaign',
            'message_template' => 'Hello {{guest_name}}',
        ]);

        $response->assertSessionHasErrors('broadcast');
        $this->assertDatabaseCount('broadcast_campaigns', 0);
    }

    public function test_guest_export_returns_csv(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner);
        $this->createGuestInvitationFor($event, 'Guest Satu', '081111111111');

        $response = $this->actingAs($owner)->get(route('dashboard.guests.export', $event));

        $response->assertOk();
        $response->assertDownload();
        $this->assertStringContainsString('Guest Satu', $response->streamedContent());
    }

    public function test_guest_import_preview_and_commit_creates_groups_and_flags(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner);
        $csv = implode(PHP_EOL, [
            'name,phone,group_name,address_note,max_pax,status,is_vip,needs_physical_invitation',
            'Kirana,081200000001,Keluarga Inti,Jakarta,3,active,yes,1',
            'Bram,,Teman Kampus,Bandung,2,inactive,no,0',
        ]);

        $previewResponse = $this->actingAs($owner)->post(route('dashboard.guests.import-preview', $event), [
            'import_file' => UploadedFile::fake()->createWithContent('guests.csv', $csv),
        ]);

        $previewResponse
            ->assertRedirect()
            ->assertSessionHas('guest_import_preview.'.$event->id);

        $this->actingAs($owner)
            ->post(route('dashboard.guests.import-commit', $event))
            ->assertRedirect();

        $this->assertDatabaseHas('guest_groups', [
            'event_id' => $event->id,
            'name' => 'Keluarga Inti',
        ]);

        $this->assertDatabaseHas('guests', [
            'event_id' => $event->id,
            'name' => 'Kirana',
            'phone' => '081200000001',
            'group_name' => 'Keluarga Inti',
            'is_vip' => true,
            'needs_physical_invitation' => true,
        ]);

        $guest = Guest::query()
            ->where('event_id', $event->id)
            ->where('name', 'Kirana')
            ->firstOrFail();

        $this->assertNotNull($guest->guest_group_id);
        $this->assertDatabaseHas('guest_invitations', [
            'event_id' => $event->id,
            'guest_id' => $guest->id,
        ]);
    }

    public function test_bulk_guest_actions_can_assign_group_and_mark_vip(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner);
        [$guestOne] = $this->createGuestInvitationFor($event, 'Guest Satu', '081111111111');
        [$guestTwo] = $this->createGuestInvitationFor($event, 'Guest Dua', '082222222222');
        $group = $event->guestGroups()->create([
            'name' => 'VIP Family',
            'slug' => 'vip-family',
            'description' => 'Tamu keluarga utama',
            'sort_order' => 1,
        ]);

        $this->actingAs($owner)->post(route('dashboard.guests.bulk', $event), [
            'guest_ids' => [$guestOne->id, $guestTwo->id],
            'action' => 'assign_group',
            'guest_group_id' => $group->id,
        ])->assertRedirect();

        $this->actingAs($owner)->post(route('dashboard.guests.bulk', $event), [
            'guest_ids' => [$guestOne->id, $guestTwo->id],
            'action' => 'mark_vip',
        ])->assertRedirect();

        $this->assertDatabaseHas('guests', [
            'id' => $guestOne->id,
            'guest_group_id' => $group->id,
            'group_name' => 'VIP Family',
            'is_vip' => true,
        ]);

        $this->assertDatabaseHas('guests', [
            'id' => $guestTwo->id,
            'guest_group_id' => $group->id,
            'group_name' => 'VIP Family',
            'is_vip' => true,
        ]);
    }

    public function test_archived_guest_can_be_restored_from_dashboard(): void
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner);
        [$guest] = $this->createGuestInvitationFor($event, 'Guest Arsip', '081233344455');

        $guest->delete();
        $this->assertSoftDeleted('guests', ['id' => $guest->id]);

        $this->actingAs($owner)
            ->patch(route('dashboard.guests.restore', [$event, $guest->id]))
            ->assertRedirect();

        $this->assertNotSoftDeleted('guests', ['id' => $guest->id]);
    }

    public function test_personal_rsvp_can_be_scoped_to_allowed_schedule(): void
    {
        [$event, $guest, $token, $invitation] = $this->createPersonalInvitation();
        $extraSchedule = $event->schedules()->create([
            'label' => 'Akad',
            'date' => now()->addMonth()->addDay()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '09:00',
            'timezone' => 'Asia/Jakarta',
            'venue_name' => 'Garden Hall',
            'sort_order' => 1,
        ]);
        $group = $event->guestGroups()->create([
            'name' => 'Keluarga Inti',
            'slug' => 'keluarga-inti',
        ]);
        $guest->update([
            'guest_group_id' => $group->id,
            'group_name' => $group->name,
        ]);
        $extraSchedule->guestGroups()->attach($group->id, ['event_id' => $event->id, 'allow_rsvp' => true]);

        $this->post(route('public.rsvp.personal', [$event, 'guestToken' => $token]), [
            'status' => 'hadir',
            'pax_count' => 2,
            'event_schedule_id' => $extraSchedule->id,
            'message' => 'Siap hadir',
        ])->assertRedirect();

        $this->assertDatabaseHas('rsvps', [
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'guest_invitation_id' => $invitation->id,
            'event_schedule_id' => $extraSchedule->id,
            'status' => 'hadir',
        ]);
    }

    public function test_personal_rsvp_rejects_schedule_outside_guest_group_scope(): void
    {
        [$event, $guest, $token] = $this->createPersonalInvitation();
        $restrictedSchedule = $event->schedules()->create([
            'label' => 'Family Dinner',
            'date' => now()->addMonth()->addDays(2)->toDateString(),
            'start_time' => '18:00',
            'end_time' => '20:00',
            'timezone' => 'Asia/Jakarta',
            'venue_name' => 'Private Lounge',
            'sort_order' => 1,
        ]);
        $allowedGroup = $event->guestGroups()->create([
            'name' => 'Teman Dekat',
            'slug' => 'teman-dekat',
        ]);
        $otherGroup = $event->guestGroups()->create([
            'name' => 'Vendor',
            'slug' => 'vendor',
        ]);
        $guest->update([
            'guest_group_id' => $otherGroup->id,
            'group_name' => $otherGroup->name,
        ]);
        $restrictedSchedule->guestGroups()->attach($allowedGroup->id, ['event_id' => $event->id, 'allow_rsvp' => true]);

        $this->from(route('public.invitation.personal', [$event, 'guestToken' => $token]))
            ->post(route('public.rsvp.personal', [$event, 'guestToken' => $token]), [
                'status' => 'hadir',
                'pax_count' => 1,
                'event_schedule_id' => $restrictedSchedule->id,
            ])
            ->assertRedirect(route('public.invitation.personal', [$event, 'guestToken' => $token]))
            ->assertSessionHasErrors('rsvp');
    }

    public function test_broadcast_campaign_can_be_targeted_scheduled_and_cancelled(): void
    {
        Queue::fake();

        $owner = User::factory()->create();
        $event = $this->createEvent($owner, [
            'settings_json' => ['addons' => ['broadcast' => true]],
        ]);
        $group = $event->guestGroups()->create([
            'name' => 'VIP Family',
            'slug' => 'vip-family',
        ]);
        [$guestOne] = $this->createGuestInvitationFor($event, 'Guest Satu', '081111111111');
        [$guestTwo] = $this->createGuestInvitationFor($event, 'Guest Dua', '082222222222');
        $guestOne->update([
            'guest_group_id' => $group->id,
            'group_name' => $group->name,
            'is_vip' => true,
        ]);
        $guestTwo->update([
            'status' => 'inactive',
        ]);
        $guestOne->invitation->update(['open_count' => 0]);
        $guestTwo->invitation->update(['open_count' => 4]);
        $owner->fonnteIntegration()->create([
            'device_token' => 'device-token-123',
            'device_token_last4' => '0123',
            'is_enabled' => true,
            'default_country_code' => '62',
            'device_status' => 'connected',
        ]);

        $scheduledAt = Carbon::now()->addHour()->format('Y-m-d\TH:i');

        $this->actingAs($owner)->post(route('dashboard.broadcasts.store', $event), [
            'name' => 'VIP Launch',
            'message_template' => 'Halo {{guest_name}} {{invitation_link}}',
            'guest_group_id' => $group->id,
            'guest_status' => 'active',
            'opened_state' => 'not_opened',
            'vip_only' => '1',
            'scheduled_at' => $scheduledAt,
            'connect_only' => '1',
        ])->assertRedirect();

        $campaign = BroadcastCampaign::query()->firstOrFail();

        $this->assertSame('scheduled', $campaign->status);
        $this->assertSame((string) $group->id, (string) $campaign->targeting_json['guest_group_id']);
        $this->assertDatabaseCount('broadcast_logs', 1);
        $this->assertDatabaseHas('broadcast_logs', [
            'campaign_id' => $campaign->id,
            'guest_id' => $guestOne->id,
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->patch(route('dashboard.broadcasts.cancel', [$event, $campaign]))
            ->assertRedirect();

        $this->assertDatabaseHas('broadcast_campaigns', [
            'id' => $campaign->id,
            'status' => 'cancelled',
        ]);
        $this->assertDatabaseHas('broadcast_logs', [
            'campaign_id' => $campaign->id,
            'guest_id' => $guestOne->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_owner_can_verify_gift_contribution(): void
    {
        [$event, $guest] = $this->createPersonalInvitation();
        $owner = $event->user;
        $contribution = GiftContribution::create([
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'guest_invitation_id' => $guest->invitation->id,
            'reference_code' => 'GFT-VERIFY-001',
            'status' => 'proof_uploaded',
        ]);

        $this->actingAs($owner)
            ->patch(route('dashboard.gifts.verify', [$event, $contribution]))
            ->assertRedirect();

        $this->assertDatabaseHas('gift_contributions', [
            'id' => $contribution->id,
            'status' => 'verified',
            'verified_by' => $owner->id,
        ]);
    }

    public function test_bundle_offer_updates_order_package_name(): void
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->post(route('dashboard.events.store'), [
            'title' => 'Bundle Event',
            'slug' => 'bundle-event',
            'couple_name_display' => 'Alya & Bima',
            'bride_name' => 'Alya',
            'groom_name' => 'Bima',
            'status' => 'draft',
            'language_variant' => 'id_formal',
            'bundle_offer_enabled' => '1',
            'is_rsvp_enabled' => '1',
            'is_gift_enabled' => '1',
            'is_comment_enabled' => '1',
            'is_guest_personalization_enabled' => '1',
            'gift_mode' => 'bank_transfer',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_holder' => 'Alya Bima',
            'schedules' => [[
                'label' => 'resepsi',
                'date' => now()->addMonth()->toDateString(),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'timezone' => 'Asia/Jakarta',
                'venue_name' => 'Gedung Bahagia',
                'address' => 'Jl. Bahagia',
                'maps_url' => 'https://maps.example.com',
            ]],
        ]);

        $response->assertRedirect();

        $event = Event::query()->where('slug', 'bundle-event')->firstOrFail();

        $this->assertDatabaseHas('orders', [
            'event_id' => $event->id,
            'package_name' => 'Wedding + Gift Experience Bundle',
        ]);
    }

    public function test_updating_event_preserves_existing_schedule_ids_used_by_rsvp(): void
    {
        [$event, $guest, $token, $invitation] = $this->createPersonalInvitation();
        $schedule = $event->schedules()->firstOrFail();

        $this->post(route('public.rsvp.personal', [$event, 'guestToken' => $token]), [
            'status' => 'hadir',
            'pax_count' => 2,
            'event_schedule_id' => $schedule->id,
            'message' => 'Tetap hadir',
        ])->assertRedirect();

        $this->actingAs($event->user)
            ->put(route('dashboard.events.update', $event), [
                'title' => 'Wedding Event Revisi',
                'slug' => $event->slug,
                'couple_name_display' => 'Alya & Bima',
                'bride_name' => 'Alya',
                'groom_name' => 'Bima',
                'status' => 'published',
                'template_id' => $event->template_id,
                'language_variant' => 'id_formal',
                'gift_mode' => 'no_gift',
                'schedules' => [[
                    'id' => $schedule->id,
                    'label' => 'resepsi utama',
                    'date' => optional($schedule->date)->format('Y-m-d'),
                    'start_time' => '10:30',
                    'end_time' => '12:30',
                    'timezone' => 'Asia/Jakarta',
                    'venue_name' => 'Gedung Bahagia Revisi',
                    'address' => 'Jl. Bahagia No. 2',
                    'maps_url' => 'https://maps.example.com/revisi',
                    'latitude' => '-6.208800',
                    'longitude' => '106.845600',
                ]],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('event_schedules', [
            'id' => $schedule->id,
            'event_id' => $event->id,
            'label' => 'resepsi utama',
            'venue_name' => 'Gedung Bahagia Revisi',
        ]);

        $this->assertDatabaseHas('rsvps', [
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'guest_invitation_id' => $invitation->id,
            'event_schedule_id' => $schedule->id,
            'status' => 'hadir',
        ]);
    }

    private function createEvent(User $owner, array $overrides = []): Event
    {
        $template = Template::query()->create([
            'name' => 'Valley of Blue',
            'code' => 'valley-blue-'.fake()->unique()->slug(),
            'category' => 'standard',
            'status' => 'active',
        ]);

        $event = Event::create(array_merge([
            'user_id' => $owner->id,
            'title' => 'Wedding Event',
            'slug' => 'event-'.fake()->unique()->slug(),
            'public_code' => 'CODE'.fake()->unique()->numerify('####'),
            'couple_name_display' => 'Alya & Bima',
            'bride_name' => 'Alya',
            'groom_name' => 'Bima',
            'status' => 'published',
            'template_id' => $template->id,
            'is_rsvp_enabled' => true,
            'is_comment_enabled' => true,
            'is_gift_enabled' => true,
            'is_guest_personalization_enabled' => true,
            'published_at' => now(),
        ], $overrides));

        $event->content()->create([
            'opening_text' => 'Assalamualaikum',
            'closing_text' => 'Terima kasih',
        ]);

        $event->schedules()->create([
            'label' => 'resepsi',
            'date' => now()->addMonth()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'timezone' => 'Asia/Jakarta',
            'venue_name' => 'Gedung Bahagia',
        ]);

        return $event;
    }

    private function createPersonalInvitation(): array
    {
        $owner = User::factory()->create();
        $event = $this->createEvent($owner);
        [$guest, $invitation, $token] = $this->createGuestInvitationFor($event, 'Andi Saputra', '081234567890');

        return [$event, $guest, $token, $invitation];
    }

    private function createGuestInvitationFor(Event $event, string $name, string $phone): array
    {
        $guest = Guest::create([
            'event_id' => $event->id,
            'name' => $name,
            'phone' => $phone,
            'group_name' => 'Keluarga',
            'max_pax' => 2,
            'status' => 'active',
        ]);

        [$invitation, $publicToken] = app(TokenService::class)->createGuestInvitation($event, $guest);

        return [$guest, $invitation, $publicToken];
    }
}
