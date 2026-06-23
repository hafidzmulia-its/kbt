@php
    $galleryPhotos = $event->albums->flatMap->photos->take(9)->values();
    $heroPhoto = $galleryPhotos->first();
    $secondaryPhotos = $galleryPhotos->slice(1, 6)->values();
    $primarySchedule = $availableSchedules->first();
@endphp

<style>
    :root {
        --ge-bg: #f9f5ef;
        --ge-bg-soft: #fdf9f4;
        --ge-primary: #0f1f3d;
        --ge-primary-soft: #1a3060;
        --ge-accent: #c9b99a;
        --ge-accent-soft: #e8dfd3;
        --ge-muted: #6b7a96;
        --ge-white: #f9f5ef;
        --ge-border: rgba(15, 31, 61, 0.1);
        --ge-display: 'Playfair Display', serif;
        --ge-serif: 'Cormorant Garamond', serif;
        --ge-script: 'Dancing Script', cursive;
        --ge-sans: 'Inter', 'Manrope', sans-serif;
    }

    .ge-template *,
    .ge-template *::before,
    .ge-template *::after {
        box-sizing: border-box;
    }

    .ge-template {
        background: var(--ge-bg);
        color: var(--ge-primary);
        font-family: var(--ge-sans);
    }

    .ge-hero {
        position: relative;
        min-height: 100svh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .ge-hero-media,
    .ge-hero-fallback {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    .ge-hero-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        display: block;
    }

    .ge-hero-fallback {
        background:
            radial-gradient(circle at top right, rgba(201, 185, 154, .24), transparent 30%),
            linear-gradient(180deg, #14284f 0%, #0f1f3d 52%, #13264a 100%);
    }

    .ge-hero-overlay {
        position: absolute;
        inset: 0;
        background:
            linear-gradient(to bottom, rgba(15, 31, 61, .62), rgba(15, 31, 61, .28), rgba(15, 31, 61, .76)),
            linear-gradient(to right, rgba(15, 31, 61, .42), transparent, rgba(15, 31, 61, .22));
    }

    .ge-hero-content {
        position: relative;
        z-index: 1;
        max-width: 56rem;
        margin: 0 auto;
        padding: 2rem 1.5rem;
        text-align: center;
    }

    .ge-kicker {
        color: #e8dfd3;
        letter-spacing: .35em;
        text-transform: uppercase;
        font-size: .75rem;
        font-weight: 300;
        margin: 0 0 1.5rem;
    }

    .ge-title {
        font-family: var(--ge-display);
        color: var(--ge-white);
        font-size: clamp(3rem, 10vw, 5rem);
        font-style: italic;
        font-weight: 400;
        line-height: 1.04;
        margin: 0;
    }

    .ge-title span {
        font-style: normal;
        font-weight: 300;
    }

    .ge-divider {
        width: 4rem;
        height: 1px;
        background: var(--ge-accent);
        margin: 1.5rem auto;
    }

    .ge-script {
        font-family: var(--ge-script);
        color: var(--ge-white);
        font-size: clamp(2.3rem, 7vw, 3.4rem);
        margin: 0;
    }

    .ge-subtitle {
        font-family: var(--ge-serif);
        color: var(--ge-accent);
        font-style: italic;
        font-size: 1.45rem;
        margin: .75rem 0 0;
    }

    .ge-hero-guest {
        margin: 2rem auto 0;
        max-width: 28rem;
        color: rgba(249, 245, 239, .88);
        font-size: .95rem;
        line-height: 1.8;
    }

    .ge-scroll {
        position: absolute;
        bottom: 2.5rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .5rem;
        color: #e8dfd3;
        text-decoration: none;
    }

    .ge-scroll span {
        font-size: .72rem;
        letter-spacing: .3em;
        text-transform: uppercase;
    }

    .ge-scroll-line {
        width: 1px;
        height: 2rem;
        background: linear-gradient(to bottom, var(--ge-accent), transparent);
    }

    .ge-chevron {
        width: .75rem;
        height: .75rem;
        border-right: 1px solid currentColor;
        border-bottom: 1px solid currentColor;
        transform: rotate(45deg);
    }

    .ge-section {
        padding: 6rem 1.5rem;
    }

    .ge-container {
        max-width: 72rem;
        margin: 0 auto;
    }

    .ge-section-header {
        text-align: center;
        margin-bottom: 3.5rem;
    }

    .ge-section-label {
        color: var(--ge-accent);
        letter-spacing: .3em;
        text-transform: uppercase;
        font-size: .75rem;
        font-weight: 300;
        margin: 0 0 .75rem;
    }

    .ge-section-title {
        font-family: var(--ge-display);
        color: var(--ge-primary);
        font-size: clamp(2.4rem, 5vw, 3.6rem);
        font-weight: 400;
        margin: 0;
    }

    .ge-section-divider {
        width: 3rem;
        height: 1px;
        background: var(--ge-accent);
        margin: 1.25rem auto 0;
    }

    .ge-quote-mark {
        color: var(--ge-accent);
        font-family: var(--ge-display);
        font-size: 5rem;
        line-height: .8;
        margin: 0;
        user-select: none;
    }

    .ge-quote-copy {
        margin: -1rem auto 0;
        max-width: 48rem;
        font-family: var(--ge-serif);
        color: var(--ge-primary);
        font-size: clamp(1.6rem, 4vw, 2.25rem);
        font-style: italic;
        line-height: 1.55;
    }

    .ge-quote-footer {
        margin-top: 1.5rem;
        color: var(--ge-muted);
        font-size: .85rem;
        letter-spacing: .25em;
        text-transform: uppercase;
    }

    .ge-gallery-shell {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .ge-gallery-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.5rem;
        box-shadow: 0 16px 40px rgba(15, 31, 61, .12);
        background: linear-gradient(135deg, #d7cab6 0%, #8ca0b9 100%);
        aspect-ratio: 3 / 4;
    }

    .ge-gallery-card.featured {
        aspect-ratio: 16 / 9;
    }

    .ge-gallery-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .ge-gallery-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(15, 31, 61, .28), transparent 55%);
        pointer-events: none;
    }

    .ge-gallery-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: rgba(249,245,239,.75);
        font-family: var(--ge-serif);
        font-style: italic;
        font-size: 1.2rem;
    }

    .ge-event-section {
        background: var(--ge-primary);
    }

    .ge-event-section .ge-section-title,
    .ge-event-section .ge-section-label {
        color: var(--ge-white);
    }

    .ge-event-section .ge-section-label {
        color: var(--ge-accent);
    }

    .ge-event-grid {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }

    .ge-event-card {
        display: flex;
        gap: 1.25rem;
        align-items: flex-start;
        padding: 1.75rem;
        border-radius: 1.5rem;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.1);
    }

    .ge-icon {
        flex-shrink: 0;
        width: 3rem;
        height: 3rem;
        border-radius: .9rem;
        background: rgba(201, 185, 154, .2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--ge-accent);
        font-size: 1.1rem;
        font-weight: 600;
    }

    .ge-event-label {
        color: var(--ge-accent);
        font-size: .72rem;
        letter-spacing: .28em;
        text-transform: uppercase;
        margin-bottom: .35rem;
    }

    .ge-event-value {
        color: var(--ge-white);
        font-family: var(--ge-serif);
        font-size: 1.7rem;
        line-height: 1.25;
        margin: 0;
    }

    .ge-event-copy {
        color: rgba(232, 223, 211, .66);
        font-size: .95rem;
        line-height: 1.6;
        margin-top: .35rem;
    }

    .ge-map-action {
        display: flex;
        justify-content: center;
        margin-top: 2.5rem;
    }

    .ge-outline-btn,
    .ge-primary-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .75rem;
        border-radius: 999px;
        padding: .95rem 2rem;
        text-decoration: none;
        font-size: .82rem;
        letter-spacing: .24em;
        text-transform: uppercase;
        transition: all .25s ease;
        cursor: pointer;
    }

    .ge-outline-btn {
        border: 1px solid var(--ge-accent);
        color: var(--ge-accent);
        background: transparent;
    }

    .ge-primary-btn {
        border: 1px solid var(--ge-primary);
        background: var(--ge-primary);
        color: var(--ge-white);
    }

    .ge-wedding-grid {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        margin-top: 2rem;
    }

    .ge-surface-card {
        background: var(--ge-bg-soft);
        border: 1px solid var(--ge-accent-soft);
        border-radius: 1.75rem;
        padding: 2rem;
        box-shadow: 0 12px 32px rgba(15, 31, 61, .06);
    }

    .ge-surface-title {
        margin: 0 0 .75rem;
        font-family: var(--ge-display);
        color: var(--ge-primary);
        font-size: 2rem;
        font-style: italic;
        font-weight: 400;
    }

    .ge-surface-copy {
        color: var(--ge-muted);
        font-size: 1rem;
        line-height: 1.8;
        margin: 0;
    }

    .ge-feature-stack {
        display: grid;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .ge-feature-pill {
        padding: 1rem 1.1rem;
        border-radius: 1.2rem;
        background: rgba(201, 185, 154, .18);
        color: var(--ge-primary);
        font-size: .95rem;
        line-height: 1.7;
    }

    .ge-form-grid {
        display: grid;
        gap: 2rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        align-items: start;
    }

    .ge-form-card,
    .ge-list-card {
        background: var(--ge-bg-soft);
        border: 1px solid var(--ge-accent-soft);
        border-radius: 2rem;
        padding: 2rem;
        box-shadow: 0 8px 24px rgba(15, 31, 61, .05);
    }

    .ge-form-label {
        display: block;
        color: var(--ge-primary);
        font-size: .72rem;
        letter-spacing: .24em;
        text-transform: uppercase;
        margin-bottom: .75rem;
    }

    .ge-field,
    .ge-select,
    .ge-textarea {
        width: 100%;
        border: 1px solid var(--ge-accent-soft);
        border-radius: 1.2rem;
        background: var(--ge-bg);
        padding: .95rem 1.15rem;
        color: var(--ge-primary);
        font-family: var(--ge-serif);
        font-size: 1.2rem;
        outline: none;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .ge-field:focus,
    .ge-select:focus,
    .ge-textarea:focus {
        border-color: var(--ge-primary);
        box-shadow: 0 0 0 3px rgba(15, 31, 61, .08);
    }

    .ge-textarea {
        min-height: 8.5rem;
        resize: vertical;
    }

    .ge-panel-note {
        color: var(--ge-muted);
        font-size: .82rem;
        letter-spacing: .2em;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }

    .ge-list-scroll {
        max-height: 30rem;
        overflow-y: auto;
        display: grid;
        gap: 1rem;
        padding-right: .35rem;
        scrollbar-width: thin;
        scrollbar-color: var(--ge-accent) var(--ge-bg);
    }

    .ge-wish-card {
        background: var(--ge-bg);
        border: 1px solid var(--ge-accent-soft);
        border-radius: 1.3rem;
        padding: 1.2rem 1.25rem;
    }

    .ge-wish-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: .65rem;
    }

    .ge-wish-user {
        display: flex;
        align-items: center;
        gap: .8rem;
    }

    .ge-wish-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        background: var(--ge-primary);
        color: var(--ge-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--ge-display);
        font-size: .9rem;
    }

    .ge-wish-name {
        color: var(--ge-primary);
        font-size: .92rem;
        font-weight: 600;
    }

    .ge-wish-time {
        color: var(--ge-accent);
        font-size: .74rem;
    }

    .ge-wish-copy {
        padding-left: 2.8rem;
        font-family: var(--ge-serif);
        color: var(--ge-primary);
        font-size: 1.18rem;
        line-height: 1.6;
    }

    .ge-support-grid {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        margin-top: 2rem;
    }

    .ge-audio {
        width: 100%;
        margin-top: 1rem;
    }

    .ge-qr-box {
        margin-top: 1.25rem;
        min-height: 15rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed var(--ge-accent);
        border-radius: 1.5rem;
        background: rgba(201, 185, 154, .12);
        padding: 1rem;
        color: var(--ge-muted);
        text-align: center;
        font-size: .85rem;
    }

    .ge-footer {
        position: relative;
        overflow: hidden;
        background: var(--ge-primary);
        padding: 7rem 1.5rem 4rem;
    }

    .ge-footer::before,
    .ge-footer::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        background: rgba(201, 185, 154, .08);
        filter: blur(12px);
    }

    .ge-footer::before {
        top: -8rem;
        right: -8rem;
        width: 24rem;
        height: 24rem;
    }

    .ge-footer::after {
        bottom: -4rem;
        left: -4rem;
        width: 18rem;
        height: 18rem;
    }

    .ge-footer-content {
        position: relative;
        max-width: 40rem;
        margin: 0 auto;
        text-align: center;
    }

    .ge-footer-kicker {
        color: var(--ge-accent);
        letter-spacing: .3em;
        text-transform: uppercase;
        font-size: .75rem;
        margin-bottom: 1.25rem;
    }

    .ge-footer-title {
        font-family: var(--ge-display);
        color: var(--ge-white);
        font-size: clamp(3rem, 8vw, 4.5rem);
        font-style: italic;
        font-weight: 400;
        line-height: 1.06;
        margin: 0;
    }

    .ge-footer-copy {
        margin: 1.5rem auto 0;
        max-width: 32rem;
        font-family: var(--ge-serif);
        color: rgba(232, 223, 211, .82);
        font-size: 1.4rem;
        font-style: italic;
        line-height: 1.6;
    }

    .ge-footer-signature {
        margin: 2.5rem 0 0;
        font-family: var(--ge-script);
        color: var(--ge-accent);
        font-size: 2.6rem;
    }

    .ge-footer-credit {
        margin-top: 4rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255,255,255,.1);
        color: var(--ge-muted);
        font-size: .72rem;
        letter-spacing: .24em;
        text-transform: uppercase;
    }

    @media (min-width: 768px) {
        .ge-gallery-shell {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .ge-gallery-card.featured {
            grid-column: span 3;
        }

        .ge-event-grid,
        .ge-wedding-grid,
        .ge-support-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ge-form-grid {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        }
    }
</style>

<div class="ge-template">
    <section class="ge-hero">
        @if ($heroPhoto)
            <div class="ge-hero-media">
                <img src="{{ asset('storage/'.$heroPhoto->image_path) }}" alt="{{ $event->couple_name_display }}">
            </div>
        @else
            <div class="ge-hero-fallback"></div>
        @endif
        <div class="ge-hero-overlay"></div>

        <div class="ge-hero-content">
            <p class="ge-kicker">{{ $labels['hero_kicker'] }}</p>
            <h1 class="ge-title">
                {{ $occasion['hero_title_primary'] ?? 'Wedding' }}
                <br>
                <span>{{ $occasion['hero_title_secondary'] ?? 'Celebration' }}</span>
            </h1>
            <div class="ge-divider"></div>
            <p class="ge-script">{{ $event->couple_name_display }}</p>
            @if ($firstSchedule?->date)
                <p class="ge-subtitle">{{ \Illuminate\Support\Carbon::parse($firstSchedule->date)->locale($carbonLocale)->translatedFormat('d F Y') }}</p>
            @endif
            <p class="ge-hero-guest">
                {{ $guest ? $labels['guest_intro_personal'] : $labels['guest_intro_general'] }}
            </p>
        </div>

        <a href="#ge-opening" class="ge-scroll">
            <span>Scroll</span>
            <div class="ge-scroll-line"></div>
            <div class="ge-chevron"></div>
        </a>
    </section>

    <section id="ge-opening" class="ge-section">
        <div class="ge-container">
            <div class="ge-section-header">
                <p class="ge-quote-mark">“</p>
                <blockquote class="ge-quote-copy">
                    {{ $event->content?->opening_text ?: $defaults['opening_text'] }}
                </blockquote>
                <div class="ge-section-divider"></div>
                <p class="ge-quote-footer">Invitely by NechCode</p>
            </div>
        </div>
    </section>

    <section class="ge-section" style="background: #fdf9f4;">
        <div class="ge-container">
            <div class="ge-section-header">
                <p class="ge-section-label">{{ $labels['album_label'] }}</p>
                <h2 class="ge-section-title">Moments Before the Stage</h2>
                <div class="ge-section-divider"></div>
            </div>

            <div class="ge-gallery-shell">
                @if ($galleryPhotos->isNotEmpty())
                    @foreach ($galleryPhotos as $photo)
                        <div class="ge-gallery-card {{ $loop->first ? 'featured' : '' }}">
                            <img src="{{ asset('storage/'.$photo->image_path) }}" alt="Album photo {{ $loop->iteration }}" loading="lazy">
                            <div class="ge-gallery-overlay"></div>
                        </div>
                    @endforeach
                @else
                    <div class="ge-gallery-card featured">
                        <div class="ge-gallery-placeholder">Photo gallery will appear here</div>
                    </div>
                    @for ($i = 0; $i < 4; $i++)
                        <div class="ge-gallery-card">
                            <div class="ge-gallery-placeholder">Memory {{ $i + 1 }}</div>
                        </div>
                    @endfor
                @endif
            </div>
        </div>
    </section>

    <section class="ge-section ge-event-section">
        <div class="ge-container">
            <div class="ge-section-header">
                <p class="ge-section-label">{{ $labels['schedule_label'] }}</p>
                <h2 class="ge-section-title">The Celebration</h2>
                <div class="ge-section-divider"></div>
            </div>

            <div class="ge-event-grid">
                @if ($primarySchedule)
                    <article class="ge-event-card">
                        <div class="ge-icon">D</div>
                        <div>
                            <p class="ge-event-label">Date</p>
                            <p class="ge-event-value">{{ \Illuminate\Support\Carbon::parse($primarySchedule->date)->locale($carbonLocale)->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </article>

                    <article class="ge-event-card">
                        <div class="ge-icon">T</div>
                        <div>
                            <p class="ge-event-label">Time</p>
                            <p class="ge-event-value">{{ $primarySchedule->start_time ?: '--:--' }} - {{ $primarySchedule->end_time ?: '--:--' }} WIB</p>
                        </div>
                    </article>

                    <article class="ge-event-card">
                        <div class="ge-icon">V</div>
                        <div>
                            <p class="ge-event-label">Venue</p>
                            <p class="ge-event-value">{{ $primarySchedule->venue_name }}</p>
                        </div>
                    </article>

                    <article class="ge-event-card">
                        <div class="ge-icon">A</div>
                        <div>
                            <p class="ge-event-label">Address</p>
                            <p class="ge-event-value">{{ $primarySchedule->address }}</p>
                        </div>
                    </article>
                @else
                    <article class="ge-event-card" style="grid-column: 1 / -1;">
                        <div>
                            <p class="ge-event-label">Schedule</p>
                            <p class="ge-event-value">Detail acara belum disiapkan</p>
                            <p class="ge-event-copy">Silakan lengkapi jadwal acara di dashboard client sebelum membagikan link undangan.</p>
                        </div>
                    </article>
                @endif
            </div>

            @if ($primarySchedule?->maps_url)
                <div class="ge-map-action">
                    <a href="{{ $primarySchedule->maps_url }}" target="_blank" rel="noreferrer" class="ge-outline-btn">{{ $labels['map_button'] }}</a>
                </div>
            @endif
        </div>
    </section>

    <section class="ge-section">
        <div class="ge-container">
            <div class="ge-section-header">
                <p class="ge-section-label">{{ $occasion['public_label'] ?? 'Event Overview' }}</p>
                <h2 class="ge-section-title">Everything in One Invitation</h2>
                <div class="ge-section-divider"></div>
            </div>

            <div class="ge-wedding-grid">
                <article class="ge-surface-card">
                    <h3 class="ge-surface-title">{{ $event->bride_name }} &amp; {{ $event->groom_name }}</h3>
                    <p class="ge-surface-copy">{{ $event->content?->invitation_text ?: $defaults['opening_text'] }}</p>
                </article>

                <article class="ge-surface-card">
                    <h3 class="ge-surface-title">Core Features</h3>
                    <div class="ge-feature-stack">
                        <div class="ge-feature-pill">Personal invitation link untuk sapaan tamu yang lebih spesifik.</div>
                        <div class="ge-feature-pill">RSVP, comments, gift confirmation, dan attendance QR tetap aktif di template ini.</div>
                        <div class="ge-feature-pill">Backsound bisa dipilih dari library lagu di `public/music/`.</div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    @if ($countdownIso)
        <section class="ge-section ge-event-section" style="padding-top: 4rem; padding-bottom: 4rem;">
            <div class="ge-container">
                <div class="ge-section-header" style="margin-bottom: 2.5rem;">
                    <p class="ge-section-label">{{ $labels['countdown_label'] }}</p>
                    <h2 class="ge-section-title">Countdown</h2>
                </div>

                <div class="ge-event-grid" data-countdown="{{ $countdownIso }}">
                    @foreach ($labels['countdown_units'] as $unitLabel)
                        <article class="ge-event-card" style="align-items:center;">
                            <div>
                                <p class="ge-event-label">{{ $unitLabel }}</p>
                                <p class="ge-event-value" data-countdown-value>0</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="ge-section" id="rsvp">
        <div class="ge-container">
            <div class="ge-section-header">
                <p class="ge-section-label">{{ $labels['comments_label'] }}</p>
                <h2 class="ge-section-title">Leave a Wish</h2>
                <div class="ge-section-divider"></div>
            </div>

            @if ($event->is_comment_enabled)
                <div class="ge-form-grid">
                    <div class="ge-form-card">
                        <form method="POST" action="{{ $guest ? route('public.comment.personal', [$event, request()->route('guestToken')]) : route('public.comment.general', $event) }}">
                            @csrf
                            @unless($guest)
                                <div style="margin-bottom: 1.25rem;">
                                    <label class="ge-form-label">{{ $labels['comments_name_placeholder'] }}</label>
                                    <input class="ge-field" name="name" placeholder="{{ $labels['comments_name_placeholder'] }}" required>
                                </div>
                            @endunless
                            <input type="hidden" name="website" value="">
                            <div style="margin-bottom: 1.25rem;">
                                <label class="ge-form-label">{{ $labels['comments_message_placeholder'] }}</label>
                                <textarea class="ge-textarea" name="message" placeholder="{{ $labels['comments_message_placeholder'] }}" required></textarea>
                            </div>
                            <button class="ge-primary-btn" style="width:100%;" type="submit">{{ $labels['comments_submit'] }}</button>
                        </form>
                    </div>

                    <div class="ge-list-card">
                        <p class="ge-panel-note">{{ $comments->count() }} Wishes Received</p>
                        <div class="ge-list-scroll">
                            @forelse ($comments as $comment)
                                <article class="ge-wish-card">
                                    <div class="ge-wish-head">
                                        <div class="ge-wish-user">
                                            <div class="ge-wish-avatar">{{ strtoupper(mb_substr($comment->name_snapshot, 0, 1)) }}</div>
                                            <span class="ge-wish-name">{{ $comment->name_snapshot }}</span>
                                        </div>
                                        <span class="ge-wish-time">{{ optional($comment->submitted_at)->diffForHumans() ?? 'Terkirim' }}</span>
                                    </div>
                                    <p class="ge-wish-copy">{{ $comment->message }}</p>
                                </article>
                            @empty
                                <p class="ge-surface-copy">Belum ada ucapan yang tampil untuk event ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            @if ($event->is_rsvp_enabled)
                <div class="ge-form-card" style="max-width: 48rem; margin: 2rem auto 0;">
                    <p class="ge-panel-note">{{ $labels['rsvp_label'] }}</p>
                    <h3 class="ge-surface-title" style="font-size: 2rem; margin-bottom: 1.5rem;">{{ $labels['rsvp_title'] }}</h3>
                    <form method="POST" action="{{ $guest ? route('public.rsvp.personal', [$event, request()->route('guestToken')]) : route('public.rsvp.general', $event) }}">
                        @csrf
                        @unless($guest)
                            <div style="margin-bottom: 1.25rem;">
                                <label class="ge-form-label">{{ $labels['rsvp_name_placeholder'] }}</label>
                                <input class="ge-field" name="name" placeholder="{{ $labels['rsvp_name_placeholder'] }}" required>
                            </div>
                            <div style="margin-bottom: 1.25rem;">
                                <label class="ge-form-label">{{ $labels['rsvp_phone_placeholder'] }}</label>
                                <input class="ge-field" name="phone" placeholder="{{ $labels['rsvp_phone_placeholder'] }}">
                            </div>
                        @endunless

                        @if ($availableSchedules->isEmpty())
                            <div class="ge-feature-pill" style="margin-bottom: 1.25rem;">RSVP belum dapat diproses karena sesi acara belum dipetakan untuk link ini.</div>
                        @elseif ($availableSchedules->count() === 1)
                            <input type="hidden" name="event_schedule_id" value="{{ $availableSchedules->first()->id }}">
                            <div class="ge-feature-pill" style="margin-bottom: 1.25rem;">
                                Sesi terpilih: {{ $availableSchedules->first()->label }} · {{ \Illuminate\Support\Carbon::parse($availableSchedules->first()->date)->locale($carbonLocale)->translatedFormat('d F Y') }}
                            </div>
                        @else
                            <div style="margin-bottom: 1.25rem;">
                                <label class="ge-form-label">Sesi acara</label>
                                <select class="ge-select" name="event_schedule_id" required>
                                    <option value="">Pilih sesi acara</option>
                                    @foreach ($availableSchedules as $schedule)
                                        <option value="{{ $schedule->id }}">{{ $schedule->label }} · {{ \Illuminate\Support\Carbon::parse($schedule->date)->locale($carbonLocale)->translatedFormat('d F Y') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div style="margin-bottom: 1.25rem;">
                            <label class="ge-form-label">Status RSVP</label>
                            <select class="ge-select" name="status" required>
                                <option value="hadir">{{ $labels['rsvp_status_labels']['hadir'] }}</option>
                                <option value="tidak_hadir">{{ $labels['rsvp_status_labels']['tidak_hadir'] }}</option>
                                <option value="ragu">{{ $labels['rsvp_status_labels']['ragu'] }}</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 1.25rem;">
                            <label class="ge-form-label">Jumlah tamu</label>
                            <input class="ge-field" name="pax_count" type="number" min="1" max="{{ $guest?->max_pax ?? 10 }}" value="1" required>
                        </div>

                        <div style="margin-bottom: 1.25rem;">
                            <label class="ge-form-label">{{ $labels['rsvp_message_placeholder'] }}</label>
                            <textarea class="ge-textarea" name="message" placeholder="{{ $labels['rsvp_message_placeholder'] }}"></textarea>
                        </div>

                        <button class="ge-primary-btn" style="width:100%;" type="submit" @disabled($availableSchedules->isEmpty())>{{ $labels['rsvp_submit'] }}</button>
                    </form>
                </div>
            @endif
        </div>
    </section>

    <section class="ge-section" style="background: #fdf9f4;">
        <div class="ge-container">
            <div class="ge-section-header">
                <p class="ge-section-label">Support Features</p>
                <h2 class="ge-section-title">Invitation Support Layer</h2>
                <div class="ge-section-divider"></div>
            </div>

            <div class="ge-support-grid">
                @if ($event->musicAsset?->resolved_url)
                    <article class="ge-surface-card">
                        <h3 class="ge-surface-title">{{ $labels['music_title'] }}</h3>
                        <p class="ge-surface-copy">{{ $event->musicAsset->title }}{{ $event->musicAsset->artist ? ' · '.$event->musicAsset->artist : '' }}</p>
                        <audio class="ge-audio" controls preload="none">
                            <source src="{{ $event->musicAsset->resolved_url }}">
                        </audio>
                    </article>
                @endif

                @if ($event->is_gift_enabled)
                    <article class="ge-surface-card">
                        <h3 class="ge-surface-title">{{ $labels['gift_title'] }}</h3>
                        @if (($event->giftSetting?->mode ?? 'no_gift') === 'no_gift')
                            <p class="ge-surface-copy">{{ $event->giftSetting?->no_gift_message ?: ($event->content?->no_gift_message ?: $defaults['no_gift_message']) }}</p>
                        @else
                            <p class="ge-surface-copy">{{ $event->giftSetting?->instructions ?: $defaults['gift_instructions'] }}</p>
                            <div class="ge-feature-stack">
                                <div class="ge-feature-pill">{{ $event->giftSetting?->bank_name }} · {{ $event->giftSetting?->account_number }}</div>
                                <div class="ge-feature-pill">{{ $event->giftSetting?->account_holder }}</div>
                            </div>
                            @if (file_exists(public_path('qris.jpeg')))
                                <img src="{{ asset('qris.jpeg') }}" alt="QRIS pembayaran" style="display:block;margin:1.25rem auto 0;width:100%;max-width:220px;border-radius:1.5rem;border:1px solid var(--ge-accent-soft);">
                            @endif
                            @if ($guest)
                                <div style="margin-top: 1.25rem;">
                                    <a href="{{ route('public.gift.show', [$event, request()->route('guestToken')]) }}" class="ge-outline-btn">{{ $labels['gift_confirm'] }}</a>
                                </div>
                            @endif
                        @endif
                    </article>
                @endif

                @if ($guest && $invitation)
                    <article class="ge-surface-card">
                        <h3 class="ge-surface-title">{{ $labels['qr_title'] }}</h3>
                        <p class="ge-surface-copy">{{ $labels['qr_note'] }}</p>
                        <div id="guest-qr" class="ge-qr-box">{{ $invitation->checkin_url_cached }}</div>
                    </article>
                @endif
            </div>
        </div>
    </section>

    <footer class="ge-footer">
        <div class="ge-footer-content">
            <p class="ge-footer-kicker">{{ $labels['closing_label'] }}</p>
            <h2 class="ge-footer-title">See You There</h2>
            <p class="ge-footer-copy">{{ $event->content?->closing_text ?: $defaults['closing_text'] }}</p>
            <p class="ge-footer-signature">{{ $event->couple_name_display }}</p>
            <div class="ge-footer-credit">Designed for Invitely by NechCode</div>
        </div>
    </footer>
</div>
