# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

Tidak ada.

## ⏳ File Belum Di-commit

Tidak ada.

## ✅ Terakhir Diselesaikan

- `feat: add accessibility and scroll indicators to CategorySection`
  - `role="region"` + `aria-label` di wrapper
  - `role="list"` + `role="listitem"` + `aria-label` di scroll container dan tiap item
  - Keyboard navigation: arrow key kiri/kanan scroll satu item, `tabindex="0"`, focus outline
  - Fade gradient di ujung kanan sebagai visual cue "ada konten lagi" (hilang saat scroll ke ujung)
  - Dot indicator di bawah carousel (mobile only, `d-flex d-md-none`), klik dot untuk jump ke posisi
  - `prefers-reduced-motion` support untuk dot transition dan fade
- `fix: replace invalid Vuetify utility classes in homepage components`
- `fix: mobile responsiveness fixes for cerita and destinations pages`
- `fix: collapsible filter panel for admin pages on mobile 390px`

## 🐛 Isu yang Ditemukan

- `DestinationCard.vue` di `web/components/` tidak dipakai (tidak ada import). Bisa dihapus kapan saja.

## 📋 Antrian Berikutnya (dari BACKLOG.md)

1. **Audit halaman detail** — `/destinations/[id]`, `/aktivasi/[slug]`, `/events/[id]` belum diaudit untuk 390px.
2. **Kelola Mitra (BACKLOG section 8)** — Fitur baru: tabel `partners`, CRUD admin, halaman publik `/mitra`.
3. **Fitur I/J dari BACKLOG** — Full-screen map mode & GPS recording (kompleks, prioritas rendah).
