# Canva UI/UX Prompt and Guide — Valley of Blue Inspired Digital Invitation

This is a prompt and production guide, not an image generation request.

Goal: create a Canva design direction for a digital wedding invitation inspired by the mood of “Valley of Blue”: serene blue watercolor landscape, misty mountains, calm premium wedding atmosphere, and elegant typography. Do not copy any exact template or asset.

---

## 1. Canva Prompt

Paste this into Canva Magic Design / Canva AI design assistant / your own design brief:

```text
Create a premium mobile-first digital wedding invitation design for a modern Indonesian wedding. The visual mood is serene, elegant, romantic, and calm, inspired by blue watercolor mountain valleys and misty ink-wash landscape paintings.

Format: vertical mobile layout, 1080 x 1920 px per section, designed as a scrollable wedding website.

Color palette: ivory white, misty blue, dusty blue, deep navy, soft sage, pale grey, and subtle champagne gold accents.

Style: watercolor mountain landscape, blue ink wash, soft botanical line art, thin gold frame, paper texture, airy spacing, elegant serif headings, romantic script names, clean readable body text.

Typography mood: luxury wedding serif for headings, romantic calligraphy/script for couple names, readable sans serif or classic serif for body copy.

Create these sections as separate pages:
1. Opening cover with misty blue valley background, couple names, wedding date, and 'Open Invitation' button style.
2. Personal salutation section: 'Kepada Yth. Bapak/Ibu/Saudara/i [Nama Tamu]'.
3. Pra kata / opening words section with elegant centered text.
4. Couple profile section with two oval photo frames and botanical accents.
5. Event details section for Akad and Resepsi with date, time, venue, and button style for maps.
6. Countdown section with four small cards: days, hours, minutes, seconds.
7. Album/gallery section with 4 to 6 photo frames in soft rounded or oval shapes.
8. RSVP section with elegant form placeholder cards: attendance, pax count, message.
9. Gift section with bank transfer/QR placeholder and alternative 'Tidak menerima kado apapun' message style.
10. Comments/ucapan section with guest message cards.
11. Closing section with thank-you text, couple initials, and soft blue mountain footer.

Keep the layout minimal, premium, and easy to read on mobile. Avoid clutter. Use smooth section transitions, soft shadows, and subtle ornaments. The result should feel like a calm blue watercolor wedding website, not a printed invitation only.
```

---

## 2. Canva Search Keywords

Use these search terms inside Canva Elements:

- blue watercolor mountain
- misty mountain watercolor
- ink wash landscape
- blue botanical line art
- watercolor blue flowers
- gold thin frame
- ivory paper texture
- elegant wedding frame
- soft cloud watercolor
- blue flower transparent
- oriental landscape watercolor
- sage leaf line art

---

## 3. Recommended Canva Fonts

Use only 2–3 font families to keep the design premium.

### Option A — Elegant Classic

- Couple names: Great Vibes / Pinyon Script / Allura
- Headings: Cormorant Garamond / Playfair Display
- Body: Lora / Libre Baskerville / Open Sans

### Option B — Modern Luxury

- Couple names: Amsterdam Four / Brittany / Signature-style script if available
- Headings: Cinzel / Cormorant SC
- Body: Montserrat / Lato

### Option C — Soft Romantic

- Couple names: Parisienne / Sacramento
- Headings: Playfair Display
- Body: Nunito / Quicksand

---

## 4. Page-by-Page Layout Guide

### Page 1 — Cover

- Full-screen misty blue mountain background.
- Couple names centered.
- Date below names.
- Small line: “The Wedding of”.
- Button style: “Open Invitation”.
- Add subtle gold or navy border.

### Page 2 — Salutation

General version:

```text
Kepada Bapak/Ibu/Saudara/i
```

Personal version:

```text
Kepada Yth.
Bapak/Ibu/Saudara/i [Nama Tamu]
```

The rest of the visual should stay the same between general and personal version.

### Page 3 — Pra Kata

Use centered text block over ivory paper texture.

Suggested copy:

```text
Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud menyelenggarakan acara pernikahan kami. Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.
```

### Page 4 — Couple Profile

- Two oval/circle photo frames.
- Bride and groom names.
- Parent names.
- Small botanical line art.

### Page 5 — Event Details

Create cards for:

- Akad Nikah.
- Resepsi.

Each card contains:

- Date.
- Time.
- Venue name.
- Address.
- Button: “Buka Maps”.

### Page 6 — Album

- 4–6 photo frames.
- Use varied crop: oval, rounded rectangle, vertical frame.
- Keep background simple.

### Page 7 — RSVP

Design form mockup cards:

- Nama.
- Konfirmasi kehadiran.
- Jumlah tamu.
- Ucapan.
- Button: “Kirim Konfirmasi”.

### Page 8 — Gift / Transfer

Two possible versions:

Version A — Transfer enabled:

```text
Doa restu Bapak/Ibu/Saudara/i merupakan hadiah terbaik bagi kami. Namun apabila ingin memberikan tanda kasih, dapat melalui informasi berikut.
```

Add bank card and QR placeholder.

Version B — No gift:

```text
Dengan segala kerendahan hati, kami tidak menerima kado dalam bentuk apa pun. Kehadiran dan doa restu Bapak/Ibu/Saudara/i sudah menjadi kebahagiaan terbesar bagi kami.
```

### Page 9 — Comments

- Guestbook cards.
- Name + message.
- Button: “Kirim Ucapan”.

### Page 10 — Closing

- Thank-you message.
- Couple initials.
- Misty blue mountain footer.
- Soft music control icon mockup.

---

## 5. How to Use Canva Output in Laravel

Canva should be used for visual direction and asset preparation, not as the full dynamic system.

Recommended workflow:

1. Create each invitation section in Canva at 1080 x 1920 px.
2. Export background-only assets as PNG/JPG.
3. Export decorative ornaments separately with transparent background if possible.
4. Rebuild the layout in Laravel Blade/Tailwind so text, guest name, RSVP, gift, and QR remain dynamic.
5. Store design config in `templates.config_json`.
6. Use Canva export as template preview image.
7. Do not put dynamic guest names directly inside static Canva images.
8. Keep text in HTML so the personal/general invitation difference can be rendered by Laravel.

---

## 6. Backsound Asset Resource List

Always verify the license for each specific track before using it for a client.

Recommended sources:

1. Pixabay Music — search “wedding”, “romantic piano”, “wedding background”.
2. Uppbeat — search “wedding”, “romantic”, “harp”, “piano”, “cinematic”.
3. Bensound — search “wedding”, “gentle piano”, “romantic”, “cinematic”.
4. Epidemic Sound — premium/subscription option for better curation.
5. ElevenLabs Music — generate custom wedding background music, but check plan and usage terms.

Suggested track moods:

- Romantic piano instrumental.
- Soft strings and piano.
- Ambient cinematic love theme.
- Harp and light orchestra.
- Soft acoustic instrumental.
- Indonesian/Javanese/Sundanese subtle traditional instrumental if culturally appropriate and licensed.

Audio requirements for the product:

- MP3 or AAC.
- 60–180 seconds loopable.
- Keep file size small for mobile.
- Store license/source metadata.
- Provide play/pause control.
- Do not force autoplay with sound.
