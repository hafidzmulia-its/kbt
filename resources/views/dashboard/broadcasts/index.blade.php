@extends('layouts.app', ['title' => 'Broadcasts'])

@section('content')
    <section class="dashboard-hero">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-4xl">
                <p class="section-kicker">Broadcast Control</p>
                <h1 class="mt-3 text-5xl text-primary md:text-6xl">Distribusikan undangan massal lewat device Fonnte milik client.</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-on-surface-variant">
                    Campaign builder ini memakai target audience yang lebih terukur: bisa dipilih per grup tamu, status RSVP, VIP, sampai tamu yang belum pernah membuka undangan.
                </p>
            </div>
            <a href="{{ route('dashboard.fonnte.show') }}" class="btn-secondary">Manage Fonnte</a>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <div class="space-y-6">
            <div class="surface-panel">
                <p class="section-kicker">Campaign Builder</p>
                <h2 class="mt-3 text-4xl text-primary">Rakit audience, preview pesan, lalu jadwalkan campaign.</h2>
                <p class="mt-4 section-copy">Fokusnya bukan sekadar kirim massal, tapi memastikan link personal, timing, dan segmentasi benar-benar sesuai flow operasional client.</p>

                <div class="mt-5 dashboard-table-card">
                    <p class="text-sm uppercase tracking-[0.18em] text-on-surface-variant">Device status</p>
                    <p class="mt-2 text-xl font-semibold text-primary">{{ $integration?->device_status ?? 'not configured' }}</p>
                </div>
                @if (! $integration?->hasUsableDeviceToken())
                    <div class="mt-4 rounded-[1.25rem] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        Fonnte belum siap untuk broadcast. Simpan device token, aktifkan integrasi, lalu verifikasi device pada halaman Fonnte.
                    </div>
                @endif

                <form method="POST" action="{{ route('dashboard.broadcasts.preview', $event) }}" class="mt-6 space-y-4">
                    @csrf
                    <input class="field" name="name" value="{{ old('name') }}" placeholder="Nama campaign" required>
                    <select class="field" name="broadcast_template_id">
                        <option value="">Pilih template tersimpan atau isi manual di bawah</option>
                        @foreach ($templates as $template)
                            <option value="{{ $template->id }}" @selected((string) old('broadcast_template_id') === (string) $template->id)>{{ $template->name }}</option>
                        @endforeach
                    </select>
                    <textarea class="field min-h-52" name="message_template" required>{{ old('message_template', $event->settings_json['experience']['broadcast_message_template_seed'] ?? "Assalamualaikum Wr. Wb.\n\nKepada Yth. {{guest_name}},\nKami mengundang Bapak/Ibu/Saudara/i untuk hadir di acara {{couple_names}}.\nSesi: {{event_session}}\nTanggal: {{event_date}}\n\n{{invitation_link}}\n\nTerima kasih.") }}</textarea>
                    <div class="grid gap-4 md:grid-cols-2">
                        <input class="field" name="country_code" value="{{ old('country_code', $integration?->default_country_code ?? '62') }}" placeholder="Country code">
                        <input class="field" name="delay" value="{{ old('delay', '2') }}" placeholder="Delay antar target, contoh 2 atau 2-10">
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <select class="field" name="guest_group_id">
                            <option value="">Semua grup</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}" @selected((string) old('guest_group_id') === (string) $group->id)>{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <select class="field" name="guest_status">
                            <option value="active" @selected(old('guest_status', 'active') === 'active')>Guest active</option>
                            <option value="inactive" @selected(old('guest_status') === 'inactive')>Guest inactive</option>
                            <option value="" @selected(old('guest_status') === '')>Semua status guest</option>
                        </select>
                        <select class="field" name="rsvp_status">
                            <option value="">Semua status RSVP</option>
                            @foreach (['hadir', 'tidak_hadir', 'ragu', 'pending'] as $status)
                                <option value="{{ $status }}" @selected(old('rsvp_status') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        <select class="field" name="opened_state">
                            <option value="all" @selected(old('opened_state', 'all') === 'all')>Semua open state</option>
                            <option value="opened" @selected(old('opened_state') === 'opened')>Sudah buka undangan</option>
                            <option value="not_opened" @selected(old('opened_state') === 'not_opened')>Belum buka undangan</option>
                        </select>
                        <input class="field" type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}">
                        <label class="module-card flex items-center gap-3">
                            <input type="checkbox" name="connect_only" value="1" @checked(old('connect_only', '1') === '1')>
                            <span class="text-sm font-semibold text-on-surface">Send only when device connected</span>
                        </label>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="module-card flex items-center gap-3">
                            <input type="checkbox" name="vip_only" value="1" @checked(old('vip_only') === '1')>
                            <span class="text-sm font-semibold text-on-surface">VIP only</span>
                        </label>
                        <label class="module-card flex items-center gap-3">
                            <input type="checkbox" name="physical_only" value="1" @checked(old('physical_only') === '1')>
                            <span class="text-sm font-semibold text-on-surface">Undangan fisik only</span>
                        </label>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button class="btn-secondary" type="submit" @disabled(! $integration?->hasUsableDeviceToken())>Preview audience</button>
                        <button class="btn-primary" type="submit" formaction="{{ route('dashboard.broadcasts.store', $event) }}" @disabled(! $integration?->hasUsableDeviceToken())>Queue campaign</button>
                    </div>
                </form>
            </div>

            <div class="surface-panel">
                <p class="section-kicker">Reusable Templates</p>
                <h2 class="mt-3 text-4xl text-primary">Simpan template yang sering dipakai tim.</h2>
                <form method="POST" action="{{ route('dashboard.broadcasts.templates.store', $event) }}" class="mt-6 space-y-4">
                    @csrf
                    <input class="field" name="name" placeholder="Nama template broadcast">
                    <textarea class="field min-h-40" name="template_body" placeholder="Template untuk save the date, invitation release, reminder H-1, dan seterusnya."></textarea>
                    <label class="module-card flex items-center gap-3">
                        <input type="checkbox" name="is_default" value="1">
                        <span class="text-sm font-semibold text-on-surface">Jadikan default untuk event ini</span>
                    </label>
                    <button class="btn-secondary" type="submit">Simpan template</button>
                </form>

                <div class="mt-6 space-y-3">
                    @forelse ($templates as $template)
                        <div class="dashboard-table-card">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-primary">{{ $template->name }}</p>
                                    <p class="mt-2 section-copy line-clamp-4">{{ $template->template_body }}</p>
                                </div>
                                @if ($template->is_default)
                                    <span class="dashboard-chip">Default</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="module-card">
                            <p class="text-lg font-semibold text-primary">Belum ada template tersimpan.</p>
                            <p class="mt-3 section-copy">Simpan template untuk flow seperti save the date, invitation release, atau reminder H-1.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if ($preview)
                <div class="surface-panel">
                    <p class="section-kicker">Audience Preview</p>
                    <h2 class="mt-3 text-4xl text-primary">Lihat target dan sample message sebelum campaign masuk queue.</h2>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="metric-card">
                            <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">Total target</p>
                            <p class="mt-3 text-4xl font-semibold text-primary">{{ $preview['summary']['total'] }}</p>
                        </div>
                        <div class="metric-card">
                            <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">VIP</p>
                            <p class="mt-3 text-4xl font-semibold text-primary">{{ $preview['summary']['vip'] }}</p>
                        </div>
                        <div class="metric-card">
                            <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">Opened</p>
                            <p class="mt-3 text-4xl font-semibold text-primary">{{ $preview['summary']['opened'] }}</p>
                        </div>
                        <div class="metric-card">
                            <p class="text-xs uppercase tracking-[0.18em] text-on-surface-variant">Not opened</p>
                            <p class="mt-3 text-4xl font-semibold text-primary">{{ $preview['summary']['not_opened'] }}</p>
                        </div>
                    </div>

                    @if ($preview['scheduled_at'])
                        <div class="mt-4 rounded-[1.2rem] border border-[#1d5a8d]/14 bg-[#f7fbff] px-4 py-3 text-sm text-on-surface-variant">
                            Campaign akan dijadwalkan pada {{ $preview['scheduled_at'] }}.
                        </div>
                    @endif

                    <div class="mt-6 space-y-4">
                        @foreach ($preview['samples'] as $sample)
                            <div class="module-card">
                                <div>
                                    <p class="text-lg font-semibold text-primary">{{ $sample['name'] }}</p>
                                    <p class="mt-2 text-sm text-on-surface-variant">{{ $sample['phone'] }} · {{ $sample['group'] ?: 'Tanpa grup' }}</p>
                                </div>
                                <div class="mt-4 rounded-[1.2rem] border border-outline-variant/18 bg-[#f7fbff] p-4 text-sm leading-7 text-on-surface-variant whitespace-pre-line">{{ $sample['message'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="surface-panel">
                <p class="section-kicker">Campaign History</p>
                <h2 class="mt-3 text-4xl text-primary">Pantau hasil kirim, gagal, terjadwal, dan cancel.</h2>
                <div class="mt-6 space-y-4">
                    @forelse ($campaigns as $campaign)
                        <div class="module-card">
                            <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="dashboard-chip">{{ $campaign->status }}</span>
                                        <span class="text-sm text-on-surface-variant">cc {{ $campaign->country_code }} · delay {{ $campaign->delay ?: '-' }}</span>
                                        @if ($campaign->template)
                                            <span class="text-sm text-on-surface-variant">template {{ $campaign->template->name }}</span>
                                        @endif
                                    </div>
                                    <h3 class="mt-4 text-2xl text-primary">{{ $campaign->name }}</h3>
                                    <p class="mt-3 section-copy">
                                        Scheduled: {{ optional($campaign->scheduled_at)->format('d M Y H:i') ?: 'langsung' }}
                                        @if ($campaign->targeting_json)
                                            · target {{ collect($campaign->targeting_json)->filter(fn ($value) => filled($value) && $value !== false && $value !== 'all')->count() }} filter aktif
                                        @endif
                                    </p>
                                </div>
                                <div class="grid grid-cols-4 gap-2 text-center text-xs">
                                    <div class="metric-card min-w-20">
                                        <p class="uppercase tracking-[0.18em] text-on-surface-variant">Sent</p>
                                        <p class="mt-2 font-semibold text-primary">{{ $campaign->sent_count }}</p>
                                    </div>
                                    <div class="metric-card min-w-20">
                                        <p class="uppercase tracking-[0.18em] text-on-surface-variant">Failed</p>
                                        <p class="mt-2 font-semibold text-primary">{{ $campaign->failed_count }}</p>
                                    </div>
                                    <div class="metric-card min-w-20">
                                        <p class="uppercase tracking-[0.18em] text-on-surface-variant">Pending</p>
                                        <p class="mt-2 font-semibold text-primary">{{ $campaign->pending_count }}</p>
                                    </div>
                                    <div class="metric-card min-w-20">
                                        <p class="uppercase tracking-[0.18em] text-on-surface-variant">Cancel</p>
                                        <p class="mt-2 font-semibold text-primary">{{ $campaign->cancelled_count }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-3">
                                @if ($campaign->failed_count > 0)
                                    <form method="POST" action="{{ route('dashboard.broadcasts.retry-failed', [$event, $campaign]) }}">
                                        @csrf
                                        <button class="btn-secondary" type="submit">Retry failed</button>
                                    </form>
                                @endif
                                @if (in_array($campaign->status, ['queued', 'scheduled'], true))
                                    <form method="POST" action="{{ route('dashboard.broadcasts.cancel', [$event, $campaign]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn-secondary" type="submit">Cancel campaign</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="module-card">
                            <p class="text-lg font-semibold text-primary">Belum ada campaign yang dibuat.</p>
                            <p class="mt-3 section-copy">Siapkan Fonnte device terlebih dahulu lalu queue campaign dari panel sebelah kiri.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
