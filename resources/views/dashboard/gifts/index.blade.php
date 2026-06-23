@extends('layouts.app', ['title' => 'Gifts'])

@section('content')
    <section class="dashboard-hero">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-4xl">
                <p class="section-kicker">Gift Tracking</p>
                <h1 class="mt-3 text-5xl text-primary md:text-6xl">Verifikasi hadiah tamu dengan alur yang aman dan jelas.</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-on-surface-variant">
                    Semua bukti transfer tetap berada di private storage. Client hanya memakai dashboard ini untuk review nominal, reference code, dan keputusan verifikasi.
                </p>
            </div>
            <a href="{{ route('dashboard.gifts.export', $event) }}" class="btn-primary">Export CSV</a>
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
        <div class="panel">
            <p class="section-kicker">Gift Configuration</p>
            <h2 class="mt-3 text-4xl text-primary">Ringkasan pengaturan hadiah event.</h2>
            <div class="mt-6 space-y-3">
                <div class="dashboard-table-card">
                    <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Mode</p>
                    <p class="mt-2 text-2xl font-semibold text-primary">{{ $setting?->mode ?? 'no_gift' }}</p>
                </div>
                <div class="dashboard-table-card">
                    <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Bank</p>
                    <p class="mt-2 text-lg font-semibold text-primary">{{ $setting?->bank_name ?? '-' }}</p>
                    <p class="mt-1 section-copy">{{ $setting?->account_holder ?? '-' }}</p>
                </div>
                <div class="dashboard-table-card">
                    <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Confirmation flow</p>
                    <p class="mt-2 text-lg font-semibold text-primary">{{ $setting?->is_proof_upload_enabled ? 'proof optional + confirmation' : 'confirmation only' }}</p>
                </div>
            </div>
        </div>

        <div class="panel">
            <p class="section-kicker">Contribution Log</p>
            <h2 class="mt-3 text-4xl text-primary">Semua konfirmasi hadiah dan tindak lanjut.</h2>
            <div class="mt-6 overflow-x-auto">
                <table class="dashboard-data-table">
                    <thead>
                        <tr>
                            <th>Guest</th>
                            <th>Ref</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Proof</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contributions as $contribution)
                            <tr>
                                <td class="font-semibold text-primary">{{ $contribution->guest?->name }}</td>
                                <td>{{ $contribution->reference_code }}</td>
                                <td>{{ $contribution->status }}</td>
                                <td>{{ $contribution->amount ? 'Rp'.number_format($contribution->amount, 0, ',', '.') : '-' }}</td>
                                <td>
                                    @if ($contribution->proof_file_path)
                                        <a href="{{ route('dashboard.gifts.proof', [$event, $contribution]) }}" class="font-semibold text-primary">Unduh bukti</a>
                                    @else
                                        <span class="text-on-surface-variant">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('dashboard.gifts.verify', [$event, $contribution]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn-secondary" type="submit">Verifikasi</button>
                                        </form>
                                        <form method="POST" action="{{ route('dashboard.gifts.reject', [$event, $contribution]) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn-secondary" type="submit">Tolak</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-on-surface-variant">Belum ada konfirmasi hadiah untuk event ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
