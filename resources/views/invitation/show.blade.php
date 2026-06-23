@php
    $firstSchedule = $event->schedules->sortBy('date')->first();
    $availableSchedules = collect($allowedSchedules ?? $event->schedules)->sortBy('sort_order')->values();
    $countdownIso = null;
    $labels = $languagePack['ui'];
    $defaults = $languagePack['defaults'];
    $carbonLocale = $languagePack['carbon_locale'] ?? 'id';
    $templateView = 'invitation.templates.'.($event->template?->code ?? 'valley-of-blue');
    $hasMapCoordinates = $availableSchedules->contains(fn ($schedule) => filled($schedule->latitude) && filled($schedule->longitude));

    if (! view()->exists($templateView)) {
        $templateView = 'invitation.templates.valley-of-blue';
    }

    if ($firstSchedule?->date) {
        $time = $firstSchedule->start_time ?: '00:00';
        $date = $firstSchedule->date instanceof \Illuminate\Support\Carbon
            ? $firstSchedule->date->format('Y-m-d')
            : (string) $firstSchedule->date;
        $countdownIso = \Illuminate\Support\Carbon::parse($date.' '.$time, $firstSchedule->timezone ?? 'Asia/Jakarta')->toIso8601String();
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_starts_with($languagePack['key'], 'en') ? 'en' : 'id' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->couple_name_display }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Manrope:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Jost:wght@200;300;400;500&family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400;1,500&family=DM+Sans:wght@200;300;400;500&display=swap" rel="stylesheet">
    @if ($hasMapCoordinates)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    @endif
    <style>
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    @if ($event->musicAsset?->resolved_url)
        <audio data-invitation-audio autoplay loop playsinline preload="auto">
            <source src="{{ $event->musicAsset->resolved_url }}">
        </audio>
        <button type="button" class="audio-floating-toggle" data-audio-toggle aria-label="Mute musik invitation">
            <span class="material-symbols-outlined text-[1.1rem]" data-audio-icon>volume_up</span>
            <span class="text-sm font-semibold" data-audio-label>Mute musik</span>
        </button>
    @endif

    @if (session('status') || $errors->any())
        <div style="position: relative; z-index: 30; max-width: 960px; margin: 0 auto; padding: 16px 16px 0;">
            @if (session('status'))
                <div style="margin-bottom: 12px; border-radius: 20px; border: 1px solid #bbf7d0; background: #ecfdf5; padding: 14px 16px; color: #166534; font: 500 14px/1.6 'Manrope', sans-serif;">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="border-radius: 20px; border: 1px solid #fecdd3; background: #fff1f2; padding: 14px 16px; color: #be123c; font: 500 14px/1.6 'Manrope', sans-serif;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    @include($templateView)

    @include('invitation.partials.scripts')
</body>
</html>
