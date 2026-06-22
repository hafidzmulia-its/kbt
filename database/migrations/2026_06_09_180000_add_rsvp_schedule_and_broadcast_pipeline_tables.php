<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_schedule_guest_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_group_id')->constrained()->cascadeOnDelete();
            $table->boolean('allow_rsvp')->default(true);
            $table->timestamps();

            $table->unique(['event_schedule_id', 'guest_group_id'], 'schedule_group_unique');
        });

        Schema::table('rsvps', function (Blueprint $table) {
            $table->foreignId('event_schedule_id')->nullable()->after('guest_invitation_id')->constrained()->nullOnDelete();
        });

        Schema::create('broadcast_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('template_body');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->foreignId('broadcast_template_id')->nullable()->after('user_id')->constrained('broadcast_templates')->nullOnDelete();
            $table->json('targeting_json')->nullable()->after('message_template');
            $table->timestamp('cancelled_at')->nullable()->after('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->dropConstrainedForeignId('broadcast_template_id');
            $table->dropColumn(['targeting_json', 'cancelled_at']);
        });

        Schema::dropIfExists('broadcast_templates');

        Schema::table('rsvps', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_schedule_id');
        });

        Schema::dropIfExists('event_schedule_guest_groups');
    }
};
