# Implementation Backlog — Laravel Digital Wedding Invitation

## Epic 1 — Auth, Roles, and Dashboard

- Add role field: admin/user.
- Add policy checks for event ownership.
- Create dashboard shell.
- Admin can list users/events/templates/orders.
- User can manage own events.

## Epic 2 — Event Builder

- Event CRUD.
- Event content: pra kata, closing, couple data.
- Event schedules: akad/resepsi/custom.
- Location/maps.
- Template selection.
- Publish/unpublish.

## Epic 3 — Public Invitation

- General invitation route.
- Personal invitation route.
- Shared Blade template with conditional salutation.
- Cover, couple, event, maps, album, music, RSVP, gift, comments, closing.
- Mobile-first responsive UI.

## Epic 4 — Guest Tokens and Personal Links

- Guest CRUD/import.
- Generate random public token.
- Store token hash.
- Cache invitation URL.
- Regenerate token.
- Rate-limit token routes.

## Epic 5 — RSVP

- RSVP forms for general/personal.
- RSVP dashboard.
- Status filters.
- Export.
- RSVP update history.

## Epic 6 — Attendance QR

- Generate check-in token per guest.
- QR generation.
- Staff scanner link.
- Scan endpoint.
- Manual search fallback.
- Duplicate scan detection.
- Attendance dashboard.

## Epic 7 — Gifts

- Gift settings.
- No-gift mode.
- Bank transfer mode.
- Guest-specific gift confirmation route.
- QR for gift route.
- Proof upload.
- Manual verification.

## Epic 8 — Album and Music

- Photo upload/reorder.
- Image optimization.
- Music library.
- Music selection.
- Guest play/pause controls.

## Epic 9 — Comments

- Guestbook form.
- Moderate comments.
- Spam/rate-limit/honeypot.
- XSS-safe rendering.

## Epic 10 — WhatsApp Broadcast via Fonnte

- Fonnte config.
- Message templates.
- Variable renderer.
- Broadcast campaigns.
- Queue jobs.
- Logs and retry.

## Epic 11 — Pricing and Orders

- Packages and add-ons.
- Manual order status.
- Feature toggles based on package/add-on.

## Epic 12 — Audit and Reports

- Audit logs.
- Export guests/RSVP/attendance/gifts.
- Dashboard metrics.

## Suggested First 10 Engineering Tasks

1. Audit existing repo structure and Laravel version.
2. Add/confirm role and event ownership policy.
3. Create event, event_content, event_schedules migrations/models.
4. Create template seed and basic template preview.
5. Build User event CRUD.
6. Build public general invitation route/view.
7. Add guests and guest_invitations tables with secure tokens.
8. Build personal invitation route/view.
9. Add RSVP form and dashboard counts.
10. Add tests for ownership and token resolution.
