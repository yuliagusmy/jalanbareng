# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

Tidak ada.

## ⏳ File Belum Di-commit

Tidak ada.

## ✅ Terakhir Diselesaikan

- `feat: add cashouts to admin nav dropdown with pending badge` — entry Pencairan Poin di admin dropdown, badge count pending, fix py-2-5 invalid class, tambah pending_cashouts ke AdminStatsController
- `fix: optimize cashouts admin page for mobile + remove unused DestinationCard` — stats compact 2 kolom, mobile card view, header responsive, hapus DestinationCard.vue yang tidak dipakai
- `feat: add Kelola Mitra link to admin nav dropdown` — tambah entry Kelola Mitra ke section Administrasi Sistem
- `feat: add Kelola Mitra feature (partners CRUD + public page)` — migration, model, controller, admin page, public page

## 🐛 Isu yang Ditemukan

- Migration `create_partners_table` belum dijalankan (PHP lokal 8.0, butuh 8.2+). Jalankan di production: `php artisan migrate`

## 📋 Antrian Berikutnya

1. **Fitur I/J dari BACKLOG** — Full-screen map mode & GPS recording (sedang dirancang ulang oleh user)
2. **Scraping data historis** (BACKLOG section 9) — butuh tool eksternal
3. **Infrastructure/DevOps** (BACKLOG section 1-7) — VPS, MySQL, CI/CD
