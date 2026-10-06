# Progress Pengerjaan Jalan Bareng

> Terakhir diperbarui: Oktober 2026 oleh Kiro

## 🔄 Sedang Dikerjakan / Belum Selesai

Tidak ada task in-progress saat ini. Semua perubahan sudah di-commit.

## ⏳ File Belum Di-commit

Tidak ada.

## ✅ Terakhir Diselesaikan

- `fix: collapsible filter panel for admin pages on mobile 390px` — di Events, Destinations, Users, Activations: mobile xs menampilkan search bar + tombol filter toggle dengan badge jumlah filter aktif; filter select muncul/hilang via v-expand-transition
- `fix: add mobile card view for admin tables (events, destinations, stories)` — tabel admin diubah ke card view di xs breakpoint
- `fix: optimize all admin pages stats cards for mobile 390px` — stats cards vertikal compact 2 kolom di Stories, Events, Users, Activations
- `fix: make admin page headers mobile-responsive` — header stack vertikal di mobile, tombol full-width
- `fix: optimize admin stats cards for mobile 390px` — Destinations & Categories

## 🐛 Isu yang Ditemukan

Tidak ada isu aktif saat ini.

## 📋 Antrian Berikutnya (dari BACKLOG.md)

Berdasarkan sisa item di BACKLOG.md:

1. **Fitur E** — Like & Komentar di halaman detail cerita `/cerita/[slug]`
   - Tombol like pakai endpoint `POST /likes/toggle` (sudah ada)
   - Section komentar pakai endpoint `GET/POST /comments` (sudah ada)
   - Perlu cek `CommentController` & `LikeController` support `commentable_type = story`

2. **Audit responsivitas halaman publik** (BACKLOG section 11)
   - Halaman `/destinations`, `/events`, `/cerita` di 320px–390px
   - Eliminasi bug horizontal scroll, margin/padding konsisten

3. **Fitur I** — Full-screen map mode di halaman destinasi (kompleks, prioritas lebih rendah)

4. **Migrasi production** — Setup VPS, MySQL, Object Storage (BACKLOG section Future Features)

### Cara Melanjutkan

```bash
# Cek kondisi terkini
git log --oneline -5
git status

# Mulai task berikutnya (Fitur E — Like & Komentar di cerita)
# 1. Cek LikeController dan CommentController
# 2. Update halaman /cerita/[slug]
```
