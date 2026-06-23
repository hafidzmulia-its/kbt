<div class="relative overflow-hidden bg-[#f5eedc] text-on-surface">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(0,188,212,0.14),transparent_28%),radial-gradient(circle_at_top_left,rgba(29,90,141,0.18),transparent_34%)]"></div>
    <div class="relative mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between rounded-full border border-white/60 bg-white/70 px-5 py-3 backdrop-blur">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('brand/logo-nechcode-blue.webp') }}" alt="NechCode" class="h-9 w-auto">
                <span class="text-sm font-semibold uppercase tracking-[0.2em] text-primary">Invitely by NechCode</span>
            </a>
            <div class="hidden items-center gap-2 md:flex">
                @if ($event->is_rsvp_enabled)
                    <a href="#rsvp" class="btn-secondary">RSVP</a>
                @endif
                @if ($event->is_gift_enabled && $guest)
                    <a href="{{ route('public.gift.show', [$event, request()->route('guestToken')]) }}" class="btn-primary">Gift</a>
                @endif
            </div>
        </div>

        <main class="space-y-6">
            <section class="invitation-shell px-6 py-8 sm:px-8 md:px-10 md:py-10">
                <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-end">
                    <div class="space-y-5">
                        <p class="invitation-kicker">{{ $labels['hero_kicker'] }}</p>
                        <h1 class="text-5xl text-primary sm:text-6xl md:text-7xl">{{ $event->couple_name_display }}</h1>
                        <p class="text-2xl text-[#c48f54] invitation-script sm:text-3xl" style="font-family: 'Great Vibes', cursive;">A calm invitation experience for one unforgettable moment.</p>
                        <p class="max-w-2xl text-base leading-8 text-on-surface-variant">
                            {{ $event->content?->opening_text ?: $defaults['opening_text'] }}
                        </p>
                        @if ($event->content?->invitation_text)
                            <p class="max-w-2xl text-sm leading-7 text-on-surface-variant">{{ $event->content->invitation_text }}</p>
                        @endif
                    </div>

                    <div class="invitation-card text-center">
                        <p class="invitation-kicker">{{ $guest ? $labels['guest_label_personal'] : $labels['guest_label_general'] }}</p>
                        <p class="mt-4 text-4xl text-primary sm:text-5xl">{{ $guest?->name ?? $labels['guest_name_fallback'] }}</p>
                        <p class="mt-4 text-sm leading-7 text-on-surface-variant">
                            {{ $guest ? $labels['guest_intro_personal'] : $labels['guest_intro_general'] }}
                        </p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            @if ($event->status === 'published')
                                <div class="invitation-soft-card">
                                    <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">{{ $labels['status_label'] }}</p>
                                    <p class="mt-2 text-lg font-semibold text-primary">Published</p>
                                </div>
                            @endif
                            @if ($firstSchedule?->date)
                                <div class="invitation-soft-card">
                                    <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">{{ $labels['main_date_label'] }}</p>
                                    <p class="mt-2 text-lg font-semibold text-primary">{{ \Illuminate\Support\Carbon::parse($firstSchedule->date)->locale($carbonLocale)->translatedFormat('d F Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($countdownIso)
                    <div class="mt-8 invitation-card">
                        <p class="invitation-kicker text-center">{{ $labels['countdown_label'] }}</p>
                        <div class="mt-5 grid grid-cols-2 gap-3 text-center sm:grid-cols-4" data-countdown="{{ $countdownIso }}">
                            @foreach ($labels['countdown_units'] as $unitLabel)
                                <div class="invitation-soft-card">
                                    <p class="text-3xl font-semibold text-primary" data-countdown-value>0</p>
                                    <p class="mt-2 text-xs uppercase tracking-[0.2em] text-on-surface-variant">{{ $unitLabel }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            <section class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
                <div class="space-y-6">
                    <div class="invitation-card">
                        <p class="invitation-kicker">{{ $labels['couple_label'] }}</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div class="invitation-soft-card">
                                <h2 class="text-3xl text-primary">{{ $event->bride_name }}</h2>
                                <p class="mt-3 text-sm leading-7 text-on-surface-variant">{{ $event->content?->bride_bio }}</p>
                            </div>
                            <div class="invitation-soft-card">
                                <h2 class="text-3xl text-primary">{{ $event->groom_name }}</h2>
                                <p class="mt-3 text-sm leading-7 text-on-surface-variant">{{ $event->content?->groom_bio }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="invitation-card">
                        <p class="invitation-kicker">{{ $labels['schedule_label'] }}</p>
                        @if ($guest && $availableSchedules->count() !== $event->schedules->count())
                            <div class="mt-4 rounded-[1.2rem] border border-[#1d5a8d]/12 bg-[#f7fbff] px-4 py-3 text-sm text-on-surface-variant">
                                Halaman ini hanya menampilkan sesi yang tersedia untuk grup tamu Anda.
                            </div>
                        @endif
                        <div class="mt-5 space-y-4">
                            @forelse ($availableSchedules as $schedule)
                                <div class="invitation-soft-card">
                                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                        <div class="max-w-2xl">
                                            <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">{{ $schedule->label }}</p>
                                            <h3 class="mt-3 text-3xl text-primary">{{ \Illuminate\Support\Carbon::parse($schedule->date)->locale($carbonLocale)->translatedFormat('l, d F Y') }}</h3>
                                            <p class="mt-2 text-sm text-on-surface-variant">{{ $schedule->start_time ?: '--:--' }} - {{ $schedule->end_time ?: '--:--' }} WIB</p>
                                            <p class="mt-4 text-sm leading-7 text-on-surface-variant">{{ $schedule->venue_name }}<br>{{ $schedule->address }}</p>
                                        </div>
                                        @if ($schedule->maps_url)
                                            <a href="{{ $schedule->maps_url }}" class="btn-secondary" target="_blank" rel="noreferrer">{{ $labels['map_button'] }}</a>
                                        @endif
                                    </div>
                                    @if (filled($schedule->latitude) && filled($schedule->longitude))
                                        <div class="invitation-map-card">
                                            <div class="invitation-map-frame" data-invitation-map data-lat="{{ $schedule->latitude }}" data-lng="{{ $schedule->longitude }}"></div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="invitation-soft-card">
                                    <p class="text-sm leading-7 text-on-surface-variant">Sesi acara untuk link ini belum disiapkan. Hubungi penyelenggara agar alokasi sesi tamu diperbarui.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if ($event->albums->count() && $event->albums->first()?->photos->count())
                        <div class="invitation-card">
                            <p class="invitation-kicker">{{ $labels['album_label'] }}</p>
                            <div class="mt-5 grid grid-cols-2 gap-4">
                                @foreach ($event->albums->flatMap->photos->take(6) as $photo)
                                    <img src="{{ asset('storage/'.$photo->image_path) }}" alt="Album photo" class="h-40 w-full rounded-[1.35rem] object-cover sm:h-48" loading="lazy">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    @if ($event->musicAsset?->resolved_url)
                        <div class="invitation-card">
                            <p class="invitation-kicker">{{ $labels['music_label'] }}</p>
                            <h2 class="mt-3 text-4xl text-primary">{{ $labels['music_title'] }}</h2>
                            <p class="mt-5 text-sm leading-7 text-on-surface-variant">
                                {{ $event->musicAsset->title }}{{ $event->musicAsset->artist ? ' · '.$event->musicAsset->artist : '' }}. Musik diputar otomatis saat halaman dibuka, dan tamu tetap bisa mute dari tombol mengambang.
                            </p>
                        </div>
                    @endif

                    @if ($event->is_gift_enabled)
                        <div class="invitation-card">
                            <p class="invitation-kicker">{{ $labels['gift_label'] }}</p>
                            <h2 class="mt-3 text-4xl text-primary">{{ $labels['gift_title'] }}</h2>
                            @if (($event->giftSetting?->mode ?? 'no_gift') === 'no_gift')
                                <p class="mt-5 text-sm leading-7 text-on-surface-variant">{{ $event->giftSetting?->no_gift_message ?: ($event->content?->no_gift_message ?: $defaults['no_gift_message']) }}</p>
                            @else
                                <p class="mt-5 text-sm leading-7 text-on-surface-variant">{{ $event->giftSetting?->instructions ?: $defaults['gift_instructions'] }}</p>
                                <div class="mt-5 invitation-soft-card">
                                    <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">{{ $event->giftSetting?->bank_name }}</p>
                                    <p class="mt-2 text-2xl font-semibold text-primary">{{ $event->giftSetting?->account_holder }}</p>
                                    <p class="mt-2 text-lg text-on-surface">{{ $event->giftSetting?->account_number }}</p>
                                </div>
                                @if (file_exists(public_path('qris.jpeg')))
                                    <div class="mt-5 invitation-soft-card">
                                        <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">QRIS Preview</p>
                                        <img src="{{ asset('qris.jpeg') }}" alt="QRIS pembayaran" class="mx-auto mt-4 w-full max-w-[220px] rounded-[1.2rem] border border-outline-variant/18 object-cover">
                                    </div>
                                @endif
                                @if ($guest)
                                    <a href="{{ route('public.gift.show', [$event, request()->route('guestToken')]) }}" class="btn-primary mt-5">Konfirmasi sudah bayar</a>
                                @endif
                            @endif
                        </div>
                    @endif

                    @if ($event->is_comment_enabled)
                        <div class="invitation-card">
                            <p class="invitation-kicker">{{ $labels['comments_label'] }}</p>
                            <h2 class="mt-3 text-4xl text-primary">{{ $labels['comments_title'] }}</h2>
                            <div class="mt-6 grid gap-4 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                                <form method="POST" action="{{ $guest ? route('public.comment.personal', [$event, request()->route('guestToken')]) : route('public.comment.general', $event) }}" class="space-y-4">
                                    @csrf
                                    @unless($guest)
                                        <input class="field" name="name" placeholder="{{ $labels['comments_name_placeholder'] }}" required>
                                    @endunless
                                    <input type="hidden" name="website" value="">
                                    <textarea class="field min-h-28" name="message" placeholder="{{ $labels['comments_message_placeholder'] }}" required></textarea>
                                    <button class="btn-secondary w-full" type="submit">{{ $labels['comments_submit'] }}</button>
                                </form>
                                <div class="space-y-3">
                                    @forelse ($comments as $comment)
                                        <div class="invitation-soft-card">
                                            <p class="font-semibold text-primary">{{ $comment->name_snapshot }}</p>
                                            <p class="mt-2 text-sm leading-7 text-on-surface-variant">{{ $comment->message }}</p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-on-surface-variant">{{ $labels['comments_empty'] }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($guest && $invitation)
                        <div class="invitation-card text-center">
                            <p class="invitation-kicker">{{ $labels['qr_label'] }}</p>
                            <h2 class="mt-3 text-4xl text-primary">{{ $labels['qr_title'] }}</h2>
                            <div id="guest-qr" class="mx-auto mt-6 flex h-56 w-56 items-center justify-center rounded-[1.5rem] border border-dashed border-[#c48f54] bg-[#f8f3ea] p-4 text-xs text-on-surface-variant">{{ $invitation->checkin_url_cached }}</div>
                            <p class="mt-3 text-xs uppercase tracking-[0.18em] text-on-surface-variant">{{ $labels['qr_note'] }}</p>
                        </div>
                    @endif

                    <div class="invitation-card">
                        <p class="invitation-kicker">{{ $labels['closing_label'] }}</p>
                        <h2 class="mt-3 text-4xl text-primary">{{ $labels['closing_title'] }}</h2>
                        <p class="mt-5 text-sm leading-8 text-on-surface-variant">{{ $event->content?->closing_text ?: $defaults['closing_text'] }}</p>
                    </div>
                </div>
            </section>

            @if ($event->is_rsvp_enabled)
                <section id="rsvp" class="invitation-card">
                    <p class="invitation-kicker">{{ $labels['rsvp_label'] }}</p>
                    <h2 class="mt-3 text-4xl text-primary">{{ $labels['rsvp_title'] }}</h2>
                    <form method="POST" action="{{ $guest ? route('public.rsvp.personal', [$event, request()->route('guestToken')]) : route('public.rsvp.general', $event) }}" class="mt-6 space-y-4">
                        @csrf
                        @unless($guest)
                            <div class="grid gap-4 md:grid-cols-2">
                                <input class="field" name="name" placeholder="{{ $labels['rsvp_name_placeholder'] }}" required>
                                <input class="field" name="phone" placeholder="{{ $labels['rsvp_phone_placeholder'] }}">
                            </div>
                        @endunless
                        @if ($availableSchedules->isEmpty())
                            <div class="invitation-soft-card">
                                <p class="text-sm leading-7 text-on-surface-variant">RSVP untuk link ini belum dapat diproses karena sesi acara belum dipetakan untuk grup tamu Anda.</p>
                            </div>
                        @elseif ($availableSchedules->count() === 1)
                            <input type="hidden" name="event_schedule_id" value="{{ $availableSchedules->first()->id }}">
                            <div class="invitation-soft-card">
                                <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">Sesi terpilih</p>
                                <p class="mt-2 text-lg font-semibold text-primary">{{ $availableSchedules->first()->label }}</p>
                                <p class="mt-2 text-sm text-on-surface-variant">{{ \Illuminate\Support\Carbon::parse($availableSchedules->first()->date)->locale($carbonLocale)->translatedFormat('d F Y') }}</p>
                            </div>
                        @elseif ($availableSchedules->count() > 1)
                            <select class="field" name="event_schedule_id" required>
                                <option value="">Pilih sesi acara</option>
                                @foreach ($availableSchedules as $schedule)
                                    <option value="{{ $schedule->id }}">{{ $schedule->label }} · {{ \Illuminate\Support\Carbon::parse($schedule->date)->locale($carbonLocale)->translatedFormat('d F Y') }}</option>
                                @endforeach
                            </select>
                        @endif
                        <div class="grid gap-4 md:grid-cols-2">
                            <select class="field" name="status" required>
                                <option value="hadir">{{ $labels['rsvp_status_labels']['hadir'] }}</option>
                                <option value="tidak_hadir">{{ $labels['rsvp_status_labels']['tidak_hadir'] }}</option>
                                <option value="ragu">{{ $labels['rsvp_status_labels']['ragu'] }}</option>
                            </select>
                            <input class="field" name="pax_count" type="number" min="1" max="{{ $guest?->max_pax ?? 10 }}" value="1" required>
                        </div>
                        <textarea class="field min-h-28" name="message" placeholder="{{ $labels['rsvp_message_placeholder'] }}"></textarea>
                        <button class="btn-primary w-full lg:max-w-sm" type="submit" @disabled($availableSchedules->isEmpty())>{{ $labels['rsvp_submit'] }}</button>
                    </form>
                </section>
            @endif
        </main>
    </div>
</div>
