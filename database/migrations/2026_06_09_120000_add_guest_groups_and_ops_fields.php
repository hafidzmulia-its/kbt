<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'slug']);
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->foreignId('guest_group_id')->nullable()->after('phone')->constrained('guest_groups')->nullOnDelete();
            $table->boolean('is_vip')->default(false)->after('status');
            $table->boolean('needs_physical_invitation')->default(false)->after('is_vip');
        });

        $legacyGroups = DB::table('guests')
            ->select('event_id', 'group_name')
            ->whereNotNull('group_name')
            ->where('group_name', '!=', '')
            ->distinct()
            ->orderBy('event_id')
            ->orderBy('group_name')
            ->get();

        $groupIds = [];
        $slugsPerEvent = [];

        foreach ($legacyGroups as $group) {
            $baseSlug = Str::slug($group->group_name) ?: 'group';
            $slug = $baseSlug;
            $index = 2;

            while (in_array($slug, $slugsPerEvent[$group->event_id] ?? [], true)) {
                $slug = $baseSlug.'-'.$index;
                $index++;
            }

            $slugsPerEvent[$group->event_id][] = $slug;

            $groupId = DB::table('guest_groups')->insertGetId([
                'event_id' => $group->event_id,
                'name' => $group->group_name,
                'slug' => $slug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $groupIds[$group->event_id.'|'.$group->group_name] = $groupId;
        }

        foreach ($groupIds as $compoundKey => $groupId) {
            [$eventId, $groupName] = explode('|', $compoundKey, 2);

            DB::table('guests')
                ->where('event_id', $eventId)
                ->where('group_name', $groupName)
                ->update(['guest_group_id' => $groupId]);
        }
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('guest_group_id');
            $table->dropColumn(['is_vip', 'needs_physical_invitation']);
        });

        Schema::dropIfExists('guest_groups');
    }
};
