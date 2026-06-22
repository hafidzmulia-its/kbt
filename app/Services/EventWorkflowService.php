<?php

namespace App\Services;

use App\Models\Event;

class EventWorkflowService
{
    public function builderSteps(Event $event): array
    {
        return [
            [
                'key' => 'start',
                'label' => 'Mulai',
                'title' => 'Identitas inti event',
                'copy' => 'Judul event, pasangan, slug, dan status publikasi dasar.',
                'is_complete' => filled($event->title)
                    && filled($event->couple_name_display)
                    && filled($event->bride_name)
                    && filled($event->groom_name)
                    && filled($event->slug),
            ],
            [
                'key' => 'event',
                'label' => 'Acara',
                'title' => 'Jadwal dan venue',
                'copy' => 'Tanggal, sesi utama, venue, alamat, dan tautan peta.',
                'is_complete' => $event->schedules->isNotEmpty()
                    && $event->schedules->contains(fn ($schedule) => filled($schedule->venue_name) && filled($schedule->date)),
            ],
            [
                'key' => 'theme',
                'label' => 'Tema',
                'title' => 'Tema, musik, dan bahasa',
                'copy' => 'Template visual, backsound, serta arah pengalaman publik.',
                'is_complete' => (bool) $event->template_id,
            ],
            [
                'key' => 'content',
                'label' => 'Konten',
                'title' => 'Copy undangan dan gifting',
                'copy' => 'Pra kata, narasi undangan, bio pasangan, dan arahan hadiah.',
                'is_complete' => filled($event->content?->opening_text)
                    && filled($event->content?->invitation_text)
                    && filled($event->content?->closing_text),
            ],
            [
                'key' => 'share',
                'label' => 'Bagikan',
                'title' => 'Publikasi dan operasi',
                'copy' => 'Toggles interaksi, add-on, album, dan jalur preview/publish.',
                'is_complete' => $event->status === 'published'
                    || ($event->albums->isNotEmpty() && $event->is_guest_personalization_enabled),
            ],
        ];
    }

    public function readiness(Event $event, array $capabilities): array
    {
        $items = [
            [
                'label' => 'Identitas event siap',
                'is_complete' => filled($event->title) && filled($event->couple_name_display),
            ],
            [
                'label' => 'Jadwal dan venue terisi',
                'is_complete' => $event->schedules->isNotEmpty()
                    && $event->schedules->contains(fn ($schedule) => filled($schedule->venue_name) && filled($schedule->date)),
            ],
            [
                'label' => 'Tema undangan dipilih',
                'is_complete' => (bool) $event->template_id,
            ],
            [
                'label' => 'Copy publik siap dibuka tamu',
                'is_complete' => filled($event->content?->opening_text)
                    && filled($event->content?->invitation_text)
                    && filled($event->content?->closing_text),
            ],
            [
                'label' => 'Tamu pertama sudah masuk',
                'is_complete' => $event->guests_count > 0,
            ],
            [
                'label' => 'Event sudah dipublish',
                'is_complete' => $event->status === 'published',
            ],
        ];

        if (($capabilities['broadcast'] ?? false) === true) {
            $items[] = [
                'label' => 'Broadcast siap dijalankan',
                'is_complete' => $event->broadcast_campaigns_count > 0,
            ];
        }

        if (($capabilities['tracking'] ?? false) === true) {
            $items[] = [
                'label' => 'Scanner hari-H tersedia',
                'is_complete' => $event->staffAccessLinks->isNotEmpty(),
            ];
        }

        $completed = collect($items)->where('is_complete', true)->count();
        $total = count($items);

        return [
            'items' => $items,
            'completed' => $completed,
            'total' => $total,
            'percentage' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    public function nextActions(Event $event, array $capabilities): array
    {
        $actions = [
            [
                'label' => '01',
                'title' => 'Rapikan builder',
                'copy' => 'Pastikan identitas pasangan, sesi acara, tema, dan copy publik sudah kuat sebelum invitation dibuka ke tamu.',
                'route' => route('dashboard.events.edit', $event),
                'route_label' => 'Buka wizard',
                'is_locked' => false,
            ],
            [
                'label' => '02',
                'title' => 'Isi tamu dan personal link',
                'copy' => 'Masukkan daftar tamu, cek link personal, lalu siapkan jalur RSVP dan check-in.',
                'route' => route('dashboard.guests.index', $event),
                'route_label' => 'Kelola tamu',
                'is_locked' => ! ($capabilities['tracking'] ?? false),
            ],
            [
                'label' => '03',
                'title' => 'Preview lalu publish',
                'copy' => 'Review draft umum dan personal, lalu aktifkan event publik saat semua komponen sudah siap.',
                'route' => route('dashboard.events.preview', $event),
                'route_label' => 'Lihat preview',
                'is_locked' => false,
            ],
            [
                'label' => '04',
                'title' => 'Distribusi dan hari-H',
                'copy' => 'Jalankan broadcast personal, siapkan link scanner, dan pantau RSVP, hadiah, serta kehadiran.',
                'route' => ($capabilities['broadcast'] ?? false)
                    ? route('dashboard.broadcasts.index', $event)
                    : route('dashboard.attendance.index', $event),
                'route_label' => ($capabilities['broadcast'] ?? false) ? 'Buka broadcast' : 'Buka attendance',
                'is_locked' => false,
            ],
        ];

        return $actions;
    }
}
