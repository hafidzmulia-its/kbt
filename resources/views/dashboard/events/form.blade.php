@extends('layouts.app', ['title' => $event->exists ? 'Edit Event' : 'Create Event'])

@php
    $schedules = old('schedules', $event->schedules->count() ? $event->schedules->map(fn ($schedule) => [
        'label' => $schedule->label,
        'date' => optional($schedule->date)->format('Y-m-d'),
        'start_time' => $schedule->start_time ? substr((string) $schedule->start_time, 0, 5) : '',
        'end_time' => $schedule->end_time ? substr((string) $schedule->end_time, 0, 5) : '',
        'timezone' => $schedule->timezone,
        'venue_name' => $schedule->venue_name,
        'address' => $schedule->address,
        'maps_url' => $schedule->maps_url,
    ])->toArray() : [[
        'label' => 'akad',
        'date' => now()->format('Y-m-d'),
        'start_time' => '09:00',
        'end_time' => '11:00',
        'timezone' => 'Asia/Jakarta',
        'venue_name' => '',
        'address' => '',
        'maps_url' => '',
    ]]);
    $addons = old('addons', $event->settings_json['addons'] ?? []);
    $pricing = config('nechcode.pricing');
    $experience = old('experience', $event->settings_json['experience'] ?? []);
    $selectedLanguage = old('language_variant', $experience['language_variant'] ?? 'id_formal');
    $bundleEnabled = old('bundle_offer_enabled', $experience['bundle_offer_enabled'] ?? false);
    $styleBrief = old('ai_style_brief', session('assistant_style_brief', $experience['ai_style_brief'] ?? ''));
    $broadcastSeed = old('broadcast_message_template_seed', $experience['broadcast_message_template_seed'] ?? '');
    $stepOrder = collect($builderSteps)->pluck('key')->all();
    $completionCount = collect($builderSteps)->where('is_complete', true)->count();
    $initialWizardStep = old('wizard_step', 'start');
@endphp

@section('content')
    <style>
        .template-option input:checked + .template-option-card {
            border-color: #1D5A8D;
            box-shadow: 0 18px 44px rgba(29, 90, 141, 0.18);
        }

        .template-option input:checked + .template-option-card .template-option-badge {
            background: #1D5A8D;
            color: #fff;
            border-color: #1D5A8D;
        }
    </style>

    <form
        method="POST"
        action="{{ $event->exists ? route('dashboard.events.update', $event) : route('dashboard.events.store') }}"
        class="space-y-6"
        enctype="multipart/form-data"
        data-event-wizard
        data-step-order='@json($stepOrder)'
        data-initial-step="{{ $initialWizardStep }}"
    >
        @csrf
        @if ($event->exists)
            @method('PUT')
        @endif
        <input type="hidden" name="wizard_step" value="{{ $initialWizardStep }}" data-wizard-step-input>

        <section class="dashboard-stage">
            <div class="relative z-10">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-4xl">
                        <p class="section-kicker !text-[#9fe8ff]">Invitation Setup Wizard</p>
                        <h1 class="mt-3 text-5xl text-white md:text-6xl">
                            {{ $event->exists ? 'Perbarui invitation system tanpa bikin user tenggelam di form panjang.' : 'Buat invitation system dengan wizard yang ringkas dan tetap lengkap.' }}
                        </h1>
                        <p class="mt-5 max-w-3xl text-base leading-8 text-white/76">
                            Flow ini mengambil kejelasan dari wizard kompetitor, tetapi dibuat lebih tenang, lebih premium, dan lebih sesuai dengan struktur Invitely yang sudah kamu bangun.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @if ($event->exists)
                            <a href="{{ route('dashboard.events.workspace', $event) }}" class="brand-action brand-action-ghost">Workspace</a>
                            <a href="{{ route('dashboard.events.preview', $event) }}" class="brand-action brand-action-ghost">Preview draft</a>
                            @if ($event->status === 'published')
                                <a href="{{ route('public.invitation.general', $event) }}" class="brand-action brand-action-solid">Preview publik</a>
                            @endif
                        @else
                            <a href="{{ route('dashboard.events.index') }}" class="brand-action brand-action-ghost">Kembali ke daftar event</a>
                        @endif
                    </div>
                </div>

                <div class="wizard-stepper mt-8">
                    @foreach ($builderSteps as $index => $step)
                        <button
                            type="button"
                            class="wizard-stepper-item {{ $index === 0 ? 'is-active' : '' }} {{ $step['is_complete'] ? 'is-complete' : '' }}"
                            data-step-trigger="{{ $step['key'] }}"
                            aria-label="{{ $step['title'] }}"
                        >
                            <span class="wizard-stepper-dot">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="wizard-stepper-text">
                                <span class="wizard-stepper-label">{{ $step['label'] }}</span>
                                <span class="wizard-stepper-copy">{{ $step['title'] }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div class="surface-panel">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="section-kicker">Progress</p>
                        <h2 class="mt-3 text-3xl text-primary">Invitation setup wizard</h2>
                    </div>
                    <p class="text-sm font-semibold text-on-surface-variant">{{ $completionCount }} / {{ count($builderSteps) }} langkah terisi</p>
                </div>

                <div class="wizard-progress-track mt-5">
                    <div class="wizard-progress-fill" style="width: {{ count($builderSteps) ? round(($completionCount / count($builderSteps)) * 100) : 0 }}%"></div>
                </div>
            </div>

            <div class="space-y-6">
                <section class="surface-panel wizard-section is-active" data-wizard-step="start" data-step-title="Mulai">
                    <p class="section-kicker">Step 1</p>
                    <h2 class="mt-3 text-4xl text-primary">Bangun identitas inti event.</h2>
                    <p class="mt-4 section-copy">Step ini harus langsung membuat event terasa nyata: nama pasangan, judul acara, alamat invitation, dan status dasar.</p>

                    <div class="mt-6 grid gap-4">
                        <input class="field" id="event_title" name="title" value="{{ old('title', $event->title) }}" placeholder="Judul event" required data-slug-source>
                        <div class="space-y-2">
                            <input class="field" id="event_slug" name="slug" value="{{ old('slug', $event->slug) }}" placeholder="Slug event / subalamat invitation" required data-slug-target>
                            <p class="text-xs text-on-surface-variant">Slug akan diisi otomatis dari judul event, tetapi tetap bisa Anda ubah manual.</p>
                        </div>
                        <input class="field" name="couple_name_display" value="{{ old('couple_name_display', $event->couple_name_display) }}" placeholder="Nama pasangan yang tampil di invitation" required>
                        <div class="grid gap-4 md:grid-cols-2">
                            <input class="field" name="bride_name" value="{{ old('bride_name', $event->bride_name) }}" placeholder="Nama mempelai wanita" required>
                            <input class="field" name="groom_name" value="{{ old('groom_name', $event->groom_name) }}" placeholder="Nama mempelai pria" required>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <select class="field" name="status">
                                @foreach (['draft', 'published', 'archived'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $event->status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <label class="module-card flex items-center gap-3">
                                <input type="checkbox" name="is_guest_personalization_enabled" value="1" @checked(old('is_guest_personalization_enabled', $event->is_guest_personalization_enabled ?? true))>
                                <span class="text-sm font-semibold text-on-surface">Aktifkan personal link per tamu</span>
                            </label>
                        </div>
                    </div>
                </section>

                <section class="surface-panel wizard-section" data-wizard-step="event" data-step-title="Acara" hidden>
                    <p class="section-kicker">Step 2</p>
                    <h2 class="mt-3 text-4xl text-primary">Isi sesi acara yang benar-benar dibutuhkan tamu.</h2>
                    <p class="mt-4 section-copy">Kita sengaja membuat bagian ini fokus ke sesi utama, venue, dan maps agar tidak bertele-tele seperti dashboard operasional yang terlalu padat.</p>

                    <div class="mt-6 grid gap-4">
                        @foreach ($schedules as $index => $schedule)
                            <div class="module-card">
                                <div class="mb-4 flex items-center justify-between">
                                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-primary">Sesi {{ $index + 1 }}</p>
                                    <span class="dashboard-chip">Acara</span>
                                </div>
                                <div class="grid gap-4 lg:grid-cols-4">
                                    <input class="field" name="schedules[{{ $index }}][label]" value="{{ $schedule['label'] }}" placeholder="Label sesi">
                                    <input class="field" type="date" name="schedules[{{ $index }}][date]" value="{{ $schedule['date'] }}">
                                    <input class="field" type="time" name="schedules[{{ $index }}][start_time]" value="{{ $schedule['start_time'] }}">
                                    <input class="field" type="time" name="schedules[{{ $index }}][end_time]" value="{{ $schedule['end_time'] }}">
                                    <input class="field lg:col-span-2" name="schedules[{{ $index }}][venue_name]" value="{{ $schedule['venue_name'] }}" placeholder="Nama venue" required>
                                    <input class="field lg:col-span-2" name="schedules[{{ $index }}][timezone]" value="{{ $schedule['timezone'] }}" placeholder="Timezone">
                                    <textarea class="field lg:col-span-2 min-h-28" name="schedules[{{ $index }}][address]" placeholder="Alamat lengkap venue">{{ $schedule['address'] }}</textarea>
                                    <input class="field lg:col-span-2" name="schedules[{{ $index }}][maps_url]" value="{{ $schedule['maps_url'] }}" placeholder="Google Maps URL">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="surface-panel wizard-section" data-wizard-step="theme" data-step-title="Tema" hidden>
                    <p class="section-kicker">Step 3</p>
                    <h2 class="mt-3 text-4xl text-primary">Tentukan tema, suara, dan arah pengalaman publik.</h2>
                    <p class="mt-4 section-copy">Di tahap ini user cukup membuat keputusan visual inti. Pilihan lain yang terlalu teknis kita tahan agar flow tetap ringan.</p>

                    <div class="mt-6 grid gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-primary">Pilih template invitation</p>
                            <p class="mt-2 text-sm leading-7 text-on-surface-variant">Klik desain yang paling mendekati nuansa event. Preview ini hanya ringkasan visual, detail lengkapnya tetap bisa dicek dari halaman preview draft.</p>

                            <div class="mt-4 grid gap-4 md:grid-cols-2">
                                @foreach ($templates as $template)
                                    <label class="template-option group cursor-pointer">
                                        <input
                                            class="peer sr-only"
                                            type="radio"
                                            name="template_id"
                                            value="{{ $template->id }}"
                                            @checked(old('template_id', $event->template_id) == $template->id)
                                        >
                                        <div class="template-option-card overflow-hidden rounded-[1.6rem] border border-[#1D5A8D]/12 bg-white shadow-[0_18px_40px_rgba(13,27,42,0.05)] transition duration-200 group-hover:-translate-y-0.5">
                                            <div class="aspect-[16/10] overflow-hidden bg-[#F6FAFD]">
                                                @if ($template->preview_url)
                                                    <img
                                                        src="{{ $template->preview_url }}"
                                                        alt="Preview {{ $template->name }}"
                                                        class="h-full w-full object-cover"
                                                        loading="lazy"
                                                    >
                                                @else
                                                    <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,#16314E,#6A8AA8)] px-6 text-center text-sm font-semibold uppercase tracking-[0.18em] text-white/80">
                                                        {{ $template->name }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex items-start justify-between gap-4 px-5 py-4">
                                                <div>
                                                    <p class="text-lg font-semibold text-primary">{{ $template->name }}</p>
                                                    <p class="mt-1 text-sm text-on-surface-variant">{{ ucfirst($template->category) }} template</p>
                                                </div>
                                                <span class="template-option-badge rounded-full border border-[#1D5A8D]/12 bg-[#F8FBFE] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-primary transition duration-200">
                                                    Pilih
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <select class="field" name="music_asset_id">
                            <option value="">Pilih backsound</option>
                            @foreach ($musicAssets as $asset)
                                <option value="{{ $asset->id }}" @selected(old('music_asset_id', $event->music_asset_id) == $asset->id)>{{ $asset->title }}</option>
                            @endforeach
                        </select>
                        <select class="field" name="language_variant">
                            @foreach ($languageOptions as $key => $label)
                                <option value="{{ $key }}" @selected($selectedLanguage === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <textarea class="field min-h-28" name="ai_style_brief" placeholder="Arah visual, tone copy, dan detail yang ingin ditekankan">{{ $styleBrief }}</textarea>
                    </div>
                </section>

                <section class="surface-panel wizard-section" data-wizard-step="content" data-step-title="Konten" hidden>
                    <p class="section-kicker">Step 4</p>
                    <h2 class="mt-3 text-4xl text-primary">Selesaikan isi undangan, gifting, dan pesan utama.</h2>
                    <p class="mt-4 section-copy">AI assist tetap tersedia, tapi posisinya sebagai percepatan, bukan menggantikan kurasi pemilik event.</p>

                    <div class="mt-5 rounded-[1.35rem] border border-[#1D5A8D]/12 bg-[#F8FBFE] p-4">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="max-w-2xl">
                                <p class="text-sm font-semibold text-primary">AI copy & style assistant</p>
                                <p class="mt-2 text-sm leading-7 text-on-surface-variant">Generate draft opening, invitation text, bio pasangan, copy no-gift, sampai seed template broadcast dari data event saat ini.</p>
                            </div>
                            <button
                                type="submit"
                                formaction="{{ route('dashboard.events.copy-assistant') }}"
                                class="btn-secondary whitespace-nowrap"
                            >
                                Generate draft AI
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4">
                        <textarea class="field min-h-28" name="opening_text" placeholder="Pra kata">{{ old('opening_text', $event->content?->opening_text) }}</textarea>
                        <textarea class="field min-h-32" name="invitation_text" placeholder="Teks mengundang">{{ old('invitation_text', $event->content?->invitation_text) }}</textarea>
                        <textarea class="field min-h-28" name="closing_text" placeholder="Penutup">{{ old('closing_text', $event->content?->closing_text) }}</textarea>
                        <div class="grid gap-4 md:grid-cols-2">
                            <textarea class="field min-h-28" name="bride_bio" placeholder="Bio mempelai wanita">{{ old('bride_bio', $event->content?->bride_bio) }}</textarea>
                            <textarea class="field min-h-28" name="groom_bio" placeholder="Bio mempelai pria">{{ old('groom_bio', $event->content?->groom_bio) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-4">
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-primary">Gifting experience</p>
                        <select class="field" name="gift_mode">
                            @foreach (['no_gift', 'bank_transfer', 'guest_specific_qr', 'qris_gateway'] as $mode)
                                <option value="{{ $mode }}" @selected(old('gift_mode', $event->giftSetting?->mode ?? 'no_gift') === $mode)>{{ $mode }}</option>
                            @endforeach
                        </select>
                        <div class="grid gap-4 md:grid-cols-3">
                            <input class="field" name="bank_name" value="{{ old('bank_name', $event->giftSetting?->bank_name) }}" placeholder="Nama bank">
                            <input class="field" name="account_number" value="{{ old('account_number', $event->giftSetting?->account_number) }}" placeholder="Nomor rekening">
                            <input class="field" name="account_holder" value="{{ old('account_holder', $event->giftSetting?->account_holder) }}" placeholder="Atas nama">
                        </div>
                        <textarea class="field min-h-24" name="gift_instructions" placeholder="Instruksi gifting">{{ old('gift_instructions', $event->giftSetting?->instructions) }}</textarea>
                        <textarea class="field min-h-24" name="no_gift_message" placeholder="Copy no-gift">{{ old('no_gift_message', $event->content?->no_gift_message) }}</textarea>
                    </div>
                </section>

                <section class="surface-panel wizard-section" data-wizard-step="share" data-step-title="Bagikan" hidden>
                    <p class="section-kicker">Step 5</p>
                    <h2 class="mt-3 text-4xl text-primary">Finalisasi toggle, add-on, dan material yang akan dibuka ke tamu.</h2>
                    <p class="mt-4 section-copy">Ini adalah layer operasi. User tinggal memutuskan fitur mana yang aktif dan apakah event benar-benar siap dipublish.</p>

                    <div class="mt-6 space-y-3">
                        @foreach ([
                            'is_rsvp_enabled' => 'Aktifkan RSVP',
                            'is_comment_enabled' => 'Aktifkan komentar',
                            'is_gift_enabled' => 'Aktifkan gifting',
                            'is_proof_upload_enabled' => 'Aktifkan upload bukti transfer',
                        ] as $name => $label)
                            <label class="module-card flex items-center gap-3">
                                <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $name === 'is_proof_upload_enabled' ? $event->giftSetting?->is_proof_upload_enabled : $event->{$name}))>
                                <span class="text-sm font-semibold text-on-surface">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-8 space-y-3">
                        <div class="dashboard-table-card">
                            <p class="text-sm font-semibold uppercase tracking-[0.12em] text-on-surface-variant">Paket dasar</p>
                            <p class="mt-2 text-lg font-semibold text-primary">{{ $pricing['base_package_name'] }}</p>
                            <p class="mt-2 section-copy">{{ $pricing['base_package_price_range'] }}</p>
                        </div>
                        <label class="module-card flex items-center gap-3">
                            <input type="checkbox" name="bundle_offer_enabled" value="1" @checked($bundleEnabled)>
                            <span class="text-sm font-semibold text-on-surface">Aktifkan potensi bundling wedding + hadiah</span>
                        </label>
                        <label class="module-card flex items-center gap-3">
                            <input type="checkbox" name="wants_broadcast_addon" value="1" @checked(old('wants_broadcast_addon', $addons['broadcast'] ?? false))>
                            <span class="text-sm font-semibold text-on-surface">{{ $pricing['addons']['broadcast']['label'] }} (+Rp{{ number_format($pricing['addons']['broadcast']['price'], 0, ',', '.') }})</span>
                        </label>
                        <label class="module-card flex items-center gap-3">
                            <input type="checkbox" name="wants_custom_design_addon" value="1" @checked(old('wants_custom_design_addon', $addons['custom_design'] ?? false))>
                            <span class="text-sm font-semibold text-on-surface">{{ $pricing['addons']['custom_design']['label'] }} (+Rp{{ number_format($pricing['addons']['custom_design']['price'], 0, ',', '.') }})</span>
                        </label>
                        <textarea class="field min-h-32" name="broadcast_message_template_seed" placeholder="Template broadcast WhatsApp hasil AI atau versi final">{{ $broadcastSeed }}</textarea>
                    </div>

                    <div class="mt-8">
                        <label class="label" for="album_photos">Album photos</label>
                        <input class="field" id="album_photos" type="file" name="album_photos[]" accept=".jpg,.jpeg,.png,.webp" multiple>
                        @if ($event->exists && $event->albums->count())
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                @foreach ($event->albums->flatMap->photos->take(4) as $photo)
                                    <img src="{{ asset('storage/'.$photo->image_path) }}" alt="Album photo" class="h-32 w-full rounded-[1.25rem] object-cover" loading="lazy">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                <section class="surface-panel wizard-footer-panel">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-primary" data-wizard-step-indicator>Step 1 dari {{ count($builderSteps) }}</p>
                            <p class="mt-2 text-sm text-on-surface-variant">Lanjutkan langkah berikutnya sampai selesai. Tombol simpan hanya muncul saat semua bagian inti sudah selesai ditinjau.</p>
                        </div>
                        <div class="flex flex-col-reverse gap-3 sm:flex-row">
                            <button type="button" class="btn-secondary" data-step-back>Langkah sebelumnya</button>
                            <button type="button" class="btn-secondary" data-step-next>Langkah berikutnya</button>
                            <button class="btn-primary" type="submit" data-step-submit hidden>Simpan event</button>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </form>
@endsection
