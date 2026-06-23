<style>
    :root {
        --cg-cream: #F9F5EE;
        --cg-ivory: #EDE7D9;
        --cg-gold: #B8975A;
        --cg-gold-light: #D4B07A;
        --cg-dark: #2A2118;
        --cg-mid: #5C4A32;
        --cg-soft: #9E8A72;
        --cg-white: #FFFFFF;
        --cg-display: 'Cormorant Garamond', serif;
        --cg-body: 'Jost', sans-serif;
        --cg-frame: 390px;
    }

    .cg-template *, .cg-template *::before, .cg-template *::after { box-sizing: border-box; }
    .cg-template {
        margin: 0;
        background: #e8e4de;
        color: var(--cg-dark);
        font-family: var(--cg-body);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 24px 0 48px;
    }

    .cg-frame {
        width: 100%;
        max-width: var(--cg-frame);
        overflow: hidden;
        box-shadow: 0 20px 80px rgba(0, 0, 0, .18);
    }

    .cg-strip {
        background: var(--cg-gold);
        padding: 6px 16px;
        color: var(--cg-white);
        font-size: 9px;
        letter-spacing: .25em;
        text-transform: uppercase;
        text-align: center;
        font-weight: 400;
    }

    .cg-section-label {
        font-size: 10px;
        letter-spacing: .25em;
        text-transform: uppercase;
        color: var(--cg-gold);
        font-weight: 300;
    }

    .cg-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin: 16px auto;
        width: fit-content;
    }

    .cg-divider::before,
    .cg-divider::after {
        content: '';
        display: block;
        width: 40px;
        height: 1px;
        background: var(--cg-gold);
        opacity: .5;
    }

    .cg-divider-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--cg-gold);
    }

    .cg-hero {
        position: relative;
        min-height: 100svh;
        background: linear-gradient(160deg, #3B2F1E 0%, #1E140A 60%, #2A1C0D 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px 32px;
        overflow: hidden;
    }

    .cg-corner {
        position: absolute;
        width: 90px;
        height: 90px;
        opacity: .35;
        color: var(--cg-gold);
        font-size: 70px;
        line-height: 1;
    }

    .cg-corner.tl { top: 16px; left: 16px; }
    .cg-corner.tr { top: 16px; right: 16px; transform: rotate(90deg); }
    .cg-corner.bl { bottom: 16px; left: 16px; transform: rotate(270deg); }
    .cg-corner.br { bottom: 16px; right: 16px; transform: rotate(180deg); }

    .cg-hero-content,
    .cg-rsvp-content,
    .cg-closing-content,
    .cg-countdown-content { position: relative; z-index: 1; }

    .cg-hero-invite,
    .cg-hero-date,
    .cg-scroll-label,
    .cg-card-meta,
    .cg-rsvp-sub,
    .cg-closing-thanks,
    .cg-credit {
        font-family: var(--cg-body);
    }

    .cg-hero-invite {
        font-weight: 200;
        font-size: 11px;
        letter-spacing: .3em;
        text-transform: uppercase;
        color: var(--cg-gold-light);
        margin-bottom: 20px;
    }

    .cg-hero-to {
        font-weight: 300;
        font-size: 11px;
        letter-spacing: .15em;
        color: rgba(255,255,255,.6);
        margin-bottom: 6px;
    }

    .cg-hero-guest {
        font-family: var(--cg-display);
        font-size: 22px;
        font-weight: 400;
        color: var(--cg-white);
        margin-bottom: 32px;
    }

    .cg-hero-names {
        font-family: var(--cg-display);
        font-size: 52px;
        font-weight: 300;
        font-style: italic;
        color: var(--cg-white);
        line-height: 1.1;
    }

    .cg-hero-names span,
    .cg-amp,
    .cg-countdown-num,
    .cg-closing-salut { color: var(--cg-gold-light); }

    .cg-amp {
        display: block;
        margin: 4px 0;
        font-size: 36px;
    }

    .cg-hero-date {
        font-weight: 300;
        font-size: 12px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: rgba(255,255,255,.7);
        margin-top: 28px;
    }

    .cg-scroll {
        position: absolute;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }

    .cg-scroll-label {
        font-size: 9px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: rgba(255,255,255,.4);
    }

    .cg-scroll-line {
        width: 1px;
        height: 32px;
        background: linear-gradient(to bottom, var(--cg-gold), transparent);
    }

    .cg-quote,
    .cg-acara,
    .cg-gallery,
    .cg-wishes,
    .cg-gift,
    .cg-qr-section { background: var(--cg-ivory); }

    .cg-quote,
    .cg-couple,
    .cg-story,
    .cg-gift,
    .cg-wishes,
    .cg-qr-section { padding: 56px 32px; text-align: center; }

    .cg-quote-arabic,
    .cg-section-title,
    .cg-card-title,
    .cg-couple-name,
    .cg-story-title,
    .cg-rsvp-title,
    .cg-gift-title,
    .cg-wishes-title,
    .cg-closing-names,
    .cg-qr-title,
    .cg-countdown-heading {
        font-family: var(--cg-display);
    }

    .cg-quote-arabic {
        font-size: 22px;
        font-weight: 400;
        color: var(--cg-mid);
        line-height: 2;
        margin: 20px 0;
    }

    .cg-quote-copy,
    .cg-story-body,
    .cg-gift-copy,
    .cg-empty,
    .cg-wish-copy,
    .cg-card-copy,
    .cg-comment-form textarea,
    .cg-comment-form input,
    .cg-rsvp-field,
    .cg-caption,
    .cg-qr-note {
        font-size: 13px;
        font-weight: 300;
        line-height: 1.8;
    }

    .cg-quote-copy,
    .cg-caption,
    .cg-empty,
    .cg-qr-note,
    .cg-card-copy,
    .cg-wish-copy { color: var(--cg-soft); }

    .cg-section-title,
    .cg-story-title,
    .cg-countdown-heading,
    .cg-rsvp-title,
    .cg-gift-title,
    .cg-wishes-title,
    .cg-qr-title {
        font-size: 36px;
        font-weight: 300;
        font-style: italic;
        color: var(--cg-dark);
        margin-bottom: 12px;
    }

    .cg-countdown,
    .cg-rsvp,
    .cg-closing {
        position: relative;
        padding: 64px 32px;
        text-align: center;
        overflow: hidden;
        background: var(--cg-dark);
    }

    .cg-countdown { background: linear-gradient(135deg, #2A1C0D 0%, #1A1208 100%); }
    .cg-rsvp { background: linear-gradient(160deg, #2A1C0D 0%, #1A0F08 100%); }
    .cg-closing { background: linear-gradient(160deg, #1E140A 0%, #0F0A05 100%); padding: 80px 32px 56px; }

    .cg-countdown-heading,
    .cg-rsvp-title,
    .cg-closing-names,
    .cg-qr-title,
    .cg-rsvp-sub,
    .cg-closing-thanks,
    .cg-credit,
    .cg-countdown-sub { color: var(--cg-white); }

    .cg-countdown-sub,
    .cg-rsvp-sub {
        font-size: 11px;
        letter-spacing: .15em;
        text-transform: uppercase;
        margin-bottom: 34px;
        color: rgba(255,255,255,.48);
    }

    .cg-countdown-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 32px;
    }

    .cg-countdown-unit,
    .cg-rsvp-card,
    .cg-wish-card,
    .cg-gift-card,
    .cg-acara-card,
    .cg-couple-card,
    .cg-story-card,
    .cg-music-card,
    .cg-comment-card,
    .cg-qr-card {
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(184,151,90,.2);
    }

    .cg-countdown-unit {
        padding: 16px 8px;
    }

    .cg-countdown-num {
        font-size: 36px;
        font-weight: 300;
        line-height: 1;
    }

    .cg-countdown-label {
        font-size: 9px;
        letter-spacing: .15em;
        text-transform: uppercase;
        color: rgba(255,255,255,.4);
        margin-top: 6px;
    }

    .cg-btn,
    .cg-btn-secondary,
    .cg-btn-gold {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s ease, color .2s ease, border-color .2s ease, opacity .2s ease;
    }

    .cg-btn-gold,
    .cg-btn-secondary {
        border: 1px solid var(--cg-gold);
        color: var(--cg-gold);
        padding: 13px 28px;
        font-size: 11px;
        letter-spacing: .2em;
        text-transform: uppercase;
        background: transparent;
    }

    .cg-btn {
        width: 100%;
        border: none;
        background: var(--cg-gold);
        color: var(--cg-white);
        padding: 16px;
        font-size: 11px;
        letter-spacing: .2em;
        text-transform: uppercase;
    }

    .cg-couple {
        background: var(--cg-cream);
    }

    .cg-couple-grid,
    .cg-comment-grid,
    .cg-actions {
        display: grid;
        gap: 16px;
    }

    .cg-couple-grid { margin-top: 28px; }

    .cg-couple-card,
    .cg-story-card,
    .cg-comment-card,
    .cg-music-card,
    .cg-qr-card {
        background: var(--cg-white);
        border: 1px solid var(--cg-ivory);
        padding: 24px;
        text-align: left;
    }

    .cg-couple-role,
    .cg-card-meta,
    .cg-gift-bank,
    .cg-qr-note {
        font-size: 10px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: var(--cg-gold);
        font-weight: 500;
    }

    .cg-couple-name {
        font-size: 32px;
        font-weight: 400;
        color: var(--cg-dark);
        margin: 8px 0;
    }

    .cg-couple-parent { color: var(--cg-soft); }

    .cg-acara {
        padding: 56px 24px;
        text-align: center;
    }

    .cg-acara-list,
    .cg-gallery-grid,
    .cg-wishes-list,
    .cg-gift-list,
    .cg-comment-list { display: flex; flex-direction: column; gap: 16px; }

    .cg-acara-card,
    .cg-gift-card,
    .cg-wish-card {
        background: var(--cg-white);
        border: 1px solid var(--cg-ivory);
        padding: 28px 24px;
        position: relative;
    }

    .cg-acara-tag {
        position: absolute;
        top: -11px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--cg-gold);
        color: var(--cg-white);
        font-size: 9px;
        letter-spacing: .2em;
        text-transform: uppercase;
        padding: 4px 16px;
    }

    .cg-card-title {
        font-size: 26px;
        font-weight: 400;
        color: var(--cg-dark);
        margin-bottom: 12px;
    }

    .cg-story {
        background: var(--cg-cream);
    }

    .cg-story-card {
        margin-top: 24px;
    }

    .cg-story-body p + p {
        margin-top: 16px;
    }

    .cg-gallery { padding: 56px 0; text-align: center; }

    .cg-gallery-header { padding: 0 32px 32px; }

    .cg-gallery-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3px;
        padding: 0 3px;
    }

    .cg-gallery-item {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        background: linear-gradient(135deg, #C9B49A 0%, #8C6A4A 100%);
    }

    .cg-gallery-item:first-child {
        grid-column: span 2;
        aspect-ratio: 16 / 9;
    }

    .cg-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .cg-gallery-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: rgba(255,255,255,.5);
        font-family: var(--cg-display);
        font-style: italic;
    }

    .cg-rsvp-field,
    .cg-comment-form input,
    .cg-comment-form textarea {
        width: 100%;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(184,151,90,.2);
        padding: 14px 16px;
        color: var(--cg-white);
        outline: none;
        margin-bottom: 12px;
        font-family: var(--cg-body);
    }

    .cg-rsvp-field::placeholder,
    .cg-comment-form input::placeholder,
    .cg-comment-form textarea::placeholder {
        color: rgba(255,255,255,.25);
    }

    .cg-comment-form input,
    .cg-comment-form textarea {
        background: var(--cg-white);
        color: var(--cg-dark);
        border-color: var(--cg-ivory);
    }

    .cg-comment-form textarea { min-height: 110px; resize: vertical; }

    .cg-rsvp-card {
        padding: 18px;
        margin-bottom: 16px;
        text-align: left;
        color: rgba(255,255,255,.78);
    }

    .cg-music-card audio { width: 100%; margin-top: 16px; }

    .cg-gift-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        text-align: left;
    }

    .cg-gift-account {
        font-family: var(--cg-display);
        font-size: 20px;
        color: var(--cg-dark);
    }

    .cg-copy-note {
        padding: 8px 14px;
        background: var(--cg-ivory);
        border: 1px solid var(--cg-gold);
        color: var(--cg-gold);
        font-size: 9px;
        letter-spacing: .15em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .cg-wish-card {
        text-align: left;
        border-left: 3px solid var(--cg-gold);
    }

    .cg-wish-name {
        font-family: var(--cg-display);
        font-size: 18px;
        font-style: italic;
        color: var(--cg-mid);
        margin-bottom: 6px;
    }

    .cg-qr-card {
        text-align: center;
    }

    .cg-qr-box {
        margin: 24px auto 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 224px;
        height: 224px;
        padding: 16px;
        border: 1px dashed var(--cg-gold);
        background: var(--cg-cream);
        color: var(--cg-soft);
        font-size: 11px;
        text-align: center;
    }

    .cg-closing-thanks {
        font-size: 12px;
        line-height: 1.8;
        color: rgba(255,255,255,.45);
        margin-bottom: 28px;
    }

    .cg-closing-names {
        font-size: 52px;
        font-weight: 300;
        font-style: italic;
        line-height: 1.1;
    }

    .cg-credit {
        margin-top: 40px;
        font-size: 10px;
        color: rgba(255,255,255,.24);
        letter-spacing: .1em;
    }

    @media (min-width: 500px) {
        .cg-template {
            padding-top: 40px;
        }
    }

    @media (min-width: 960px) {
        :root {
            --cg-frame: 1080px;
        }

        .cg-template {
            padding: 40px 0 72px;
        }

        .cg-hero {
            min-height: 840px;
            padding: 64px;
        }

        .cg-quote,
        .cg-couple,
        .cg-story,
        .cg-rsvp,
        .cg-gift,
        .cg-wishes {
            padding-left: 56px;
            padding-right: 56px;
        }

        .cg-acara-list,
        .cg-gallery-grid,
        .cg-gift-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="cg-template">
    <div class="cg-frame">
        <div class="cg-strip">01 · Hero / Opening</div>
        <section class="cg-hero">
            <div class="cg-corner tl">❦</div>
            <div class="cg-corner tr">❦</div>
            <div class="cg-corner bl">❦</div>
            <div class="cg-corner br">❦</div>

            <div class="cg-hero-content">
                <p class="cg-hero-invite">{{ $labels['hero_kicker'] }}</p>
                <p class="cg-hero-to">{{ $guest ? $labels['guest_label_personal'] : $labels['guest_label_general'] }}</p>
                <p class="cg-hero-guest">{{ $guest?->name ?? $labels['guest_name_fallback'] }}</p>
                <div class="cg-divider"><div class="cg-divider-dot"></div></div>
                <div class="cg-hero-names">
                    {{ $event->bride_name }}<br>
                    <span class="cg-amp">&</span>
                    <span>{{ $event->groom_name }}</span>
                </div>
                @if ($firstSchedule?->date)
                    <p class="cg-hero-date">{{ \Illuminate\Support\Carbon::parse($firstSchedule->date)->locale($carbonLocale)->translatedFormat('d F Y') }}</p>
                @endif
            </div>

            <div class="cg-scroll">
                <span class="cg-scroll-label">Scroll</span>
                <div class="cg-scroll-line"></div>
            </div>
        </section>

        <div class="cg-strip">02 · Opening Copy</div>
        <section class="cg-quote">
            <p class="cg-section-label">{{ $labels['hero_kicker'] }}</p>
            <div class="cg-divider"><div class="cg-divider-dot"></div></div>
            <p class="cg-quote-arabic">بِسْمِ ٱللَّٰهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</p>
            <p class="cg-quote-copy">{{ $event->content?->opening_text ?: $defaults['opening_text'] }}</p>
        </section>

        <div class="cg-strip">03 · Couple Profile</div>
        <section class="cg-couple">
            <p class="cg-section-label">{{ $labels['couple_label'] }}</p>
            <h2 class="cg-section-title">{{ $event->couple_name_display }}</h2>
            <div class="cg-couple-grid">
                <article class="cg-couple-card">
                    <p class="cg-couple-role">Bride</p>
                    <h3 class="cg-couple-name">{{ $event->bride_name }}</h3>
                    <p class="cg-couple-parent">{{ $event->content?->bride_bio ?: 'Profil mempelai wanita akan tampil di sini.' }}</p>
                </article>
                <article class="cg-couple-card">
                    <p class="cg-couple-role">Groom</p>
                    <h3 class="cg-couple-name">{{ $event->groom_name }}</h3>
                    <p class="cg-couple-parent">{{ $event->content?->groom_bio ?: 'Profil mempelai pria akan tampil di sini.' }}</p>
                </article>
            </div>
        </section>

        @if ($countdownIso)
            <div class="cg-strip">04 · Countdown</div>
            <section class="cg-countdown">
                <div class="cg-countdown-content">
                    <h2 class="cg-countdown-heading">{{ $labels['countdown_label'] }}</h2>
                    <p class="cg-countdown-sub">Menuju hari perayaan yang sudah disiapkan</p>
                    <div class="cg-countdown-grid" data-countdown="{{ $countdownIso }}">
                        @foreach ($labels['countdown_units'] as $unitLabel)
                            <div class="cg-countdown-unit">
                                <div class="cg-countdown-num" data-countdown-value>0</div>
                                <div class="cg-countdown-label">{{ $unitLabel }}</div>
                            </div>
                        @endforeach
                    </div>
                    @if ($firstSchedule?->maps_url)
                        <a href="{{ $firstSchedule->maps_url }}" target="_blank" rel="noreferrer" class="cg-btn-gold">{{ $labels['map_button'] }}</a>
                    @endif
                </div>
            </section>
        @endif

        <div class="cg-strip">05 · Event Details</div>
        <section class="cg-acara">
            <p class="cg-section-label">{{ $labels['schedule_label'] }}</p>
            <h2 class="cg-section-title">Detail Acara</h2>
            <div class="cg-acara-list">
                @forelse ($availableSchedules as $schedule)
                    <article class="cg-acara-card">
                        <div class="cg-acara-tag">{{ $schedule->label }}</div>
                        <h3 class="cg-card-title">{{ $schedule->venue_name }}</h3>
                        <p class="cg-card-meta">{{ \Illuminate\Support\Carbon::parse($schedule->date)->locale($carbonLocale)->translatedFormat('l, d F Y') }}</p>
                        <p class="cg-card-meta" style="margin-top:6px;">{{ $schedule->start_time ?: '--:--' }} - {{ $schedule->end_time ?: '--:--' }} WIB</p>
                        <p class="cg-card-copy" style="margin-top:16px;">{{ $schedule->address }}</p>
                        @if ($schedule->maps_url)
                            <div style="margin-top:20px;">
                                <a href="{{ $schedule->maps_url }}" target="_blank" rel="noreferrer" class="cg-btn-secondary">{{ $labels['map_button'] }}</a>
                            </div>
                        @endif
                        @if (filled($schedule->latitude) && filled($schedule->longitude))
                            <div class="invitation-map-card">
                                <div class="invitation-map-frame" data-invitation-map data-lat="{{ $schedule->latitude }}" data-lng="{{ $schedule->longitude }}"></div>
                            </div>
                        @endif
                    </article>
                @empty
                    <article class="cg-acara-card">
                        <p class="cg-empty">Sesi acara untuk link ini belum disiapkan.</p>
                    </article>
                @endforelse
            </div>
        </section>

        <div class="cg-strip">06 · Invitation Story</div>
        <section class="cg-story">
            <p class="cg-section-label">Invitation Copy</p>
            <h2 class="cg-story-title">Cerita Undangan</h2>
            <article class="cg-story-card">
                <div class="cg-story-body">
                    <p>{{ $event->content?->invitation_text ?: $defaults['opening_text'] }}</p>
                    <p>{{ $event->content?->closing_text ?: $defaults['closing_text'] }}</p>
                </div>
            </article>
        </section>

        @if ($event->albums->count() && $event->albums->first()?->photos->count())
            <div class="cg-strip">07 · Gallery</div>
            <section class="cg-gallery">
                <div class="cg-gallery-header">
                    <p class="cg-section-label">{{ $labels['album_label'] }}</p>
                    <h2 class="cg-section-title">Gallery</h2>
                </div>
                <div class="cg-gallery-grid">
                    @foreach ($event->albums->flatMap->photos->take(6) as $photo)
                        <div class="cg-gallery-item">
                            <img src="{{ asset('storage/'.$photo->image_path) }}" alt="Album photo" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($event->is_rsvp_enabled)
            <div class="cg-strip">08 · RSVP</div>
            <section id="rsvp" class="cg-rsvp">
                <div class="cg-rsvp-content">
                    <p class="cg-section-label" style="color: var(--cg-gold-light);">{{ $labels['rsvp_label'] }}</p>
                    <h2 class="cg-rsvp-title">{{ $labels['rsvp_title'] }}</h2>
                    <p class="cg-rsvp-sub">{{ $guest ? $labels['guest_intro_personal'] : $labels['guest_intro_general'] }}</p>
                    <form method="POST" action="{{ $guest ? route('public.rsvp.personal', [$event, request()->route('guestToken')]) : route('public.rsvp.general', $event) }}">
                        @csrf
                        @unless($guest)
                            <input class="cg-rsvp-field" name="name" placeholder="{{ $labels['rsvp_name_placeholder'] }}" required>
                            <input class="cg-rsvp-field" name="phone" placeholder="{{ $labels['rsvp_phone_placeholder'] }}">
                        @endunless
                        @if ($availableSchedules->isEmpty())
                            <div class="cg-rsvp-card">RSVP belum dapat diproses karena sesi acara belum dipetakan untuk link ini.</div>
                        @elseif ($availableSchedules->count() === 1)
                            <input type="hidden" name="event_schedule_id" value="{{ $availableSchedules->first()->id }}">
                            <div class="cg-rsvp-card">
                                <strong>{{ $availableSchedules->first()->label }}</strong><br>
                                {{ \Illuminate\Support\Carbon::parse($availableSchedules->first()->date)->locale($carbonLocale)->translatedFormat('d F Y') }}
                            </div>
                        @else
                            <select class="cg-rsvp-field" name="event_schedule_id" required>
                                <option value="">Pilih sesi acara</option>
                                @foreach ($availableSchedules as $schedule)
                                    <option value="{{ $schedule->id }}">{{ $schedule->label }} · {{ \Illuminate\Support\Carbon::parse($schedule->date)->locale($carbonLocale)->translatedFormat('d F Y') }}</option>
                                @endforeach
                            </select>
                        @endif
                        <select class="cg-rsvp-field" name="status" required>
                            <option value="hadir">{{ $labels['rsvp_status_labels']['hadir'] }}</option>
                            <option value="tidak_hadir">{{ $labels['rsvp_status_labels']['tidak_hadir'] }}</option>
                            <option value="ragu">{{ $labels['rsvp_status_labels']['ragu'] }}</option>
                        </select>
                        <input class="cg-rsvp-field" name="pax_count" type="number" min="1" max="{{ $guest?->max_pax ?? 10 }}" value="1" required>
                        <textarea class="cg-rsvp-field" name="message" placeholder="{{ $labels['rsvp_message_placeholder'] }}"></textarea>
                        <button class="cg-btn" type="submit" @disabled($availableSchedules->isEmpty())>{{ $labels['rsvp_submit'] }}</button>
                    </form>
                </div>
            </section>
        @endif

        @if ($event->is_gift_enabled)
            <div class="cg-strip">09 · Gift</div>
            <section class="cg-gift">
                <p class="cg-section-label">{{ $labels['gift_label'] }}</p>
                <h2 class="cg-gift-title">{{ $labels['gift_title'] }}</h2>
                @if (($event->giftSetting?->mode ?? 'no_gift') === 'no_gift')
                    <p class="cg-gift-copy">{{ $event->giftSetting?->no_gift_message ?: ($event->content?->no_gift_message ?: $defaults['no_gift_message']) }}</p>
                @else
                    <p class="cg-gift-copy">{{ $event->giftSetting?->instructions ?: $defaults['gift_instructions'] }}</p>
                    <div class="cg-gift-list" style="margin-top:24px;">
                        <article class="cg-gift-card">
                            <div>
                                <p class="cg-gift-bank">{{ $event->giftSetting?->bank_name }}</p>
                                <p class="cg-gift-account">{{ $event->giftSetting?->account_number }}</p>
                                <p class="cg-card-copy">{{ $event->giftSetting?->account_holder }}</p>
                            </div>
                            <span class="cg-copy-note">Transfer</span>
                        </article>
                    </div>
                    @if (file_exists(public_path('qris.jpeg')))
                        <div class="cg-gift-list" style="margin-top:16px;">
                            <article class="cg-gift-card">
                                <div class="w-full">
                                    <p class="cg-gift-bank">QRIS Preview</p>
                                    <img src="{{ asset('qris.jpeg') }}" alt="QRIS pembayaran" style="margin-top:12px;width:100%;border-radius:22px;border:1px solid rgba(184,151,90,.22);">
                                </div>
                            </article>
                        </div>
                    @endif
                    @if ($guest)
                        <div style="margin-top:24px;">
                            <a href="{{ route('public.gift.show', [$event, request()->route('guestToken')]) }}" class="cg-btn-secondary">Konfirmasi sudah bayar</a>
                        </div>
                    @endif
                @endif
            </section>
        @endif

        @if ($event->musicAsset?->resolved_url)
            <div class="cg-strip">10 · Music</div>
            <section class="cg-gift" style="background: var(--cg-cream);">
                <p class="cg-section-label">{{ $labels['music_label'] }}</p>
                <h2 class="cg-gift-title">{{ $labels['music_title'] }}</h2>
                <article class="cg-music-card">
                    <p class="cg-card-title" style="font-size: 24px;">{{ $event->musicAsset->title }}</p>
                    @if ($event->musicAsset->artist)
                        <p class="cg-card-copy">{{ $event->musicAsset->artist }}</p>
                    @endif
                    <p class="cg-card-copy" style="margin-top:16px;">Musik akan berjalan otomatis ketika undangan dibuka. Untuk pengalaman desktop dan mobile yang tetap nyaman, tamu bisa mematikan suara dari tombol mute mengambang.</p>
                </article>
            </section>
        @endif

        @if ($event->is_comment_enabled)
            <div class="cg-strip">11 · Wishes</div>
            <section class="cg-wishes">
                <p class="cg-section-label">{{ $labels['comments_label'] }}</p>
                <h2 class="cg-wishes-title">{{ $labels['comments_title'] }}</h2>
                <form method="POST" action="{{ $guest ? route('public.comment.personal', [$event, request()->route('guestToken')]) : route('public.comment.general', $event) }}" class="cg-comment-form">
                    @csrf
                    @unless($guest)
                        <input name="name" placeholder="{{ $labels['comments_name_placeholder'] }}" required>
                    @endunless
                    <input type="hidden" name="website" value="">
                    <textarea name="message" placeholder="{{ $labels['comments_message_placeholder'] }}" required></textarea>
                    <button class="cg-btn-secondary" type="submit" style="width: 100%; margin-bottom: 16px;">{{ $labels['comments_submit'] }}</button>
                </form>
                <div class="cg-wishes-list">
                    @forelse ($comments as $comment)
                        <article class="cg-wish-card">
                            <div class="cg-wish-name">{{ $comment->name_snapshot }}</div>
                            <div class="cg-wish-copy">{{ $comment->message }}</div>
                        </article>
                    @empty
                        <p class="cg-empty">{{ $labels['comments_empty'] }}</p>
                    @endforelse
                </div>
            </section>
        @endif

        @if ($guest && $invitation)
            <div class="cg-strip">12 · Check-in QR</div>
            <section class="cg-qr-section">
                <p class="cg-section-label">{{ $labels['qr_label'] }}</p>
                <h2 class="cg-qr-title">{{ $labels['qr_title'] }}</h2>
                <article class="cg-qr-card">
                    <div id="guest-qr" class="cg-qr-box">{{ $invitation->checkin_url_cached }}</div>
                    <p class="cg-qr-note" style="margin-top: 16px;">{{ $labels['qr_note'] }}</p>
                </article>
            </section>
        @endif

        <div class="cg-strip">13 · Closing</div>
        <section class="cg-closing">
            <div class="cg-closing-content">
                <p class="cg-closing-thanks">{{ $event->content?->closing_text ?: $defaults['closing_text'] }}</p>
                <p class="cg-closing-salut">{{ $labels['closing_label'] }}</p>
                <div class="cg-closing-names">
                    {{ $event->bride_name }} <span class="cg-amp">&</span> {{ $event->groom_name }}
                </div>
                <div class="cg-credit">Invitely by NechCode</div>
            </div>
        </section>
    </div>
</div>
