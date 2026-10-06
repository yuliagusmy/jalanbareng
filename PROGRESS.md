# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

**Fitur Kelola Mitra (BACKLOG section 8)** — belum dimulai, siap dikerjakan.

Scope yang perlu dibangun:
- Backend: migration tabel `partners`, Model `Partner.php`, `PartnerController.php` dengan endpoint publik (`GET /api/partners`) dan admin CRUD (`POST/PUT/DELETE /api/admin/partners`)
- Frontend admin: `web/pages/manage/partners/index.vue` — tabel + filter + form modal
- Frontend publik: `web/pages/mitra.vue` — halaman daftar mitra

## ⏳ File Belum Di-commit

Tidak ada.

## ✅ Terakhir Diselesaikan

- `fix: batch replace all invalid Vuetify utility classes across codebase`
  - 13 file difix, 37 penggantian class invalid (ga-1.5→ga-2, mb-2.5→mb-3, mr-0.5→mr-1, dll.)
  - File: AuthPromptDialog, register, profile/[id], login, ActivationTestimonials, ActivationCard, aktivasi/index, SubmitStoryDialog, cerita/index, manage/activations/index, destinations/index, LargeDestinationMapSection, DestinationWalkerGuide
- `fix: mobile responsiveness audit fixes for detail pages` — events, aktivasi, destinations detail pages
- `feat: add accessibility and scroll indicators to CategorySection`
- `fix: collapsible filter panel for admin pages on mobile 390px`

## 🐛 Isu yang Ditemukan

- `DestinationCard.vue` di `web/components/` tidak dipakai (tidak ada import). Bisa dihapus kapan saja.

## 📋 Antrian Berikutnya (dari BACKLOG.md)

1. **Kelola Mitra (BACKLOG section 8)** — sedang dalam antrian, scope ada di atas
2. **Fitur I/J dari BACKLOG** — Full-screen map mode & GPS recording (kompleks, prioritas rendah)

### Cara Melanjutkan (Kelola Mitra)

1. Buat migration: `php artisan make:migration create_partners_table`
2. Buat model: `php artisan make:model Partner`
3. Buat controller: `php artisan make:controller Api/PartnerController`
4. Tambahkan route di `backend/routes/api.php`
5. Buat halaman admin: `web/pages/manage/partners/index.vue`
6. Buat halaman publik: `web/pages/mitra.vue`
