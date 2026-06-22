@extends('layouts.app', ['title' => 'RSVP'])

@section('content')
    <section class="dashboard-hero">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-4xl">
                <p class="section-kicker">RSVP Monitor</p>
                <h1 class="mt-3 text-5xl text-primary md:text-6xl">Pantau konfirmasi kehadiran untuk {{ $event->couple_name_display }}.</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-on-surface-variant">
                    Riwayat RSVP sekarang bisa dibaca per grup tamu dan per sesi acara, sehingga client tidak hanya melihat jumlah hadir, tetapi juga komposisi tamu yang benar-benar akan datang.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard.rsvps.export', array_merge(['event' => $event], $filters)) }}" class="btn-primary">Export CSV</a>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <div class="space-y-6">
            <div class="surface-panel">
                <p class="section-kicker">RSVP Snapshot</p>
                <h2 class="mt-3 text-4xl text-primary">Baca intensi tamu per sesi, grup, dan sumber submit.</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="metric-card">
                        <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">Total submit</p>
                        <p class="mt-3 text-4xl font-semibold text-primary">{{ $summary['total'] }}</p>
                    </div>
                    <div class="metric-card">
                        <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">Rata-rata pax</p>
                        <p class="mt-3 text-4xl font-semibold text-primary">{{ number_format($summary['avg_pax'], 1) }}</p>
                    </div>
                    <div class="metric-card">
                        <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">Hadir</p>
                        <p class="mt-3 text-4xl font-semibold text-primary">{{ $summary['hadir'] }}</p>
                    </div>
                    <div class="metric-card">
                        <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">Ragu / tidak hadir</p>
                        <p class="mt-3 text-4xl font-semibold text-primary">{{ $summary['ragu'] + $summary['tidak_hadir'] }}</p>
                    </div>
                </div>
            </div>

            <div class="surface-panel">
                <p class="section-kicker">Group Session Matrix</p>
                <h2 class="mt-3 text-4xl text-primary">Kontrol sesi mana yang terlihat oleh tiap grup tamu.</h2>
                <p class="mt-4 section-copy">Jika grup tidak dipetakan ke sesi mana pun, personal invitation tetap melihat seluruh jadwal acara. Gunakan matrix ini saat Anda butuh distribusi sesi yang lebih rapi.</p>

                <form method="POST" action="{{ route('dashboard.rsvps.assignments', $event) }}" class="mt-6 space-y-4">
                    @csrf
                    @forelse ($assignmentMatrix as $row)
                        <div class="module-card">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                <div class="max-w-xl">
                                    <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">{{ $row['schedule']->label }}</p>
                                    <h3 class="mt-2 text-2xl text-primary">{{ \Illuminate\Support\Carbon::parse($row['schedule']->date)->translatedFormat('d F Y') }}</h3>
                                    <p class="mt-2 section-copy">{{ $row['schedule']->venue_name }}</p>
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    @forelse ($row['groups'] as $groupState)
                                        <label class="module-card flex cursor-pointer items-center gap-3 px-4 py-3">
                                            <input type="checkbox" name="assignments[{{ $row['schedule']->id }}][]" value="{{ $groupState['group']->id }}" @checked($groupState['is_assigned'])>
                                            <span class="text-sm font-semibold text-on-surface">{{ $groupState['group']->name }}</span>
                                        </label>
                                    @empty
                                        <p class="section-copy">Belum ada grup tamu untuk dipetakan.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="module-card">
                            <p class="text-lg font-semibold text-primary">Belum ada sesi acara.</p>
                            <p class="mt-3 section-copy">Tambahkan jadwal acara dulu dari event builder agar matrix RSVP bisa diatur.</p>
                        </div>
                    @endforelse

                    @if (count($assignmentMatrix))
                        <button class="btn-secondary" type="submit">Simpan matrix sesi</button>
                    @endif
                </form>
            </div>
        </div>

        <div class="surface-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="section-kicker">Submission Log</p>
                    <h2 class="mt-3 text-4xl text-primary">Filter RSVP berdasarkan grup, sesi, dan sumber.</h2>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard.rsvps.index', $event) }}" class="mt-6 grid gap-4 lg:grid-cols-[1.2fr_0.8fr_0.85fr_0.8fr_0.8fr_auto]">
                <input class="field" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama, phone, grup, sesi">
                <select class="field" name="status">
                    <option value="">Semua status</option>
                    @foreach (['hadir', 'tidak_hadir', 'ragu', 'pending'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select class="field" name="guest_group_id">
                    <option value="">Semua grup</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected((string) ($filters['guest_group_id'] ?? '') === (string) $group->id)>{{ $group->name }}</option>
                    @endforeach
                </select>
                <select class="field" name="event_schedule_id">
                    <option value="">Semua sesi</option>
                    @foreach ($schedules as $schedule)
                        <option value="{{ $schedule->id }}" @selected((string) ($filters['event_schedule_id'] ?? '') === (string) $schedule->id)>{{ $schedule->label }}</option>
                    @endforeach
                </select>
                <select class="field" name="source">
                    <option value="">Semua source</option>
                    @foreach (['general_link', 'personal_link'] as $source)
                        <option value="{{ $source }}" @selected(($filters['source'] ?? '') === $source)>{{ $source }}</option>
                    @endforeach
                </select>
                <button class="btn-primary" type="submit">Filter</button>
            </form>

            <div class="mt-6 overflow-x-auto">
                <table class="dashboard-data-table">
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Grup / sesi</th>
                            <th>Status</th>
                            <th>Pax</th>
                            <th>Source</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rsvps as $rsvp)
                            <tr>
                                <td>
                                    <p class="font-semibold text-primary">{{ $rsvp->name_snapshot }}</p>
                                    <p class="mt-2 text-xs text-on-surface-variant">{{ $rsvp->phone_snapshot ?: 'Tanpa phone' }}</p>
                                    @if ($rsvp->message)
                                        <p class="mt-3 max-w-sm text-sm leading-6 text-on-surface-variant">{{ $rsvp->message }}</p>
                                    @endif
                                </td>
                                <td>
                                    <p class="font-semibold text-primary">{{ $rsvp->guest?->resolved_group_name ?: 'Umum' }}</p>
                                    <p class="mt-2 text-xs text-on-surface-variant">{{ $rsvp->schedule?->label ?: 'Tanpa sesi spesifik' }}</p>
                                </td>
                                <td><span class="dashboard-chip">{{ $rsvp->status }}</span></td>
                                <td>{{ $rsvp->pax_count }}</td>
                                <td>{{ $rsvp->source }}</td>
                                <td>{{ optional($rsvp->submitted_at)->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-on-surface-variant">Belum ada data RSVP untuk filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
