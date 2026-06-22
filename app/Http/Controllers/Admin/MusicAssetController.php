<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusicAsset;
use App\Services\PublicMusicLibraryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MusicAssetController extends Controller
{
    public function __construct(
        private readonly PublicMusicLibraryService $publicMusicLibraryService,
    ) {
    }

    public function index(): View
    {
        $this->publicMusicLibraryService->sync();

        return view('admin.music.index', [
            'assets' => MusicAsset::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'artist' => ['nullable', 'string', 'max:255'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url'],
            'license_note' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        MusicAsset::create($data);

        return back()->with('status', 'Backsound disimpan.');
    }

    public function update(Request $request, MusicAsset $musicAsset): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'artist' => ['nullable', 'string', 'max:255'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url'],
            'license_note' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $musicAsset->update($data);

        return back()->with('status', 'Backsound diperbarui.');
    }
}
