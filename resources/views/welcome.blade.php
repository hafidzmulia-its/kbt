@php
    $pricing = config('nechcode.pricing');
    $brand = config('nechcode.brand');
    $highlightCards = [
        [
            'title' => 'Personal Salutation',
            'eyebrow' => '01',
            'copy' => 'Nama tamu, teks mengundang, dan route unik yang membedakan undangan personal dari undangan umum.',
            'tags' => ['Nama tamu dinamis', 'Link personal aman'],
        ],
        [
            'title' => 'Event Essentials',
            'eyebrow' => '02',
            'copy' => 'Countdown, jadwal, venue, maps, dan detail acara yang rapi untuk dibuka cepat di ponsel.',
            'tags' => ['Countdown real-time', 'Venue + maps'],
        ],
        [
            'title' => 'Memory & Mood',
            'eyebrow' => '03',
            'copy' => 'Album foto, backsound, dan komposisi visual tenang yang memperkuat nuansa pernikahan.',
            'tags' => ['Album terkurasi', 'Backsound elegan'],
        ],
        [
            'title' => 'Interactive Flow',
            'eyebrow' => '04',
            'copy' => 'RSVP, ucapan, gift confirmation, dan QR attendance agar interaksi tamu terasa lengkap.',
            'tags' => ['Guestbook aktif', 'QR attendance'],
        ],
    ];
    $systemCards = [
        ['number' => '01', 'title' => 'General Invitation', 'copy' => 'Halaman undangan umum untuk dibagikan lebih luas tanpa membuka data tamu personal.'],
        ['number' => '02', 'title' => 'Personal Invitation', 'copy' => 'Route unik per tamu untuk sapaan personal, RSVP yang tepat, dan pengalaman yang lebih eksklusif.'],
        ['number' => '03', 'title' => 'Gift & RSVP', 'copy' => 'Konfirmasi kehadiran, transfer hadiah, bukti kirim, dan ucapan bisa dilakukan langsung di halaman undangan.'],
        ['number' => '04', 'title' => 'Attendance QR', 'copy' => 'Check-in berbasis QR yang tetap menjaga token aman dan tidak membuka informasi sensitif.'],
    ];
    $basePackageFeatures = [
        'Cover, salutation, couple profile, jadwal, maps, dan closing.',
        'Album, backsound, guestbook, dan tampilan elegan yang siap dibagikan.',
        'Route publik yang aman tanpa membuka raw numeric ID.',
    ];
    $portfolioSlides = [
        ['image' => asset('template-previews/valley-of-blue.svg'), 'title' => 'Valley of Blue', 'copy' => 'Serene blue watercolor dengan struktur premium mobile-first.'],
        ['image' => asset('template-previews/playfair-blush.svg'), 'title' => 'Playfair Blush', 'copy' => 'Mood romantis editorial untuk invitation yang lebih playful.'],
        ['image' => asset('template-previews/cormorant-gold.svg'), 'title' => 'Cormorant Gold', 'copy' => 'Nuansa formal hangat dengan komposisi yang lebih klasik.'],
        ['image' => asset('template-previews/graduation-elegance.svg'), 'title' => 'Graduation Elegance', 'copy' => 'Template showcase yang tetap dipertahankan apa adanya.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brand['name'] }}</title>
    <meta name="description" content="{{ $brand['description'] }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-white selection:bg-secondary/20 selection:text-primary">
    <div class="relative overflow-hidden bg-white">
        <section class="relative isolate min-h-[90svh] overflow-hidden bg-[#020202] text-white">
            <div class="animate-intro-fade-out pointer-events-none absolute inset-0 z-40 flex items-center justify-center bg-[#020202]">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('brand/logo-aseli.png') }}" alt="" class="h-16 w-16 object-contain">
                    <span class="text-[34px] font-bold tracking-tight text-[#1782c4]">NechCode</span>
                </div>
            </div>

            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <img src="{{ asset('brand/img/bg_home.png') }}" alt="" class="absolute inset-0 h-full w-full object-cover object-center animate-bg-drop">
                <img src="{{ asset('brand/img/bg_home_blur1.png') }}" alt="" class="animate-blur-left-in pointer-events-none absolute -left-36 top-[-18%] z-[3] hidden h-[178%] w-auto max-w-none opacity-70 [filter:brightness(1.55)_saturate(1)] md:block">
                <img src="{{ asset('brand/img/bg_home_blur2.png') }}" alt="" class="animate-blur-right-in pointer-events-none absolute -right-48 top-[-20%] z-[3] hidden h-[182%] w-auto max-w-none opacity-65 [filter:brightness(1.45)_saturate(1)] md:block">
            </div>

            <div class="absolute inset-0 z-[2] bg-[radial-gradient(circle_at_50%_34%,rgba(15,33,46,0)_0%,rgba(3,9,13,0.12)_36%,rgba(2,4,6,0.86)_80%),linear-gradient(90deg,rgba(6,25,36,0.34)_0%,rgba(0,0,0,0.08)_42%,rgba(40,21,64,0.22)_100%)]"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 z-[3] h-28 bg-[linear-gradient(180deg,rgba(255,255,255,0.12)_0%,rgba(255,255,255,0)_100%)]"></div>

            <div class="relative">
                @include('partials.site-header', ['brand' => $brand, 'isDark' => true])

                <div class="relative z-10 mx-auto flex min-h-[calc(90svh-96px)] w-full max-w-[1480px] flex-col px-5 pb-0 pt-2 sm:px-8 lg:px-14">
                    <div class="relative flex flex-1 items-center justify-center overflow-hidden pb-8 pt-2 md:pb-10">
                        <div class="animate-hero-copy-in absolute inset-x-0 top-0 z-20 mx-auto w-full max-w-[1300px] px-2">
                            <div class="grid grid-cols-1 items-start gap-4 lg:grid-cols-[0.92fr_1.08fr] lg:gap-4">
                                <div class="text-center lg:text-left">
                                    <p class="text-[clamp(3.3rem,8.1vw,6.2rem)] font-light uppercase leading-[0.9] tracking-[0.01em] text-[#a8ecff] [text-shadow:0_5px_10px_rgba(0,0,0,0.35)]">
                                        DIGITAL
                                    </p>
                                    <p class="mt-2 text-[clamp(2.9rem,6.4vw,4.8rem)] font-light uppercase leading-[0.9] tracking-[0.01em] text-white [text-shadow:0_5px_10px_rgba(0,0,0,0.35)]">
                                        WEDDING
                                    </p>
                                </div>

                                <div class="pt-2 text-center lg:pt-16 lg:text-right">
                                    <p class="text-[clamp(3rem,7.3vw,5.6rem)] font-light uppercase leading-[0.9] tracking-[0.01em] text-[#a8ecff] [text-shadow:0_5px_10px_rgba(0,0,0,0.35)]">
                                        INVITATION
                                    </p>
                                    <p class="mt-2 text-[clamp(2.4rem,5vw,3.8rem)] font-light uppercase leading-[0.88] tracking-[0.01em] text-white [text-shadow:0_5px_10px_rgba(0,0,0,0.35)]">
                                        SYSTEM
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="animate-hero-copy-in relative z-20 mx-auto mt-[13rem] flex w-full max-w-[900px] flex-col items-center text-center sm:mt-[15rem] lg:mt-[17rem]">
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#a8ecff]">Invitely by NechCode</p>
                            <p class="mt-4 max-w-[42rem] text-[clamp(0.92rem,1.2vw,1.12rem)] leading-[1.75] text-white/80">
                                Platform undangan digital yang berfokus pada pengalaman tamu: personalisasi undangan, RSVP, peta acara, album, ucapan, gift confirmation, dan QR attendance dalam satu halaman yang elegan.
                            </p>
                            <div class="mt-6 flex flex-wrap justify-center gap-3">
                                @guest
                                    <a href="{{ route('register') }}" class="brand-action brand-action-solid">Mulai Sekarang</a>
                                    <a href="{{ route('login') }}" class="brand-action brand-action-ghost">Login Client</a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="brand-action brand-action-solid">Buka Dashboard</a>
                                    <a href="{{ route('dashboard.events.create') }}" class="brand-action brand-action-ghost">Buat Event</a>
                                @endguest
                            </div>
                        </div>

                        <div class="pointer-events-none absolute inset-x-0 bottom-0 z-10 flex justify-center px-4 sm:px-8">
                            <div class="animate-service-asset-rise relative h-[50vw] w-full max-w-[980px] min-h-[260px] sm:h-[42vw] md:h-[35vw] lg:h-[28vw] xl:h-[26vw]">
                            <div class="hero-envelope absolute inset-x-[6%] bottom-[2%] top-[14%]">
                                <div class="hero-envelope-letter">
                                    <span class="hero-envelope-line" style="top: 26%;"></span>
                                    <span class="hero-envelope-line" style="top: 42%; left: 24%; right: 24%;"></span>
                                    <span class="hero-envelope-line" style="top: 58%; left: 28%; right: 28%;"></span>
                                </div>
                                    <div class="hero-envelope-seal"></div>
                                </div>
                            </div>
                        </div>

                        <a href="#portfolio" class="hero-scroll-hint animate-hero-copy-in absolute bottom-6 left-1/2 z-20 -translate-x-1/2 md:bottom-10">
                            <span>Scroll</span>
                            <span class="h-3 w-3 rotate-45 border-b-2 border-r-2 border-white transition-colors"></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="absolute inset-x-0 bottom-0 z-20 h-[2px] bg-white/90"></div>
        </section>

        <main class="bg-white text-on-surface">
            <section id="portfolio" class="bg-white py-14 md:py-18">
                <div class="shell">
                    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div class="max-w-3xl">
                            <p class="section-kicker">Portfolio highlights</p>
                            <h2 class="section-heading-title !mt-3 !max-w-[16ch]">Lihat arah visual template sebelum masuk ke detail produk.</h2>
                        </div>
                        <p class="max-w-xl text-[0.95rem] leading-7 text-on-surface-variant">
                            Dua jalur preview ini berjalan terus untuk menunjukkan range template yang sudah bisa dipilih langsung dari wizard event.
                        </p>
                    </div>
                </div>

                <div class="portfolio-marquee">
                    <div class="portfolio-marquee-track">
                        @foreach (array_merge($portfolioSlides, $portfolioSlides) as $slide)
                            <article class="portfolio-marquee-card">
                                <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }} preview" loading="lazy">
                                <div class="portfolio-marquee-meta">
                                    <p class="portfolio-marquee-title">{{ $slide['title'] }}</p>
                                    <p class="portfolio-marquee-copy">{{ $slide['copy'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="portfolio-marquee mt-4">
                    <div class="portfolio-marquee-track reverse">
                        @foreach (array_merge(array_reverse($portfolioSlides), array_reverse($portfolioSlides)) as $slide)
                            <article class="portfolio-marquee-card">
                                <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }} preview" loading="lazy">
                                <div class="portfolio-marquee-meta">
                                    <p class="portfolio-marquee-title">{{ $slide['title'] }}</p>
                                    <p class="portfolio-marquee-copy">{{ $slide['copy'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="about" class="bg-white py-20 md:py-24 lg:py-28">
                <div class="shell">
                    <div class="grid grid-cols-1 gap-12 lg:grid-cols-[minmax(260px,0.38fr)_minmax(0,1fr)] lg:items-start lg:gap-10 xl:gap-14">
                        <x-section-heading
                            kicker="Digital invitation highlights"
                            title="Pengalaman undangan yang terasa personal, tenang, dan siap dibuka di mobile."
                            copy="Setiap blok di bawah ini menjelaskan hasil akhir yang langsung terasa oleh tamu: sapaan yang relevan, detail acara yang rapi, mood visual yang konsisten, dan alur interaksi yang lengkap."
                            title-class="max-w-[22ch] text-[32px] tracking-[-0.02em] text-[#121212]"
                            copy-class="max-w-[31ch] text-[1.02rem]"
                        />

                        <div class="grid gap-5 md:grid-cols-2">
                            @foreach ($highlightCards as $item)
                                <article class="highlight-detail-card">
                                    <div class="relative z-10 flex h-full flex-col justify-between p-7">
                                        <div>
                                            <p class="highlight-detail-number">{{ $item['eyebrow'] }}</p>
                                            <h3 class="highlight-detail-title">{{ $item['title'] }}</h3>
                                            <p class="highlight-detail-copy">{{ $item['copy'] }}</p>
                                        </div>
                                        <div class="highlight-detail-tags">
                                            @foreach ($item['tags'] as $tag)
                                                <span class="highlight-detail-tag">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section id="services" class="bg-white pb-20">
                <div class="shell">
                    <x-section-heading
                        kicker="Services"
                        title="Paket invitation yang dibuat khusus untuk kebutuhan pernikahan digital."
                        title-class="max-w-[20ch]"
                    />
                </div>
            </section>

            <section id="system" class="bg-[#F8FBFE] py-20 md:py-24">
                <div class="shell">
                    <x-section-heading
                        kicker="Feature system"
                        title="Semua komponen penting undangan digital sudah tersusun dalam satu flow."
                        align="center"
                        title-class="max-w-[19ch] text-[clamp(1.75rem,3.2vw,3.15rem)]"
                    />

                    <div class="mt-14 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($systemCards as $item)
                            <article class="feature-flow-card">
                                <span class="feature-flow-number">{{ $item['number'] }}</span>
                                <div class="mt-auto pt-12">
                                    <h3 class="feature-flow-title">{{ strtoupper($item['title']) }}</h3>
                                    <p class="feature-flow-copy">{{ $item['copy'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="pricing" class="w-full bg-white pb-24 md:pb-32">
                <div class="shell">
                    <x-section-heading
                        kicker="Normal Pricelist"
                        title="Harga inti undangan digital dan add-on yang mengikuti aturan produk."
                        align="center"
                        class="mx-auto max-w-[76rem] pb-12 pt-20 md:pt-24"
                        title-class="max-w-[19ch]"
                    />

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-3 lg:gap-5 md:items-start">
                        <article class="pricing-showcase-card light">
                            <div class="relative z-10 flex h-full flex-col text-[#0D0D0D]">
                                <div class="flex items-start justify-between gap-4">
                                    <h3 class="pricing-showcase-heading gradient">
                                        <span class="block">Template</span>
                                        <span class="block">Standar</span>
                                    </h3>
                                </div>

                                <p class="pricing-showcase-price text-[#0D0D0D]">
                                    <span class="block">IDR</span>
                                    <span class="block">200.000 -</span>
                                    <span class="block">300.000</span>
                                </p>

                                <div class="pricing-showcase-divider bg-[#292929]"></div>

                                <p class="pricing-showcase-copy max-w-[24ch] text-[#717171]">
                                    Undangan digital utama dengan visual premium mobile-first untuk general invitation maupun personal invitation.
                                </p>

                                <ul class="pricing-showcase-list">
                                    @foreach ($basePackageFeatures as $feature)
                                        <li class="flex items-start gap-4">
                                            <span class="material-symbols-outlined mt-1 text-[#1D5A8D]">check_circle</span>
                                            <span class="font-body text-[1.04rem] leading-[1.35] text-[#0D0D0D]">{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <a href="{{ route('register') }}" class="pricing-showcase-button light">
                                    Pilih paket ini
                                </a>
                            </div>
                        </article>

                        <article class="pricing-showcase-card dark">
                            <div class="relative z-10 flex h-full flex-col text-white">
                                <div class="flex items-start justify-between gap-4">
                                    <h3 class="pricing-showcase-heading gradient-light">
                                        <span class="block">RSVP Tracking</span>
                                        <span class="block">+ Gifting</span>
                                        <span class="block">On-Site</span>
                                    </h3>
                                    <span class="pricing-showcase-tag bg-[#2A6DA8] text-[#B8D8F7]">Best Choice</span>
                                </div>
                                <p class="pricing-showcase-price text-white">
                                    <span class="block">+ IDR</span>
                                    <span class="block">75.000</span>
                                </p>
                                <div class="pricing-showcase-divider bg-white/92"></div>
                                <p class="pricing-showcase-copy text-white/72">
                                    Menambahkan flow RSVP yang lebih terukur sekaligus fitur gifting untuk kebutuhan konfirmasi hadiah tamu.
                                </p>
                                <ul class="pricing-showcase-list">
                                    @foreach ([
                                        'RSVP per tamu lebih rapi untuk dipantau dari dashboard.',
                                        'Gift confirmation menyatu dengan invitation flow.',
                                        'Cocok untuk penawaran bundling wedding + hadiah.',
                                    ] as $feature)
                                        <li class="flex items-start gap-4">
                                            <span class="material-symbols-outlined mt-1 text-[#A8ECFF]">check_circle</span>
                                            <span class="font-body text-[1.04rem] leading-[1.35] text-white">{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('register') }}" class="pricing-showcase-button dark">
                                    Tambahkan add-on
                                </a>
                            </div>
                        </article>

                        <article class="pricing-showcase-card light">
                            <div class="relative z-10 flex h-full flex-col text-[#0D0D0D]">
                                <h3 class="pricing-showcase-heading gradient">
                                    <span class="block">Automation</span>
                                    <span class="block">kirim undangan</span>
                                </h3>
                                <p class="pricing-showcase-price text-[#0D0D0D]">
                                    <span class="block">+ IDR</span>
                                    <span class="block">50.000</span>
                                </p>
                                <div class="pricing-showcase-divider bg-[#292929]"></div>
                                <p class="pricing-showcase-copy text-[#717171]">
                                    Opsional untuk distribusi WhatsApp undangan secara lebih praktis melalui channel yang sudah disiapkan.
                                </p>
                                <ul class="pricing-showcase-list">
                                    @foreach ([
                                        'Queue campaign dari satu panel client.',
                                        'Device Fonnte tetap milik client, bukan shared token.',
                                        'Personal link per tamu tetap berbeda untuk tiap pesan.',
                                    ] as $feature)
                                        <li class="flex items-start gap-4">
                                            <span class="material-symbols-outlined mt-1 text-[#1D5A8D]">check_circle</span>
                                            <span class="font-body text-[1.04rem] leading-[1.35] text-[#0D0D0D]">{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('register') }}" class="pricing-showcase-button light">
                                    Aktivasi automation
                                </a>
                            </div>
                        </article>

                        <article class="pricing-showcase-card dark md:col-span-3">
                            <div class="relative z-10 grid gap-8 lg:grid-cols-[1.3fr_0.9fr_0.7fr] lg:items-end">
                                <div class="max-w-3xl">
                                    <p class="text-sm uppercase tracking-[0.12em] text-white/72">Add On Service</p>
                                    <h3 class="mt-4 text-[clamp(2rem,3vw,3rem)] font-semibold uppercase leading-[1.02] tracking-[-0.04em] text-white">
                                        Custom Template / Design
                                    </h3>
                                    <p class="mt-4 font-body text-[1rem] leading-[1.7] text-white/82">
                                        Untuk kebutuhan visual yang lebih eksklusif dan tidak memakai template standar.
                                    </p>
                                </div>
                                <ul class="space-y-3 text-sm leading-7 text-white/82">
                                    <li>Penyesuaian visual penuh agar invitation terasa lebih personal.</li>
                                    <li>Lebih cocok untuk pasangan yang ingin identitas acara berbeda dari template dasar.</li>
                                    <li>Masih tetap menyatu dengan flow RSVP, gift, dan attendance yang sama.</li>
                                </ul>
                                <div class="flex flex-col gap-5 lg:items-end">
                                    <p class="font-sans text-[clamp(2.2rem,3.2vw,3.4rem)] font-semibold leading-none tracking-[-0.04em] text-[#A8ECFF]">
                                        + IDR 150.000
                                    </p>
                                    <a href="{{ route('register') }}" class="pricing-showcase-button dark !mx-0 max-w-[17rem]">
                                        Request custom design
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="contact" class="bg-white pb-16 md:pb-20">
                <div class="shell">
                    <div class="rounded-[2rem] border border-[#1D5A8D]/18 bg-[linear-gradient(120deg,rgba(29,90,141,0.06),rgba(168,236,255,0.12))] p-8 md:p-10">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-secondary">Next step</p>
                        <h2 class="mt-3 text-5xl text-primary md:text-6xl">Pilih invitation yang tenang, personal, dan siap dibagikan.</h2>
                        <p class="mt-5 max-w-3xl text-base leading-8 text-on-surface-variant">
                            Halaman ini sekarang mengikuti gaya layanan terbaru NechCode, tetapi seluruh isi dan arahnya sudah dikunci khusus untuk digital wedding invitation system.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            @guest
                                <a href="{{ route('register') }}" class="brand-action brand-action-primary">Buat akun</a>
                                <a href="{{ route('login') }}" class="brand-action brand-action-surface">Login client</a>
                            @else
                                <a href="{{ route('dashboard') }}" class="brand-action brand-action-primary">Masuk ke dashboard</a>
                            @endguest
                            <a href="https://wa.me/{{ $brand['whatsapp'] }}" target="_blank" rel="noreferrer" class="brand-action brand-action-surface">Diskusi via WhatsApp</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        @include('partials.site-footer', ['brand' => $brand])
    </div>
</body>
</html>
