@extends('layouts.app', ['title' => 'Login'])

@php
    $stageItems = [
        ['eyebrow' => 'Invitation', 'title' => 'General dan personal invitation'],
        ['eyebrow' => 'Guest Flow', 'title' => 'RSVP, gift, dan attendance'],
        ['eyebrow' => 'Distribution', 'title' => 'Broadcast WhatsApp via Fonnte'],
    ];
@endphp

@section('content')
    <section class="auth-shell">
        <div class="grid gap-8 lg:grid-cols-[1.02fr_0.98fr] lg:items-center">
            <div class="auth-stage">
                <div class="relative z-10">
                    <p class="section-kicker !text-[#9fe8ff]">Client Login</p>
                    <h1 class="mt-3 text-5xl text-white md:text-6xl">Masuk ke ruang kerja invitation yang sudah siap pakai.</h1>
                    <p class="mt-5 max-w-2xl text-base leading-8 text-white/76">
                        Login diposisikan seperti halaman produk, bukan form polos. Client masuk ke workspace yang tetap konsisten dengan shell NechCode, namun diarahkan khusus untuk wedding invitation flow.
                    </p>

                    <div class="mt-8 grid gap-4 md:grid-cols-3">
                        @foreach ($stageItems as $item)
                            <div class="auth-stage-card">
                                <p class="text-xs uppercase tracking-[0.16em] text-white/45">{{ $item['eyebrow'] }}</p>
                                <p class="mt-3 text-lg font-semibold text-white">{{ $item['title'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="auth-panel">
                <p class="section-kicker">Access Form</p>
                <h2 class="mt-3 text-4xl text-primary">Masuk ke dashboard client.</h2>
                <p class="mt-4 text-sm leading-7 text-on-surface-variant">
                    Gunakan akun yang sudah terdaftar untuk membuka event workspace, builder, dan monitoring operasional.
                </p>

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label class="label" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="field" required>
                    </div>
                    <div>
                        <label class="label" for="password">Password</label>
                        <input id="password" name="password" type="password" class="field" required>
                    </div>
                    <button class="btn-primary w-full" type="submit">Masuk ke dashboard</button>
                </form>

                <div class="mt-8 rounded-[1.25rem] border border-outline-variant/16 bg-[#F8FBFE] p-4">
                    <p class="text-sm font-semibold text-primary">Demo account</p>
                    <p class="mt-2 text-sm leading-7 text-on-surface-variant">User: <strong>user@nechcode.test</strong> / <strong>password</strong></p>
                    <p class="mt-1 text-sm leading-7 text-on-surface-variant">Admin: <strong>admin@nechcode.test</strong> / <strong>password</strong></p>
                </div>

                <p class="mt-6 text-sm text-on-surface-variant">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-secondary">Daftar di sini</a>.
                </p>
            </div>
        </div>
    </section>
@endsection
