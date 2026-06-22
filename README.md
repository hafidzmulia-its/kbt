# NechCode Digital Wedding Invitation & Guest Management

Laravel application for digital wedding invitations, guest personalization, RSVP tracking, gift confirmation, receptionist QR check-in, and WhatsApp broadcast automation via Fonnte.

## Stack

- Laravel 13
- PHP 8.3
- MySQL as the application DBMS
- Tailwind + Blade
- Database queue driver by default

## Local Setup

1. Create a MySQL database, for example `nechcode_wedding`.
2. Copy `.env.example` to `.env`.
3. Set your MySQL credentials in `.env`.
4. Install dependencies:

```bash
composer install
npm install
```

5. Generate the app key and run migrations:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

6. Build frontend assets:

```bash
npm run build
```

7. Start the app:

```bash
php artisan serve
php artisan queue:work
```

## Environment Notes

- Runtime/local development uses MySQL by default.
- Tests still use in-memory SQLite through `phpunit.xml` for speed and isolation.
- Fonnte credentials are managed per user from the dashboard panel, not from `.env`.

## Fonnte Setup In App

1. Login as a user.
2. Open the `Fonnte` menu from the dashboard header.
3. Save:
   - `Account token` if you want device auto-discovery from Fonnte.
   - `Device token` if you want to connect a specific sending device directly.
4. Click `Refresh status` to verify the selected device through Fonnte's device API.
5. Optionally choose a device from the discovered account device list.
6. Send a test message from the same panel.
7. Open an event broadcast page and queue the campaign.

## Seeded Accounts

- Admin: `admin@nechcode.test` / `password`
- User: `user@nechcode.test` / `password`

## Production Deployment

Panduan deploy production via GitHub Actions + Hostdata-style SSH ada di:

- [docs/deployment/hostdata-github-actions.md](/D:/Downloads/KBT/docs/deployment/hostdata-github-actions.md)
- [docs/deployment/hostdata-cicd-known-good.md](/D:/Downloads/KBT/docs/deployment/hostdata-cicd-known-good.md)

## Core Modules

- Admin/User authentication
- Event builder with schedules, copy, gift settings, albums, and backsound selection
- General and personal invitation routes with secure guest tokens
- RSVP tracking
- Gift proof upload to non-public storage
- Receptionist staff links and QR check-in
- Fonnte broadcast campaigns and logs
- Pricing/order records and audit logging
