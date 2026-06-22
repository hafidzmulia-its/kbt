@if ($guest && $invitation)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        const qrContainer = document.getElementById('guest-qr');

        if (qrContainer && window.QRCode) {
            new QRCode(qrContainer, {
                text: @json($invitation->checkin_url_cached),
                width: 180,
                height: 180,
            });
        }
    </script>
@endif

<script>
    document.querySelectorAll('[data-countdown]').forEach((element) => {
        const values = element.querySelectorAll('[data-countdown-value]');
        const target = new Date(element.dataset.countdown);

        const update = () => {
            const diff = target.getTime() - Date.now();

            if (diff <= 0) {
                values.forEach((value) => value.textContent = '0');
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
            const minutes = Math.floor((diff / (1000 * 60)) % 60);
            const seconds = Math.floor((diff / 1000) % 60);

            [days, hours, minutes, seconds].forEach((part, index) => {
                if (values[index]) {
                    values[index].textContent = String(part).padStart(2, '0');
                }
            });
        };

        update();
        setInterval(update, 1000);
    });
</script>
