@extends('layouts.app', ['title' => 'Attendance'])

@section('content')
    <section class="dashboard-hero">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-4xl">
                <p class="section-kicker">Attendance Control</p>
                <h1 class="mt-3 text-5xl text-primary md:text-6xl">Check-in log untuk {{ $event->couple_name_display }}.</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-on-surface-variant">
                    Panel attendance dibuat untuk mode operasional hari-H: buat staff link, pantau check-in secara real-time, dan ekspor catatan kehadiran setelah acara selesai.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard.attendance.export', $event) }}" class="btn-secondary">Export CSV</a>
                <form method="POST" action="{{ route('dashboard.staff-links.store', $event) }}">
                    @csrf
                    <button class="btn-primary" type="submit">Generate staff link</button>
                </form>
            </div>
        </div>
    </section>

    <section class="mt-6 panel">
        <p class="section-kicker">Check-in History</p>
        <h2 class="mt-3 text-4xl text-primary">Status kehadiran berdasarkan scan yang berhasil.</h2>
        <div class="mt-6 space-y-4">
            @forelse ($checkins as $checkin)
                <div class="module-card">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-2xl text-primary">{{ $checkin->guest->name }}</h3>
                            <p class="mt-2 section-copy">{{ $checkin->checked_in_at }} - {{ $checkin->device_label }}</p>
                        </div>
                        <span class="dashboard-chip">Checked in</span>
                    </div>
                </div>
            @empty
                <div class="module-card">
                    <p class="text-lg font-semibold text-primary">Belum ada tamu yang check-in.</p>
                    <p class="mt-3 section-copy">Buat staff link lalu gunakan scanner saat hari acara untuk mulai mencatat kehadiran.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
