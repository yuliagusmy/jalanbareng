# Jalan Bareng — Agent Rules & Constraints

> Dokumen ini adalah **kontrak kerja agent**. Baca dan ikuti sebelum mengubah apapun.
> Diperbarui: Oktober 2026

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

---

## 10. Protokol Keamanan Database (WAJIB DIBACA SEBELUM MENYENTUH SCHEMA)

> Aturan ini melindungi data production di Railway/MySQL dari kehilangan akibat migration yang salah.

### Prinsip Dasar

| Jenis Perubahan | Aman? | Tindakan |
|---|---|---|
| Ubah tampilan / komponen Vue | ✅ Aman | Langsung push |
| Tambah kolom baru | ✅ Aman | Buat migration baru |
| Tambah tabel baru | ✅ Aman | Buat migration baru |
| Ubah nama kolom / tabel | ⚠️ Hati-hati | Backup dulu, baru migration baru |
| Edit migration yang sudah pernah dijalankan | 🚨 **DILARANG** | Buat migration baru sebagai gantinya |
| `DROP TABLE` / `TRUNCATE` | 🚨 **DILARANG** | Harus konfirmasi eksplisit dari user |

### Prosedur Wajib Sebelum Mengubah Schema Database

Setiap kali ada perubahan yang menyentuh **struktur database** (tambah kolom, tambah tabel, ubah tipe data, dll), agent **wajib** mengikuti langkah berikut:

1. **Informasikan ke user** — jelaskan perubahan schema apa yang akan dilakukan dan dampaknya
2. **Minta konfirmasi eksplisit** — jangan lanjutkan tanpa persetujuan user
3. **Test di lokal dulu** — jalankan migration di SQLite lokal (`php artisan migrate`) dan pastikan tidak ada error
4. **Backup production** — ingatkan user untuk export data dari Railway sebelum migrate ke production:
   ```bash
   # Di Railway console atau via Adminer:
   # Export seluruh database ke file .sql sebelum migrate
   ```
5. **Baru jalankan di production** — setelah backup dikonfirmasi, baru jalankan `php artisan migrate` di production

### Aturan Migration

- ✅ **Selalu buat file migration baru** — jangan edit file migration yang sudah pernah di-commit
- ✅ Nama migration harus deskriptif: `add_photo_url_to_events_table`, bukan `fix_events`
- ✅ Setiap migration harus punya method `down()` yang bisa rollback dengan aman
- ❌ Jangan jalankan `php artisan migrate:fresh` atau `php artisan migrate:reset` di production — **data akan hilang semua**
- ❌ Jangan jalankan `php artisan db:seed` di production tanpa konfirmasi user

### Untuk Perubahan UI/Tampilan Saja

Kalau hanya mengubah komponen Vue, halaman, atau styling:
- Tidak perlu backup database
- Tidak perlu konfirmasi tambahan
- Langsung push ke GitHub — Vercel akan auto-deploy

---

## 11. Protokol Dokumentasi & Handoff Antar Agent (WAJIB)

> Aturan ini memastikan pekerjaan tidak hilang saat berganti akun, IDE, atau agent.

### Prinsip Dasar

Setiap agent yang mengerjakan proyek ini **wajib** menjaga file `PROGRESS.md` di root project selalu up-to-date. File ini adalah satu-satunya sumber kebenaran tentang status pekerjaan saat ini.

### Kapan Harus Update `PROGRESS.md`

| Kejadian | Tindakan |
|---|---|
| Selesai mengerjakan sebuah fitur/fix | Update section "Terakhir Diselesaikan" |
| Pekerjaan terhenti di tengah jalan | Update section "Sedang Dikerjakan / Belum Selesai" |
| Ada file diubah tapi belum di-commit | Catat di section "File Belum Di-commit" |
| Menemukan bug atau isu baru | Catat di section "Isu yang Ditemukan" |
| Akan berhenti bekerja (end of session) | **Wajib** update PROGRESS.md sebelum berhenti |

### Format Wajib `PROGRESS.md`

File `PROGRESS.md` harus selalu mengikuti format ini:

```
# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: [TANGGAL] oleh [nama agent/IDE]

## 🔄 Sedang Dikerjakan / Belum Selesai
[Deskripsi task in-progress, langkah sudah dan belum selesai]

## ⏳ File Belum Di-commit
[List file yang sudah diubah tapi belum di-commit]

## ✅ Terakhir Diselesaikan
[3–5 item terakhir yang sudah di-commit]

## 🐛 Isu yang Ditemukan
[Bug atau masalah yang belum difix]

## 📋 Antrian Berikutnya
[Task paling logis berdasarkan konteks terakhir]
```

### Aturan Commit

- ✅ Setiap commit harus punya pesan deskriptif: `fix:`, `feat:`, `refactor:`, `docs:`
- ✅ Sebelum akhiri session panjang, selalu commit dan push perubahan yang sudah stabil
- ✅ Kalau ada perubahan belum siap di-commit, catat di `PROGRESS.md` section "File Belum Di-commit"
- ❌ Jangan tinggalkan perubahan uncommitted tanpa mencatatnya di `PROGRESS.md`

### Cara Agent Baru Memulai

Ketika agent baru mulai bekerja di proyek ini:

1. **Baca `AGENTS.md`** ini dulu — terutama section stack, larangan, dan protokol keamanan
2. **Baca `PROGRESS.md`** — pahami apa yang sedang dikerjakan dan apa yang belum selesai
3. **Jalankan `git log --oneline -10`** — konfirmasi commit terakhir sesuai PROGRESS.md
4. **Cek `git status`** — kalau ada file modified yang belum di-commit, tanyakan ke user dulu
5. **Baru mulai bekerja** setelah konteks jelas
