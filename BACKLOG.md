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
- Tombol utama "Daftar Kegiatan" membuka `registration_link` (Google Form / Instagram) di tab baru
- Tombol sekunder "Tandai Ikut" (hanya untuk user login) memanggil `POST /api/events/{event}/join` → tracking ke tabel `event_participants`
- `hasJoined` state mencegah double-submit; error "already joined" ditangani gracefully
- Terintegrasi di desktop sidebar CTA card dan mobile CTA section

---

## Prioritas Sedang

### ✅ ~~Halaman Profil Publik Member~~ — SELESAI
- URL: `/profile/{id}` — halaman baru di `web/pages/profile/[id].vue`
- Hero gelap dengan avatar, role badge, stats (destinasi / event / cerita), social links (Instagram, Twitter, Facebook)
- Tab konten: Destinasi yang ditambahkan, Cerita yang dipublikasi (approved), Event Mendatang yang diikuti
- Loading state, empty state, not-found state lengkap
- SEO meta title & description dinamis
- Backend `ProfileController::show()` diupdate untuk menyertakan `stories` + `stories_count`

### ✅ ~~Laporan & Ekspor Data (Admin)~~ — SELESAI
- Halaman Laporan Admin di `/manage/reports` (`web/pages/manage/reports/index.vue`)
- Ringkasan KPI bulanan: Total Member, Destinasi Baru, Komentar, Suka (Likes), Pendaftar Aktivasi
- Grafik engagement bulanan interaktif + breakdown table per bulan (`EngagementChart.vue`)
- Pilihan rentang waktu analitik (3 bulan, 6 bulan, 12 bulan)
- Ekspor direktori member ke format CSV (UTF-8 BOM untuk kompatibilitas Excel)
- Ekspor data pendaftar aktivasi/event ke Excel/CSV lengkap dengan filter per event (`ReportExportCard.vue`)
- Terintegrasi di sidebar admin, menu navigasi, dan aksi cepat dashboard manage
- Backend service `ReportService.php` + endpoint `GET /api/admin/reports/engagement`, `GET /api/admin/reports/members`, `GET /api/admin/reports/participants`

### ✅ ~~Peta Rute Walking Tour~~ — SELESAI
- Komponen MapLibre viewer baru di `web/components/events/WalkingRouteMapViewer.vue`
- Rute digambar sebagai polyline halus dengan lapisan halo putih + garis merah utama Jalan Bareng via GeoJSON
- Penanda titik awal (Start 🚩), titik akhir (Finish 🏁), dan waypoint checkpoint bernomor
- Setiap waypoint/checkpoint interaktif dapat diklik pada peta untuk menampilkan popup ringkasan nama tempat, jarak kumulatif dari start, dan catatan
- Timeline/chip strip checkpoint di bawah peta yang dapat diklik untuk otomatis flyTo dan membuka popup pada peta
- Kontrol peta lengkap: 2D/3D tilt perspektif dengan gedung 3D, pergantian gaya peta (Positron Minimalis / Liberty Detail), fit bounds otomatis, dan layar penuh
- Editor rute admin berbasis MapLibre di `web/components/events/RouteMapEditor.vue` dengan klik-untuk-menggambar, kalkulasi jarak Haversine otomatis, undo, dan reset rute
- Backend `EventController::show()` diperbaiki agar mem-parsing WKT/LINESTRING & POINT untuk database SQLite dan MySQL secara aman
- Terintegrasi di halaman detail event walking tour `web/pages/events/[id]/index.vue` dan formulir pembuatan event `web/pages/events/form.vue`

---

## Prioritas Rendah / Nice-to-Have
 
### ✅ ~~Migrasi Google Cloud Console~~ — SELESAI (5 Oktober 2026)
- Project resmi `jalan-bareng` telah dibuat di Google Cloud Console.
- OAuth 2.0 Web Client ID & Client Secret resmi telah dibuat dan dikonfigurasi.
- Origins & Redirect URIs mencakup `localhost`, `vercel.app`, `jalanbareng.id`, dan `jalanbareng.web.id`.
- Kredensial telah diperbarui di backend `.env`.

---

## 🚀 Rencana Masa Depan: Migrasi ke Hosting Berbayar & Infrastruktur Production

Daftar persiapan lengkap untuk migrasi backend Laravel dari Railway (free tier / ephemeral) ke VPS / Hosting Berbayar (misal: Niagahoster VPS, Biznet Gio, IDCloudHost, DigitalOcean, Hetzner, AWS Lightsail):

### 1. Database Production (MySQL / PostgreSQL Mandiri)
- [ ] **Migrasi dari SQLite ke MySQL 8 / PostgreSQL 16**:
  - SQLite di Railway container bersifat non-persistent / ephemeral saat redeploy.
  - Setup instance database dedicated dengan connection pooling & persistent volume.
  - Ekspor data SQLite eksisting (`sqlite3 database.sqlite .dump`) dan impor ke database production.
  - Setup auto-backup harian (mysqldump / pg_dump ke S3 / Cloud Storage).

### 2. File Storage Persisten (S3 / R2 / MinIO)
- [ ] **Migrasi Upload Gambar ke Cloud Object Storage**:
  - Saat ini upload foto tersimpan di disk lokal container (`storage/app/public/destinations`).
  - Ganti filesystem driver ke `s3` (Cloudflare R2, AWS S3, atau Wasabi) menggunakan `league/flysystem-aws-s3-v3`.
  - Keuntungan: Gambar destinasi, avatar profil, dan bukti cashout tidak hilang saat server di-restart atau di-scale.

### 3. Server Web & Runtime (Nginx + PHP 8.2+ FPM + Supervisor)
- [ ] **Konfigurasi Server VPS / PaaS Berbayar**:
  - Web Server: Nginx dengan reverse proxy, HTTP/2, Gzip/Brotli compression, dan SSL otomatis (Let's Encrypt / Certbot).
  - Process Manager: Supervisor untuk menjalankan antrean Laravel Queue worker (`php artisan queue:work`) dan Task Scheduler (`php artisan schedule:run`).
  - PHP OPcache & JIT diaktifkan untuk performa maksimal.
  - Setup CI/CD deployment via GitHub Actions (auto-deploy via SSH ke VPS atau via webhook).

### 4. Domain & DNS Management
- [ ] **DNS Record Subdomain API**:
  - Konfigurasi DNS di IDwebhost / Cloudflare: buat A record atau CNAME `api.jalanbareng.web.id` mengarah ke IP VPS / hosting berbayar.
  - Update `GOOGLE_REDIRECT_URI` ke `https://api.jalanbareng.web.id/api/auth/google/callback`.
  - Update `NUXT_PUBLIC_API_BASE` di Vercel ke `https://api.jalanbareng.web.id`.

### 5. Keamanan & Monitoring Server
- [ ] **Server Hardening**:
  - Firewall (UFW) hanya membuka port 80, 443, dan custom SSH port.
  - Fail2ban untuk mencegah brute-force SSH.
  - Integrasi error tracking & monitoring (Sentry / GlitchTip) dan uptime monitor (Uptime Kuma / Better Stack).

---

## ✅ Kerjakan PALING AKHIR — Sebelum Go-Live

### 🔒 ~~Security Review: OWASP Top 10~~ — SELESAI & DIPERBAIKI (5 Oktober 2026)

- Audit OWASP Top 10 menyeluruh telah dijalankan (laporan lengkap di `security_review_owasp.md`).
- **5 Celah Utama Berhasil Diperbaiki Langsung:**
  1. **A01: Broken Access Control**: Dibuat middleware server `EnsureUserIsAdmin` (`role:admin,community_admin` & `role:admin`), mengunci seluruh route `/api/admin/*`, `/api/users/*`, `/api/categories/*`, `/api/activations/*`, dan `/api/admin/cashouts/*` dari privilege escalation.
  2. **A07: Google One-Tap Signature Bypass & Banned Check**: Ditambahkan verifikasi kriptografi token Google via Google TokenInfo API, pengecekan audiens client ID, dan pencegahan akun berstatus banned untuk login.
  3. **A05: Tutup Backdoor Database Seeding**: Endpoint `/system/seed-database` dikunci mutlak hanya untuk environment lokal (`app()->isLocal()`).
  4. **A03: Stored XSS di Cerita Komunitas**: Ditambahkan sanitasi HTML (`sanitizeHtml`) pada `StoryController` untuk menyaring tag script dan event handler JavaScript berbahaya.
  5. **A04 & A02: Rate Limiting & Sanctum Token Expiration**: Ditambahkan middleware `throttle:10,1` pada endpoint autentikasi publik untuk mencegah brute-force, dan waktu kedaluwarsa token Sanctum diatur default 30 hari.

---

*Last updated: 5 Oktober 2026 — Seluruh Fitur Backlog & Security Review OWASP Top 10 Selesai*
