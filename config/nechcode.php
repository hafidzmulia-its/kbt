<?php

return [
    'brand' => [
        'parent_name' => 'NechCode',
        'product_name' => 'Invitely',
        'name' => 'Invitely by NechCode',
        'tagline' => 'Beautiful digital invitation + operational guest tracking',
        'description' => 'Platform undangan nikah digital yang menggabungkan visual premium, RSVP, gifting, broadcast WhatsApp, dan check-in resepsionis dalam satu alur.',
        'email' => 'projectmulyos1@gmail.com',
        'whatsapp' => '6289531848511',
        'whatsapp_label' => 'NechMin',
        'site_url' => 'https://nechcode.id',
        'socials' => [
            [
                'label' => 'Instagram',
                'href' => 'https://www.instagram.com/nechcode.id',
                'icon' => 'brand/img/instagram_icon.png',
            ],
            [
                'label' => 'TikTok',
                'href' => 'https://www.tiktok.com/@nechcode.id',
                'icon' => 'brand/img/tiktok_icon.png',
            ],
            [
                'label' => 'LinkedIn',
                'href' => 'https://www.linkedin.com/company/NechCode',
                'icon' => 'brand/img/linkedln_icon.png',
            ],
        ],
        'ecosystem_services' => [
            [
                'slug' => 'web',
                'label' => 'Website',
                'price' => 'Mulai dari Rp1.200.000',
                'description' => 'Company profile, landing page, katalog, dan website bisnis yang kredibel, cepat, dan siap dipakai.',
            ],
            [
                'slug' => 'mobile',
                'label' => 'Mobile Apps',
                'price' => 'Scope via konsultasi',
                'description' => 'Aplikasi mobile untuk pengalaman mobile-first, operasi lapangan, dan produk digital yang dipakai langsung pengguna.',
            ],
            [
                'slug' => 'data',
                'label' => 'Predictive Data',
                'price' => 'Mulai dari Rp1.500.000',
                'description' => 'Analisis data, model prediksi, dan visualisasi machine learning untuk bisnis, UMKM, dan riset.',
            ],
            [
                'slug' => 'ai',
                'label' => 'AI Automation & Chatbot',
                'price' => 'Mulai dari Rp1.200.000',
                'description' => 'Chatbot, workflow otomatis, knowledge routing, dan integrasi AI yang mengurangi kerja manual tim.',
            ],
            [
                'slug' => 'invitely',
                'label' => 'Invitely',
                'price' => 'Mulai dari Rp200.000',
                'description' => 'Subdomain wedding product untuk undangan digital premium, personal link, RSVP, gifting, dan QR check-in.',
            ],
        ],
    ],

    'pricing' => [
        'base_package_name' => 'Template Standar',
        'base_package_price' => 250000,
        'base_package_price_range' => 'Rp200.000-Rp300.000',
        'addons' => [
            'tracking_gift' => [
                'label' => 'RSVP tracking + gifting on-site',
                'price' => 75000,
            ],
            'broadcast' => [
                'label' => 'Automation kirim undangan',
                'price' => 50000,
            ],
            'custom_design' => [
                'label' => 'Custom template/design',
                'price' => 150000,
            ],
        ],
    ],
];
