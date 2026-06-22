@extends('layouts.app', ['title' => 'Templates'])

@section('content')
    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="panel">
            <h1 class="text-3xl font-semibold text-slate-900">Tambah template</h1>
            <form method="POST" action="{{ route('admin.templates.store') }}" class="mt-6 space-y-4">
                @csrf
                <input class="field" name="name" placeholder="Nama template" required>
                <input class="field" name="code" placeholder="Kode template" required>
                <input class="field" name="category" placeholder="Kategori" value="standard" required>
                <select class="field" name="status"><option value="active">active</option><option value="inactive">inactive</option></select>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_premium" value="1"> Premium</label>
                <button class="btn-primary" type="submit">Simpan</button>
            </form>
        </div>
        <div class="panel">
            <h2 class="text-3xl font-semibold text-slate-900">Library template</h2>
            <div class="mt-6 space-y-4">
                @foreach ($templates as $template)
                    <form method="POST" action="{{ route('admin.templates.update', $template) }}" class="rounded-[24px] border border-slate-200 p-4">
                        @csrf
                        @method('PUT')
                        <input class="field" name="name" value="{{ $template->name }}">
                        <div class="mt-3 grid gap-3 md:grid-cols-3">
                            <input class="field" name="category" value="{{ $template->category }}">
                            <input class="field" name="status" value="{{ $template->status }}">
                            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_premium" value="1" @checked($template->is_premium)> Premium</label>
                        </div>
                        <button class="btn-secondary mt-3" type="submit">Update</button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
@endsection
