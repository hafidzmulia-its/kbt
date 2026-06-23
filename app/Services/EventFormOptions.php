<?php

namespace App\Services;

use App\Models\MusicAsset;
use App\Models\Template;

class EventFormOptions
{
    public function __construct(
        private readonly OfficialTemplateCatalogService $officialTemplateCatalogService,
        private readonly PublicMusicLibraryService $publicMusicLibraryService,
    ) {
    }

    public function get(): array
    {
        $this->officialTemplateCatalogService->sync();
        $this->publicMusicLibraryService->sync();

        return [
            'musicAssets' => MusicAsset::query()->where('status', 'active')->orderBy('title')->get(),
            'templates' => Template::query()->where('status', 'active')->orderBy('name')->get(),
            'occasionTypes' => config('nechcode.occasion_types', []),
        ];
    }
}
