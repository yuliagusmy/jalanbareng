# Jalan Bareng Design System

## Brand Identity

**Product**: Jalan Bareng — Platform komunitas untuk menjelajah kota bersama, berbagi destinasi, dan mengikuti event.

**Visual Language**: Warm, inclusive, community-driven. Inspired by urban exploration, walking culture, and authentic local experiences.

---

## Color Palette

### Core Colors
- **Primary Red**: `#DC2626` — Bold, energetic, call-to-action. Represents movement and community energy.
- **Secondary Dark**: `#111827` — Text, headers, structured content. Professional yet approachable.
- **Accent Yellow**: `#FACC15` — Highlights, playful moments, discovery moments.

### Supporting Colors
- **Success Green**: `#16A34A` — Active status, confirmation, positive actions.
- **Info Blue**: `#0284C7` — Information, secondary actions, details.
- **Warning Orange**: `#F59E0B` — Alerts, collaboration badges, partner highlights.
- **Error Red**: `#EF4444` — Errors, blocked states, warnings.

### Neutral Scale
- **Background**: `#FAFAF9` — Clean, light, breathing room.
- **Surface**: `#FFFFFF` — Cards, containers, primary surfaces.
- **Grey Scale**: `#9CA3AF` → `#6B7280` → `#4B5563` → `#1F2937` — Text hierarchy, borders, subtle elements.

**Note**: Dark theme not shipped; light-only for now.

---

## Typography

### Typeface Choices
- **Sans-serif (body/UI)**: Inter or equivalent. Clean, modern, highly legible. Used for: body text, buttons, labels, UI.
- **Serif (display moments)**: Georgia or equivalent for pull quotes, testimonials. Adds warmth and humanity.
- **Monospace (none for now)**: Reserved for future code/terminal features.

### Font Hierarchy
- **H1 (Hero Headlines)**: 2.4rem–5.25rem, font-weight 800, line-height 1.08, letter-spacing `-0.035em`. Tight, impactful, editorial. Example: "Setiap Sudut Kota Punya Cerita"
- **H2 (Section Headers)**: 1.875rem–2.25rem (desktop), ~1.65rem (mobile), font-weight 800, line-height 1.2, letter-spacing `-0.035em`. Wajib gunakan utility class `.section-headline`.
- **H3 (Card Titles)**: 1.25rem, font-weight 700, line-height 1.35.
- **Body**: 1rem, font-weight 400, line-height 1.6.
- **Caption/Small**: 0.875rem, font-weight 500.

### Letter Spacing
- **Headlines (H1 & H2)**: Seragam ketat (`-0.035em` / `-0.03em`) di semua section tanpa kecuali untuk konsistensi visual editorial.
- **Labels/Buttons**: Wide (`0.05em`) untuk kejelasan dan distingsi.
- **Body**: Normal (`0em`).

---

## Layout & Spacing

### Grid & Breakpoints
- **Mobile**: 320px–599px — Single-column, 3-lane hero collage, full-width stacking.
- **Tablet**: 600px–959px — 2–3 column grids, collage with 5 lanes visible.
- **Desktop**: 960px+, sections max 1200px centered.

### Spacing System (8px base)
- **xs**: 4px
- **sm**: 8px
- **md**: 16px
- **lg**: 24px
- **xl**: 32px
- **2xl**: 48px

### Border Radius
- **Buttons & Pills**: 9999px (full pill).
- **Cards & Containers**: 14–18px (mobile 390px) | 18–24px (desktop).
- **Small Elements (chips, badges, thumbnails)**: 6–10px.
- **No pill-radius uniformity**: radius varies by purpose (buttons pill-shaped, cards rounded-xl, inputs rounded-lg).

---

## Standar Tampilan Mobile 390px (Mobile 390px Compact Standards)

Layar 390px (sekelas iPhone 14/15/16 Pro dan smartphone Android modern) adalah **viewport utama yang paling sering diakses pejalan kaki**. Halaman tidak boleh terasa "membengkak" dengan card raksasa yang memakan satu layar penuh atau teks yang tidak proporsional.

### 1. Prinsip Utama "Compact & Scannable"
- **Dilarang Card Raksasa (No Giant Cards)**: Kartu konten reguler pada mobile dilarang memakan tinggi layar vertikal lebih dari 260px tanpa scroll.
- **Pola Grid 2-Kolom (`cols="6"`) untuk Katalog**: Halaman direktori (Aktivasi, Event, Destinasi, Cerita) **wajib menggunakan 2 kolom di layar mobile (`cols="6"` atau `grid-template-columns: repeat(2, 1fr)`)**, bukan 1 kolom penuh (`cols="12"`). Ini membuat katalog 3x lebih cepat di-scan dan terasa seperti aplikasi modern (native app feel).
- **Horizontal Scroll Snap (`.events-hscroll`) untuk Konten Sekunder**: Section seperti "Event Mendatang", "Sorotan Pekan Ini", atau "Kategori Pilihan" di dalam halaman detail/beranda menggunakan horizontal scroll strip (lebar kartu 180px–200px) dengan `scroll-snap-type: x mandatory` dan scrollbar tersembunyi.

### 2. Standar Tipografi Mobile 390px
| Elemen | Ukuran di 390px | Weight & Line-height | Catatan |
|---|---|---|---|
| **H1 Hero Headline** | `clamp(2.2rem, 9.5vw, 2.75rem)` | 900, `line-height: 1.05`, `-0.035em` | Stacked 2 baris editorial |
| **H2 Section Heading** | `clamp(1.2rem, 4.8vw, 1.45rem)` | 800, `line-height: 1.2`, `-0.02em` | Seragam di seluruh section |
| **Section Subtitle** | `0.825rem - 0.875rem` | 400, `line-height: 1.45`, `#6B7280` | Ringkas, max 2 baris |
| **Card Title (Grid 2-col)** | `0.825rem - 0.875rem` | 700, `line-height: 1.3`, `-webkit-line-clamp: 2` | Wajib clamp 2 baris |
| **Body / Deskripsi** | `0.875rem (14px)` | 400, `line-height: 1.6`, `#374151` | Tidak bertele-tele |
| **Meta & Captions** | `0.68rem - 0.75rem` | 500–600, `#9CA3AF` / `#6B7280` | Tanggal, jarak km, kota |

### 3. Standar Padding, Margin & Radius Mobile 390px
- **Container Padding**: `py-5 px-3` atau `py-5 py-md-10 px-4` (jangan gunakan `py-16` di mobile!).
- **Card Padding**: Maksimal `pa-3` (12px) untuk kartu grid 2-kolom, atau `pa-4` (16px) untuk featured card tunggal. DILARANG menggunakan `pa-6` (24px) di layar mobile.
- **Image Aspect Ratio & Height**:
  - Grid 2-kolom: `aspect-ratio: 4 / 3` atau tinggi tetap `105px–115px`, `object-fit: cover`.
  - Featured Card / Banner: tinggi maksimal `160px–170px`.
  - Galeri Dokumentasi: `aspect-ratio: 1 / 1` (kotak 2-kolom seimbang), dilarang ada foto yang span 2 kolom di mobile.
- **Card Border Radius**: `14px–16px` (halus dan proporsional terhadap ukuran kartu yang lebih kecil).

### 4. Standar CTA Section di Mobile
- **Wadah**: Background gradien brand `#DC2626` ke `#991B1B`.
- **Padding**: `20px 16px` (bukan 40px+).
- **Judul**: `1.15rem` (18px), font-weight 700.
- **Subtitle**: `0.825rem` (13px), opacity 0.9.
- **Tombol**: Pill shape, tinggi `36px`, font-size `0.8rem`, padding `0 16px`.

### 5. Komponen Pendukung Mobile
- **Chapter Quick Facts Bar**: Bar pill lonjong (`border-radius: 9999px`, background putih/surface, border halus) berisi ringkasan cepat: 📍 Kota • 🗓️ Jadwal • 🎟️ Gratis & Terbuka.
- **Tag Status Selesai**: Chip kecil di pojok kartu arsip (`font-size: 0.65rem`, padding `2px 6px`).
- **Date Badge Mengambang**: Badge tanggal kompak (`14px` day, `9px` month) di pojok kiri atas poster event.

---

## Components & Patterns

### Buttons
- **Primary CTA**: Red (`#DC2626`), pill-shaped, bold font, white text, shadow for elevation. Example: "Daftar Aktivasi Pekan Ini".
- **Secondary**: Outlined or tonal variants, same pill shape.
- **Ghost**: Text-only, minimal.
- **Icon buttons**: 24–32px, centered icon.

### Cards
- **Activation/Event Cards**: Rounded corners (24px), shadow on hover, image overlay with gradient, badges for category.
- **Feature Cards**: Varied heights & layouts based on content hierarchy, not identical copy-paste.
- **Quote Cards**: Serif font, left-aligned, warm background tint.

### Navigation
- **Top Nav**: Sticky, split-button pattern for "Aktivasi" (link + dropdown). Logo + links + profile/auth.
- **Mobile Nav**: Hamburger menu + bottom sheet for "Aktivasi" + BottomNav with 5 icons.
- **BottomNav**: Beranda, Aktivasi, Destinasi, Cerita, Profil. Active state: color + label visible.

### Badges & Labels
- **Status Badges**: Pill-shaped, tonal color, icon + text. Example: "Open Registration", "4 Aktivasi Dibuka".
- **Category Badges**: Colored chip on activation cards. Example: "Jalan Santai • City Chapter".
- **Collaboration Badge**: Orange handshake icon + partner name. Example on activation cards.

### Forms & Inputs
- **Text Fields**: Outlined variant, rounded-lg, focus state with primary color border.
- **Select/Dropdown**: Same outlined style.
- **Checkbox/Radio**: Primary color accent.

---

## Motion & Animation

### Principles
- **Purpose-driven**: Motion guides attention, reveals content, confirms actions. No motion for decoration.
- **Timing**: Fast transitions (0.2s–0.3s) for UI interactions. Slower (0.5s–1s) for scroll reveals.
- **Easing**: Cubic-bezier for springy feel on hover; ease for fade/reveal.

### Common Patterns
- **Hover States**: Slight lift (`translateY(-4px)`), shadow increase, subtle scale (1.02–1.03).
- **Scroll Reveal**: Fade in + slide up on page scroll.
- **Button Press**: Quick scale down (0.98) + back to normal.
- **Loading**: Spinner or skeleton with pulse animation.

### Motion Dial
**MOTION: 2 (Balanced)** — Scroll-reveal and transitions present but not excessive. No parallax or pin animations; hero collage auto-rotates photos smoothly.

---

## Liveliness Dials

**ENERGY: 2 (Balanced)** — Warm, approachable community energy. Not flat/sterile (ENERGY 1), not maximalist/experimental (ENERGY 3). Primary red CTA pops; emoji stickers add playful moments.

**RHYTHM: 2 (Balanced)** — Sections vary in composition (hero collage, card grids, quote blocks, teaser) but follow a cohesive structure. Not uniformly repetitive; intentional variety based on content.

**MOTION: 2 (Balanced)** — Smooth scroll-reveal and hover transitions. Hero photo carousel rotates gently every 2.4s. No heavy parallax or choreography.

---

## Visual Identity Motifs

### 1. Photo Collage Hero
A 5-lane or 3-lane image collage (depending on breakpoint) with colored backgrounds (amber, cream, teal, yellow, coral, sky, mint). Auto-rotating photos every 2.4s for dynamic feel. **Purpose**: Authentic community moments, visual hierarchy through card height variation.

### 2. Warm Color Accents & Stickers
Playful emoji stickers (🌻 🌸 ✦ 🌼 🥀) placed near headlines and call-outs. **Purpose**: Humanizes the brand, adds local warmth without being childish.

### 3. Quote Block with Serif Font
Community testimonials in serif font (Georgia), left-aligned, with quotation mark. **Purpose**: Adds credibility, human voice, and design variation.

### 4. Red Primary Button with Shadow
Pill-shaped, bold font, red background with box-shadow. **Purpose**: High visibility, unmistakable CTA, energetic community spirit.

### 5. Category Chips & Badges
Tonal chips for filter tabs (e.g., "Jalan Santai", "Diskusi Buku"). Colored badges on cards for category (e.g., "Jalan Santai • City Chapter"). **Purpose**: Clear content organization, visual scanning, identity through color consistency.

---

## Accessibility & Contrast

- **Text Contrast**: All body text meets WCAG AA (4.5:1 for normal, 3:1 for large).
- **Focus States**: Visible focus outline on all interactive elements (buttons, links, form inputs).
- **Keyboard Navigation**: All interactive elements reachable via Tab; Enter/Space to activate; Escape to close modals.
- **Mobile Tap Targets**: Minimum 44px (buttons, links).
- **Alt Text**: All images have descriptive alt text (e.g., "Dokumentasi jalan santai komunitas", not "img_123").

---

## Design Decisions & Reasoning (R-31)

| Decision | Reason |
|----------|--------|
| Red (#DC2626) as primary | Energy, movement, matches brand name "Jalan Bareng" activism; warm and inviting for a community platform. |
| Warm palette (yellow, orange, teal, mint) on cards | Reflects diverse, inclusive community. Each card color feels distinct yet cohesive. |
| Pill-shaped buttons | Friendly, modern, clearly clickable. Consistent with community-forward brand. |
| Photo collage hero with auto-rotate | Showcases authentic community moments; dynamic without being gimmicky. Variant heights create visual interest. |
| No dark mode (for now) | Light theme aligns with outdoor exploration, walkability, and daytime activities. Can add later if needed. |
| Serif for quotes | Adds humanity, credibility, and design texture to break rhythm. |
| Emoji stickers | Playful, locally accessible (no design asset cost), humanizes brand without kitsch. |
| Balanced (2/2/2) dials | Community-forward brand that's energetic but approachable; varied sections but cohesive; smooth motion but not overdone. |

---

## What This Enables

- **Consistency**: All future components follow this palette, typography, spacing, and motion rules.
- **Flexibility**: Designers/devs can apply these guidelines to new features without needing AI to re-invent the wheel.
- **Liveliness**: Emoji, photo carousel, warm colors, and varied layouts prevent sterile/generic feel.
- **Accessibility**: WCAG AA contrast, keyboard nav, focus states built in from the start.
