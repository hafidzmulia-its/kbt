@extends('layouts.app', ['title' => 'Gift Confirmation'])

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <section class="dashboard-hero">
            <div class="max-w-3xl">
                <p class="section-kicker">Gift Confirmation</p>
                <h1 class="mt-3 text-5xl text-primary md:text-6xl">{{ $guest->name }}</h1>
                <p class="mt-5 text-base leading-8 text-on-surface-variant">
                    Gunakan halaman ini untuk mengirim nominal, catatan, dan bukti transfer. Semua file tetap masuk ke private storage dan akan diverifikasi oleh client dari dashboard gift tracking.
                </p>
            </div>
        </section>

        <section class="panel">
            <div class="grid gap-6 lg:grid-cols-[0.78fr_1.22fr]">
                <div class="space-y-4">
                    <div class="dashboard-table-card">
                        <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Reference</p>
                        <p class="mt-2 text-2xl font-semibold text-primary">{{ $gift->reference_code }}</p>
                    </div>
                    <div class="dashboard-table-card">
                        <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Event</p>
                        <p class="mt-2 text-2xl font-semibold text-primary">{{ $event->couple_name_display }}</p>
                    </div>
                </div>

                <div>
                    <p class="section-kicker">Upload Proof</p>
                    <h2 class="mt-3 text-4xl text-primary">Kirim bukti transfer hadiah.</h2>
                    <form method="POST" action="{{ route('public.gift.upload', [$event, request()->route('guestToken')]) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                        @csrf
                        <input class="field" name="amount" type="number" min="0" placeholder="Nominal">
                        <textarea class="field min-h-28" name="notes" placeholder="Catatan"></textarea>
                        <input class="field" name="proof" type="file" accept=".jpg,.jpeg,.png,.pdf" required>
                        <button class="btn-primary w-full" type="submit">Upload bukti transfer</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
