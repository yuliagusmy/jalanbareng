# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

Tidak ada.

## ⏳ File Belum Di-commit

Tidak ada.

## ✅ Terakhir Diselesaikan

- `feat: add Kelola Mitra feature (partners CRUD + public page)`
  - **Backend**: migration `create_partners_table` (name, category, role, collab_type, initial, logo_url, bg_color, text_color, website_url, sort_order, is_active)
  - **Model**: `Partner.php` dengan scope `active()`
  - **Controller**: `PartnerController.php` — `GET /api/partners` (publik), admin CRUD di `/api/admin/partners`
  - **Admin page**: `web/pages/manage/partners/index.vue` — stats cards, filter, mobile card + desktop table, form dialog dengan live preview, toggle aktif/nonaktif
  - **Public page**: `web/pages/mitra.vue` — hero, grouping per kategori, partner card grid, logo/initial fallback, CTA kolaborasi
- `fix: batch replace all invalid Vuetify utility classes across codebase` — 37 penggantian di 13 file
- `fix: mobile responsiveness audit fixes for detail pages`
- `feat: add accessibility and scroll indicators to CategorySection`

## 🐛 Isu yang Ditemukan

- Migration `create_partners_table` belum dijalankan di lokal (PHP 8.0 di mesin dev tidak kompatibel, butuh PHP 8.2+). Jalankan di production setelah deploy: `php artisan migrate`
- Halaman `/mitra` dan `/manage/partners` belum ada di navbar/sidebar navigasi — perlu ditambahkan agar bisa ditemukan user.
- `DestinationCard.vue` di `web/components/` tidak dipakai, bisa dihapus.

## 📋 Antrian Berikutnya

1. **Tambahkan link `/mitra` ke navbar** dan `/manage/partners` ke sidebar navigasi admin
2. **Jalankan migration** di production setelah deploy: `php artisan migrate`
3. **Fitur I/J dari BACKLOG** — Full-screen map mode & GPS recording (kompleks, prioritas rendah)
