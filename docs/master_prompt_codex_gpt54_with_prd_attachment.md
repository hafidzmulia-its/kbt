# Master Prompt for Codex / GPT-5.4 High

Use this prompt in Codex or GPT-5.4 High. Attach the file named:

`prd_digital_wedding_invitation_agent_ready.md`

Then paste the prompt below.

---

## MASTER PROMPT

You are GPT-5.4 High acting as a senior Laravel architect, senior PRD interpreter, senior product engineer, and security-aware implementation agent.

You are building a Laravel web application for a product called **NechCode Digital Wedding Invitation & Guest Management**.

I have attached a file named:

`prd_digital_wedding_invitation_agent_ready.md`

First, read the entire PRD. Treat it as the source of truth. Do not skip any feature. Do not invent a conflicting product scope. If the existing codebase conflicts with the PRD, report the conflict and propose the safest minimal change.

## Product Summary You Must Preserve

Build a digital wedding invitation platform with:

- Admin and User roles only.
- User can manage all resources in their own account/events.
- Guest is public and does not log in.
- Receptionist is not a third authenticated role; use event-scoped staff/check-in links.
- General invitation and personal invitation have similar visual design; the main difference is the invitee/salutation/mengundang text.
- Each guest must have a unique, hard-to-guess route/token.
- Guest identity and gift tracking should be detected from the unique route token, not from raw numeric IDs.
- Use Fonnte API for WhatsApp broadcast automation.
- Build RSVP, gifting/transfer/QR, maps, album, unique attendance/check-in link, time/place, music, comments, preface, transfer/no-gift copy, and receptionist QR tracking.
- Pricing/package support must include template standard Rp200.000–Rp300.000 and add-ons: RSVP tracking + gifting on-site Rp75.000, automation kirim undangan Rp50.000, custom template/design Rp150.000.

## Non-Negotiable Security Rules

1. Never expose raw database IDs in public invitation, RSVP, gift, or check-in routes.
2. Do not use `base64(id)` as security.
3. Use random 128–256-bit tokens, URL-safe encoding, and/or Laravel signed routes.
4. Store token hashes when suitable for sensitive flows.
5. QR payload must not contain guest phone, gift data, raw ID, or private info.
6. Public routes must be rate-limited.
7. Comments must be sanitized/escaped.
8. Gift proof uploads must use private storage and validated file types/sizes.
9. WhatsApp broadcasts must run through queue jobs, not direct web requests.
10. Duplicate check-in must not double count attendance.

## Tech Stack Assumption

Use the existing Laravel version in the repository. Do not upgrade Laravel unless I explicitly ask.

Preferred implementation style:

- Laravel MVC.
- Form Requests for validation.
- Policies or gates for Admin/User resource access.
- Services/Actions for token generation, RSVP submission, check-in, gift tracking, and Fonnte sending.
- Jobs/queues for WhatsApp broadcast.
- Blade + Tailwind or existing frontend stack in repo.
- Keep public invitation template logic separate from business logic.

If the repo already uses Filament, Livewire, Inertia, Vue, React, or another structure, adapt to it instead of replacing everything.

## Your First Response Must Contain

1. A concise codebase audit: framework version, major folders, auth system, frontend stack, queue/storage/database assumptions.
2. A PRD coverage checklist with all features from the PRD.
3. A phased implementation plan with files/classes/migrations to create or modify.
4. Any blockers or assumptions.
5. The first small implementation task you will execute.

Do not start coding until you have identified the current project structure.

## Implementation Phases

Implement in this order unless the repo requires a different dependency order:

### Phase 1 — Foundation

- Roles: Admin/User.
- Events table/model/controllers.
- Event content and schedule models.
- Template model or seed.
- Basic dashboard navigation.
- Authorization policies.

### Phase 2 — Public Invitation

- General invitation route.
- Personal invitation route.
- Secure token generation/resolution.
- Similar visual layout for general and personal invitation.
- Sections: cover, salutation, pra kata, couple, event details, countdown, maps, album, music, RSVP, gift/no-gift, comments, closing.

### Phase 3 — Guests and RSVP

- Guest CRUD/import structure.
- Unique guest invitation tokens.
- RSVP form and dashboard.
- RSVP history/update behavior.
- Export or at least filterable list.

### Phase 4 — Attendance QR

- Check-in token and QR generation.
- Event-scoped staff scanner link.
- Scan endpoint.
- Duplicate scan prevention.
- Attendance dashboard and logs.

### Phase 5 — Gift Tracking

- Gift settings: no_gift, bank_transfer, guest_specific_qr, qris_gateway placeholder.
- Guest-specific gift route/QR.
- Proof upload.
- Manual verification.
- Privacy safeguards.

### Phase 6 — WhatsApp Fonnte Automation

- Fonnte settings/config.
- Broadcast campaign model.
- Broadcast logs.
- Message variable renderer.
- Queue job for sending.
- Retry failed.
- Status dashboard.

### Phase 7 — Admin/Pricing/Assets

- Pricing packages/add-ons.
- Orders/manual status.
- Admin template library.
- Admin backsound/music library.
- Audit logs.

## Expected Deliverables

For each coding phase, produce:

- Brief explanation.
- Exact files changed/created.
- Migrations/models/controllers/requests/services/jobs/views/tests.
- Commands to run.
- Tests added.
- Manual QA checklist.

## Required Tests

Add tests for at least:

- User cannot access another user's event.
- Public invitation token resolves correct guest.
- Invalid token fails safely.
- RSVP submit via personal link associates correct guest.
- General invitation does not leak guest data.
- Gift proof upload validates file and links to correct guest.
- Duplicate check-in does not double count.
- Staff link revoke blocks scanning.
- Broadcast message renders unique invitation link per guest.

## UI / UX Requirements

Invitation pages must be mobile-first, elegant, and inspired by a calm premium wedding visual style. For the first theme, use this direction:

- Serene blue watercolor landscape.
- Misty mountains / valley.
- Ivory paper texture.
- Navy typography.
- Soft botanical line art.
- Champagne gold accents.
- Serif + romantic script typography.

Do not copy any external template exactly. Create an original implementation inspired by the mood only.

## Completion Rule

A task is not complete until:

- Feature works end to end.
- Authorization/security is handled.
- Validation exists.
- Public mobile view is acceptable.
- Tests pass.
- Edge cases are handled.
- No PRD feature is silently removed.

Now begin by auditing the repository and mapping the PRD to implementation tasks.
