@extends('layouts.app', ['title' => 'Register'])

@php
    $stageSteps = [
        [
            'title' => '1. Create Event',
            'copy' => 'Isi identitas pasangan, template, pra kata, jadwal, maps, album, dan backsound dalam satu builder.',
        ],
        [
            'title' => '2. Add Guests',
            'copy' => 'Tambah atau impor tamu untuk menghasilkan personal link yang aman dan siap dibagikan.',
        ],
        [
            'title' => '3. Share The Moment',
            'copy' => 'Aktifkan RSVP, gift confirmation, guestbook, dan QR attendance untuk menghadirkan flow yang lengkap.',
        ],
    ];
@endphp

@section('content')
    <section class="auth-shell">
        <div class="grid gap-8 lg:grid-cols-[0.98fr_1.02fr] lg:items-center">
            <div class="auth-panel">
                <p class="section-kicker">Create Account</p>
                <h1 class="mt-3 text-5xl text-primary md:text-6xl">Buat akun client untuk memulai event pertama.</h1>
                <p class="mt-5 text-base leading-8 text-on-surface-variant">
                    Akun baru otomatis masuk sebagai role <strong>user</strong>. Setelah itu client bisa langsung membuat event, mengatur invitation, dan menyiapkan distribusi tamu.
                </p>

                <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label class="label" for="name">Nama</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" class="field" required>
                    </div>
                    <div>
                        <label class="label" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" class="field" required>
                    </div>
                    <div>
                        <label class="label" for="phone">Nomor WhatsApp</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="field">
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="label" for="password">Password</label>
                            <input id="password" name="password" type="password" class="field" required>
                        </div>
                        <div>
                            <label class="label" for="password_confirmation">Konfirmasi Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="field" required>
                        </div>
                    </div>
                    <button class="btn-primary w-full" type="submit">Buat akun client</button>
                </form>
            </div>

            <div class="auth-stage">
                <div class="relative z-10">
                    <p class="section-kicker !text-[#9fe8ff]">Client Flow</p>
                    <h2 class="mt-3 text-5xl text-white">Mulai dari invitation, lalu bangun pengalaman tamu yang utuh.</h2>
                    <div class="mt-8 grid gap-4">
                        @foreach ($stageSteps as $step)
                            <div class="auth-stage-card">
                                <p class="text-sm font-semibold text-white">{{ $step['title'] }}</p>
                                <p class="mt-2 text-sm leading-7 text-white/72">{{ $step['copy'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
