@php
    $brand = $brand ?? config('nechcode.brand');
    $aboutLinks = [
        ['label' => 'About Invitely', 'href' => route('welcome')],
        ['label' => 'Digital Invitation', 'href' => route('welcome').'#services'],
        ['label' => 'Feature System', 'href' => route('welcome').'#system'],
        ['label' => 'Pricing', 'href' => route('welcome').'#pricing'],
    ];
    $serviceLinks = [
        ['label' => 'Website & Landing Pages', 'href' => 'https://nechcode.id/services/web'],
        ['label' => 'Mobile Applications', 'href' => 'https://nechcode.id/services/mobile'],
        ['label' => 'AI Automation & Chatbot', 'href' => 'https://nechcode.id/services/ai'],
        ['label' => 'Predictive Data', 'href' => 'https://nechcode.id/services/predictive-data'],
    ];
@endphp

<footer id="contact" class="w-full bg-black text-white">
    <div class="shell py-16 md:py-20 lg:py-24">
        <div class="grid grid-cols-1 gap-14 lg:grid-cols-[minmax(0,1.2fr)_minmax(240px,0.42fr)_minmax(280px,0.5fr)] lg:gap-12">
            <div>
                <a href="{{ route('welcome') }}" aria-label="{{ $brand['name'] }}" class="inline-flex shrink-0">
                    <img src="{{ asset('brand/logonav.png') }}" alt="{{ $brand['name'] }}" class="h-auto w-[210px] md:w-[250px] lg:w-[300px]">
                </a>

                <div class="mt-10 flex items-center gap-5">
                    @foreach ($brand['socials'] as $social)
                        <a href="{{ $social['href'] }}" target="_blank" rel="noreferrer" aria-label="{{ $social['label'] }}" class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-white text-[#2B67A1] transition-transform hover:-translate-y-0.5">
                            <img src="{{ asset($social['icon']) }}" alt="" class="h-7 w-7 object-contain">
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="brand-footer-heading">About us</h3>
                <nav class="mt-8 flex flex-col gap-5">
                    @foreach ($aboutLinks as $item)
                        <a href="{{ $item['href'] }}" class="brand-footer-link">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </div>

            <div>
                <h3 class="brand-footer-heading">Services</h3>
                <nav class="mt-8 flex flex-col gap-5">
                    @foreach ($serviceLinks as $item)
                        <a href="{{ $item['href'] }}" target="_blank" rel="noreferrer" class="brand-footer-link">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="mt-14 md:mt-16">
            <img src="{{ asset('brand/img/asset_line.png') }}" alt="" class="block h-auto w-full opacity-20">
        </div>

        <p class="mt-12 text-center font-body text-[clamp(1rem,1vw,1.2rem)] font-normal text-white/92">
            &copy; {{ now()->year }} Invitely by
            <a href="https://nechcode.id" target="_blank" rel="noreferrer" class="font-semibold text-white underline decoration-white/30 underline-offset-4 transition hover:text-[#9fe8ff] hover:decoration-[#9fe8ff]">
                NechCode
            </a>
            | All Rights Reserved
        </p>
    </div>
</footer>
