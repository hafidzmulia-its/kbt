@extends('layouts.app', ['title' => 'Music Assets'])

@section('content')
    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="panel">
            <h1 class="text-3xl font-semibold text-slate-900">Tambah backsound</h1>
            <form method="POST" action="{{ route('admin.music.store') }}" class="mt-6 space-y-4">
                @csrf
                <input class="field" name="title" placeholder="Judul" required>
                <input class="field" name="artist" placeholder="Artist">
                <input class="field" name="source_name" placeholder="Sumber">
                <input class="field" name="source_url" placeholder="URL sumber">
                <textarea class="field" name="license_note" placeholder="Catatan lisensi"></textarea>
                <input class="field" name="external_url" placeholder="URL audio">
                <select class="field" name="status"><option value="active">active</option><option value="inactive">inactive</option></select>
                <button class="btn-primary" type="submit">Simpan</button>
            </form>
        </div>
        <div class="panel">
            <h2 class="text-3xl font-semibold text-slate-900">Music library</h2>
            <div class="mt-6 space-y-4">
                @foreach ($assets as $asset)
                    <form method="POST" action="{{ route('admin.music.update', $asset) }}" class="rounded-[24px] border border-slate-200 p-4">
                        @csrf
                        @method('PUT')
                        <input class="field" name="title" value="{{ $asset->title }}">
                        <div class="mt-3 grid gap-3 md:grid-cols-2">
                            <input class="field" name="artist" value="{{ $asset->artist }}">
                            <input class="field" name="external_url" value="{{ $asset->external_url }}">
                        </div>
                        <button class="btn-secondary mt-3" type="submit">Update</button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
@endsection
