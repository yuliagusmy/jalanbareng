# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

Tidak ada.

## ⏳ File Belum Di-commit

Tidak ada.

## ✅ Terakhir Diselesaikan

- `fix: replace invalid Vuetify utility classes in homepage components`
  - `CommunityStoriesSection.vue`: `ga-1.5` → `ga-2`
  - `FeaturedActivationSection.vue`: `mb-md-2.5` → `mb-2` (Vuetify tidak punya step 0.5 di utility spacing)
- `fix: mobile responsiveness fixes for cerita and destinations pages`
  - `cerita/index.vue`: `ga-1.5` → `ga-2`, `pa-2.5` → `pa-3`, search field min-width jadi responsive class
  - `destinations/index.vue`: CTA button diizinkan wrap teks di 320px
- `fix: collapsible filter panel for admin pages on mobile 390px`
- `fix: add mobile card view for admin tables (events, destinations, stories)`

## 🐛 Isu yang Ditemukan

- `DestinationCard.vue` di `web/components/` tidak dipakai (tidak ada import). Bisa dihapus kapan saja tapi tidak urgent.

## 📋 Antrian Berikutnya (dari BACKLOG.md)

1. **Aksesibilitas `CategorySection.vue`** — scroll container belum punya `aria-label` dan tidak ada indikator visual dot/pagination untuk mobile.

2. **Audit halaman detail** — `/destinations/[id]`, `/aktivasi/[slug]`, `/events/[id]` belum diaudit untuk 390px.

3. **Kelola Mitra (BACKLOG section 8)** — Tabel `partners`, CRUD admin, halaman publik `/mitra` — fitur baru yang belum dimulai.

4. **Fitur I/J dari BACKLOG** — Full-screen map mode & GPS recording (kompleks, prioritas rendah).
