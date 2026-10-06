# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

Tidak ada.

## ⏳ File Belum Di-commit

Tidak ada.

## ✅ Terakhir Diselesaikan

- `fix: mobile responsiveness audit fixes for detail pages`
  - `events/[id]/index.vue`: `ga-1.5` → `ga-2`, `mb-2.5` → `mb-3`, `mt-0.5` → `mt-1`
  - `aktivasi/[slug].vue`: `mr-1.5` → `mr-2`, `mb-0.5` → `mb-1`, `mr-0.5` → `mr-1`, `mb-1.5` → `mb-2`
  - `destinations/[id]/index.vue`: tambah `overflow-wrap: break-word; word-break: break-word` pada `.hero-title`; tambah `mask-image` fade hint pada `.quick-stats` di mobile sebagai scroll hint visual
- `feat: add accessibility and scroll indicators to CategorySection`
- `fix: replace invalid Vuetify utility classes in homepage components`
- `fix: mobile responsiveness fixes for cerita and destinations pages`
- `fix: collapsible filter panel for admin pages on mobile 390px`

## 🐛 Isu yang Ditemukan

- `DestinationCard.vue` di `web/components/` tidak dipakai (tidak ada import). Bisa dihapus kapan saja.
- Masih ada invalid Vuetify utility classes di file lain yang belum difix: `register.vue`, `login.vue`, `aktivasi/index.vue`, `profile/[id].vue`, `manage/activations/index.vue`, `ContributorPointsWallet.vue`, `SubmitStoryDialog.vue`. Bisa difix dalam satu batch berikutnya.

## 📋 Antrian Berikutnya (dari BACKLOG.md)

1. **Fix sisa invalid Vuetify utility classes** — batch cleanup di file-file yang ditemukan saat audit
2. **Kelola Mitra (BACKLOG section 8)** — Fitur baru: tabel `partners`, CRUD admin, halaman publik `/mitra`
3. **Fitur I/J dari BACKLOG** — Full-screen map mode & GPS recording (kompleks, prioritas rendah)

### Cara Melanjutkan

```bash
git log --oneline -5
git status
```
