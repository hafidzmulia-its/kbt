@extends('layouts.app', ['title' => 'Dashboard'])

@php
    $firstEvent = $events->first();
    $priorityActions = [
        [
            'step' => '01',
            'title' => 'Buat event baru',
            'copy' => 'Mulai kalau Anda belum punya event yang aktif.',
            'route' => route('dashboard.events.create'),
            'label' => 'Mulai wizard event',
            'is_primary' => $events->isEmpty(),
        ],
        [
            'step' => '02',
            'title' => 'Lanjutkan event yang sudah ada',
            'copy' => 'Masuk ke event terakhir untuk meneruskan setup, tamu, dan publikasi.',
            'route' => $firstEvent ? route('dashboard.events.workspace', $firstEvent) : route('dashboard.events.index'),
            'label' => $firstEvent ? 'Buka workspace terakhir' : 'Lihat daftar event',
            'is_primary' => $events->isNotEmpty(),
        ],
        [
            'step' => '03',
            'title' => 'Hubungkan Fonnte saat siap broadcast',
            'copy' => 'Langkah ini baru penting jika Anda ingin kirim undangan massal dari panel.',
            'route' => route('dashboard.fonnte.show'),
            'label' => 'Atur Fonnte',
            'is_primary' => false,
        ],
    ];
@endphp

@section('content')
    <section class="dashboard-stage">
        <div class="relative z-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-4xl">
                    <p class="section-kicker !text-[#9fe8ff]">Client Overview</p>
                    <h1 class="mt-3 text-5xl text-white md:text-6xl">Mulai dari sini, lalu lanjutkan event Anda langkah demi langkah.</h1>
                    <p class="mt-5 max-w-3xl text-base leading-8 text-white/76">
                        Dashboard client ini saya sederhanakan supaya urutan kerjanya jelas: buat event, rapikan isi, tambah tamu, lalu bagikan saat siap.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('dashboard.events.create') }}" class="brand-action brand-action-solid">Buat event baru</a>
                    <a href="{{ route('dashboard.events.index') }}" class="brand-action brand-action-ghost">Lihat semua event</a>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($metrics as $label => $value)
                    <div class="dashboard-stage-card">
                        <p class="text-sm uppercase tracking-[0.16em] text-white/45">{{ str_replace('_', ' ', $label) }}</p>
                        <p class="mt-3 text-4xl font-semibold text-white">{{ $value }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <div class="surface-panel">
            <p class="section-kicker">Langkah Utama</p>
            <h2 class="mt-3 text-[2.1rem] text-primary">Tiga hal yang paling mungkin Anda butuhkan sekarang.</h2>
            <div class="mt-8 grid gap-4">
                @foreach ($priorityActions as $item)
                    <div class="dashboard-quick-card">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="max-w-2xl">
                                <p class="dashboard-chip">Step {{ $item['step'] }}</p>
                                <p class="mt-4 text-2xl font-semibold text-primary">{{ $item['title'] }}</p>
                                <p class="mt-3 section-copy">{{ $item['copy'] }}</p>
                            </div>
                            <a href="{{ $item['route'] }}" class="{{ $item['is_primary'] ? 'btn-primary' : 'btn-secondary' }}">{{ $item['label'] }}</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="surface-panel">
            <p class="section-kicker">Ringkasan Cepat</p>
            <h2 class="mt-3 text-[2.1rem] text-primary">Apa yang sudah berjalan di akun Anda.</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="module-card">
                    <p class="text-sm font-semibold text-primary">Event aktif</p>
                    <p class="mt-3 text-3xl font-semibold text-on-surface">{{ $metrics['events'] }}</p>
                    <p class="mt-2 section-copy">Workspace invitation yang sudah Anda mulai.</p>
                </div>
                <div class="module-card">
                    <p class="text-sm font-semibold text-primary">Broadcast terkirim</p>
                    <p class="mt-3 text-3xl font-semibold text-on-surface">{{ $metrics['broadcast_sent'] }}</p>
                    <p class="mt-2 section-copy">Pesan yang sudah berhasil dikirim lewat Fonnte.</p>
                </div>
                <div class="module-card">
                    <p class="text-sm font-semibold text-primary">RSVP masuk</p>
                    <p class="mt-3 text-3xl font-semibold text-on-surface">{{ $metrics['rsvps'] }}</p>
                    <p class="mt-2 section-copy">Konfirmasi tamu yang sudah masuk ke sistem.</p>
                </div>
                <div class="module-card">
                    <p class="text-sm font-semibold text-primary">Gift pending</p>
                    <p class="mt-3 text-3xl font-semibold text-on-surface">{{ $metrics['gift_pending'] }}</p>
                    <p class="mt-2 section-copy">Bukti transfer yang masih perlu dicek.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 surface-panel">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="section-kicker">Event Anda</p>
                <h2 class="mt-3 text-[2.1rem] text-primary">Pilih event yang ingin Anda lanjutkan.</h2>
            </div>
            <a href="{{ route('dashboard.events.index') }}" class="btn-secondary">Kelola daftar event</a>
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
                            <p class="mt-3 section-copy">Masuk ke workspace jika Anda ingin tahu langkah berikutnya. Gunakan wizard hanya saat isi event masih perlu dirapikan.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('dashboard.events.workspace', $event) }}" class="btn-primary">Lanjutkan event</a>
                            <a href="{{ route('dashboard.events.edit', $event) }}" class="btn-secondary">Edit wizard</a>
                            <a href="{{ route('dashboard.events.preview', $event) }}" class="btn-secondary">Preview draft</a>
                            @if ($event->status === 'published')
                                <a href="{{ route('public.invitation.general', $event) }}" class="btn-secondary">Preview publik</a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="module-card">
                    <p class="text-lg font-semibold text-primary">Belum ada event.</p>
                    <p class="mt-3 section-copy">Buat event pertama untuk mulai menyusun invitation flow, personal link, dan pengalaman tamu.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
