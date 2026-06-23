@php
    $brand = $brand ?? config('nechcode.brand');
    $isDark = $isDark ?? false;
    $user = auth()->user();
    $isAdmin = $user?->isAdmin() ?? false;
    $isAuthedUser = $user !== null && ! $isAdmin;
    $currentRoute = request()->route()?->getName();
@endphp

@if ($isDark && ! $user)
    <header class="relative z-20 w-full">
        <div class="grid w-full grid-cols-[1fr_auto] items-center gap-6 px-6 py-6 md:px-10 md:py-7 lg:grid-cols-[1fr_auto_1fr] lg:px-[70px] lg:py-8">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 transition-opacity hover:opacity-90" aria-label="{{ $brand['parent_name'] }} home">
                <img src="{{ asset('brand/logo-aseli.png') }}" alt="{{ $brand['parent_name'] }} logo" class="h-10 w-10 object-contain">
                <div>
                    <p class="text-[22px] font-bold tracking-tight text-[#1782c4]">{{ $brand['parent_name'] }}</p>
                    <p class="text-xs uppercase tracking-[0.18em] text-white/55">{{ $brand['product_name'] }}</p>
                </div>
            </a>

            <nav class="hidden items-center gap-[46px] justify-self-center lg:flex">
                <a href="{{ route('welcome') }}#about" class="site-nav-link">About Us</a>
                <a href="{{ route('welcome') }}#services" class="site-nav-link">Services</a>
                <a href="{{ route('welcome') }}#contact" class="site-nav-link">Contact</a>
            </nav>

            <div class="hidden items-center justify-self-end gap-3 lg:flex">
                <a href="{{ route('login') }}" class="brand-action brand-action-ghost">Login</a>
                <a href="{{ route('register') }}" class="brand-action brand-action-solid">Get Your Service</a>
            </div>
        </div>
    </header>
    
@else
    <header class="sticky top-0 z-50 w-full border-b border-white/10 bg-[#071b2a]/96 backdrop-blur-xl">
        <div class="grid w-full grid-cols-[1fr_auto] items-center gap-6 px-6 py-5 md:px-10 lg:grid-cols-[1fr_auto_1fr] lg:px-[70px]">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 transition-opacity hover:opacity-90" aria-label="{{ $brand['parent_name'] }} home">
                <img src="{{ asset('brand/logo-aseli.png') }}" alt="{{ $brand['parent_name'] }} logo" class="h-10 w-10 object-contain">
                <div>
                    <p class="text-[22px] font-bold tracking-tight text-[#1782c4]">{{ $brand['parent_name'] }}</p>
                    <p class="text-xs uppercase tracking-[0.18em] text-white/55">{{ $brand['product_name'] }}</p>
                </div>
            </a>

            <nav class="hidden items-center gap-[46px] justify-self-center lg:flex">
                @if ($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="site-nav-link {{ $currentRoute === 'admin.dashboard' ? 'is-current' : '' }}">Admin</a>
                    <a href="{{ route('admin.templates.index') }}" class="site-nav-link {{ str_starts_with($currentRoute ?? '', 'admin.templates.') ? 'is-current' : '' }}">Templates</a>
                    <a href="{{ route('admin.orders.index') }}" class="site-nav-link {{ str_starts_with($currentRoute ?? '', 'admin.orders.') ? 'is-current' : '' }}">Orders</a>
                @elseif ($isAuthedUser)
                    <a href="{{ route('dashboard') }}" class="site-nav-link {{ $currentRoute === 'dashboard' ? 'is-current' : '' }}">Overview</a>
                    <a href="{{ route('dashboard.events.index') }}" class="site-nav-link {{ str_starts_with($currentRoute ?? '', 'dashboard.events.') ? 'is-current' : '' }}">Events</a>
                    <a href="{{ route('dashboard.fonnte.show') }}" class="site-nav-link {{ str_starts_with($currentRoute ?? '', 'dashboard.fonnte.') ? 'is-current' : '' }}">Fonnte</a>
                    <a href="{{ route('welcome') }}#pricing" class="site-nav-link">Pricing</a>
                @else
                    <a href="{{ route('welcome') }}#about" class="site-nav-link">About Us</a>
                    <a href="{{ route('welcome') }}#services" class="site-nav-link">Services</a>
                    <a href="{{ route('welcome') }}#contact" class="site-nav-link">Contact</a>
                @endif
            </nav>

            <div class="hidden items-center justify-self-end gap-3 lg:flex">
                @auth
                    @if ($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="brand-action brand-action-ghost">Open Admin</a>
                    @else
                        <a href="{{ route('dashboard.events.create') }}" class="brand-action brand-action-solid">Create Event</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="brand-action brand-action-ghost" type="submit">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="brand-action brand-action-ghost">Login</a>
                    <a href="{{ route('register') }}" class="brand-action brand-action-solid">Get Your Service</a>
                @endauth
            </div>
        </div>
    </header>
@endif
