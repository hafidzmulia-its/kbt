# Invitely User Flow Backlog

Dokumen ini memetakan gap antara flow user Invitely saat ini dan pola produk yang berhasil dipakai oleh Wedew, dengan prinsip:

- jangan membuat user tenggelam dalam terlalu banyak opsi
- ambil lapisan fitur yang benar-benar menaikkan value
- prioritaskan wizard, operasional tamu, distribusi, dan hari-H
- hindari fitur partner/collaborator karena memang tidak masuk scope

## Prinsip Produk

Invitely tidak akan meniru Wedew 1:1.

Yang dipertahankan sebagai identitas Invitely:

- visual lebih tenang dan premium
- WhatsApp-first invitation flow
- personal route per tamu yang aman
- AI-assisted copy dan bahasa lokal
- wedding invitation + guest ops + gifting dalam satu sistem

## Fase 1 - Wizard dan Command Center

Target:

- builder diubah dari form panjang menjadi wizard sederhana
- workspace diubah menjadi command center berbasis readiness dan next actions

Status:

- sudah diimplementasikan

## Fase 2 - Guest Operations yang Lebih Matang

Target:

- grup tamu
- bulk action per grup
- VIP / undangan fisik
- archive / restore tamu
- import preview sebelum commit
- impor XLSX selain CSV

File / modul:

- migration `guest_groups`
- relasi `guests.group_id`
- `GuestController`
- `GuestManagementService`
- `dashboard/guests/index.blade.php`

Status:

- inti implementasi sudah masuk:
- guest group per event
- bulk action tamu
- flag VIP / undangan fisik
- archive / restore
- preview import CSV / XLSX sebelum commit
- masih bisa diteruskan nanti untuk:
- import template download
- deduplication warning yang lebih eksplisit di UI
- preset segment untuk broadcast dan RSVP

## Fase 3 - RSVP dan Session Control

Target:

- sesi acara per grup
- override sesi per tamu
- RSVP setting per grup
- custom RSVP field yang tetap sederhana

File / modul:

- migration `event_sessions` jika perlu dipisah dari schedules
- pivot atau mapping grup ke sesi
- setting RSVP per grup
- public invitation rendering untuk sesi personal

Status:

- sudah diimplementasikan dengan pendekatan ringan:
- pivot grup tamu ke `event_schedules`
- RSVP menyimpan `event_schedule_id`
- personal invitation hanya menampilkan sesi yang memang tersedia untuk grup tamu
- dashboard RSVP punya matrix grup x sesi + filter sesi
- catatan: custom RSVP field per grup belum dibuat, itu tetap masuk backlog lanjutan

## Fase 4 - Delivery Pipeline

Target:

- template pesan WhatsApp reusable
- preview pesan final per tamu
- jadwal kirim
- cancel pesan terjadwal
- status delivery yang lebih jelas
- filter kirim berdasarkan grup / RSVP / belum buka

File / modul:

- `broadcast_message_templates`
- perluasan `broadcast_campaigns`
- `BroadcastCampaignController`
- `BroadcastCampaignService`
- `dashboard/broadcasts/index.blade.php`

Status:

- sudah diimplementasikan sebagian besar:
- reusable broadcast template
- preview audience + sample personalized message
- targeting per grup / RSVP / VIP / opened state / undangan fisik
- scheduling campaign
- cancel queued / scheduled campaign
- retry failed tetap tersedia
- catatan: status delivery masih berbasis log internal dan belum ada webhook sinkronisasi provider

## Fase 5 - Flexible Guest Data

Target:

- custom field tamu
- field tambahan di dashboard
- field tambahan untuk RSVP
- field tambahan untuk scanner

File / modul:

- migration `event_guest_fields`
- migration `guest_field_values`
- service normalizer untuk field tambahan

## Fase 6 - Guest Intake dan Form Publik

Target:

- form pendaftaran tamu per grup
- intake data langsung ke dashboard
- optional approval / moderation

File / modul:

- public form route
- `GuestRegistrationController`
- group-level form settings

## Fase 7 - Event Day Toolkit

Target:

- scanner mode check-in / check-out
- greeting screen / layar sapa
- print label tamu
- print kartu QR
- daftar tamu cetak

File / modul:

- extend `staff_access_links`
- template print views
- attendance presentation view

## Fase 8 - Publish and Site Controls

Target:

- publish toggle yang lebih jelas
- preview device mode
- favicon / og image / thumbnail
- domain / subdomain warning UX
- notifikasi event

## Prioritas Implementasi

Urutan yang disarankan:

1. grup tamu + import preview
2. message template + scheduled send
3. RSVP per grup + sesi acara per grup
4. archive / restore tamu
5. custom field tamu
6. label / QR print
7. greeting screen

## Catatan Scope

Fitur berikut tidak diprioritaskan:

- partner / collaborator management
- mobile app khusus penerima tamu
- terlalu banyak variasi paket yang membingungkan user
- dashboard dengan terlalu banyak statistik yang tidak dipakai

Invitely harus terasa lebih sederhana dari Wedew, bukan lebih miskin fitur secara membabi buta.
