# PRD.md — Agent-Ready Product Requirements Document

**Product name:** NechCode Digital Wedding Invitation & Guest Management  
**Target stack:** Laravel-based web application  
**Primary business context:** Entrepreneurship / Math ITS subject project  
**Document style:** Optimized for coding agents, product agents, and implementation planning  
**Version:** v1.0  

---

## 0. Executive Summary

Build a digital wedding invitation platform where a paying customer, called **User**, can create and manage a full wedding invitation event: visual invitation, guest list, personal guest routes, RSVP, gift transfer/QR, maps, album, attendance QR tracking at receptionist, WhatsApp invitation automation, comments, music, and event details.

The system has exactly **2 authenticated roles**:

1. **Admin** — platform owner / internal operator.
2. **User** — customer account, usually EO, bride/groom, or wedding organizer. The User can manage the whole resources for their own account and events.

A third actor exists but is **not an authenticated role**:

- **Guest / Visitor** — public recipient who opens a unique invitation link, submits RSVP, sees gift instructions, opens maps, comments, and shows QR at receptionist.

Core product positioning: **beautiful digital invitation + operational guest tracking**, not only invitation design. The MVP research shows strong interest in visual invitation and attendance tracking, so the product must balance aesthetic quality and operational utility.

---

## 1. Product Rationale From MVP Research

Use these as product constraints and prioritization signals:

- Strongest product signals: attractive invitation visuals and tracking attendance/absensi.
- Core feature priority: RSVP, check-in, dashboard kehadiran, reminder, and real-time status.
- User journey should stay simple: create event → add/import guests → send invitation → monitor RSVP/attendance.
- Design should be premium enough to differentiate, but MVP should avoid building a complex design editor too early.
- Pricing sensitivity exists, so feature packaging must be clear and value-based.

**Strategic implication:** Build “Core Ops → Automation → Design Differentiation.”

---

## 2. Goals

### 2.1 Product Goals

- Let a User create a premium digital wedding invitation with two visual variants:
  - **General invitation page**.
  - **Personal invitation page**.
- Let each guest receive a unique, hard-to-guess link.
- Let the User track RSVP, gift intent/transfer status, and on-site attendance.
- Let the User send invitation messages in bulk using the Fonnte WhatsApp API.
- Let the receptionist scan unique guest QR codes to track attendance.
- Let the User offer gifting via transfer/QR or choose “Tidak menerima kado apapun.”
- Let guests enjoy the invitation visually: cover, couple details, event time/place, album, map, music, comments, and closing message.

### 2.2 Business Goals

- Sell template standard packages for **Rp200.000–Rp300.000**.
- Offer paid add-ons:
  - RSVP tracking + gifting on-site: **Rp75.000**.
  - Automation kirim undangan: **Rp50.000**.
  - Custom template/design: **Rp150.000**.
- Make the product attractive for EO and bride/groom customers who want a visual wedding dream and operational tracking.

### 2.3 Technical Goals

- Build on Laravel with maintainable MVC/domain structure.
- Use secure public tokens instead of raw auto-increment IDs in public routes.
- Keep public invitation pages fast, responsive, and mobile-first.
- Queue all WhatsApp broadcasts.
- Keep all critical events auditable: broadcast sent, RSVP submitted, gift proof uploaded, QR scanned, comment moderated.

---

## 3. Non-Goals for MVP

Do **not** build these in MVP unless explicitly requested later:

- Full drag-and-drop invitation editor.
- Real multi-tenant white-label SaaS with custom domain automation.
- Full payment gateway settlement reconciliation if no provider is selected.
- Complex AI design generation pipeline.
- Guest login system.
- Separate receptionist user role. Receptionist access must be handled via event-scoped staff/check-in link owned by User.

---

## 4. Roles and Permissions

### 4.1 Role: Admin

Admin manages platform-wide resources:

- Users.
- Events across all users.
- Templates and template versions.
- Backsound/music library.
- Pricing packages and add-ons.
- Orders/payments/manual purchase status.
- Fonnte integration settings.
- System logs and audit logs.
- Abuse moderation and reported comments.

### 4.2 Role: User

User manages all resources inside their own account:

- Profile/account.
- Wedding events.
- Bride/groom information.
- Invitation content.
- Event schedule, date, time, location, maps.
- Guest list and guest groups.
- General and personal invitation links.
- RSVP settings and RSVP results.
- Attendance/check-in QR tracking.
- Gift/transfer/QR settings.
- Albums/photos.
- Comments/guestbook moderation.
- Backsound selection.
- WhatsApp broadcast campaigns.
- Receptionist scan mode link.
- Reports/export.

### 4.3 Public Actor: Guest / Visitor

Guest does not log in. Guest can:

- Open unique invitation link.
- View personalized invitation.
- Play/pause music.
- View event date/time/location/maps.
- Submit RSVP / kehadiran konfirmasi.
- View gift instructions or “Tidak menerima kado apapun.”
- Upload transfer proof if enabled.
- Write comment/ucapan if enabled.
- View album.
- Show QR code to receptionist.

---

## 5. Core Experience Definition

### 5.1 Experience 1 — Guest / Consumer Experience

The guest should feel the invitation is beautiful, personal, clear, and easy.

Must include:

- Elegant landing/cover animation.
- Couple names.
- Personal salutation: “Kepada Yth. [Guest Name]” on personal page.
- General salutation on general page: “Kepada Bapak/Ibu/Saudara/i.”
- Pra kata / opening words.
- Couple profile.
- Event date, time, and place.
- Map button.
- Album/gallery.
- Backsound/music with user control.
- RSVP form.
- Gift/transfer/QR section.
- “Tidak menerima kado apapun” option.
- Comments/ucapan section.
- QR code for attendance check-in.

### 5.2 Experience 2 — Customer/User Experience: EO / Bride / Groom

The User should feel they can realize the bride’s “visual wedding dream” while controlling guest operations.

Must include:

- Create event wizard.
- Select template.
- Manage invitation content.
- Manage general and personal invitation pages.
- Import/add guests.
- Generate unique links.
- Send WhatsApp invitations via Fonnte.
- Monitor RSVP, gift, comments, and attendance.
- Export guest list/attendance/gift data.
- Configure gift mode safely because QRIS/transfer has privacy/security risk.

### 5.3 Experience 3 — Receptionist / On-site Experience

Receptionist scans guest QR codes and updates attendance quickly.

Important constraint:

- Receptionist is not a new authenticated role.
- Receptionist access uses an event-scoped signed staff URL generated by User.
- Staff URL can be revoked/regenerated.

Must include:

- Scanner page optimized for mobile/tablet.
- Scan QR from guest invitation.
- Manual search fallback by guest name/phone.
- Mark attendance as checked in.
- Show guest group, RSVP status, pax count, gift status indicator if enabled.
- Prevent duplicate scan from double-counting.
- Show real-time count: invited, RSVP yes/no/pending, checked-in.

---

## 6. Feature Requirements

### 6.1 Invitation Page

#### Required Sections

- Cover / opening screen.
- Couple names.
- Personal/general salutation.
- Pra kata / preface.
- Couple profile.
- Wedding event details.
- Date/time countdown.
- Maps/location.
- Album/gallery.
- Backsound/music.
- RSVP / Kehadiran Konfirmasi.
- Gift transfer/QR or “Tidak menerima kado apapun.”
- Comments/guestbook.
- Closing words.

#### General vs Personal Page

The product has **2 similar visual invitation variants**:

1. **General Invitation**
   - URL can be shared publicly.
   - Uses generic invite line.
   - Example text: “Kepada Bapak/Ibu/Saudara/i.”
   - Does not identify a specific guest.
   - RSVP can be disabled or require guest self-identification.

2. **Personal Invitation**
   - Unique URL per guest.
   - Same visual template as general page.
   - Only key text differs, especially **“mengundang …”** / salutation.
   - Example text: “Kepada Yth. Bapak/Ibu [Guest Name].”
   - Personal route determines guest identity and tracking context.

#### Acceptance Criteria

- Guest can open invitation on mobile without login.
- Music must not autoplay with sound before user interaction if browser blocks it. Show play button.
- Personal invitation must display correct guest name.
- General invitation must not leak any guest-specific data.
- Page must work even if optional sections are disabled.

---

### 6.2 Guest Management

#### Requirements

User can:

- Create guest manually.
- Import guests from CSV/XLSX.
- Assign guest group/category.
- Set phone/WhatsApp number.
- Set max pax/person count.
- Generate unique invitation route per guest.
- Regenerate token if leaked.
- Mark guest inactive.
- Export guest list.

Guest fields:

- Name.
- Phone/WhatsApp.
- Group.
- Address/note.
- Max pax.
- Invitation token.
- RSVP status.
- Attendance status.
- Gift tracking status.

#### Acceptance Criteria

- Duplicate phone detection exists per event.
- Import preview catches invalid rows before committing.
- Deleting a guest should soft-delete or deactivate, not destroy audit history.

---

### 6.3 Encoded / Hard-to-Guess Route System

The user described this as “encoding route” so guests cannot easily access other invitations or unknown guests.

#### Requirement

Do **not** expose raw database IDs in public invitation routes.

Use one of these secure approaches:

- Random token: 128–256-bit random string encoded as Base62/Base64URL.
- Laravel signed URL with random public token.
- Store only hash of token in database for sensitive flows.

Recommended route pattern:

```text
General:  /inv/{event_slug}
Personal: /inv/{event_slug}/g/{guest_token}
Check-in: /checkin/{event_code}/{checkin_token}
```

Alternative short route:

```text
Personal: /i/{public_invitation_code}
```

#### Security Rules

- Never use `base64(id)` as security.
- Never use sequential ID as public identifier.
- Guest token must be random, unique, and unguessable.
- Add rate limiting to public token lookup.
- Add audit log for RSVP, gift proof, and check-in actions.
- If URL signing is used, signing validates tampering; token still identifies guest.
- Public token may be long-lived because wedding invitations need stable links.

#### Acceptance Criteria

- Opening another guest’s invitation is not feasible by changing a number in URL.
- Regenerating token invalidates old link.
- Public route never returns database IDs.

---

### 6.4 RSVP / Kehadiran Konfirmasi

Guest can submit:

- Attendance status:
  - Hadir.
  - Tidak hadir.
  - Ragu / belum pasti if enabled.
- Number of attendees, capped by `max_pax`.
- Optional message.
- Optional phone/name if using general invitation.

User can:

- View RSVP dashboard.
- Filter by status.
- Export RSVP results.
- Reset RSVP if needed.

Acceptance criteria:

- Personal link auto-associates RSVP with guest.
- General link requires name/phone to create an unidentified RSVP record or guest candidate.
- RSVP count updates dashboard.
- Duplicate submissions update latest RSVP but preserve history.

---

### 6.5 Attendance QR Tracking at Receptionist

#### Guest Side

Each personal invitation displays a unique QR code for check-in.

QR payload should contain:

- Event code.
- Check-in token.
- Optional signature.

Do not include raw guest ID, phone, gift status, or private info inside QR payload.

#### User / Receptionist Side

User can open/generate receptionist mode:

- “Open Scanner.”
- “Copy Staff Link.”
- “Regenerate Staff Link.”
- “Revoke Staff Link.”

Scanner features:

- Scan QR.
- Search guest fallback.
- Show guest name/group/max pax.
- Mark checked-in.
- Prevent duplicate check-in.
- Show latest scan logs.
- Show dashboard count.

Acceptance criteria:

- Duplicate scans show “already checked in” with time and scanner metadata.
- Invalid token shows safe error, not database info.
- Check-in is logged with timestamp and optional staff device label.

---

### 6.6 Gift Transfer / QR / QRIS Tracking

The invitation must support gift settings with security and privacy controls.

#### Gift Modes

1. **Tidak menerima kado apapun**
   - Gift section shows polite message.
   - No bank/QR displayed.

2. **Manual bank transfer**
   - Shows bank name, account holder, account number.
   - Optional copy button.
   - Optional unique reference code.
   - Optional proof upload.

3. **Guest-specific QR / QRIS / payment link**
   - Each guest receives a different QR or payment route.
   - System tracks gift intent using guest token.
   - If using real QRIS dynamic tracking, integrate payment gateway/provider later.
   - If using static QR image only, system cannot automatically know payer; use proof upload/manual verification.

#### “QR-nya beda-beda” Implementation Options

MVP-safe implementation:

- Generate a unique **gift confirmation URL** per guest.
- Show QR code that points to the guest-specific gift confirmation page.
- On that page, guest sees transfer info and can upload proof / fill nominal.
- This tracks intent and proof, not bank settlement.

Advanced implementation:

- Integrate dynamic QRIS provider/payment gateway.
- Generate unique QRIS/payment request per guest.
- Reconcile payment callback/webhook.

#### Security Rules

- Do not expose total gifts to public guests.
- Do not expose other guests’ gift data.
- Gift proof uploads must be private storage.
- Validate file type and size.
- Do not trust amount input without User verification unless payment provider webhook confirms settlement.
- Add audit log for gift status changes.

Acceptance criteria:

- User can enable/disable gifting.
- User can switch to “Tidak menerima kado apapun.”
- Personal guest link maps gift intent to correct guest.
- Gift status can be: none, intent_created, proof_uploaded, verified, rejected.
- Public page shows only safe, guest-specific information.

---

### 6.7 Maps / Location

Requirements:

- User can input venue name, address, Google Maps link, and optional lat/lng.
- Invitation shows location section.
- Guest can tap “Buka Maps.”
- Multiple event locations supported, e.g. Akad and Resepsi.

Acceptance criteria:

- Map button opens external map link.
- Venue details visible even if map embed fails.

---

### 6.8 Time and Place

Requirements:

- Event supports one or more schedules:
  - Akad.
  - Resepsi.
  - Custom schedule label.
- Each schedule has date, start time, end time, timezone, venue.
- Invitation shows countdown to primary schedule.

Acceptance criteria:

- Time format is localized Indonesian.
- Countdown handles expired events gracefully.

---

### 6.9 Album / Gallery

Requirements:

- User can upload photos.
- User can reorder photos.
- User can set cover photo.
- Invitation displays responsive gallery.

Acceptance criteria:

- Images are compressed/resized for web.
- Gallery works on mobile.
- Missing album does not break layout.

---

### 6.10 Backsound / Music

Requirements:

- Admin can manage music library.
- User can select one backsound per event.
- User can upload custom audio if package allows.
- Guest sees play/pause/mute control.

Acceptance criteria:

- Audio file URL should not expose private storage paths.
- Page remains usable without audio.
- Respect browser restrictions on autoplay.
- Store license/source notes for each backsound asset.

---

### 6.11 Comments / Guestbook / Ucapan

Requirements:

- Guest can submit comment/ucapan.
- User can moderate comments.
- User can hide/delete comments.
- Admin can remove abusive comments.

Acceptance criteria:

- Comments require guest name, either from token or form input.
- Add spam protection: rate limit, honeypot, optional captcha.
- Comments are escaped/sanitized to prevent XSS.

---

### 6.12 WhatsApp Automation via Fonnte API

Requirements:

- User can create broadcast campaign.
- User can choose message template.
- System inserts personalized invitation link per guest.
- System sends messages via Fonnte API using queue jobs.
- System logs status per guest.
- User can retry failed messages.

Message variables:

```text
{{guest_name}}
{{couple_names}}
{{event_date}}
{{invitation_link}}
{{rsvp_link}}
{{checkin_qr_hint}}
```

Acceptance criteria:

- Broadcast is queued, not executed inside web request.
- Each guest receives their own personal route.
- Failed API calls are logged.
- User sees sent/failed/pending counts.
- System rate-limits or batches sending to reduce blocking risk.

---

### 6.13 Pricing and Packages

#### Base Package

- **Template standar:** Rp200.000–Rp300.000.
- Includes basic invitation page, couple info, event time/place, maps, album, music, comments, and general link.

#### Add-ons

- **RSVP tracking + gifting on-site:** Rp75.000.
  - RSVP dashboard.
  - Guest-specific links.
  - Gift transfer/QR tracking.
  - Receptionist QR check-in.
- **Automation kirim undangan:** Rp50.000.
  - Fonnte broadcast campaign.
  - Personal link injection.
  - Send logs.
- **Custom template/design:** Rp150.000.
  - Custom styling/template adjustment.

#### Suggested Packaging

1. **Standard — Rp200.000–Rp300.000**
   - General invitation.
   - Visual template.
   - Event details.
   - Maps.
   - Album.
   - Music.
   - Comments.

2. **Tracking Add-on — +Rp75.000**
   - Personal routes.
   - RSVP tracking.
   - Gift tracking.
   - QR attendance check-in.
   - Dashboard/export.

3. **Broadcast Add-on — +Rp50.000**
   - WhatsApp automation.
   - Broadcast logs.
   - Retry failed messages.

4. **Custom Design Add-on — +Rp150.000**
   - Template customization.
   - Custom visual mood board.

---

## 7. UX Flows

### 7.1 User Creates Event

1. User logs in.
2. User creates wedding event.
3. User fills couple data.
4. User selects template.
5. User fills preface, event details, venue/maps.
6. User uploads album.
7. User selects music.
8. User configures RSVP/gift/comment visibility.
9. User previews general invitation.
10. User publishes event.

### 7.2 User Adds Guests and Sends Personal Invitations

1. User opens guest manager.
2. User adds/imports guests.
3. System generates personal invitation token per guest.
4. User previews sample personal page.
5. User creates WhatsApp broadcast.
6. System queues messages through Fonnte.
7. User monitors broadcast status.

### 7.3 Guest Opens Invitation

1. Guest receives WhatsApp link.
2. Guest opens personal invitation.
3. Page shows correct salutation.
4. Guest views event details, maps, album, music.
5. Guest confirms RSVP.
6. Guest views gift section or no-gift message.
7. Guest writes comment.
8. Guest shows QR at event entrance.

### 7.4 Receptionist Checks In Guest

1. User opens scanner/staff mode.
2. Receptionist scans guest QR.
3. System validates token.
4. System marks guest checked in.
5. Scanner displays success or duplicate status.
6. Dashboard updates attendance count.

---

## 8. Laravel-Oriented Architecture

### 8.1 Recommended Laravel Packages / Patterns

Use the current project’s Laravel version. Do not upgrade the framework unless explicitly asked.

Recommended:

- Laravel MVC + Form Requests + Policies.
- Blade + Tailwind CSS for invitation pages and dashboard, or Filament for Admin/User panels if preferred.
- Laravel Queues for WhatsApp broadcast.
- Laravel Scheduler for retries/reminders.
- Laravel Storage for photos/audio/proof uploads.
- Laravel signed routes for staff/check-in links where useful.
- Database transactions for import and status changes.
- Soft deletes for guest/event records.

Optional:

- Filament Admin Panel.
- Laravel Horizon if Redis is available.
- Spatie Laravel Permission if role complexity grows, but for MVP simple role enum is enough.
- Laravel Excel for import/export.
- Simple QR code package for QR generation.

### 8.2 Suggested Modules

```text
app/Domain/Events
app/Domain/Guests
app/Domain/Invitations
app/Domain/Rsvp
app/Domain/Attendance
app/Domain/Gifts
app/Domain/Broadcasts
app/Domain/Templates
app/Domain/Media
app/Domain/Comments
app/Domain/Billing
```

For simpler MVP, normal Laravel structure is acceptable:

```text
app/Models
app/Http/Controllers/Admin
app/Http/Controllers/User
app/Http/Controllers/Public
app/Services
app/Jobs
app/Policies
app/Actions
```

---

## 9. Data Model

### 9.1 users

- id
- name
- email
- password
- role: admin | user
- phone
- status
- created_at
- updated_at

### 9.2 events

- id
- user_id
- title
- slug
- couple_name_display
- bride_name
- groom_name
- status: draft | published | archived
- template_id
- published_at
- settings_json
- created_at
- updated_at

### 9.3 event_contents

- id
- event_id
- opening_text / pra_kata
- invitation_text
- closing_text
- bride_bio
- groom_bio
- love_story_json
- no_gift_message
- created_at
- updated_at

### 9.4 event_schedules

- id
- event_id
- label: akad | resepsi | custom
- date
- start_time
- end_time
- timezone
- venue_name
- address
- maps_url
- latitude
- longitude
- sort_order

### 9.5 templates

- id
- name
- code
- category
- preview_image_path
- status
- is_premium
- created_at
- updated_at

### 9.6 template_versions

- id
- template_id
- version
- config_json
- css_path
- view_path
- status

### 9.7 guests

- id
- event_id
- name
- phone
- group_name
- address_note
- max_pax
- status: active | inactive
- created_at
- updated_at
- deleted_at

### 9.8 guest_invitations

- id
- event_id
- guest_id nullable for general/self-identified guests
- public_token_hash
- public_token_last4
- checkin_token_hash
- checkin_token_last4
- invitation_url_cached
- checkin_qr_path nullable
- token_regenerated_at nullable
- last_opened_at nullable
- open_count
- created_at
- updated_at

### 9.9 rsvps

- id
- event_id
- guest_id nullable
- guest_invitation_id nullable
- name_snapshot
- phone_snapshot
- status: hadir | tidak_hadir | ragu | pending
- pax_count
- message
- submitted_at
- source: personal_link | general_link | user_manual

### 9.10 attendance_checkins

- id
- event_id
- guest_id
- guest_invitation_id
- checked_in_at
- checked_in_by_type: staff_link | user | admin
- staff_session_id nullable
- device_label nullable
- notes nullable

### 9.11 gift_settings

- id
- event_id
- mode: no_gift | bank_transfer | guest_specific_qr | qris_gateway
- bank_name nullable
- account_number_encrypted nullable
- account_holder nullable
- static_qr_path nullable
- no_gift_message nullable
- instructions nullable
- is_proof_upload_enabled bool

### 9.12 gift_contributions

- id
- event_id
- guest_id nullable
- guest_invitation_id nullable
- reference_code
- amount nullable
- proof_file_path nullable
- status: none | intent_created | proof_uploaded | verified | rejected
- verified_by nullable
- verified_at nullable
- notes nullable
- created_at
- updated_at

### 9.13 albums

- id
- event_id
- title
- sort_order

### 9.14 album_photos

- id
- album_id
- image_path
- caption nullable
- sort_order

### 9.15 music_assets

- id
- title
- artist nullable
- source_name
- source_url nullable
- license_note
- audio_path or external_url
- duration_seconds nullable
- status

### 9.16 comments

- id
- event_id
- guest_id nullable
- name_snapshot
- message
- status: pending | approved | hidden | deleted
- submitted_at
- ip_hash nullable

### 9.17 broadcast_campaigns

- id
- event_id
- user_id
- name
- channel: whatsapp_fonnte
- message_template
- status: draft | queued | running | completed | failed | cancelled
- scheduled_at nullable
- created_at
- updated_at

### 9.18 broadcast_logs

- id
- campaign_id
- guest_id
- guest_invitation_id
- phone
- personalized_message
- provider_message_id nullable
- status: pending | sent | failed | skipped
- error_message nullable
- sent_at nullable

### 9.19 staff_access_links

- id
- event_id
- token_hash
- label
- permissions_json
- revoked_at nullable
- expires_at nullable
- created_at
- updated_at

### 9.20 orders / billing

- id
- user_id
- event_id nullable
- package_name
- base_price
- addon_rsvp_gift_price
- addon_broadcast_price
- addon_custom_design_price
- total_price
- status: draft | unpaid | paid | cancelled
- paid_at nullable

### 9.21 audit_logs

- id
- actor_type: admin | user | guest | staff_link | system
- actor_id nullable
- event_id nullable
- action
- subject_type
- subject_id nullable
- metadata_json
- created_at

---

## 10. Public Routes and Controller Intent

### 10.1 Public Invitation

```php
GET /inv/{event:slug}
// PublicInvitationController@showGeneral

GET /inv/{event:slug}/g/{guestToken}
// PublicInvitationController@showPersonal
```

### 10.2 RSVP

```php
POST /inv/{event:slug}/rsvp
// RsvpController@storeGeneral

POST /inv/{event:slug}/g/{guestToken}/rsvp
// RsvpController@storePersonal
```

### 10.3 Gift

```php
GET /inv/{event:slug}/g/{guestToken}/gift
// GiftController@showGuestGift

POST /inv/{event:slug}/g/{guestToken}/gift/proof
// GiftController@uploadProof
```

### 10.4 Check-in

```php
GET /staff/checkin/{staffToken}
// StaffCheckinController@scanner

POST /staff/checkin/{staffToken}/scan
// StaffCheckinController@scan
```

### 10.5 User Dashboard

```php
GET /dashboard/events
GET /dashboard/events/{event}/edit
GET /dashboard/events/{event}/guests
GET /dashboard/events/{event}/rsvps
GET /dashboard/events/{event}/attendance
GET /dashboard/events/{event}/gifts
GET /dashboard/events/{event}/broadcasts
```

---

## 11. Dashboard Requirements

User dashboard must show:

- Event publish status.
- Total guests.
- RSVP counts.
- Checked-in counts.
- Broadcast sent/failed/pending.
- Gift proof pending/verified.
- Latest comments.
- Quick actions:
  - Preview general invitation.
  - Preview sample personal invitation.
  - Add/import guests.
  - Send WhatsApp broadcast.
  - Open scanner.
  - Export report.

Admin dashboard must show:

- User count.
- Active events.
- Package/order summary.
- Template usage.
- Broadcast usage/errors.
- Recent audit logs.

---

## 12. Notification and Message Templates

### 12.1 WhatsApp Invitation Template

```text
Assalamualaikum Wr. Wb.

Kepada Yth. {{guest_name}},

Dengan penuh rasa syukur, kami mengundang Bapak/Ibu/Saudara/i untuk hadir di acara pernikahan {{couple_names}}.

Detail undangan dapat dibuka melalui link berikut:
{{invitation_link}}

Mohon konfirmasi kehadiran melalui undangan digital tersebut.

Terima kasih.
```

### 12.2 RSVP Reminder Template

```text
Halo {{guest_name}},

Kami ingin mengingatkan undangan pernikahan {{couple_names}} pada {{event_date}}.

Mohon konfirmasi kehadiran melalui link berikut:
{{invitation_link}}

Terima kasih.
```

---

## 13. UI / Visual Direction

### 13.1 Visual Positioning

- Premium, calm, elegant, romantic.
- Mobile-first.
- Smooth scrolling sections.
- Strong cover visual.
- High readability.
- Not overloaded with animation.

### 13.2 “Valley of Blue” Inspired Theme Direction

Create a theme inspired by serene blue watercolor landscapes, misty mountains, soft paper texture, botanical details, and refined serif typography. Do not copy exact assets from any reference; use it as mood direction only.

Design keywords:

- Serene blue valley.
- Watercolor mountain.
- Misty landscape.
- Blue ink wash.
- Ivory paper.
- Navy typography.
- Soft botanical line art.
- Champagne gold accent.
- Elegant serif.
- Romantic script.

### 13.3 Suggested Sections Order

1. Opening cover.
2. Invitee salutation.
3. Pra kata.
4. Couple profile.
5. Event details.
6. Countdown.
7. Maps.
8. Album.
9. RSVP.
10. Gift/transfer/no-gift.
11. Comments.
12. Closing.

---

## 14. Non-Functional Requirements

### 14.1 Performance

- Public invitation page should load quickly on mobile.
- Use optimized images.
- Lazy-load gallery and comments.
- Avoid large JS bundles.

### 14.2 Security

- Use policies for Admin/User access.
- Public routes use unguessable tokens.
- Validate all form inputs.
- Sanitize comments.
- Private storage for proofs.
- Rate limit public forms.
- CSRF protection for forms.
- Audit sensitive actions.

### 14.3 Privacy

- Do not expose guest phone numbers publicly.
- Do not expose gift/contribution data publicly.
- Allow User to delete/export event data.
- Avoid placing private data inside QR payload.

### 14.4 Reliability

- Queue WhatsApp sends.
- Retry failed jobs safely.
- Prevent duplicate attendance check-in.
- Preserve history for RSVP and gift status changes.

### 14.5 Accessibility

- High contrast text.
- Buttons have accessible labels.
- Music controls visible.
- Invitation readable without animation/audio.

---

## 15. MVP Roadmap

### Phase 1 — Core Invitation

- Auth with Admin/User roles.
- User event CRUD.
- Template selection.
- Invitation content management.
- Public general invitation.
- Event schedule/place/maps.
- Album.
- Music selection.
- Comments.

### Phase 2 — Guest and Personalization

- Guest CRUD/import.
- Unique guest token routes.
- Personal invitation variant.
- RSVP.
- Dashboard counts.
- Export.

### Phase 3 — Attendance and Gift

- Guest check-in QR.
- Staff scanner link.
- Attendance dashboard.
- Gift settings.
- Guest-specific gift route/QR.
- Proof upload/manual verification.
- No-gift mode.

### Phase 4 — WhatsApp Automation

- Fonnte config.
- Broadcast campaigns.
- Personalized message variables.
- Queue sending.
- Logs/retry.

### Phase 5 — Pricing and Admin Ops

- Package/add-on management.
- Orders/manual payment status.
- Template library management.
- Music library management.
- Audit logs.

---

## 16. Metrics

Track:

- Events created.
- Events published.
- Guests imported.
- Invitation links opened.
- RSVP conversion rate.
- WhatsApp sent success rate.
- Attendance check-in rate.
- Gift proof upload rate.
- Comments submitted.
- Package/add-on purchases.
- Time from event creation to publish.

---

## 17. Edge Cases

- Guest opens old token after regeneration.
- Guest submits RSVP twice.
- Guest changes attendance from yes to no.
- QR scanned twice.
- QR scanned for wrong event.
- Staff link revoked while scanner page is open.
- Gift proof uploaded with invalid file.
- Fonnte API fails mid-campaign.
- Guest phone duplicated in import.
- Event unpublished after links sent.
- General link receives unknown RSVP.
- Music file missing/unavailable.
- Browser blocks autoplay.

---

## 18. Implementation Guardrails for Agents

When coding this product, agents must follow these rules:

1. Preserve the two-role system: Admin and User only.
2. Treat Guest and Receptionist as public/scoped actors, not authenticated roles.
3. Never expose raw IDs in public invitation, gift, RSVP, or check-in routes.
4. Build secure token resolution as a reusable service/action.
5. Build features behind clear settings flags so packages/add-ons can enable/disable them.
6. Queue WhatsApp broadcast jobs.
7. Add tests for token security, RSVP submission, gift proof upload, duplicate check-in, and broadcast personalization.
8. Keep template/design layer separate from business logic.
9. Do not hardcode one wedding event; support multiple events per User.
10. Do not omit: invitation, RSVP, gift transfer/QR, maps, album, different attendance links, time/place, music, comments, receptionist QR tracking, preface, transfer/no-gift copy, Fonnte broadcast, general and personal visual variants.

---

## 19. Definition of Done

A feature is done only if:

- UI exists for User/Admin when needed.
- Public guest experience works on mobile.
- Permissions/policies are enforced.
- Validation exists.
- Audit or logs exist for sensitive actions.
- Tests cover happy path and at least one edge case.
- Empty states are clear.
- User-facing text is understandable in Indonesian.
- Feature does not break if optional sections are disabled.

---

## 20. Suggested Indonesian Copy Blocks

### Pra Kata

```text
Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan acara pernikahan putra-putri kami. Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.
```

### Transfer / Gift

```text
Doa restu Bapak/Ibu/Saudara/i merupakan hadiah terbaik bagi kami. Namun apabila ingin memberikan tanda kasih, dapat melalui informasi berikut.
```

### Tidak Menerima Kado Apapun

```text
Dengan segala kerendahan hati, kami tidak menerima kado dalam bentuk apa pun. Kehadiran dan doa restu Bapak/Ibu/Saudara/i sudah menjadi kebahagiaan terbesar bagi kami.
```

### RSVP

```text
Mohon konfirmasi kehadiran Bapak/Ibu/Saudara/i agar kami dapat mempersiapkan acara dengan lebih baik.
```

---

## 21. Backlog Summary

### Must Have

- Admin/User auth.
- Event CRUD.
- Template standard.
- General invitation.
- Personal invitation.
- Guest management.
- Secure unique routes.
- RSVP.
- Gift/no-gift section.
- Maps.
- Album.
- Music.
- Comments.
- Attendance QR check-in.
- Fonnte broadcast.
- Dashboard.

### Should Have

- Import/export guests.
- Staff scanner link revoke/regenerate.
- Gift proof upload.
- Broadcast retry.
- Package/add-on toggles.
- Audit logs.

### Could Have

- Dynamic QRIS payment gateway.
- AI copy generator.
- Custom domain.
- Advanced analytics.
- Reminder automation.
- Template marketplace.

---

## 22. Final Product Thesis

NechCode should be positioned as:

> A premium digital wedding invitation and guest management platform that helps couples and EO create beautiful invitations, send personalized links, track RSVP, manage gift/transfer interactions, and check in guests with QR at the venue.

The winning angle is not “just another invitation website,” but **visual wedding experience + guest operation control + WhatsApp distribution**.
