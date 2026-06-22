<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('category')->default('standard');
            $table->string('preview_image_path')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_premium')->default(false);
            $table->timestamps();
        });

        Schema::create('template_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained()->cascadeOnDelete();
            $table->string('version');
            $table->json('config_json')->nullable();
            $table->string('css_path')->nullable();
            $table->string('view_path')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('music_assets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url')->nullable();
            $table->text('license_note')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('external_url')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('public_code')->unique();
            $table->string('couple_name_display');
            $table->string('bride_name');
            $table->string('groom_name');
            $table->string('status')->default('draft');
            $table->foreignId('template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('music_asset_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_rsvp_enabled')->default(true);
            $table->boolean('is_comment_enabled')->default(true);
            $table->boolean('is_gift_enabled')->default(true);
            $table->boolean('is_guest_personalization_enabled')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->json('settings_json')->nullable();
            $table->timestamps();
        });

        Schema::create('event_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->text('opening_text')->nullable();
            $table->text('invitation_text')->nullable();
            $table->text('closing_text')->nullable();
            $table->text('bride_bio')->nullable();
            $table->text('groom_bio')->nullable();
            $table->json('love_story_json')->nullable();
            $table->text('no_gift_message')->nullable();
            $table->timestamps();
        });

        Schema::create('event_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('resepsi');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('venue_name');
            $table->text('address')->nullable();
            $table->string('maps_url')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('group_name')->nullable();
            $table->text('address_note')->nullable();
            $table->unsignedInteger('max_pax')->default(1);
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['event_id', 'phone']);
        });

        Schema::create('guest_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('public_token_hash', 128)->unique();
            $table->string('public_token_last4', 4);
            $table->string('checkin_token_hash', 128)->unique();
            $table->string('checkin_token_last4', 4);
            $table->string('invitation_url_cached')->nullable();
            $table->string('gift_url_cached')->nullable();
            $table->string('checkin_url_cached')->nullable();
            $table->timestamp('token_regenerated_at')->nullable();
            $table->timestamp('last_opened_at')->nullable();
            $table->unsignedInteger('open_count')->default(0);
            $table->timestamps();
        });

        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_invitation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_snapshot');
            $table->string('phone_snapshot')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedInteger('pax_count')->default(1);
            $table->text('message')->nullable();
            $table->timestamp('submitted_at');
            $table->string('source')->default('personal_link');
            $table->timestamps();
        });

        Schema::create('staff_access_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('token_hash', 128)->unique();
            $table->string('label')->default('Receptionist');
            $table->json('permissions_json')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('attendance_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_invitation_id')->constrained()->cascadeOnDelete();
            $table->timestamp('checked_in_at');
            $table->string('checked_in_by_type')->default('staff_link');
            $table->foreignId('staff_access_link_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device_label')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'guest_id']);
        });

        Schema::create('gift_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('mode')->default('no_gift');
            $table->string('bank_name')->nullable();
            $table->text('account_number')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('static_qr_path')->nullable();
            $table->text('no_gift_message')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_proof_upload_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('gift_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('guest_invitation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_code')->unique();
            $table->unsignedBigInteger('amount')->nullable();
            $table->string('proof_file_path')->nullable();
            $table->string('status')->default('none');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('Album');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('album_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_snapshot');
            $table->text('message');
            $table->string('status')->default('pending');
            $table->timestamp('submitted_at');
            $table->string('ip_hash', 128)->nullable();
            $table->timestamps();
        });

        Schema::create('broadcast_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('channel')->default('whatsapp_fonnte');
            $table->text('message_template');
            $table->string('status')->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('broadcast_campaigns')->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_invitation_id')->constrained()->cascadeOnDelete();
            $table->string('phone');
            $table->longText('personalized_message');
            $table->string('provider_message_id')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('package_name');
            $table->unsignedBigInteger('base_price')->default(0);
            $table->unsignedBigInteger('addon_rsvp_gift_price')->default(0);
            $table->unsignedBigInteger('addon_broadcast_price')->default(0);
            $table->unsignedBigInteger('addon_custom_design_price')->default(0);
            $table->unsignedBigInteger('total_price')->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_type');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('broadcast_logs');
        Schema::dropIfExists('broadcast_campaigns');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('album_photos');
        Schema::dropIfExists('albums');
        Schema::dropIfExists('gift_contributions');
        Schema::dropIfExists('gift_settings');
        Schema::dropIfExists('attendance_checkins');
        Schema::dropIfExists('staff_access_links');
        Schema::dropIfExists('rsvps');
        Schema::dropIfExists('guest_invitations');
        Schema::dropIfExists('guests');
        Schema::dropIfExists('event_schedules');
        Schema::dropIfExists('event_contents');
        Schema::dropIfExists('events');
        Schema::dropIfExists('music_assets');
        Schema::dropIfExists('template_versions');
        Schema::dropIfExists('templates');
    }
};
