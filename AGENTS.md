# Jalan Bareng — Agent Rules & Constraints

> Dokumen ini adalah **kontrak kerja agent**. Baca dan ikuti sebelum mengubah apapun.
> Diperbarui: September 2026

---

## 1. Stack yang Digunakan (JANGAN GANTI)

| Layer | Tech | Versi / Catatan |
|-------|------|-----------------|
| Frontend | Nuxt 3 + Vue 3 | Composition API + `<script setup>` |
| UI Library | Vuetify 3 | via `vuetify-nuxt-module` |
| State | Pinia | satu store per domain |
| Styling | Scoped CSS (di dalam `.vue`) | JANGAN pakai Tailwind |
| Backend | Laravel 11 | REST API, Sanctum auth |
| Database | SQLite (dev) / MySQL (prod) | |
| Deploy FE | Vercel | preset: `vercel` di nuxt.config |
| Deploy BE | Shared hosting / VPS | |

---

## 2. Konvensi Coding

### Vue / Nuxt
- Selalu pakai `<script setup lang="ts">` — bukan Options API
- Nama komponen: **PascalCase** (`WeeklyRegistrationHub.vue`)
- Nama file halaman: **kebab-case** (`aktivasi/index.vue`)
- Style: **scoped CSS** di dalam komponen, bukan file terpisah
- **Batas ukuran komponen: 400 baris** — kalau lebih, pecah jadi sub-komponen
- `defineEmits` dan `defineProps` wajib diketik dengan TypeScript
- Composable diletakkan di `web/composables/` — prefix `use` (contoh: `useApi.ts`)

### Laravel / PHP
- Controller di `app/Http/Controllers/Api/` — semua API hanya via prefix `/api`
- Model di `app/Models/` — relasi didefinisikan di dalam model
- Gunakan `auth:sanctum` middleware untuk endpoint yang butuh login
- Response API: selalu pakai format `{ data: ..., message: ... }` atau Resource
- **Batas ukuran controller method: 50 baris** — logic berat pindahkan ke Service/Action class

### Umum
- Komentar kode: hanya kalau menjelaskan *kenapa*, bukan *apa*
- Jangan hapus komentar `<!-- antislop:start -->` dan `<!-- antislop:end -->`
- Jangan commit file `.env` yang berisi nilai asli

---

## 3. File & Folder yang DILARANG Diubah Agent

```
AGENTS.md                    ← dokumen ini
GEMINI.md                    ← alias dokumen ini
DESIGN.md                    ← design system, ubah hanya jika diminta eksplisit
web/nuxt.config.ts           ← config Nuxt, jangan ubah kecuali diminta
web/node_modules/            ← JANGAN sentuh
backend/vendor/              ← JANGAN sentuh
backend/config/              ← jangan ubah tanpa diskusi dulu
backend/database/migrations/ ← jangan edit migration lama, buat yang baru
.env / .env.example          ← jangan ubah nilai, hanya tambah key baru
```

---

## 4. File yang Boleh Diubah Agent

```
web/components/**/*.vue      ← komponen UI
web/pages/**/*.vue           ← halaman Nuxt
web/composables/*.ts         ← composables
web/stores/*.ts              ← Pinia stores
backend/app/Http/Controllers/Api/*.php
backend/app/Models/*.php
backend/routes/api.php
```

---

## 5. Design System (Ringkasan — Detail di DESIGN.md)

- **Primary color**: `#DC2626` (merah) — wajib dipakai untuk CTA utama
- **Dark text**: `#111827`
- **Background**: `#FAFAF9`
- **Font**: Inter / system-ui — jangan ganti ke font lain tanpa konfirmasi
- **Card radius**: 16–24px | **Button**: pill shape (`border-radius: 9999px`)
- Untuk animasi hover: `translateY(-4px) scale(1.02)`, durasi `0.25s`
- Lihat `DESIGN.md` untuk lengkapnya

---

## 6. Pola API di Frontend

Gunakan composable `useApi()` yang sudah ada di `web/composables/useApi.ts`:

```ts
// ✅ Benar
const { data } = await useApi('/activations')

// ❌ Salah — jangan pakai $fetch langsung tanpa useApi
const data = await $fetch('/api/activations')
```

Auth token diurus oleh Pinia store `auth.ts` — jangan hardcode token.

---

## 7. Antislop Rules

Untuk pekerjaan UI, copy, layout mobile, atau komentar kode:
- Baca `antislop.md` (core) dulu
- Lalu baca skill yang relevan:
  - UI / visual: `skills/antislop-ui/SKILL.md`
  - Copy & teks: `skills/antislop-copywriting/SKILL.md`
  - Orang / aksesibilitas: `skills/antislop-human/SKILL.md`
  - Mobile / responsive: `skills/antislop-layoutmobile/SKILL.md`
  - Komentar kode: `skills/antislop-code/SKILL.md`

Tanya user: antislop diterapkan *selama* pengerjaan, atau *setelah selesai*?

---

## 8. Acceptance Criteria Umum

Setiap fitur yang dikerjakan agent harus memenuhi:
- [ ] Tampil dengan benar di mobile (320px), tablet (768px), desktop (1280px)
- [ ] Tidak ada error di console browser
- [ ] Loading state ditampilkan saat fetch data
- [ ] Empty state ditampilkan jika data kosong
- [ ] Tombol CTA utama menggunakan warna primary (`#DC2626`)
- [ ] Aksesibilitas: alt text pada gambar, aria-label pada tombol icon

---

## 9. Larangan Keras

- ❌ Jangan install package baru tanpa konfirmasi user
- ❌ Jangan drop atau truncate tabel database
- ❌ Jangan ubah struktur migration yang sudah ada
- ❌ Jangan ganti Vuetify ke library UI lain
- ❌ Jangan tambah Tailwind CSS
- ❌ Jangan buat endpoint API baru yang tidak ada di `ARCHITECTURE.md`
- ❌ Jangan override design system tanpa mengacu ke `DESIGN.md`
