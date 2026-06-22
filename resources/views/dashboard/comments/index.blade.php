@extends('layouts.app', ['title' => 'Comments'])

@section('content')
    <section class="dashboard-hero">
        <div class="max-w-4xl">
            <p class="section-kicker">Comment Moderation</p>
            <h1 class="mt-3 text-5xl text-primary md:text-6xl">Kurasi guestbook agar ucapan yang tampil tetap rapi.</h1>
            <p class="mt-5 max-w-3xl text-base leading-8 text-on-surface-variant">
                Moderasi komentar dipusatkan di halaman ini. Client dapat mengubah status ucapan tanpa menyentuh halaman publik invitation secara langsung.
            </p>
        </div>
    </section>

    <section class="mt-6 panel">
        <p class="section-kicker">Guestbook Queue</p>
        <h2 class="mt-3 text-4xl text-primary">Semua ucapan yang masuk untuk event ini.</h2>
        <div class="mt-6 space-y-4">
            @forelse ($comments as $comment)
                <div class="module-card">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div class="max-w-3xl">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="dashboard-chip">{{ $comment->status }}</span>
                            </div>
                            <h3 class="mt-4 text-2xl text-primary">{{ $comment->name_snapshot }}</h3>
                            <p class="mt-3 section-copy">{{ $comment->message }}</p>
                        </div>
                        <form method="POST" action="{{ route('dashboard.comments.update', [$event, $comment]) }}" class="flex flex-wrap gap-2">
                            @csrf
                            @method('PATCH')
                            <select class="field min-w-48" name="status">
                                @foreach (['pending', 'approved', 'hidden', 'deleted'] as $status)
                                    <option value="{{ $status }}" @selected($comment->status === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                            <button class="btn-secondary" type="submit">Simpan</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="module-card">
                    <p class="text-lg font-semibold text-primary">Belum ada komentar masuk.</p>
                    <p class="mt-3 section-copy">Ucapan dari tamu akan muncul di sini setelah dikirim dari invitation page.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
