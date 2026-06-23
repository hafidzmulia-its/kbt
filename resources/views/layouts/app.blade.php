<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">
    @stack('head')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white">
    <div class="min-h-screen bg-white">
        @include('partials.site-header', ['brand' => config('nechcode.brand')])

        <main class="shell pb-16 pt-3">
            @if (session('status'))
                <div class="status-banner border-emerald-200 bg-emerald-50 text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="status-banner border-rose-200 bg-rose-50 text-rose-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>

        @auth
            <footer class="border-t border-outline-variant/20 bg-white">
                <div class="shell flex flex-col gap-3 py-6 text-sm text-on-surface-variant md:flex-row md:items-center md:justify-between">
                    <p>{{ config('nechcode.brand.name') }} workspace</p>
                    <p>Secure invitation operations, guest personalization, and event distribution.</p>
                </div>
            </footer>
        @else
            @include('partials.site-footer', ['brand' => config('nechcode.brand')])
        @endauth
    </div>
    @stack('scripts')
</body>
</html>
