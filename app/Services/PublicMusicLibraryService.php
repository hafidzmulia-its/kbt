<?php

namespace App\Services;

use App\Models\MusicAsset;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublicMusicLibraryService
{
    private const SUPPORTED_EXTENSIONS = ['mp3', 'wav', 'ogg', 'm4a'];

    public function sync(): void
    {
        $musicDirectory = public_path('music');

        if (! File::isDirectory($musicDirectory)) {
            return;
        }

        foreach (File::files($musicDirectory) as $file) {
            if (! in_array(Str::lower($file->getExtension()), self::SUPPORTED_EXTENSIONS, true)) {
                continue;
            }

            $relativePath = 'music/'.$file->getFilename();
            $asset = MusicAsset::query()->firstOrNew(['audio_path' => $relativePath]);

            if (! $asset->exists) {
                $asset->title = $this->humanizeFileName($file->getFilenameWithoutExtension());
                $asset->source_name = 'public/music';
                $asset->license_note = 'Local public music asset';
                $asset->status = 'active';
            }

            if (! $asset->title) {
                $asset->title = $this->humanizeFileName($file->getFilenameWithoutExtension());
            }

            $asset->audio_path = $relativePath;
            $asset->source_name = $asset->source_name ?: 'public/music';
            $asset->status = $asset->status ?: 'active';
            $asset->save();
        }
    }

    private function humanizeFileName(string $fileName): string
    {
        return Str::of($fileName)
            ->replace(['_', '-'], ' ')
            ->squish()
            ->toString();
    }
}
