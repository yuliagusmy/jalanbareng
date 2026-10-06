# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

Tidak ada task in-progress saat ini.

## ⏳ File Belum Di-commit

Tidak ada.

## ✅ Terakhir Diselesaikan

- `fix: mobile responsiveness fixes for cerita and destinations pages`
  - `cerita/index.vue`: `ga-1.5` → `ga-2` (valid Vuetify), `pa-2.5` → `pa-3` (valid Vuetify), inline `min-width: 170px` dihapus diganti class `.stories-search-field` dengan responsive min-width (140px mobile, 170px sm+)
  - `destinations/index.vue`: `.cta-contrib-btn` di mobile diubah dari `white-space: nowrap; height: 40px` ke `white-space: normal; height: auto; min-height: 40px` agar tidak overflow di 320px
- `docs: add PROGRESS.md and agent handoff protocol to AGENTS.md` — Section 11 AGENTS.md + file PROGRESS.md dibuat
- `fix: collapsible filter panel for admin pages on mobile 390px` — Events, Destinations, Users, Activations: search bar selalu tampil + tombol filter toggle dengan badge count
- `fix: add mobile card view for admin tables (events, destinations, stories)` — tabel admin diganti card view di xs breakpoint
- `fix: optimize all admin pages stats cards for mobile 390px` — stats cards vertikal compact 2 kolom

## 🐛 Isu yang Ditemukan

- `DestinationCard.vue` di `web/components/` tidak dipakai di mana pun (tidak ada import). Bisa dihapus kapan saja tapi tidak urgent.

## 📋 Antrian Berikutnya (dari BACKLOG.md)

1. **Audit sub-komponen homepage** — `EditorialCollageHero`, `WeeklyRegistrationHub`, `FeaturedStorySection`, dll. belum diaudit untuk 390px. Homepage modular, semua isu ada di sub-komponen.

2. **Aksesibilitas `CategorySection.vue`** — scroll container belum punya `aria-label` dan tidak ada indikator visual (dot pagination) bahwa ada item di luar layar.

3. **Fitur I/J dari BACKLOG** — Full-screen map mode & GPS recording di halaman destinasi (kompleks, prioritas rendah).

4. **Kelola Mitra (BACKLOG section 8)** — Tabel `partners`, CRUD admin, halaman publik `/mitra`.

### Cara Melanjutkan

```bash
git log --oneline -5
git status
```
