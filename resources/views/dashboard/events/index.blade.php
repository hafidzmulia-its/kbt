@extends('layouts.app', ['title' => 'Events'])

@section('content')
    <section class="dashboard-stage">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-4xl">
                <p class="section-kicker !text-[#9fe8ff]">Event Registry</p>
                <h1 class="mt-3 text-5xl text-white md:text-6xl">Kelola seluruh invitation workspace dalam satu daftar rapi.</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-white/76">
                    Setiap event sekarang memakai alur setup, preview draft, personal invitation, dan publication flow yang sama. Halaman ini jadi titik kontrol utama untuk client.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard.events.create') }}" class="brand-action brand-action-solid">Buat event baru</a>
                <a href="{{ route('dashboard') }}" class="brand-action brand-action-ghost">Kembali ke overview</a>
            </div>
        </div>
    </section>

    <section class="mt-6 panel">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="section-kicker">Event List</p>
                <h2 class="mt-3 text-[2.1rem] text-primary">Semua event yang sudah masuk ke sistem.</h2>
            </div>
            <p class="section-copy max-w-xl">Pilih workspace untuk operasional, halaman setup untuk revisi konten, preview draft untuk review internal, dan public link saat event siap dipublikasikan.</p>
        </div>

        <div class="mt-6 grid gap-4">
            @forelse ($events as $event)
                <div class="module-card">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                        <div class="max-w-3xl">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="dashboard-chip">{{ ucfirst($event->status) }}</span>
                                <span class="text-sm text-on-surface-variant">{{ $event->template?->name ?? 'Template belum dipilih' }}</span>
                            </div>
                            <h3 class="mt-4 text-3xl text-primary">{{ $event->couple_name_display }}</h3>
                            <p class="mt-2 text-base font-medium text-on-surface">{{ $event->title }}</p>
                            <p class="mt-3 section-copy">
                                {{ $event->bride_name }} dan {{ $event->groom_name }}.
                                {{ $event->schedules->count() ? ' Jadwal aktif: '.$event->schedules->count().' sesi.' : ' Jadwal belum diisi.' }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2 xl:max-w-sm xl:justify-end">
                            <a href="{{ route('dashboard.events.workspace', $event) }}" class="btn-secondary">Workspace</a>
                            <a href="{{ route('dashboard.events.edit', $event) }}" class="btn-secondary">Setup</a>
                            <a href="{{ route('dashboard.events.preview', $event) }}" class="btn-secondary">Preview draft</a>
                            @if ($event->status === 'published')
                                <a href="{{ route('public.invitation.general', $event) }}" class="btn-primary">Public link</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="module-card">
                    <p class="text-lg font-semibold text-primary">Belum ada event.</p>
                    <p class="mt-3 section-copy">Mulai dari event pertama untuk mengaktifkan seluruh alur undangan digital, tamu, RSVP, dan operasional hari-H.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
