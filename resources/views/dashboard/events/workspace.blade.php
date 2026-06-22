@extends('layouts.app', ['title' => 'Workspace Event'])

@php
    $trackingEnabled = $capabilities['tracking'] ?? false;
    $broadcastEnabled = $capabilities['broadcast'] ?? false;
    $bundleEnabled = $capabilities['bundle'] ?? false;
    $firstSchedule = $event->schedules->sortBy('date')->first();
    $primaryAction = collect($nextActions)->first();
    $secondaryActions = collect($nextActions)->slice(1)->values();
    $flowModules = [
        [
            'label' => 'Preview & Publish',
            'title' => 'Cek tampilan umum dan personal',
            'copy' => 'Review draft invitation, lihat versi personal, lalu aktifkan event publik saat semuanya siap.',
            'actions' => [
                ['label' => 'Preview draft', 'route' => route('dashboard.events.preview', $event)],
                ['label' => 'Wizard', 'route' => route('dashboard.events.edit', $event)],
            ],
        ],
        [
            'label' => 'Guests',
            'title' => 'Masukkan tamu dan personal link',
            'copy' => $trackingEnabled
                ? 'Tamu, personal link, RSVP, dan scanner dibuka dari satu basis data tamu.'
                : 'Aktifkan add-on tracking jika ingin mengelola tamu, RSVP, dan scanner secara penuh.',
            'actions' => [
                ['label' => 'Kelola tamu', 'route' => route('dashboard.guests.index', $event)],
                ['label' => 'Lihat RSVP', 'route' => route('dashboard.rsvps.index', $event)],
            ],
            'locked' => ! $trackingEnabled,
        ],
        [
            'label' => 'Distribution',
            'title' => 'Jalankan pengiriman undangan',
            'copy' => $broadcastEnabled
                ? 'Broadcast WhatsApp berjalan sebagai campaign yang bisa dipantau, diulang, dan dihubungkan ke device client.'
                : 'Tambahkan automation kirim undangan jika ingin mengirim personal invitation ke banyak tamu dari satu panel.',
            'actions' => [
                ['label' => 'Broadcast', 'route' => route('dashboard.broadcasts.index', $event)],
                ['label' => 'Fonnte', 'route' => route('dashboard.fonnte.show')],
            ],
            'locked' => ! $broadcastEnabled,
        ],
        [
            'label' => 'Event Day',
            'title' => 'Siapkan attendance dan hadiah',
            'copy' => $trackingEnabled
                ? 'Attendance, gift verification, dan scanner link sudah siap dipakai sebagai lapisan operasional hari-H.'
                : 'Lapisan hari-H dibuka penuh saat add-on tracking aktif.',
            'actions' => [
                ['label' => 'Attendance', 'route' => route('dashboard.attendance.index', $event)],
                ['label' => 'Gifts', 'route' => route('dashboard.gifts.index', $event)],
            ],
            'locked' => ! $trackingEnabled,
        ],
    ];
@endphp

@section('content')
    <section class="dashboard-stage">
        <div class="relative z-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-4xl">
                    <p class="section-kicker !text-[#9fe8ff]">Workspace Event</p>
                    <h1 class="mt-3 text-5xl text-white md:text-6xl">{{ $event->couple_name_display }}</h1>
                    <p class="mt-4 text-base leading-8 text-white/76">
                        {{ $event->title }} · {{ ucfirst($event->status) }} · {{ $event->template?->name ?? 'Template belum dipilih' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('dashboard.events.edit', $event) }}" class="brand-action brand-action-solid">Rapikan setup event</a>
                    <a href="{{ route('dashboard.events.preview', $event) }}" class="brand-action brand-action-ghost">Preview draft</a>
                    @if ($trackingEnabled && $event->guests_count)
                        <a href="{{ route('dashboard.events.preview.personal', $event) }}" class="brand-action brand-action-ghost">Preview personal</a>
                    @endif
                    @if ($event->status === 'published')
                        <a href="{{ route('public.invitation.general', $event) }}" class="brand-action brand-action-ghost">Lihat halaman publik</a>
                    @endif
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="dashboard-stage-card">
                    <p class="text-sm uppercase tracking-[0.16em] text-white/45">Tamu</p>
                    <p class="mt-3 text-4xl font-semibold text-white">{{ $event->guests_count }}</p>
                </div>
                <div class="dashboard-stage-card">
                    <p class="text-sm uppercase tracking-[0.16em] text-white/45">RSVP</p>
                    <p class="mt-3 text-4xl font-semibold text-white">{{ $event->rsvps_count }}</p>
                </div>
                <div class="dashboard-stage-card">
                    <p class="text-sm uppercase tracking-[0.16em] text-white/45">Check-In</p>
                    <p class="mt-3 text-4xl font-semibold text-white">{{ $event->attendance_checkins_count }}</p>
                </div>
                <div class="dashboard-stage-card">
                    <p class="text-sm uppercase tracking-[0.16em] text-white/45">Broadcast</p>
                    <p class="mt-3 text-4xl font-semibold text-white">{{ $event->broadcast_campaigns_count }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[1.08fr_0.92fr]">
        <div class="space-y-6">
            <div class="surface-panel">
                <p class="section-kicker">Urutan Kerja</p>
                <h2 class="mt-3 text-4xl text-primary">Mulai dari aksi yang paling penting untuk client.</h2>
                <p class="mt-4 section-copy">Urutan ini dibuat supaya user langsung tahu langkah berikutnya: rapikan event, isi tamu, lalu distribusikan ketika semuanya sudah siap.</p>

                @if ($primaryAction)
                    <div class="dashboard-quick-card mt-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="max-w-2xl">
                                <p class="dashboard-chip">Prioritas utama</p>
                                <h3 class="mt-4 text-3xl font-semibold text-primary">{{ $primaryAction['title'] }}</h3>
                                <p class="mt-3 section-copy">{{ $primaryAction['copy'] }}</p>
                            </div>
                            <a href="{{ $primaryAction['route'] }}" class="{{ $primaryAction['is_locked'] ? 'btn-secondary opacity-70' : 'btn-primary' }}">
                                {{ $primaryAction['route_label'] }}
                            </a>
                        </div>
                    </div>
                @endif

                @if ($secondaryActions->isNotEmpty())
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        @foreach ($secondaryActions as $action)
                            <div class="dashboard-quick-card">
                                <p class="dashboard-chip">{{ $action['label'] }}{{ $action['is_locked'] ? ' · Locked' : '' }}</p>
                                <h3 class="mt-4 text-2xl font-semibold text-primary">{{ $action['title'] }}</h3>
                                <p class="mt-3 section-copy">{{ $action['copy'] }}</p>
                                <a href="{{ $action['route'] }}" class="mt-5 inline-flex {{ $action['is_locked'] ? 'btn-secondary opacity-70' : 'btn-secondary' }}">
                                    {{ $action['route_label'] }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="surface-panel">
                <p class="section-kicker">Modul Tambahan</p>
                <h2 class="mt-3 text-4xl text-primary">Fitur lain tetap ada saat user butuh lanjut lebih jauh.</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    @foreach ($flowModules as $module)
                        <div class="module-card">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="dashboard-chip">{{ $module['label'] }}{{ ($module['locked'] ?? false) ? ' · Locked' : '' }}</span>
                            </div>
                            <h3 class="mt-4 text-2xl text-primary">{{ $module['title'] }}</h3>
                            <p class="mt-3 section-copy">{{ $module['copy'] }}</p>
                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach ($module['actions'] as $action)
                                    <a href="{{ $action['route'] }}" class="{{ ($module['locked'] ?? false) ? 'btn-secondary opacity-70' : 'btn-secondary' }}">{{ $action['label'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="workflow-readiness-card">
                <p class="section-kicker">Kesiapan Event</p>
                <h2 class="mt-3 text-4xl text-primary">Lihat seberapa siap event ini untuk dibagikan.</h2>
                <p class="mt-4 section-copy">{{ $readiness['completed'] }} dari {{ $readiness['total'] }} checkpoint sudah beres.</p>

                <div class="wizard-progress-track mt-6">
                    <div class="wizard-progress-fill" style="width: {{ $readiness['percentage'] }}%"></div>
                </div>

                <div class="workflow-checklist mt-6">
                    @foreach ($readiness['items'] as $item)
                        <div class="workflow-checklist-item {{ $item['is_complete'] ? 'is-complete' : '' }}">
                            <span class="workflow-checkmark">{{ $item['is_complete'] ? 'OK' : '...' }}</span>
                            <span>{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="workflow-readiness-card">
                <p class="section-kicker">Ringkasan Cepat</p>
                <h2 class="mt-3 text-4xl text-primary">Hal penting yang paling sering dicek client.</h2>

                <div class="mt-6 space-y-3 text-sm text-on-surface-variant">
                    <div class="flex items-center justify-between rounded-[1.1rem] bg-[#f7fbff] px-4 py-3">
                        <span>Bahasa publik</span>
                        <span class="font-semibold text-primary">{{ $languagePack['label'] }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-[1.1rem] bg-[#f7fbff] px-4 py-3">
                        <span>Tema aktif</span>
                        <span class="font-semibold text-primary">{{ $event->template?->name ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-[1.1rem] bg-[#f7fbff] px-4 py-3">
                        <span>Jadwal terdekat</span>
                        <span class="font-semibold text-primary">{{ $firstSchedule?->label ? ucfirst($firstSchedule->label) : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-[1.1rem] bg-[#f7fbff] px-4 py-3">
                        <span>Bundle hadiah</span>
                        <span class="font-semibold text-primary">{{ $bundleEnabled ? 'Aktif' : 'Tidak aktif' }}</span>
                    </div>
                </div>
            </div>

            <div class="workflow-readiness-card">
                <p class="section-kicker">Ringkasan Paket</p>
                @if ($latestOrder)
                    <h2 class="mt-3 text-4xl text-primary">{{ $latestOrder->package_name }}</h2>
                    <p class="mt-3 section-copy">{{ $bundleEnabled ? 'Mode bundling aktif dan mendukung narasi wedding + hadiah.' : 'Paket saat ini masih memakai mode invitation standar.' }}</p>
                    <div class="mt-6 space-y-3 text-sm text-on-surface-variant">
                        <div class="flex items-center justify-between rounded-[1.1rem] bg-[#f7fbff] px-4 py-3">
                            <span>Paket standar</span>
                            <span class="font-semibold text-primary">Rp{{ number_format($latestOrder->base_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-[1.1rem] bg-[#f7fbff] px-4 py-3">
                            <span>{{ $pricing['addons']['tracking_gift']['label'] }}</span>
                            <span class="font-semibold text-primary">{{ $latestOrder->addon_rsvp_gift_price ? 'Rp'.number_format($latestOrder->addon_rsvp_gift_price, 0, ',', '.') : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-[1.1rem] bg-[#f7fbff] px-4 py-3">
                            <span>{{ $pricing['addons']['broadcast']['label'] }}</span>
                            <span class="font-semibold text-primary">{{ $latestOrder->addon_broadcast_price ? 'Rp'.number_format($latestOrder->addon_broadcast_price, 0, ',', '.') : '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-[1.1rem] bg-[#f7fbff] px-4 py-3">
                            <span>{{ $pricing['addons']['custom_design']['label'] }}</span>
                            <span class="font-semibold text-primary">{{ $latestOrder->addon_custom_design_price ? 'Rp'.number_format($latestOrder->addon_custom_design_price, 0, ',', '.') : '-' }}</span>
                        </div>
                    </div>
                @else
                    <p class="mt-4 section-copy">Belum ada ringkasan order untuk event ini.</p>
                @endif
            </div>

            <div class="surface-panel">
                <p class="section-kicker">Staff Scanner</p>
                <h2 class="mt-3 text-4xl text-primary">Akses resepsionis tetap sederhana.</h2>
                @if ($trackingEnabled)
                    <form method="POST" action="{{ route('dashboard.staff-links.store', $event) }}" class="mt-6 space-y-3">
                        @csrf
                        <input class="field" name="label" placeholder="Label staff, misal: Resepsionis utama">
                        <button class="btn-primary w-full" type="submit">Buat link scanner</button>
                    </form>
                @else
                    <div class="mt-6 rounded-[1.25rem] border border-dashed border-outline-variant/50 bg-white/70 px-4 py-5 text-sm text-on-surface-variant">
                        Scanner hari-H dibuka penuh saat add-on tracking aktif.
                    </div>
                @endif

                <div class="mt-6 space-y-3">
                    @forelse ($event->staffAccessLinks as $staffLink)
                        <div class="dashboard-table-card">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-lg font-semibold text-primary">{{ $staffLink->label }}</p>
                                    <p class="mt-1 text-sm text-on-surface-variant">{{ $staffLink->isRevoked() ? 'Revoked' : 'Active' }} · dibuat {{ $staffLink->created_at->diffForHumans() }}</p>
                                </div>
                                @unless($staffLink->isRevoked())
                                    <form method="POST" action="{{ route('dashboard.staff-links.revoke', [$event, $staffLink]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn-secondary" type="submit">Revoke</button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    @empty
                        <p class="section-copy">Belum ada link scanner. Buat jika event sudah masuk ke mode operasional hari-H.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
