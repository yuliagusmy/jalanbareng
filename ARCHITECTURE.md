# Jalan Bareng — Architecture Snapshot

> **Snapshot arsitektur aktual** per September 2026.
> Tujuan: memberi agent konteks struktur yang sudah ada agar tidak merusak kode lama.

---

## Gambaran Besar

**Jalan Bareng** adalah platform komunitas pejalan kaki urban Indonesia. Fokus: aktivasi (event jalan santai), destinasi, dan cerita komunitas.

```
Arsitektur: Decoupled Monolith
Frontend  : Nuxt 3 (SSR/SPA) → deploy ke Vercel
Backend   : Laravel 11 REST API → deploy ke hosting/VPS
Database  : SQLite (dev lokal) / MySQL (prod)
Auth      : Laravel Sanctum (cookie + token)
```

---

## Struktur Folder

### Root
```
jalan-bareng-with db/
├── AGENTS.md              ← aturan agent
├── DESIGN.md              ← design system
├── ARCHITECTURE.md        ← dokumen ini
├── backend/               ← Laravel 11
├── web/                   ← Nuxt 3
├── run-backend.bat        ← jalankan BE lokal
├── run-frontend.bat       ← jalankan FE lokal
├── teman_jalan.sql        ← seed data produksi
└── .env                   ← env root (tidak dipakai langsung)
```

### Frontend (`web/`)
```
web/
├── app.vue                ← root app
├── nuxt.config.ts         ← config Nuxt (UI lib, modules, runtime config)
├── pages/
│   ├── index.vue          ← halaman beranda (hero + sections)
│   ├── login.vue
│   ├── register.vue
│   ├── profile.vue
│   ├── map.vue
│   ├── aktivasi/          ← listing & detail aktivasi
│   ├── destinations/      ← listing & detail destinasi
│   ├── cerita/            ← tulisan komunitas
│   ├── events/            ← events lama (legacy)
│   ├── auth/              ← callback Google OAuth
│   ├── manage/            ← halaman admin
│   └── mitra/             ← halaman mitra/partner
├── components/
│   ├── home/              ← komponen beranda
│   │   ├── EditorialCollageHero.vue    ← hero utama (5-lane collage + typewriter)
│   │   ├── WeeklyRegistrationHub.vue   ← hub pendaftaran aktivasi
│   │   ├── ActiveActivationsSection.vue
│   │   ├── CategorySection.vue
│   │   ├── CommunityStoriesSection.vue
│   │   ├── FeaturedActivationSection.vue
│   │   ├── HeroCarousel.vue            ← hero alternatif (carousel)
│   │   ├── StatsSection.vue
│   │   └── TypewriterHeadline.vue
│   ├── layout/            ← Navbar, BottomNav, Footer
│   ├── activations/       ← komponen halaman aktivasi
│   ├── destinations/      ← komponen halaman destinasi
│   ├── stories/           ← komponen halaman cerita
│   ├── events/            ← komponen events
│   ├── common/            ← komponen shared (loader, empty state, dll)
│   └── pwa/               ← install prompt PWA
├── composables/
│   ├── useApi.ts          ← wrapper fetch ke backend API
│   ├── useImageUrl.ts     ← helper URL gambar dari backend
│   ├── useImageCompressor.ts  ← kompresi gambar sebelum upload
│   └── useNotifications.ts   ← notifikasi in-app
├── stores/
│   └── auth.ts            ← Pinia store: user session, login/logout
├── layouts/               ← layout default dengan Navbar + BottomNav
├── middleware/            ← route guard (auth check)
├── plugins/               ← Vue plugins (Google OAuth, dll)
└── public/
    ├── images/hero/       ← foto walk_1.jpg s/d walk_12.jpg (hero collage)
    ├── icons/             ← PWA icons
    └── manifest.webmanifest
```

### Backend (`backend/`)
```
backend/
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Activation.php        ← model utama (event aktivasi)
│   │   ├── ActivationFaq.php
│   │   ├── ActivationMedia.php
│   │   ├── ActivationTestimonial.php
│   │   ├── Category.php
│   │   ├── Destination.php
│   │   ├── DestinationPhoto.php
│   │   ├── Event.php             ← model lama (akan dihapus/merge)
│   │   ├── EventPhoto.php
│   │   ├── Story.php             ← tulisan komunitas
│   │   ├── Comment.php
│   │   ├── Like.php
│   │   ├── Page.php              ← static pages (footer, dll)
│   │   ├── Setting.php           ← konfigurasi global
│   │   └── Role.php
│   └── Http/Controllers/Api/    ← semua controller REST
├── routes/
│   └── api.php                  ← semua route API
└── database/
    ├── migrations/              ← jangan edit migration lama
    └── seeders/
```

---

## Data Model (Ringkasan Relasi)

```
User
 ├── hasMany Destination (author)
 ├── hasMany Story (author)
 ├── hasMany Comment
 ├── hasMany Like
 ├── belongsToMany Event (via event_user pivot)
 └── belongsTo Role

Activation  ← MODEL UTAMA
 ├── hasMany ActivationFaq
 ├── hasMany ActivationMedia
 ├── hasMany ActivationTestimonial
 └── belongsTo Category

Category
 ├── hasMany Activation
 └── hasMany Destination

Destination
 ├── hasMany DestinationPhoto
 ├── hasMany Comment (polymorphic)
 ├── hasMany Like (polymorphic)
 └── belongsTo Category

Story
 ├── hasMany Comment (polymorphic)
 └── belongsTo User

Event  ← LEGACY (dulu ada, sekarang Activation yang dipakai)
 ├── hasMany EventPhoto
 └── belongsToMany User
```

---

## API Endpoints (Ringkasan)

### Publik (tanpa auth)
| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `/api/activations` | Daftar aktivasi |
| GET | `/api/activations/{slug}` | Detail aktivasi |
| GET | `/api/categories` | Semua kategori |
| GET | `/api/destinations` | Daftar destinasi |
| GET | `/api/destinations/{id}` | Detail destinasi |
| GET | `/api/stories` | Daftar cerita |
| GET | `/api/stories/{slug}` | Detail cerita |
| GET | `/api/settings/footer` | Data footer |
| GET | `/api/pages/{slug}` | Static page |
| POST | `/api/auth/register` | Register |
| POST | `/api/auth/login` | Login |

### Butuh Auth (Sanctum)
| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `/api/auth/me` | Data user login |
| POST | `/api/auth/logout` | Logout |
| PUT | `/api/profile` | Update profil |
| POST/PUT/DELETE | `/api/destinations` | CRUD destinasi |
| POST/PUT/DELETE | `/api/events` | CRUD events |
| POST/PUT/DELETE | `/api/comments` | CRUD komentar |
| POST | `/api/likes/toggle` | Toggle like |
| GET | `/api/notifications` | Notifikasi |

### Admin Only
| Method | Endpoint | Fungsi |
|--------|----------|--------|
| POST/PUT/DELETE | `/api/activations` | CRUD aktivasi |
| POST/PUT/DELETE | `/api/categories` | CRUD kategori |
| POST/PUT/DELETE | `/api/users` | CRUD user |
| GET/PUT | `/api/admin/settings` | Setting global |
| GET/POST/PUT/DELETE | `/api/admin/pages` | Static pages |
| GET/PUT/DELETE | `/api/admin/stories` | Moderasi cerita |

---

## Flow Autentikasi

```
1. User klik Login
2. FE → POST /api/auth/login → dapat token Sanctum
3. Token disimpan di Pinia store (auth.ts) + cookie
4. Setiap request ke endpoint protected: kirim Bearer token
5. Logout → POST /api/auth/logout → hapus token di server + clear store
6. Google OAuth: GET /api/auth/google/url → redirect → callback → dapat token
```

---

## Halaman yang Sudah Ada (Status)

| Halaman | Route | Status |
|---------|-------|--------|
| Beranda | `/` | ✅ Done (hero, aktivasi, destinasi, cerita) |
| Detail Aktivasi | `/aktivasi/[slug]` | ✅ Done |
| Listing Aktivasi | `/aktivasi` | ✅ Done |
| Destinasi | `/destinations` | ✅ Done |
| Cerita | `/cerita` | ✅ Done |
| Login | `/login` | ✅ Done |
| Register | `/register` | ✅ Done |
| Profil | `/profile` | ✅ Done |
| Peta | `/map` | 🟡 Basic (Google Maps) |
| Manage (Admin) | `/manage` | 🟡 Partial |
| Mitra | `/mitra` | 🟡 Partial |

---

## Tech Dependencies Kunci

### Frontend
- `nuxt` 3.x
- `vuetify-nuxt-module` — Vuetify 3
- `@pinia/nuxt` — state management
- `@vueuse/nuxt` — Vue utility composables
- `@mdi/font` — Material Design Icons

### Backend
- `laravel/sanctum` — API auth
- `laravel/socialite` — Google OAuth
- Standard Laravel 11 stack

---

## Catatan Penting untuk Agent

1. **`Activation` bukan `Event`** — model `Event` adalah legacy, fitur baru pakai `Activation`
2. **Hero foto** ada di `web/public/images/hero/walk_1.jpg` s/d `walk_12.jpg`
3. **`useApi.ts`** wajib dipakai untuk semua fetch ke backend — jangan `$fetch` langsung
4. **Vuetify classes** (`d-flex`, `ga-2`, `text-primary`, dll) boleh dipakai di template
5. **Scoped CSS** adalah standar — jangan taruh style global kecuali di `app.vue`
6. **`cards_collage_grid`** di `EditorialCollageHero.vue` adalah 5-lane di desktop, 3-lane di mobile — jangan ubah strukturnya kecuali diminta
