# Jalan Bareng — Future Feature Backlog

> Daftar fitur dan tugas yang sudah direncanakan tapi **belum dikerjakan**.
> Dikerjakan **berurutan sesuai prioritas** — baris paling atas = paling diprioritaskan.
> Diperbarui: Oktober 2026

---

## Prioritas Tinggi

### ✅ ~~Notifikasi In-App~~ — SELESAI
- Bell icon + badge counter sudah ada di navbar
- `NotificationBell.vue` + `useNotifications.ts` + `NotificationController.php` lengkap
- Backend routes: `GET /api/notifications`, `POST /api/notifications/{id}/read`, `POST /api/notifications/mark-all-read`
- Trigger otomatis saat ada like/komentar via `NewLikeNotification` & `NewCommentNotification`

### ✅ ~~Sistem Komentar Interaktif (Destinasi)~~ — SELESAI
- `CommentSection.vue` + `CommentItem.vue` terintegrasi di halaman detail destinasi
- Komentar baru, reply 1 level, like per komentar, hapus komentar sendiri
- Backend: `GET /api/comments`, `POST /api/comments`, `DELETE /api/comments/{id}`
- Optimistic UI update + empty/loading state

### ✅ ~~Kalender Aktivasi~~ — SELESAI
- Halaman `/aktivasi/kalender` dengan grid kalender bulanan interaktif
- Klik tanggal → lihat daftar event hari itu
- API filter `month` + `year` di `EventController`
- Link Kalender Event di NavigationMenu (desktop) dan BottomNav (mobile)

### ✅ ~~Polish Halaman Detail Cerita~~ — SELESAI
- `/cerita/[slug].vue` — full-width hero + reading progress bar
- Author bio card + Instagram handle
- Related stories section (3 artikel terkait)
- Estimasi waktu baca otomatis

### ✅ ~~Registrasi Aktivasi Online~~ — SELESAI
- Formulir & tombol daftar event/aktivasi diintegrasikan
- Endpoint backend `POST /api/events/{event}/join` (tabel `event_participants`) lengkap
- Komponen `EventRegistrationForm.vue` terhubung

---

## Prioritas Sedang

### 🪪 Halaman Profil Publik Member
- URL: `/profile/{username}` atau `/profile/{id}`
- Tampilkan destinasi yang ditambahkan, cerita yang dipublikasi, lencana yang diraih
- Tombol follow/unfollow (opsional, perlu relasi di DB)

### 📊 Laporan & Ekspor Data (Admin)
- Ekspor daftar member ke CSV
- Ekspor pendaftar aktivasi ke Excel
- Grafik engagement bulanan (komentar, likes, destinasi baru)

### 🗺️ Peta Rute Walking Tour
- Rute digambar sebagai polyline di peta MapLibre
- Setiap waypoint/checkpoint bisa diklik untuk lihat info tempat
- Admin bisa definisikan rute per aktivasi

---

## Prioritas Rendah / Nice-to-Have

### 🌐 Migrasi Google Cloud Console
- Saat ini project `ruang-bahagia` masih milik developer lama
- Perlu migrasi OAuth credentials ke akun Google Cloud milik admin Jalan Bareng
- Lakukan **sebelum rilis production** agar Google Login tidak bergantung pihak ketiga
- Langkah: buat project GCP baru → buat OAuth 2.0 client → update `.env` BE & FE

---

## ⛔ Kerjakan PALING AKHIR — Sebelum Go-Live

### 🔒 Security Review: OWASP Top 10

> **Prompt yang dipakai:**
> ```
> Review security based on top 10 OWASP
> ```

Cakupan review minimal:
| # | OWASP | Yang perlu diperiksa di Jalan Bareng |
|---|-------|--------------------------------------|
| A01 | Broken Access Control | Route guard admin, endpoint `/api/admin/*`, akses file upload |
| A02 | Cryptographic Failures | HTTPS enforced? Token sanctum expiry? Password hashing bcrypt? |
| A03 | Injection | Input sanitasi di semua controller, query Eloquent (sudah parameterized?) |
| A04 | Insecure Design | Rate limiting login, brute-force protection |
| A05 | Security Misconfiguration | `.env` tidak ter-expose, `APP_DEBUG=false` di prod, CORS config |
| A06 | Vulnerable Components | `composer audit`, `npm audit` untuk dependency |
| A07 | Auth & Session Failures | Sanctum cookie SameSite, token invalidation saat logout |
| A08 | Software & Data Integrity | Upload file validation (type, size, ekstensi) |
| A09 | Logging & Monitoring | Error logging aktif? Log tidak expose data sensitif? |
| A10 | SSRF | Apakah ada fetch/curl ke URL eksternal dari backend? |

> ⚠️ Jangan rilis ke production sebelum review ini selesai dan semua temuan high/critical sudah diperbaiki.

---

*Last updated: 5 Oktober 2026 — Notifikasi, Komentar, Kalender Aktivasi & Polish Cerita selesai*
