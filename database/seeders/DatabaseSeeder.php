<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\OfficialTemplateCatalogService;
use App\Services\PublicMusicLibraryService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@nechcode.test'],
            [
                'name' => 'Platform Admin',
                'phone' => '081234567890',
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'user@nechcode.test'],
            [
                'name' => 'Wedding User',
                'phone' => '081111111111',
                'role' => 'user',
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        app(OfficialTemplateCatalogService::class)->sync();
        app(PublicMusicLibraryService::class)->sync();
    }
}
