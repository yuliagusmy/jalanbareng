- Terintegrasi di sidebar admin, menu navigasi, dan aksi cepat dashboard manage
- Backend service `ReportService.php` + endpoint `GET /api/admin/reports/engagement`, `GET /api/admin/reports/members`, `GET /api/admin/reports/participants`

---

## 📝 Pengembangan Fitur Cerita & Kabar Jalan Bareng

> Dicatat: 6 Oktober 2026

### A. Fix Form Kirim Tulisan — Mobile UX (Prioritas Tinggi)
- [ ] Ganti `<v-dialog>` kecil ke **bottom sheet full-height** di mobile 390px
- [ ] Textarea lebih besar, padding cukup, tidak terpotong keyboard
- [ ] Label & placeholder yang jelas untuk setiap field

### B. Fix Ukuran Headline Mobile (Kecil)
- [ ] Samakan ukuran `h1` hero di `/cerita` dengan halaman lain (events, aktivasi) di viewport 390px
- [ ] Target: konsisten `clamp(1.8rem, ...)` sesuai design system

### C. Halaman Cerita Index — Rich Layout
- [ ] Bagian atas: **"Cerita Terbaik"** — 2–3 card featured (sorted by likes + comments)
- [ ] Bagian bawah: **"Cerita Terbaru"** — grid cerita dengan opsi sortir (Terbaru / Terpopuler)
- [ ] Backend: tambah parameter `?sort=popular` di `GET /stories`

### D. Beranda — Featured Story
- [ ] Tampilkan **1 cerita terbaik** (most engaged) secara menonjol di section cerita beranda
- [ ] Di bawahnya: 2–3 cerita terbaru sebagai preview

### E. Like & Komentar di Detail Cerita (Prioritas Tinggi)
- [ ] Tombol **like** di halaman `/cerita/[slug]` — pakai endpoint `POST /likes/toggle` (sudah ada)
- [ ] Section **komentar** di bawah artikel — pakai endpoint `GET/POST /comments` (sudah ada)
- [ ] **Reply** komentar — penulis & pembaca bisa saling balas
- [ ] Perlu cek apakah `CommentController` & `LikeController` support `commentable_type = story`

---

## 🔐 Kejelasan Nilai Login & Access Control Member

> Dicatat: 6 Oktober 2026

**Konteks:** Saat ini user tidak cukup paham *kenapa* harus login. Perlu dibuat lebih tegas & jelas.

### Prinsip Akses

| Aksi | Guest (Tanpa Login) | Member (Login) |
|---|---|---|
| Baca cerita & artikel | ✅ Boleh | ✅ Boleh |
| Lihat event & cari link Google Form | ✅ Boleh | ✅ Boleh |
| Jelajah destinasi & peta | ✅ Boleh | ✅ Boleh |
| **Like** cerita / destinasi | ❌ Harus login | ✅ Bisa |
| **Komentar** & reply | ❌ Harus login | ✅ Bisa |
| **Submit cerita** Jalan Bareng | ❌ Harus login | ✅ Bisa |
| **Bagikan destinasi** baru | ❌ Harus login | ✅ Bisa |
| Lihat riwayat keikutsertaan event | ❌ | ✅ Di profil |

### F. Value Proposition Login — UI/UX
- [ ] **Halaman login** — tambahkan seksi "Kenapa bergabung?" dengan manfaat konkret member
- [ ] **Prompt login** yang muncul saat guest klik tombol like/komentar/submit — bukan error, tapi undangan yang ramah: *"Bergabung dulu untuk ikut meramaikan 👇"*
- [ ] **Prompt login** di form submit cerita & bagikan destinasi — jika belum login, redirect ke login dengan pesan konteks yang jelas
- [ ] CTA "Masuk / Daftar" di navbar lebih menonjol untuk guest

### G. Guard Aksi Terproteksi di Frontend
- [ ] Tombol **Like** — jika guest klik → muncul dialog/snackbar undangan login
- [ ] Tombol **Komentar** — jika guest klik textarea → muncul prompt login
- [ ] Tombol **Kirim Tulisan** — jika guest klik → redirect ke login dengan `?redirect=/cerita`
- [ ] Tombol **Bagikan Destinasi** — jika guest klik → redirect ke login
- [ ] Semua guard pakai composable `useAuthGuard()` yang konsisten (buat baru jika belum ada)

### H. Halaman Profil Member — Dashboard Kontribusi
- [ ] Setelah login, user lihat ringkasan kontribusi: cerita yang dikirim, destinasi yang dibagikan, komentar aktif
- [ ] Ini menjawab "saya login untuk apa?" — ada jejak aktivitas yang terasa nyata

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

## 🗺️ Future Feature: Live Map & GPS Route Tracking Jalan Bareng

> Dicatat: 6 Oktober 2026
> Inspirasi: Google Maps + Strava + fitur peta rute walking tour yang sudah ada

**Konsep:** Halaman `/destinations` berevolusi menjadi **full-screen interactive map** seperti Google Maps — dengan fitur live GPS tracking khusus untuk admin/tour leader saat memimpin jalan.

---

### I. Full-Screen Map Mode di Halaman Destinasi
- [ ] Halaman `/destinations` tampil sebagai **peta penuh 1 layar** (tanpa navbar/header) saat dibuka di mobile
- [ ] **Titik lokasi user saat ini** (GPS dot biru) — pakai `navigator.geolocation.watchPosition()`
- [ ] Tombol **"Direktori"** (slide-up panel) untuk menampilkan daftar destinasi & landmark terdekat
- [ ] Filter cepat di atas peta: Landmark, Kuliner, Titik Kumpul, dll.
- [ ] Marker destinasi interaktif: klik → muncul card mini preview + tombol "Lihat Detail"

### J. Tombol "Mulai Jalan" — GPS Route Recording (Admin/Tour Leader Only)
- [ ] Tombol **"Mulai Jalan"** hanya muncul untuk user dengan role `admin` atau `community_admin`
- [ ] Saat ditekan: mulai merekam trek GPS secara real-time (`watchPosition` setiap N detik)
- [ ] Rekaman trek ditampilkan sebagai **polyline merah** di peta secara live
- [ ] Indikator status recording (waktu berjalan, jarak tempuh, kecepatan rata-rata)
- [ ] Tombol **"Selesai"** untuk menghentikan recording dan masuk ke halaman review rute

### K. Review & Share Rute ke Halaman Event
- [ ] Setelah selesai recording: tampil **ringkasan rute** (peta mini, total jarak, durasi, elevasi)
- [ ] Tombol **"Bagikan ke Event"** — admin pilih event yang sedang aktif (hari ini / minggu ini)
- [ ] Rute yang di-share tersimpan ke database event → muncul di halaman detail event sebagai **"Rute Jalan Kita Hari Ini"**
- [ ] Rute ditampilkan menggunakan komponen `WalkingRouteMapViewer.vue` yang sudah ada

### L. Fitur Tambahan yang Bisa Dikembangkan
- [ ] **Live tracking publik** — peserta event bisa melihat posisi tour leader bergerak secara real-time (WebSocket / SSE)
- [ ] **Checkpoint system** — admin bisa drop pin checkpoint saat jalan, otomatis tercatat di rute
- [ ] **Foto di lokasi** — peserta bisa upload foto yang otomatis di-pin ke koordinat saat diambil
- [ ] **Riwayat rute per event** — arsip semua rute yang pernah direkam, bisa dibandingkan antar edisi
- [ ] **Ekspor GPX** — rute bisa diunduh sebagai file GPX untuk dibuka di Strava/Komoot/Maps
- [ ] **Statistik komunitas** — total km yang sudah dijelajahi komunitas Jalan Bareng sejak berdiri

### Catatan Teknis
- GPS tracking: `navigator.geolocation.watchPosition()` + simpan ke `ref([])` di store
- Penyimpanan rute: kolom `recorded_route` (JSON/GeoJSON) di tabel `events` — migration baru
- Map engine: **MapLibre GL JS** (sudah dipakai di `WalkingRouteMapViewer.vue`)
- Real-time (opsional): Laravel Broadcasting + Pusher/Soketi atau Server-Sent Events
- Backend endpoint baru yang perlu dibuat: `POST /events/{id}/route` (simpan rute hasil recording)

---

## Prioritas Rendah / Nice-to-Have
 
### ✅ ~~Migrasi Google Cloud Console~~ — SELESAI (5 Oktober 2026)
- Project resmi `jalan-bareng` telah dibuat di Google Cloud Console.
- OAuth 2.0 Web Client ID & Client Secret resmi telah dibuat dan dikonfigurasi.
- Origins & Redirect URIs mencakup `localhost`, `vercel.app`, `jalanbareng.id`, dan `jalanbareng.web.id`.
- Kredensial telah diperbarui di backend `.env`.

---

## 🚀 Future Features & Roadmap: Migrasi ke Hosting Berbayar (Production-Ready Architecture)

Persiapan komprehensif untuk memigrasikan backend Jalan Bareng dari Railway (free tier / ephemeral SQLite) ke infrastruktur hosting berbayar (VPS Niagahoster, Biznet GIO, IDCloudHost, DigitalOcean, Hetzner, atau AWS Lightsail) guna menjamin performa tinggi, database persisten, dan stabilitas jangka panjang:

### 1. Database Production (Dedicated MySQL 8 / PostgreSQL 16)
- [ ] **Pemisahan Database dari Container App**:
  - Ganti koneksi SQLite dengan MySQL 8 / MariaDB 10.11 / PostgreSQL 16 dengan storage NVMe persisten.
  - Setup auto-tuning MySQL (`innodb_buffer_pool_size`, `max_connections`, connection pooling).
  - Skrip migrasi data dari SQLite lokal/Railway ke MySQL menggunakan data dumper & database seeder.
  - Setup Automated Daily Backup (Cron snapshot + kompresi gzip + upload otomatis ke Cloud Storage S3 / R2 offsite).

### 2. Object Storage Persisten (Cloudflare R2 / AWS S3 / Wasabi)
- [ ] **Penyimpanan Media & Upload Tanpa Batas**:
  - Pasang driver `league/flysystem-aws-s3-v3` di backend Laravel.
  - Konfigurasi Cloudflare R2 (gratis 10GB storage, 0 egress / bandwidth fee) atau AWS S3 / Wasabi untuk penyimpanan:
    - Foto Destinasi (`destinations/`)
    - Foto Aktivasi & Dokumentasi (`activations/`)
    - Avatar User & Profil (`avatars/`)
    - Bukti Pembayaran / Struk Cashout (`cashouts/`)
  - Integrasi CDN Cloudflare untuk image delivery berkecepatan tinggi dengan caching otomatis WebP/AVIF.

### 3. Konfigurasi Server Web & Runtime (Nginx + PHP 8.2-FPM + OPcache)
- [ ] **Setup Dedicated Linux VPS (Ubuntu 24.04 LTS)**:
  - Nginx web server dengan HTTP/2, Gzip/Brotli compression, dan SSL otomatis Let's Encrypt (Certbot autorenew).
  - PHP 8.2-FPM dengan OPcache diaktifkan (`opcache.enable=1`, `opcache.jit=tracing`, `opcache.memory_consumption=256`).
  - Redis Server untuk Cache, Session persisten, dan antrean job berkecepatan tinggi.

### 4. Background Workers & Task Scheduler (Supervisor + Cron)
- [ ] **Antrean Asinkron & Penjadwalan Tugas Otomatis**:
  - Supervisor daemon untuk menjalankan `php artisan queue:work --tries=3 --timeout=90`.
  - Cronjob sistem untuk Laravel Scheduler (`* * * * * cd /var/www/jalanbareng/backend && php artisan schedule:run >> /dev/null 2>&1`).
  - Otomatisasi pengiriman notifikasi email, pembuatan ringkasan mingguan, dan pembersihan token kedaluwarsa.

### 5. Domain & Jaringan DNS Resmi (`api.jalanbareng.web.id`)
- [ ] **Konfigurasi Subdomain API Mandiri**:
  - Konfigurasi DNS di IDwebhost / Cloudflare DNS: Buat `A record` untuk `api.jalanbareng.web.id` mengarah ke IP Publik VPS.
  - Pasang SSL Certbot untuk `api.jalanbareng.web.id`.
  - Update `GOOGLE_REDIRECT_URI` ke `https://api.jalanbareng.web.id/api/auth/google/callback` di Google Cloud Console.
  - Update `NUXT_PUBLIC_API_BASE` dan `NUXT_PUBLIC_API_URL` di Vercel Dashboard mengarah ke `https://api.jalanbareng.web.id`.

### 6. Pipeline CI/CD Deployment Otomatis (GitHub Actions)
- [ ] **Zero-Downtime Deployment via Git**:
  - Workflow GitHub Actions: Setiap push ke branch `main`, otomatis test lint/syntax, SSH ke VPS, jalankan `git pull`, `composer install --no-dev --optimize-autoloader`, `php artisan migrate --force`, dan `php artisan optimize`.

### 7. Keamanan & Monitoring Server (Hardening)
- [ ] **Proteksi Server Kelas Enterprise**:
  - UFW Firewall: Hanya izinkan port 80 (HTTP), 443 (HTTPS), dan custom SSH port terproteksi SSH Key (Non-Password login).
  - Fail2ban: Blokir otomatis upaya brute-force pada port SSH dan endpoint API.
  - Sentry / GlitchTip untuk error tracking real-time di backend dan frontend.
  - Uptime monitor gratis (Better Stack / Uptime Kuma) dengan alert notifikasi Telegram/WhatsApp jika server down.

### 8. Sistem Kelola Mitra & Kolaborator (Admin CRUD & Dynamic Directory)
- [ ] **Database & Backend API (`partners`)**:
  - Migration tabel `partners` (`id`, `name`, `category` [brand, government, bumn, community], `role`, `collab_type`, `initial`, `logo_url`, `bg_color`, `text_color`, `website_url`, `sort_order`, `is_active`, timestamps).
  - Model `Partner.php` dan seeder dari direktori 80+ mitra yang sudah ada agar data historis tetap aman.
  - Controller `Api/PartnerController.php`:
    - `GET /api/partners` (Publik: daftar mitra aktif untuk halaman `/mitra`).
    - `POST /api/admin/partners` (Admin/Sanctum: tambah mitra baru beserta upload logo).
    - `PUT /api/admin/partners/{id}` (Admin/Sanctum: edit informasi & peran mitra).
    - `DELETE /api/admin/partners/{id}` (Admin/Sanctum: hapus mitra dengan soft/hard delete).
- [ ] **Panel Admin (`/manage/partners`)**:
  - Halaman `web/pages/manage/partners/index.vue` lengkap dengan tabel data Vuetify 3, filter kategori, search bar, preview logo, form modal dialog tambah/edit, dan dialog konfirmasi hapus.
  - Integrasi shortcut navigasi di `AdminNavDropdown.vue` dan dashboard `manage/index.vue`.
- [ ] **Integrasi Halaman Publik (`/mitra`)**:
  - Mengambil data dinamis via `useApi('/partners')` dengan fallback ke data statis awal jika API sedang offline.

### 9. Scraping & Pengarsipan Data Historis Chapter & Aktivasi (Edisi Awal s/d Sekarang)
- [ ] **Instagram Data Extraction Pipeline**:
  - Ekstraksi / pengarsipan data riwayat aktivasi dari akun Instagram resmi Jalan Bareng dari edisi perdana (Chapter #01) hingga edisi terbaru yang sedang berjalan.
  - Ekstraksi data kunci: Nomor Edisi/Chapter, Nama Wilayah/Kota (Makassar, Gowa, Bone, Palopo, Jaksel, dll.), Tanggal & Waktu Pelaksanaan, Titik Kumpul (Start/Finish), Deskripsi Rute/Tema, dan Poster Kegiatan (resolusi tinggi).
- [ ] **Penyimpanan Aset & Database Seeder**:
  - Skrip pengunduh dan optimasi poster (WebP) ke direktori penyimpanan media (`storage/app/public/activations/posters/` atau Cloudflare R2 / S3).
  - Database seeder komprehensif untuk menyuntikkan seluruh histori perjalanan Jalan Bareng ke tabel `activations` dan `events`, sehingga arsip komunitas lengkap dan dapat ditelusuri warga.

### 10. Redesain Komprehensif UI/UX Panel Admin (Mobile 390px Priority & Desktop Comfort)
- [ ] **Mobile-First Experience (Prioritas Utama Layar ~390px)**:
  - Redesain menyeluruh pengalaman admin di layar smartphone modern (~390px - 414px) agar pengelolaan terasa cepat dan nyaman.
  - Penggantian tabel horizontal lebar dengan kartu modular (card view / stacked list) yang mudah discroll secara vertikal dengan satu tangan.
  - Bottom-sheet dialog untuk form input cepat, filter, dan konfirmasi aksi agar tidak terpotong oleh keyboard virtual mobile.
  - Tap target ramah jempol (minimal 44x44px) dan floating action buttons yang tidak menutupi informasi penting.
- [ ] **Desktop Comfort & Ergonomics**:
  - Layout dashboard lapang dengan visual hierarchy yang jelas, indikator status real-time, panel multi-kolom yang efisien, dan breadcrumbs navigasi cepat.
  - Desain antarmuka profesional yang bersih, modern, dan tidak melelahkan mata untuk pemakaian jangka panjang oleh admin dan super admin.

### 11. Audit Responsivitas Universal Lintas Berbagai Model Perangkat (Cross-Device Responsiveness)
- [ ] **Adaptasi Berbagai Skala Layar HP**:
  - Pengujian & penyesuaian khusus pada layar HP kecil (320px - 360px, misal seri Galaxy A, iPhone SE) agar tidak ada kartu terjepit, padding terlalu sempit, atau teks terpotong.
  - Pengujian pada viewport standar (375px, 390px, 412px, 428px) serta tablet & perangkat layar lipat (600px - 1024px).
  - Eliminasi bug horizontal scroll tak disengaja (`overflow-x`), perataan margin/padding yang konsisten di semua breakpoint Vuetify, serta penyesuaian font scaling agar nyaman dibaca di model HP apapun.



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
