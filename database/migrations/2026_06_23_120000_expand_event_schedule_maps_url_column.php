<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE event_schedules MODIFY maps_url TEXT NULL');

            return;
        }

        Schema::table('event_schedules', function (Blueprint $table) {
            $table->text('maps_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE event_schedules MODIFY maps_url VARCHAR(255) NULL');

            return;
        }

        Schema::table('event_schedules', function (Blueprint $table) {
            $table->string('maps_url')->nullable()->change();
        });
    }
};
