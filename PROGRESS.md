# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

Tidak ada.

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

## 📋 Antrian Berikutnya

1. **Fitur L lanjutan** — statistik komunitas (total km dijelajahi), checkpoint system GPS
2. **Halaman profil user publik** — `/profil/[id]` untuk lihat kontribusi member lain
3. **Notifikasi in-app** — sudah ada backend (`notifications` table), perlu frontend bell yang lebih lengkap
4. **Infrastructure** — setup VPS/MySQL production yang lebih stabil (BACKLOG section 1-7)
