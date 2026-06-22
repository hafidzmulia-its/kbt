<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fonnte_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('account_token')->nullable();
            $table->text('device_token')->nullable();
            $table->string('device_token_last4', 4)->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_number')->nullable();
            $table->string('device_status')->default('unknown');
            $table->string('package_name')->nullable();
            $table->unsignedInteger('quota')->nullable();
            $table->string('expires_label')->nullable();
            $table->string('default_country_code', 8)->default('62');
            $table->text('last_error_message')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
        });

        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->string('country_code', 8)->default('62')->after('status');
            $table->string('delay')->nullable()->after('country_code');
            $table->boolean('connect_only')->default(true)->after('delay');
        });
    }

    public function down(): void
    {
        Schema::table('broadcast_campaigns', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'delay', 'connect_only']);
        });

        Schema::dropIfExists('fonnte_integrations');
    }
};
