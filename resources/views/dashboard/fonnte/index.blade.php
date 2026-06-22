@extends('layouts.app', ['title' => 'Fonnte Integration'])

@section('content')
    <section class="dashboard-hero">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-4xl">
                <p class="section-kicker">Fonnte Integration</p>
                <h1 class="mt-3 text-5xl text-primary md:text-6xl">Hubungkan device WhatsApp client ke sistem broadcast.</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-on-surface-variant">
                    Panel ini dipakai client sendiri untuk menyimpan token, memilih device, melakukan refresh status, dan test send sebelum campaign undangan dijalankan.
                </p>
            </div>
            <form method="POST" action="{{ route('dashboard.fonnte.refresh') }}">
                @csrf
                <button class="btn-secondary" type="submit">Refresh status</button>
            </form>
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
        <div class="space-y-6">
            <div class="panel">
                <p class="section-kicker">Device Summary</p>
                <h2 class="mt-3 text-4xl text-primary">Status integrasi yang sedang aktif.</h2>
                <div class="mt-6 dashboard-stat-grid">
                    <div class="metric-card">
                        <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Device status</p>
                        <p class="mt-3 text-2xl font-semibold text-primary">{{ $integration->device_status }}</p>
                    </div>
                    <div class="metric-card">
                        <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Selected device</p>
                        <p class="mt-3 text-2xl font-semibold text-primary">{{ $integration->device_name ?: '-' }}</p>
                        <p class="mt-1 text-sm text-on-surface-variant">{{ $integration->device_number ?: 'Belum ada device aktif' }}</p>
                    </div>
                    <div class="metric-card">
                        <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Quota</p>
                        <p class="mt-3 text-2xl font-semibold text-primary">{{ $integration->quota ?? '-' }}</p>
                    </div>
                    <div class="metric-card">
                        <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Verified at</p>
                        <p class="mt-3 text-lg font-semibold text-primary">{{ $integration->verified_at ?: '-' }}</p>
                    </div>
                </div>

                @if ($integration->last_error_message)
                    <div class="mt-4 rounded-[1.25rem] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        {{ $integration->last_error_message }}
                    </div>
                @endif
            </div>

            <div class="panel">
                <p class="section-kicker">Token Setup</p>
                <h2 class="mt-3 text-4xl text-primary">Simpan account token dan device token.</h2>
                <p class="mt-3 section-copy">Account token bersifat opsional namun direkomendasikan untuk memuat daftar device. Device token tetap dibutuhkan untuk pengiriman pesan.</p>
                <form method="POST" action="{{ route('dashboard.fonnte.update') }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="label" for="account_token">Account token</label>
                        <textarea id="account_token" class="field min-h-28" name="account_token" placeholder="Paste Fonnte account token here"></textarea>
                    </div>
                    <div>
                        <label class="label" for="device_token">Device token</label>
                        <textarea id="device_token" class="field min-h-28" name="device_token" placeholder="Paste Fonnte device token here"></textarea>
                        @if ($integration->device_token_last4)
                            <p class="mt-2 text-xs text-on-surface-variant">Current device token ends with {{ $integration->device_token_last4 }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="label" for="default_country_code">Default country code</label>
                        <input id="default_country_code" class="field" name="default_country_code" value="{{ old('default_country_code', $integration->default_country_code) }}" placeholder="62">
                    </div>
                    <label class="module-card flex items-center gap-3">
                        <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $integration->is_enabled))>
                        <span class="text-sm font-semibold text-on-surface">Enable this integration for broadcasts</span>
                    </label>
                    <button class="btn-primary w-full" type="submit">Save integration</button>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div class="panel">
                <p class="section-kicker">Account Devices</p>
                <h2 class="mt-3 text-4xl text-primary">Pilih device langsung dari akun Fonnte.</h2>
                <p class="mt-3 section-copy">Jika account token valid, daftar device akan muncul di bawah ini dan bisa dipilih tanpa menyalin token satu per satu.</p>
                <div class="mt-6 space-y-4">
                    @forelse ($devices as $device)
                        <form method="POST" action="{{ route('dashboard.fonnte.select-device') }}" class="module-card">
                            @csrf
                            <input type="hidden" name="device_token" value="{{ $device['token'] }}">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h3 class="text-2xl text-primary">{{ $device['name'] ?? 'Unnamed device' }}</h3>
                                    <p class="mt-2 section-copy">{{ $device['device'] ?? '-' }} - {{ $device['status'] ?? 'unknown' }} - quota {{ $device['quota'] ?? '-' }}</p>
                                </div>
                                <button class="btn-secondary" type="submit">Use this device</button>
                            </div>
                        </form>
                    @empty
                        <div class="module-card">
                            <p class="text-lg font-semibold text-primary">No device list available yet.</p>
                            <p class="mt-3 section-copy">Simpan account token terlebih dahulu, lalu refresh halaman ini untuk memuat device dari Fonnte.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="panel">
                <p class="section-kicker">Test Send</p>
                <h2 class="mt-3 text-4xl text-primary">Kirim satu pesan uji sebelum bulk campaign.</h2>
                <form method="POST" action="{{ route('dashboard.fonnte.test-send') }}" class="mt-6 space-y-4">
                    @csrf
                    <input class="field" name="phone" placeholder="081234567890" required>
                    <textarea class="field min-h-32" name="message" required>Assalamualaikum, ini pesan uji dari panel Fonnte NechCode.</textarea>
                    <button class="btn-primary w-full" type="submit">Send test message</button>
                </form>
            </div>
        </div>
    </section>
@endsection
