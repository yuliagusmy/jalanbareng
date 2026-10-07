# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

### Dokumentasi Migrasi VPS
- ✅ `MIGRATION-VPS.md` — dokumentasi lengkap migrasi dari Railway ke VPS Biznet Gio
- ✅ `scripts/install-stack.sh` — auto-install LEMP stack (Nginx, PHP 8.2, MySQL 8)
- ✅ `scripts/backup-mysql.sh` — auto-backup database ke Cloudflare R2 (cron job)
- ✅ `scripts/restore-backup.sh` — restore database dari backup R2 (interactive)
- ✅ `scripts/deploy-laravel.sh` — deploy/update Laravel application
- ✅ `scripts/README.md` — panduan penggunaan semua script

**Status:** Dokumentasi selesai, siap untuk eksekusi migrasi kapan saja

## ⏳ File Belum Di-commit

Tidak ada.

## ✅ Terakhir Diselesaikan

- `fix: replace backslash-in-string validation with closure for likes and comments` — fix validasi `in:` rule dengan backslash yang menyebabkan 422 di production
- `feat: user can edit own story + fix silent comment errors` — user bisa edit cerita sendiri (pending/rejected), status reset ke pending, tombol Edit di author card
- `feat: GPX export for walking tour routes` — tombol Ekspor GPX di section rute event walking, composable useGpxExport.ts
- `feat: full-screen map mode for /destinations on mobile (Fitur I)` — DestinationMobileMapView, DestinationDirectorySheet, useGeolocation
- `feat: add Kelola Mitra feature (partners CRUD + public page)` — migration, model, controller, admin page, public page /mitra

## 🐛 Isu yang Ditemukan

- Tombol edit cerita hanya muncul untuk tulisan dengan status `pending` atau `rejected` — tulisan yang sudah `approved` tidak bisa diedit langsung (by design, harus hubungi admin)
- Railway perlu redeploy manual setelah push karena auto-deploy kadang tidak trigger
- **DATA LOSS:** User-created events hilang setelah deploy production karena auto-seed logic di `CategoryController` & `ActivationController` (sudah diperbaiki)
- **NO BACKUP:** Railway free plan tidak ada backup otomatis → solusi: migrasi ke VPS dengan auto-backup system

## 📋 Antrian Berikutnya

1. **[PRIORITAS] Migrasi ke VPS** — eksekusi migrasi dari Railway ke VPS Biznet Gio (Rp 50k/bulan), setup auto-backup system, dokumentasi sudah siap di `MIGRATION-VPS.md`
2. **Fitur L lanjutan** — statistik komunitas (total km dijelajahi), checkpoint system GPS
3. **Halaman profil user publik** — `/profil/[id]` untuk lihat kontribusi member lain
4. **Notifikasi in-app** — sudah ada backend (`notifications` table), perlu frontend bell yang lebih lengkap
5. **Performance optimization** — lazy load images, bundle splitting, cache strategy (BACKLOG section 10)
