<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scanner Check-in</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-slate-950 text-white">
    <main class="mx-auto max-w-5xl px-4 py-8">
        <div class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
            <section class="rounded-[32px] bg-slate-900 p-6">
                <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Staff mode</p>
                <h1 class="mt-3 text-4xl font-semibold">{{ $event->couple_name_display }}</h1>
                <p class="mt-2 text-sm text-slate-400">Invited: {{ $event->guests_count }} • RSVP: {{ $event->rsvps_count }} • Checked-in: {{ $event->attendance_checkins_count }}</p>
                <video id="preview" class="mt-6 aspect-video w-full rounded-[28px] bg-slate-800" autoplay playsinline muted></video>
                <div class="mt-6 grid gap-4">
                    <input id="payload" class="field bg-white text-slate-900" placeholder="Paste payload NCI:eventCode:token">
                    <button id="submit-payload" class="btn-primary" type="button">Submit payload</button>
                </div>
                <div id="result" class="mt-6 rounded-[24px] bg-slate-800 p-4 text-sm text-slate-200">Menunggu scan, manual payload, atau pencarian tamu.</div>
            </section>

            <section class="rounded-[32px] bg-slate-900 p-6">
                <p class="text-sm uppercase tracking-[0.3em] text-slate-400">Fallback manual</p>
                <h2 class="mt-3 text-3xl font-semibold">Cari tamu jika QR gagal</h2>
                <div class="mt-6 flex gap-3">
                    <input id="search-query" class="field bg-white text-slate-900" placeholder="Cari nama, phone, atau group">
                    <button id="search-button" class="btn-secondary" type="button">Cari</button>
                </div>
                <div id="search-results" class="mt-5 space-y-3 text-sm text-slate-200">
                    <p class="text-slate-400">Belum ada pencarian.</p>
                </div>
            </section>
        </div>
    </main>

    <script>
        const endpoint = @json(route('staff.checkin.scan', ['staffToken' => request()->route('staffToken')]));
        const searchEndpoint = @json(route('staff.checkin.search', ['staffToken' => request()->route('staffToken')]));
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || @json(csrf_token());
        const result = document.getElementById('result');
        const payloadInput = document.getElementById('payload');
        const searchInput = document.getElementById('search-query');
        const searchResults = document.getElementById('search-results');

        function renderResult(data) {
            result.innerHTML = `
                <div class="space-y-1">
                    <p class="font-semibold text-white">${data.guest?.name ?? 'Unknown guest'}</p>
                    <p>Status: ${data.status}</p>
                    <p>Group: ${data.guest?.group_name ?? '-'}</p>
                    <p>Checked in at: ${data.checked_in_at ?? '-'}</p>
                </div>
            `;
        }

        async function submitScan(body) {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify(body)
            });

            const data = await response.json();

            if (!response.ok) {
                result.textContent = data.message || 'Gagal melakukan check-in.';
                return;
            }

            renderResult(data);
        }

        async function searchGuests() {
            const query = searchInput.value.trim();

            if (!query) {
                searchResults.innerHTML = '<p class="text-slate-400">Masukkan kata kunci pencarian.</p>';
                return;
            }

            const response = await fetch(`${searchEndpoint}?q=${encodeURIComponent(query)}`, {
                headers: {'Accept': 'application/json'}
            });
            const data = await response.json();

            if (!response.ok) {
                searchResults.innerHTML = `<p class="text-rose-300">${data.message || 'Pencarian gagal.'}</p>`;
                return;
            }

            if (!data.guests.length) {
                searchResults.innerHTML = '<p class="text-slate-400">Tidak ada tamu yang cocok.</p>';
                return;
            }

            searchResults.innerHTML = data.guests.map((guest) => `
                <div class="rounded-[22px] border border-slate-700 p-4">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-semibold text-white">${guest.name}</p>
                            <p class="text-xs text-slate-400">${guest.phone ?? 'Tanpa phone'} • ${guest.group_name ?? 'Tanpa group'} • RSVP ${guest.rsvp_status}</p>
                        </div>
                        <button type="button" class="btn-primary" data-guest-id="${guest.id}" ${guest.checked_in ? 'disabled' : ''}>
                            ${guest.checked_in ? 'Sudah check-in' : 'Check-in manual'}
                        </button>
                    </div>
                </div>
            `).join('');

            searchResults.querySelectorAll('[data-guest-id]').forEach((button) => {
                button.addEventListener('click', () => submitScan({guest_id: button.dataset.guestId}));
            });
        }

        document.getElementById('submit-payload').addEventListener('click', () => submitScan({payload: payloadInput.value}));
        document.getElementById('search-button').addEventListener('click', searchGuests);
        searchInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchGuests();
            }
        });

        if ('BarcodeDetector' in window && navigator.mediaDevices?.getUserMedia) {
            const detector = new BarcodeDetector({formats: ['qr_code']});
            navigator.mediaDevices.getUserMedia({video: {facingMode: 'environment'}}).then((stream) => {
                const video = document.getElementById('preview');
                video.srcObject = stream;
                const tick = async () => {
                    try {
                        const codes = await detector.detect(video);
                        if (codes[0]?.rawValue) {
                            payloadInput.value = codes[0].rawValue;
                            await submitScan({payload: codes[0].rawValue});
                        }
                    } catch (error) {
                    }
                    requestAnimationFrame(tick);
                };
                tick();
            }).catch(() => {
                result.textContent = 'Kamera tidak tersedia. Gunakan input manual atau pencarian tamu.';
            });
        }
    </script>
</body>
</html>
