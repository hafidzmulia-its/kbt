<style>
    :root {
        --pb-blush: #FDF6F0;
        --pb-rose: #F2D9D0;
        --pb-coral: #E8735A;
        --pb-plum: #2D1B2E;
        --pb-wine: #6B2D4A;
        --pb-muted: #9A7A86;
        --pb-white: #FFFFFF;
        --pb-lilac: #C9AEC3;
        --pb-gold: #D4A853;
        --pb-display: 'Playfair Display', serif;
        --pb-body: 'DM Sans', sans-serif;
        --pb-frame: 390px;
    }

    .pb-template *, .pb-template *::before, .pb-template *::after { box-sizing: border-box; }
    .pb-template {
        background: var(--pb-blush);
        color: var(--pb-plum);
        font-family: var(--pb-body);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 24px 0 48px;
    }

    .pb-frame {
        width: 100%;
        max-width: var(--pb-frame);
        overflow: hidden;
        box-shadow: 0 20px 80px rgba(45, 27, 46, .18);
    }

    .pb-strip {
        background: var(--pb-coral);
        padding: 6px 16px;
        font-size: 9px;
        letter-spacing: .25em;
        text-transform: uppercase;
        color: var(--pb-white);
        text-align: center;
        font-weight: 400;
    }

    .pb-section-label {
        font-size: 10px;
        letter-spacing: .28em;
        text-transform: uppercase;
        color: var(--pb-coral);
        font-weight: 300;
    }

    .pb-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin: 14px auto;
        width: fit-content;
    }

    .pb-divider::before,
    .pb-divider::after {
        content: '';
        display: block;
        width: 36px;
        height: 1px;
        background: var(--pb-coral);
        opacity: .4;
    }

    .pb-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--pb-coral);
    }

    .pb-hero {
        position: relative;
        min-height: 100svh;
        background: radial-gradient(ellipse at 30% 20%, #3D1F3F 0%, #1A0E1B 60%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px 32px;
        overflow: hidden;
    }

    .pb-confetti {
        position: absolute;
        border-radius: 50%;
        opacity: .25;
    }

    .pb-confetti.d1 { width:10px;height:10px;background:var(--pb-coral);top:12%;left:8%; }
    .pb-confetti.d2 { width:6px;height:6px;background:var(--pb-gold);top:20%;left:80%; }
    .pb-confetti.d3 { width:8px;height:8px;background:var(--pb-lilac);top:35%;left:12%; }
    .pb-confetti.d4 { width:12px;height:12px;background:var(--pb-coral);top:60%;left:88%; }
    .pb-confetti.d5 { width:5px;height:5px;background:var(--pb-gold);top:75%;left:15%; }
    .pb-confetti.d6 { width:9px;height:9px;background:var(--pb-lilac);top:80%;left:70%; }
    .pb-confetti.d7 { width:7px;height:7px;background:var(--pb-coral);top:5%;left:55%; }
    .pb-confetti.d8 { width:11px;height:11px;background:var(--pb-gold);top:90%;left:45%; }

    .pb-big-mark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-family: var(--pb-display);
        font-size: 320px;
        font-weight: 700;
        color: rgba(255,255,255,.03);
        line-height: 1;
        user-select: none;
        white-space: nowrap;
    }

    .pb-hero-content,
    .pb-countdown-content,
    .pb-rsvp-content,
    .pb-closing-content { position: relative; z-index: 1; }

    .pb-hero-kicker,
    .pb-hero-date,
    .pb-scroll-label,
    .pb-countdown-sub,
    .pb-rsvp-sub,
    .pb-closing-copy { font-family: var(--pb-body); }

    .pb-hero-kicker {
        font-weight: 300;
        font-size: 10px;
        letter-spacing: .32em;
        text-transform: uppercase;
        color: var(--pb-coral);
        margin-bottom: 16px;
    }

    .pb-hero-to {
        font-weight: 200;
        font-size: 11px;
        letter-spacing: .18em;
        color: rgba(255,255,255,.45);
        margin-bottom: 8px;
    }

    .pb-hero-guest {
        font-family: var(--pb-display);
        font-size: 20px;
        font-weight: 400;
        color: var(--pb-white);
        margin-bottom: 32px;
    }

    .pb-hero-name,
    .pb-profile-name,
    .pb-section-title,
    .pb-card-title,
    .pb-story-title,
    .pb-rsvp-title,
    .pb-wishes-title,
    .pb-closing-name,
    .pb-gift-title,
    .pb-qr-title,
    .pb-countdown-heading { font-family: var(--pb-display); }

    .pb-hero-name {
        font-size: 58px;
        font-weight: 700;
        font-style: italic;
        color: var(--pb-white);
        line-height: 1;
    }

    .pb-hero-and {
        font-size: 70px;
        font-weight: 700;
        font-style: italic;
        color: var(--pb-coral);
        line-height: 1;
        margin: 6px 0;
    }

    .pb-hero-date {
        font-weight: 300;
        font-size: 11px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: rgba(255,255,255,.55);
        margin-top: 28px;
    }

    .pb-scroll {
        position: absolute;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .pb-scroll-label {
        font-size: 9px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: rgba(255,255,255,.3);
    }

    .pb-scroll-line {
        width: 1px;
        height: 32px;
        background: linear-gradient(to bottom, var(--pb-coral), transparent);
    }

    .pb-quote,
    .pb-profile,
    .pb-story,
    .pb-gallery,
    .pb-wishes,
    .pb-gift,
    .pb-qr-section { background: var(--pb-rose); }

    .pb-quote,
    .pb-profile,
    .pb-story,
    .pb-wishes,
    .pb-gift,
    .pb-qr-section { padding: 56px 32px; text-align: center; }

    .pb-quote-copy,
    .pb-profile-copy,
    .pb-card-copy,
    .pb-story-copy,
    .pb-wish-copy,
    .pb-empty,
    .pb-gift-copy,
    .pb-qr-note {
        font-size: 13px;
        font-weight: 300;
        line-height: 1.8;
        color: var(--pb-muted);
    }

    .pb-section-title,
    .pb-story-title,
    .pb-wishes-title,
    .pb-gift-title,
    .pb-qr-title,
    .pb-countdown-heading,
    .pb-rsvp-title {
        font-size: 34px;
        font-weight: 400;
        font-style: italic;
        color: var(--pb-plum);
        margin-bottom: 12px;
    }

    .pb-profile-grid,
    .pb-acara-list,
    .pb-wishes-list,
    .pb-gift-list,
    .pb-comment-list { display: grid; gap: 16px; }

    .pb-profile-card,
    .pb-acara-card,
    .pb-story-card,
    .pb-wish-card,
    .pb-gift-card,
    .pb-music-card,
    .pb-qr-card,
    .pb-comment-card {
        background: var(--pb-white);
        border: 1px solid rgba(232,115,90,.15);
    }

    .pb-profile-card,
    .pb-story-card,
    .pb-wish-card,
    .pb-music-card,
    .pb-qr-card,
    .pb-comment-card { padding: 24px; text-align: left; }

    .pb-profile-name {
        font-size: 36px;
        font-weight: 400;
        color: var(--pb-plum);
        margin-bottom: 6px;
    }

    .pb-role {
        font-size: 10px;
        letter-spacing: .28em;
        text-transform: uppercase;
        color: var(--pb-coral);
        margin-bottom: 8px;
    }

    .pb-countdown,
    .pb-rsvp,
    .pb-closing {
        position: relative;
        padding: 64px 32px;
        text-align: center;
        overflow: hidden;
        background: linear-gradient(135deg, #2D1B2E 0%, #1A0C1B 100%);
    }

    .pb-countdown-heading,
    .pb-rsvp-title,
    .pb-closing-name,
    .pb-closing-copy,
    .pb-countdown-sub,
    .pb-rsvp-sub { color: var(--pb-white); }

    .pb-countdown-sub,
    .pb-rsvp-sub {
        font-size: 11px;
        font-weight: 300;
        letter-spacing: .15em;
        text-transform: uppercase;
        margin-bottom: 34px;
        color: rgba(255,255,255,.46);
    }

    .pb-countdown-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 36px;
    }

    .pb-countdown-unit {
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(232,115,90,.2);
        padding: 16px 8px;
    }

    .pb-countdown-num {
        font-family: var(--pb-display);
        font-size: 34px;
        font-weight: 700;
        color: var(--pb-coral);
        line-height: 1;
    }

    .pb-countdown-label {
        font-size: 9px;
        letter-spacing: .15em;
        text-transform: uppercase;
        color: rgba(255,255,255,.35);
        margin-top: 6px;
    }

    .pb-btn,
    .pb-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s ease, color .2s ease, border-color .2s ease, opacity .2s ease;
    }

    .pb-btn-secondary {
        border: 1px solid var(--pb-coral);
        color: var(--pb-coral);
        font-size: 10px;
        letter-spacing: .2em;
        text-transform: uppercase;
        padding: 10px 20px;
        background: transparent;
    }

    .pb-btn {
        width: 100%;
        background: var(--pb-coral);
        color: var(--pb-white);
        border: none;
        padding: 16px;
        font-size: 11px;
        letter-spacing: .2em;
        text-transform: uppercase;
    }

    .pb-acara {
        background: var(--pb-rose);
        padding: 56px 24px;
        text-align: center;
    }

    .pb-acara-card {
        padding: 32px 24px;
        position: relative;
    }

    .pb-tag {
        position: absolute;
        top: -11px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--pb-coral);
        color: var(--pb-white);
        font-size: 9px;
        letter-spacing: .2em;
        text-transform: uppercase;
        padding: 4px 16px;
        white-space: nowrap;
    }

    .pb-card-title {
        font-size: 26px;
        font-weight: 400;
        color: var(--pb-plum);
        margin-bottom: 12px;
    }

    .pb-card-meta {
        font-size: 12px;
        color: var(--pb-wine);
        letter-spacing: .05em;
        margin-bottom: 4px;
    }

    .pb-gallery {
        padding: 56px 0;
        text-align: center;
    }

    .pb-gallery-header { padding: 0 32px 32px; }

    .pb-gallery-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3px;
        padding: 0 3px;
    }

    .pb-gallery-item {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        background: linear-gradient(135deg, #E8B4A8 0%, #C07080 100%);
    }

    .pb-gallery-item:first-child {
        grid-column: span 2;
        aspect-ratio: 16 / 9;
    }

    .pb-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .pb-gallery-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255,255,255,.4);
        font-family: var(--pb-display);
        font-style: italic;
    }

    .pb-rsvp-field,
    .pb-comment-form input,
    .pb-comment-form textarea {
        width: 100%;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(232,115,90,.2);
        padding: 14px 16px;
        color: var(--pb-white);
        font-family: var(--pb-body);
        font-size: 13px;
        font-weight: 300;
        outline: none;
        margin-bottom: 12px;
    }

    .pb-rsvp-field::placeholder,
    .pb-comment-form input::placeholder,
    .pb-comment-form textarea::placeholder {
        color: rgba(255,255,255,.22);
    }

    .pb-comment-form input,
    .pb-comment-form textarea {
        background: var(--pb-white);
        color: var(--pb-plum);
    }

    .pb-comment-form textarea {
        min-height: 100px;
        resize: vertical;
    }

    .pb-rsvp-card {
        padding: 18px;
        margin-bottom: 14px;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(232,115,90,.2);
        color: rgba(255,255,255,.8);
        text-align: left;
    }

    .pb-wish-card {
        border-left: 3px solid var(--pb-coral);
    }

    .pb-wish-name {
        font-family: var(--pb-display);
        font-size: 16px;
        font-style: italic;
        color: var(--pb-wine);
        margin-bottom: 6px;
    }

    .pb-gift-card {
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        text-align: left;
    }

    .pb-gift-bank {
        font-size: 10px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--pb-coral);
        font-weight: 500;
        margin-bottom: 4px;
    }

    .pb-gift-account {
        font-family: var(--pb-display);
        font-size: 20px;
        color: var(--pb-plum);
    }

    .pb-pill {
        background: var(--pb-blush);
        border: 1px solid rgba(232,115,90,.2);
        padding: 8px 14px;
        font-size: 9px;
        letter-spacing: .15em;
        text-transform: uppercase;
        color: var(--pb-coral);
        white-space: nowrap;
    }

    .pb-music-card audio { width: 100%; margin-top: 16px; }

    .pb-qr-card { text-align: center; }

    .pb-qr-box {
        margin: 22px auto 0;
        width: 224px;
        height: 224px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed var(--pb-coral);
        background: var(--pb-blush);
        color: var(--pb-muted);
        font-size: 11px;
        text-align: center;
    }

    .pb-closing {
        padding: 80px 32px 56px;
        background: linear-gradient(160deg, #2D1B2E 0%, #0F0710 100%);
    }

    .pb-closing-copy {
        font-size: 12px;
        line-height: 1.8;
        margin-bottom: 28px;
        color: rgba(255,255,255,.45);
    }

    .pb-closing-name {
        font-size: 52px;
        font-weight: 400;
        font-style: italic;
        line-height: 1.1;
        color: var(--pb-white);
    }

    .pb-credit {
        margin-top: 38px;
        font-size: 10px;
        color: rgba(255,255,255,.24);
        letter-spacing: .1em;
    }
</style>

<div class="pb-template">
    <div class="pb-frame">
        <div class="pb-strip">01 · Hero</div>
        <section class="pb-hero">
            <div class="pb-confetti d1"></div>
            <div class="pb-confetti d2"></div>
            <div class="pb-confetti d3"></div>
            <div class="pb-confetti d4"></div>
            <div class="pb-confetti d5"></div>
            <div class="pb-confetti d6"></div>
            <div class="pb-confetti d7"></div>
            <div class="pb-confetti d8"></div>
            <div class="pb-big-mark">&amp;</div>

            <div class="pb-hero-content">
                <p class="pb-hero-kicker">{{ $labels['hero_kicker'] }}</p>
                <p class="pb-hero-to">{{ $guest ? $labels['guest_label_personal'] : $labels['guest_label_general'] }}</p>
                <p class="pb-hero-guest">{{ $guest?->name ?? $labels['guest_name_fallback'] }}</p>
                <div class="pb-divider"><div class="pb-dot"></div></div>
                <div class="pb-hero-name">{{ $event->bride_name }}</div>
                <div class="pb-hero-and">&amp;</div>
                <div class="pb-hero-name">{{ $event->groom_name }}</div>
                @if ($firstSchedule?->date)
                    <p class="pb-hero-date">{{ \Illuminate\Support\Carbon::parse($firstSchedule->date)->locale($carbonLocale)->translatedFormat('d F Y') }}</p>
                @endif
            </div>

            <div class="pb-scroll">
                <span class="pb-scroll-label">Scroll</span>
                <div class="pb-scroll-line"></div>
            </div>
        </section>

        <div class="pb-strip">02 · Opening Copy</div>
        <section class="pb-quote">
            <p class="pb-section-label">Invitation Notes</p>
            <div class="pb-divider"><div class="pb-dot"></div></div>
            <p class="pb-quote-copy">{{ $event->content?->opening_text ?: $defaults['opening_text'] }}</p>
        </section>

        <div class="pb-strip">03 · Couple</div>
        <section class="pb-profile">
            <p class="pb-section-label">{{ $labels['couple_label'] }}</p>
            <h2 class="pb-section-title">{{ $event->couple_name_display }}</h2>
            <div class="pb-profile-grid">
                <article class="pb-profile-card">
                    <div class="pb-role">Bride</div>
                    <h3 class="pb-profile-name">{{ $event->bride_name }}</h3>
                    <p class="pb-profile-copy">{{ $event->content?->bride_bio ?: 'Profil mempelai wanita akan tampil di sini.' }}</p>
                </article>
                <article class="pb-profile-card">
                    <div class="pb-role">Groom</div>
                    <h3 class="pb-profile-name">{{ $event->groom_name }}</h3>
                    <p class="pb-profile-copy">{{ $event->content?->groom_bio ?: 'Profil mempelai pria akan tampil di sini.' }}</p>
                </article>
            </div>
        </section>

        @if ($countdownIso)
            <div class="pb-strip">04 · Countdown</div>
            <section class="pb-countdown">
                <div class="pb-countdown-content">
                    <h2 class="pb-countdown-heading">{{ $labels['countdown_label'] }}</h2>
                    <p class="pb-countdown-sub">Hitung mundur menuju hari spesial kalian</p>
                    <div class="pb-countdown-grid" data-countdown="{{ $countdownIso }}">
                        @foreach ($labels['countdown_units'] as $unitLabel)
                            <div class="pb-countdown-unit">
                                <div class="pb-countdown-num" data-countdown-value>0</div>
                                <div class="pb-countdown-label">{{ $unitLabel }}</div>
                            </div>
                        @endforeach
                    </div>
                    @if ($firstSchedule?->maps_url)
                        <a href="{{ $firstSchedule->maps_url }}" target="_blank" rel="noreferrer" class="pb-btn-secondary">{{ $labels['map_button'] }}</a>
                    @endif
                </div>
            </section>
        @endif

        <div class="pb-strip">05 · Event Details</div>
        <section class="pb-acara">
            <p class="pb-section-label">{{ $labels['schedule_label'] }}</p>
            <h2 class="pb-section-title">Detail Acara</h2>
            <div class="pb-acara-list">
                @forelse ($availableSchedules as $schedule)
                    <article class="pb-acara-card">
                        <div class="pb-tag">{{ $schedule->label }}</div>
                        <h3 class="pb-card-title">{{ $schedule->venue_name }}</h3>
                        <p class="pb-card-meta">{{ \Illuminate\Support\Carbon::parse($schedule->date)->locale($carbonLocale)->translatedFormat('l, d F Y') }}</p>
                        <p class="pb-card-meta">{{ $schedule->start_time ?: '--:--' }} - {{ $schedule->end_time ?: '--:--' }} WIB</p>
                        <p class="pb-card-copy" style="margin-top:16px;">{{ $schedule->address }}</p>
                        @if ($schedule->maps_url)
                            <div style="margin-top:20px;">
                                <a href="{{ $schedule->maps_url }}" target="_blank" rel="noreferrer" class="pb-btn-secondary">{{ $labels['map_button'] }}</a>
                            </div>
                        @endif
                    </article>
                @empty
                    <article class="pb-acara-card">
                        <p class="pb-empty">Sesi acara untuk link ini belum disiapkan.</p>
                    </article>
                @endforelse
            </div>
        </section>

        <div class="pb-strip">06 · Story</div>
        <section class="pb-story">
            <p class="pb-section-label">Invitation Story</p>
            <h2 class="pb-story-title">Cerita Perayaan</h2>
            <article class="pb-story-card">
                <p class="pb-story-copy">{{ $event->content?->invitation_text ?: $defaults['opening_text'] }}</p>
                <p class="pb-story-copy" style="margin-top:14px;">{{ $event->content?->closing_text ?: $defaults['closing_text'] }}</p>
            </article>
        </section>

        @if ($event->albums->count() && $event->albums->first()?->photos->count())
            <div class="pb-strip">07 · Gallery</div>
            <section class="pb-gallery">
                <div class="pb-gallery-header">
                    <p class="pb-section-label">{{ $labels['album_label'] }}</p>
                    <h2 class="pb-section-title">Gallery</h2>
                </div>
                <div class="pb-gallery-grid">
                    @foreach ($event->albums->flatMap->photos->take(6) as $photo)
                        <div class="pb-gallery-item">
                            <img src="{{ asset('storage/'.$photo->image_path) }}" alt="Album photo" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($event->is_rsvp_enabled)
            <div class="pb-strip">08 · RSVP</div>
            <section id="rsvp" class="pb-rsvp">
                <div class="pb-rsvp-content">
                    <p class="pb-section-label" style="color: var(--pb-lilac);">{{ $labels['rsvp_label'] }}</p>
                    <h2 class="pb-rsvp-title">{{ $labels['rsvp_title'] }}</h2>
                    <p class="pb-rsvp-sub">{{ $guest ? $labels['guest_intro_personal'] : $labels['guest_intro_general'] }}</p>
                    <form method="POST" action="{{ $guest ? route('public.rsvp.personal', [$event, request()->route('guestToken')]) : route('public.rsvp.general', $event) }}">
                        @csrf
                        @unless($guest)
                            <input class="pb-rsvp-field" name="name" placeholder="{{ $labels['rsvp_name_placeholder'] }}" required>
                            <input class="pb-rsvp-field" name="phone" placeholder="{{ $labels['rsvp_phone_placeholder'] }}">
                        @endunless
                        @if ($availableSchedules->isEmpty())
                            <div class="pb-rsvp-card">RSVP belum dapat diproses karena sesi acara belum dipetakan untuk link ini.</div>
                        @elseif ($availableSchedules->count() === 1)
                            <input type="hidden" name="event_schedule_id" value="{{ $availableSchedules->first()->id }}">
                            <div class="pb-rsvp-card">
                                <strong>{{ $availableSchedules->first()->label }}</strong><br>
                                {{ \Illuminate\Support\Carbon::parse($availableSchedules->first()->date)->locale($carbonLocale)->translatedFormat('d F Y') }}
                            </div>
                        @else
                            <select class="pb-rsvp-field" name="event_schedule_id" required>
                                <option value="">Pilih sesi acara</option>
                                @foreach ($availableSchedules as $schedule)
                                    <option value="{{ $schedule->id }}">{{ $schedule->label }} · {{ \Illuminate\Support\Carbon::parse($schedule->date)->locale($carbonLocale)->translatedFormat('d F Y') }}</option>
                                @endforeach
                            </select>
                        @endif
                        <select class="pb-rsvp-field" name="status" required>
                            <option value="hadir">{{ $labels['rsvp_status_labels']['hadir'] }}</option>
                            <option value="tidak_hadir">{{ $labels['rsvp_status_labels']['tidak_hadir'] }}</option>
                            <option value="ragu">{{ $labels['rsvp_status_labels']['ragu'] }}</option>
                        </select>
                        <input class="pb-rsvp-field" name="pax_count" type="number" min="1" max="{{ $guest?->max_pax ?? 10 }}" value="1" required>
                        <textarea class="pb-rsvp-field" name="message" placeholder="{{ $labels['rsvp_message_placeholder'] }}"></textarea>
                        <button class="pb-btn" type="submit" @disabled($availableSchedules->isEmpty())>{{ $labels['rsvp_submit'] }}</button>
                    </form>
                </div>
            </section>
        @endif

        @if ($event->is_comment_enabled)
            <div class="pb-strip">09 · Wishes</div>
            <section class="pb-wishes">
                <p class="pb-section-label">{{ $labels['comments_label'] }}</p>
                <h2 class="pb-wishes-title">{{ $labels['comments_title'] }}</h2>
                <form method="POST" action="{{ $guest ? route('public.comment.personal', [$event, request()->route('guestToken')]) : route('public.comment.general', $event) }}" class="pb-comment-form">
                    @csrf
                    @unless($guest)
                        <input name="name" placeholder="{{ $labels['comments_name_placeholder'] }}" required>
                    @endunless
                    <input type="hidden" name="website" value="">
                    <textarea name="message" placeholder="{{ $labels['comments_message_placeholder'] }}" required></textarea>
                    <button class="pb-btn-secondary" type="submit" style="width: 100%; margin-bottom: 16px;">{{ $labels['comments_submit'] }}</button>
                </form>
                <div class="pb-comment-list">
                    @forelse ($comments as $comment)
                        <article class="pb-wish-card">
                            <div class="pb-wish-name">{{ $comment->name_snapshot }}</div>
                            <div class="pb-wish-copy">{{ $comment->message }}</div>
                        </article>
                    @empty
                        <p class="pb-empty">{{ $labels['comments_empty'] }}</p>
                    @endforelse
                </div>
            </section>
        @endif

        @if ($event->is_gift_enabled)
            <div class="pb-strip">10 · Gift</div>
            <section class="pb-gift">
                <p class="pb-section-label">{{ $labels['gift_label'] }}</p>
                <h2 class="pb-gift-title">{{ $labels['gift_title'] }}</h2>
                @if (($event->giftSetting?->mode ?? 'no_gift') === 'no_gift')
                    <p class="pb-gift-copy">{{ $event->giftSetting?->no_gift_message ?: ($event->content?->no_gift_message ?: $defaults['no_gift_message']) }}</p>
                @else
                    <p class="pb-gift-copy">{{ $event->giftSetting?->instructions ?: $defaults['gift_instructions'] }}</p>
                    <div class="pb-gift-list" style="margin-top:24px;">
                        <article class="pb-gift-card">
                            <div>
                                <p class="pb-gift-bank">{{ $event->giftSetting?->bank_name }}</p>
                                <p class="pb-gift-account">{{ $event->giftSetting?->account_number }}</p>
                                <p class="pb-card-copy">{{ $event->giftSetting?->account_holder }}</p>
                            </div>
                            <span class="pb-pill">Transfer</span>
                        </article>
                    </div>
                    @if ($guest)
                        <div style="margin-top:24px;">
                            <a href="{{ route('public.gift.show', [$event, request()->route('guestToken')]) }}" class="pb-btn-secondary">{{ $labels['gift_confirm'] }}</a>
                        </div>
                    @endif
                @endif
            </section>
        @endif

        @if ($event->musicAsset?->resolved_url)
            <div class="pb-strip">11 · Music</div>
            <section class="pb-gift" style="background: var(--pb-blush);">
                <p class="pb-section-label">{{ $labels['music_label'] }}</p>
                <h2 class="pb-gift-title">{{ $labels['music_title'] }}</h2>
                <article class="pb-music-card">
                    <h3 class="pb-card-title" style="font-size: 24px;">{{ $event->musicAsset->title }}</h3>
                    @if ($event->musicAsset->artist)
                        <p class="pb-card-copy">{{ $event->musicAsset->artist }}</p>
                    @endif
                    <audio controls preload="none">
                        <source src="{{ $event->musicAsset->resolved_url }}">
                    </audio>
                </article>
            </section>
        @endif

        @if ($guest && $invitation)
            <div class="pb-strip">12 · Check-in QR</div>
            <section class="pb-qr-section">
                <p class="pb-section-label">{{ $labels['qr_label'] }}</p>
                <h2 class="pb-qr-title">{{ $labels['qr_title'] }}</h2>
                <article class="pb-qr-card">
                    <div id="guest-qr" class="pb-qr-box">{{ $invitation->checkin_url_cached }}</div>
                    <p class="pb-qr-note" style="margin-top: 14px;">{{ $labels['qr_note'] }}</p>
                </article>
            </section>
        @endif

        <div class="pb-strip">13 · Closing</div>
        <section class="pb-closing">
            <div class="pb-closing-content">
                <p class="pb-closing-copy">{{ $event->content?->closing_text ?: $defaults['closing_text'] }}</p>
                <div class="pb-closing-name">{{ $event->bride_name }} &amp; {{ $event->groom_name }}</div>
                <div class="pb-credit">Invitely by NechCode</div>
            </div>
        </section>
    </div>
</div>
